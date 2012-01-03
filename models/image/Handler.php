<?php

namespace ezr_keymedia\models\image;

use \eZMimeType;
use \eZHTTPFile;
use \eZContentObjectVersion;
use \eZCharTransform;
use \eZURLAliasML;
use \eZINI;
use \eZSys;
use \eZImageShellHandler;
use \ezr_keymedia\models\Backend;

// TODO When parsing a content attribute do an API call to
// the KeyMedia and get more image information (crops, filesize etc)
// instead of having this happen multiple times
class Handler
{
    protected $attr;
    protected $_backend;
    protected $_image;
    protected $attributeValues = false;
    protected $postfix = 0;

    public function parseContentObjectAttribute($attribute)
    {
        $this->attr = $attribute;
    }

    /**
     * Upload image to KeyMedia and connect to this attribute afterwards
     *
     * @param eZHTTPFile|string $file The uploaded image or a local file
     * @param array $tags Tags to add to image in KeyMedia
     * @param string $alt Alternative image text
     *
     * @return \ezr_keymedia\models\Image|false
     */
    public function uploadFile($file, array $tags = array(), $alt = '')
    {
        //$this->removeAliases( $attr );

        if ($file instanceof \eZHTTPFile)
            $filepath = $file->Filename;
        elseif (is_string($file))
            $filepath = $file;

        $version = eZContentObjectVersion::fetchVersion(
            $this->attr->attribute('version'),
            $this->attr->attribute('contentobject_id')
        );

        $filename = $this->imageName($this->attr, $version, $this->postfix);

        $data = array('title' => $alt);
        return $this->backend()->upload($filepath, $filename, $tags, $data);
    }

    protected function values($save = false)
    {
        if ($save)
        {
            $this->attr->setAttribute('data_text', json_encode($save));
            $this->attributeValues = $save;
            return $this->attr->storeData();
        }
        else
        {
            if (!$this->attributeValues)
            {
                $data = $this->attr->attribute('data_text');
                if (is_string($data) && strlen($data) > 0)
                    $data = json_decode($data, true);
                else
                    $data = array();
                $this->attributeValues = $data;
            }
            return $this->attributeValues;;
        }
    }

    /**
     * Create a new version of the currently loaded image attributes image
     * Will both store the version-information (slug, coords, size) locally
     * as well as notify KeyMedia about the vanity url to make it actually work
     *
     * Usage:
     * <code>
     *   $size = array(100,100);
     *   $coords = array($x, $y, $x2, $y2);
     *   $handler->addVersion('my-slug-500x500', compact('size', 'coords'));
     * </code>
     *
     * Both `width`, `height` and `coords` are not needed
     *
     * @param string $name The postfix to use for the filename
     * @param array $transformations
     * @return string Returns the image url
     */
    public function addVersion($name, array $transformations = array())
    {
        // Fetch existing values
        $data = $this->values();

        if (!isset($data['id']))
            throw new \Exception(__CLASS__ . '::' . __METHOD__ . ' called without an image connection made first');

        $version = eZContentObjectVersion::fetchVersion(
            $this->attr->attribute('version'),
            $this->attr->attribute('contentobject_id')
        );
        $filename = $this->imageName($this->attr, $version, false, $name);
        $filename = mb_strtolower($filename);

        // Push to backend
        $backend = $this->backend();
        $resp = $backend->addVersion($data['id'], $filename, $transformations);

        if (isset($resp->error))
            throw new \Exception('Backend failed: ' . $resp->error);

        // Ensure a versions index exists in the data
        $data += array('versions' => array());

        $url = $resp->url;
        $scaling = compact('name', 'url') + $transformations;
        $data['versions'][$name] = $scaling;

        // Save values
        $this->values($data);

        return $scaling;
    }

    /**
     * The image name will generated from the name of the current version.
     * If this is empty it will use the object name or the alternative text.
     *
     * This ensures that the image has a name which corresponds to the object it belongs to.
     *
     * The normalization ensures that the name only contains filename and URL friendly characters.
     *
     * @param object $attribute
     * @param object $version
     * @param string $language
     * @return string Normalized name for the image.
    */
    public function imageName($attr, $version, $language = false, $postfix = '')
    {
        // Initialize transformation system
        $trans = eZCharTransform::instance();

        // Use either passed language or the attributes language_code
        $language = $language ?: $attr->attribute('language_code');

        // Use version name of default to name
        $name = $version->versionName($language) ?: $version->name($language);
        // Finally fall back ona  default name
        $name = $name ?: \ezpI18n::tr( 'kernel/classes/datatypes', 'image', 'Default image name' );

        $name = \eZURLAliasML::convertToAlias($name);
        if ($postfix) $name .= '-' . $postfix;
        return $name;
    }

    /**
     * Tell the template if an attribute is accessed
     * Is called before `attribute()` is called
     *
     * @param string $name Name of attribute
     * @return bool
     */
    public function hasAttribute($name)
    {
        $ok = array('backend', 'thumb', 'filesize', 'mime_type', 'image', 'toscale');
        if (in_array($name, $ok)) return true;
        $values = $this->values();
        return isset($values[$name]);
    }

    /**
     * Return the value for an attribute
     * Origins from {$attribute.content.foo} -> attribute('foo')
     *
     * @param string $name Name of attribute
     * @return mixed
     */
    public function attribute($name)
    {
        $image = $this->image();
        switch ($name) {
            case 'backend':
                return $this->backend();
            case 'selected':
                return $this->selected();
            case 'toscale':
                return $this->toScale();
            case 'image':
                return $image;
            case 'thumb':
                return $image ? $image->thumb(300, 200) : '';
            case 'filesize':
                return $image ? $image->file->size : 0;
            case 'mime_type':
                return $image ? $image->file->type : '';
            default:
                $values = $this->values();
                return $values[$name];
        }
    }

    protected function thumb($width, $height)
    {
        $data = $this->attr->attribute(\KeyMedia::FIELD_VALUE);
        $data = json_decode($data);

        $url = 'http://' . $data->host . "/{$width}x{$height}/{$data->id}.jpg";
        return $url;
    }

    /**
     * @throws \Exception
     * @param string|array|null $format
     * @return array
     */
    public function media($format=null)
    {
        $availableFormats = json_decode($this->attr->DataText, true);

        // If format array, go on and just rescale
        if (isset($format) && !is_array($availableFormats) && !is_array($format))
            return null; //throw new \Exception("Image attribute does not contain any information.");

        // Fetch image data and build original part of return array
        $data = $this->image()->attribute('data');
        $originalImageInfo =  array(
                'url' => $data->file->url,
                'size' => $data->file->size,
                'width' => $data->file->width,
                'height' => $data->file->height,
                'name' => isset($data->file->name) ? $data->file->name : null,
                'ratio' => $data->file->ratio
            );

        // Init version to null
        $version = null;

        if (!isset($format)){

            foreach($availableFormats['versions'] as $key => $value){

                if (isset($value['url']))
                {
                    $version = $value;
                    break;
                }
            }

        }
        else
        {
            // Create a simple rescale
            if (is_array($format))
            {
                $mediaUrl = $this->thumb($format[0], $format[1]);
                $version = array();
            }
            else {
                $version = $availableFormats['versions'][$format];

                // No version available - we need to autogenerate
                if (!isset($version))
                {

                    list($versionWidth, $versionHeight) = $this->formatSize($format);
                    $bestFit = \ezr_keymedia\models\Image::fitToBox($versionWidth, $versionHeight, $originalImageInfo['width'], $originalImageInfo['height']);

                    $bestFit['size'] = array($versionWidth, $versionHeight);

                    // Autocreate the best fit version
                    self::addVersion($format, $bestFit);

                    $availableFormats = json_decode($this->attr->DataText, true);
                    $version = $availableFormats['versions'][$format];

                }

            }
        }

        $typeArr = explode('/', $data->file->type);
          $mediaInfo = array(
                'mime-type' => $data->file->type,
                'type' => array_shift($typeArr),
                'format' => $format,
                'original' => $originalImageInfo
            );
        if (isset($mediaUrl))
        {
            $mediaInfo['url'] = $mediaUrl;
        }
        else
        {
            // Build simple reply array
            $mediaInfo = array_merge($mediaInfo,array(

                'width' => $version['size'][0],
                'height' => $version['size'][1],
                'ratio' => $version['size'][0]/$version['size'][1],

                'coords' => $version['coords'],

            ));
        }

        if (isset($mediaUrl))
            return $mediaInfo;

        if (isset($version) && !isset($mediaUrl)){

            $ext = '';
            switch($data->file->type)
            {
                case 'image/png' :
                    $ext ='.png';
                    break;
                case 'image/gif' :
                    $ext ='.gif';
                    break;
                default :
                    $ext = '.jpg';
            }
            $mediaUrl = "http://" . $this->image()->host()   . $version['url'] . $ext;

            $mediaInfo['url'] = $mediaUrl;

            return $mediaInfo;
        }
        else {
            //throw new \Exception("Unable to generate version.");
        }

    }

    protected function formatSize($format){
        $versions = $this->toScale();
        $currentFormatSize = null;
        foreach ($versions AS $versionItem)
        {
            // TEMP
            $slug = strtolower($format) . "-" . join('x', $versionItem['size']);
            if ($versionItem['name'] == $slug)
                $currentVersionSize = $versionItem['size'];
        }

        return $currentVersionSize;
    }

    protected function mimeType()
    {
        return '';
    }

    protected function filesize()
    {
        return 100;
    }

    /**
     * Cached loading of the KeyMedia Backend for this Handlers attribute
     *
     * @return \ezr_keymedia\models\Backend
     */
    protected function backend()
    {
        if (!$this->_backend)
        {
            $class = $this->attr->contentClassAttribute();
            $id = $class->attribute(\KeyMedia::FIELD_BACKEND);
            $this->_backend = Backend::first(compact('id'));
        }
        return $this->_backend;
    }

    /**
     * What backend is selected for this content class attribute
     *
     * @return int
     */
    protected function selected()
    {
        return $this->attr->contentClassAttribute()->attribute(\KeyMedia::FIELD_BACKEND);
    }

    /**
     * Cached loading of the image for this content object attribute
     *
     * @return \ezr_keymedia\models\Image
     */
    protected function toScale()
    {
        $class = $this->attr->contentClassAttribute();

        // Array of versions in db
        $values = $this->values() + array('versions' => array());
        $versions = $values['versions'];

        // 1 line = 1 scaling
        $data = json_decode($class->attribute(\KeyMedia::FIELD_JSON));
        $toScale = array();
        foreach ($data->versions as $version)
        {
            list($size, $name) = explode(',', $version);
            $size = explode('x', $size);
            // Lookup key in my versions
            $name = strtolower($name . '-' . implode('x', $size));
            $toScale[] = isset($versions[$name]) ? $versions[$name] : compact('name', 'size');
        }
        return $toScale;
    }

    /**
     * Cached loading of the image for this content object attribute
     *
     * @return \ezr_keymedia\models\Image
     */
    protected function image()
    {
        if (!$this->_image)
        {
            $backend = $this->backend();

            $data = $this->attr->attribute(\KeyMedia::FIELD_VALUE);
            $data = json_decode($data);

            if (is_object($data) && isset($data->id))
                $this->_image = $backend->get($data->id);
            else
                $this->_image = false;
        }
        return $this->_image;
    }
}
