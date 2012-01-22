<?php

namespace keymedia\models\image;

use \eZMimeType;
use \eZHTTPFile;
use \eZContentObjectVersion;
use \eZURLAliasML;
use \ezpI18n;
use \keymedia\models\Backend;
use \keymedia\models\Image;
use \Exception;
use \ezote\lib\Inflector;

class Handler
{
    protected $attr;
    protected $_backend;
    protected $_image;
    protected $attributeValues = false;

    /**
     * Cache of current version object
     * @var eZContentObjectVersion|false
     */
    protected $_version = false;

    /**
     * Cache of scale versions available
     * @var array
     */
    protected $_toScale = false;

    /**
     * Construct a new handler, wrapping an ezContentObjectAttribute instance
     *
     * @param eZContentObjectAttribute $attribute
     */
    public function __construct($attribute = false)
    {
        if ($attribute)
            $this->attr = $attribute;
    }

    /**
     * Upload image to KeyMedia and connect to this attribute afterwards
     *
     * @param eZHTTPFile|string $file The uploaded image or a local file
     * @param array $tags Tags to add to image in KeyMedia
     * @param string $title Alternative image text
     *
     * @return \keymedia\models\Image|false
     */
    public function uploadFile($file, array $tags = array(), $title = '')
    {
        if ($file instanceof \eZHTTPFile)
            $filepath = $file->Filename;
        elseif (is_string($file))
            $filepath = $file;

        $filename = $this->imageName($this->attr, $this->version());
        $image = $this->backend()->upload($filepath, $filename, $tags, compact('title'));
        $this->setImage($image->id, $image->host(), $image->ending());
        return $image;
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
            throw new Exception(__CLASS__ . '::' . __METHOD__ . ' called without an image connection made first');

        $filename = $this->imageName($this->attr, $this->version(), false, $name);

        // Push to backend
        $backend = $this->backend();
        $resp = $backend->addVersion($data['id'], $filename, $transformations);

        if (isset($resp->error))
            throw new Exception('Backend failed: ' . $resp->error);

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
        // Use either passed language or the attributes language_code
        $language = $language ?: $attr->attribute('language_code');

        // Use version name of default to name
        $name = $version->versionName($language) ?: $version->name($language);
        // Finally fall back ona  default name
        $name = $name ?: ezpI18n::tr( 'kernel/classes/datatypes', 'image', 'Default image name' );
        if ($postfix) $name .= '-' . $postfix;
        $name .= implode('-', array('', $attr->ContentObjectID, $attr->Version));
        return Inflector::slug($name);
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

    /**
     * Update the image set in db for the ContentObjectAttribute loaded
     * in the handler at the moment.
     *
     * @param string $id Remote id from KeyMedia
     * @param string $host Host that will serve the image later on
     * @return bool
     */
    public function setImage($id, $host, $ending = 'jpg')
    {
        if (!$this->hasImage($id))
            $this->values(compact('id', 'host', 'ending'));
        return true;
    }

    /**
     * Check if Handlers attribute has image set
     *
     * @param string|null $id
     * @return bool
     */
    public function hasImage($id = null)
    {
        $values = $this->values();
        if (!($hasId = isset($values['id'])))
            return false;
        return $id ? $id === $values['id'] : true;
    }

    /**
     * @throws Exception
     * @param string|array|null $format
     * @return array
     */
    public function media($format = array(300, 200))
    {
        $availableFormats = $this->values();

        // If format array, go on and just rescale
        if (!$availableFormats && !is_array($format))
            return null; //throw new Exception("Image attribute does not contain any information.");

        // Fetch image data and build original part of return array
        if (!($image = $this->image())) return null;
        if ($data = $image->data())
        {
            $originalImageInfo =  array(
                'size' => $data->file->size,
                'width' => $data->file->width,
                'height' => $data->file->height,
                'name' => isset($data->file->name) ? $data->file->name : null,
                'ratio' => $data->file->ratio
            );
        }

        // Init version to null
        $version = null;

        if (!isset($format))
        {
            $urls = array_filter($availableFormats['versions'], function($v) {
                return isset($v['url']);
            });
            $version = array_shift($urls);

        }
        elseif (is_array($format))
        {
            $mediaUrl = $this->thumb($format[0], $format[1]);
            $version = array();
        }
        else {
            if (isset($availableFormats['versions']) && isset($availableFormats['versions'][$format]))
                $version = $availableFormats['versions'][$format];
            else
            {
                // No version available - we need to autogenerate
                if (!$formatSize = $this->formatSize($format))
                    return null;
                list($versionWidth, $versionHeight) = $formatSize;

                $bestFit = $image->boxInside($versionWidth, $versionHeight);
                $bestFit['size'] = array($versionWidth, $versionHeight);

                // Autocreate the best fit version
                self::addVersion($format, $bestFit);

                $availableFormats = $this->values();
                $version = $availableFormats['versions'][$format];

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
            $url = $mediaUrl;
            return compact('url') + $mediaInfo;
        }
        else
        {
            // Build simple reply array
            list($width, $height) = $version['size'];
            $coords = $version['coords'];
            if ($width && $height)
                $ratio = $width / $height;
            $mediaInfo = array_merge($mediaInfo, compact('width', 'height', 'ratio', 'coords'));
        }

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
            $mediaUrl = "http://" . $image->host()   . $version['url'] . $ext;

            $mediaInfo['url'] = $mediaUrl;

            return $mediaInfo;
        }
        else {
            //throw new Exception("Unable to generate version.");
        }

    }

    /**
     * Save or get values for this content object attribute
     *
     * @param array|false $save Values to save
     * @return mixed
     */
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
     * Get size for a named format as defined in
     * scale rules for this specific use of an attribute for
     * a content class
     *
     * @param string $format Name of format to get size for
     * @return array array(width, height)
     */
    protected function formatSize($format) {
        $toScale = $this->toScale();
        $version = array_filter($toScale, function($version) use($format) {
            return isset($version['name']) && $format === $version['name'];
        });
        if (!$version) return false;
        $version = array_shift($version);
        return $version['size'];
    }

    /**
     * Cached loading of the KeyMedia Backend for this Handlers attribute
     *
     * @return \keymedia\models\Backend
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
     * @return \keymedia\models\Image
     */
    protected function toScale()
    {
        if (!$this->_toScale)
        {
            $class = $this->attr->contentClassAttribute();

            // Array of versions in db
            $values = $this->values() + array('versions' => array());
            $versions = $values['versions'];

            // 1 line = 1 scaling
            $data = json_decode($class->attribute(\KeyMedia::FIELD_JSON));
            $toScale = array();
            // Iterate over class definition sizes
            foreach ($data->versions as $version)
            {
                list($size, $name) = explode(',', $version);
                $size = explode('x', $size);
                // Lookup key in my versions
                $row = isset($versions[$name]) ? $versions[$name] : array();
                $toScale[] = compact('size') + $row + compact('name');
            }
            $this->_toScale = $toScale;
        }
        return $this->_toScale;
    }

    /**
     * Cached loading of the image for this content object attribute
     *
     * @return \keymedia\models\Image
     */
    protected function image()
    {
        if (!$this->_image)
        {
            $backend = $this->backend();

            $data = $this->attr->attribute(\KeyMedia::FIELD_VALUE);
            $data = json_decode($data);

            if (is_object($data) && isset($data->id) && $data->id)
                $this->_image = $backend->get($data->id);
            else
                $this->_image = false;
        }
        return $this->_image;
    }

    /**
     * Get current version object
     *
     * @return \eZContentObjectVersion
     */
    protected function version()
    {
        if (!$this->_version)
        {
            $this->_version = eZContentObjectVersion::fetchVersion(
                $this->attr->attribute('version'),
                $this->attr->attribute('contentobject_id')
            );
        }
        return $this->_version;
    }
    /**
     * Build a thumb string for the currently selected image
     *
     * @param int $width
     * @param int $height
     * @return string
     */
    protected function thumb($width, $height)
    {
        $data = $this->values();
        $host = $data['host'];
        $ending = isset($data['ending']) ? $data['ending'] : 'jpg';
        $id = $data['id'];
        return 'http://' . $host . "/{$width}x{$height}/{$id}.{$ending}";
    }
}
