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
     * Initializes the content object attribute with the uploaded HTTP file
     *
     * @param eZHTTPFile $file The uploaded image
     * @param string $alt Alternative image text
     *
     * @return bool
     */
    public function uploadFile(eZHTTPFile $file, $alt = '')
    {
        /**
         * Procedure:
         *
         * 1: Increase serial number (appended on filename if needed)
         * 2: Get mime data
         * 3: Clear old aliases
         * 4: Get storage name
         */
        $this->postfix++;

        $mimeData = eZMimeType::findByFileContents($file->attribute('filename'));
        if (!$mimeData['is_valid'])
        {
            $mimeData = eZMimeType::findByName($file->attribute('mime_type'));
            if (!$mimeData['is_valid'])
                $mimeData = eZMimeType::findByURL($file->attribute('original_filename'));
        }

        //$this->removeAliases( $attr );

        $version = eZContentObjectVersion::fetchVersion(
            $this->attr->attribute('version'),
            $this->attr->attribute('contentobject_id')
        );

        $filename = $this->imageName($this->attr, $version, $this->postfix);
        $filepath = $this->imagePath($this->attr, $version, true);

        // Uses out params for $mimeData to reassign some values in the array
        // TODO Patch this in ezpublish and pull request it
        eZMimeType::changeBaseName($mimeData, $filename);
        eZMimeType::changeDirectoryPath($mimeData, $filepath);

        $file->store(false, false, $mimeData);

        //$originalFilename = $file->attribute('original_filename');
        return $this->store($file, $mimeData, array('source' => 'computer'));
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
    protected function store($file, $mimeData, array $extras = array())
    {
        $data = array();
        $data['original'] = array(
            'filename' => $file->Filename,
            'originalFilane' => $file->OriginalFilename,
            'url' => $mimeData['url'],
            'size' => $file->Size,
            'mime' => array(
                'full' => $file->Type,
                'category' => $file->MimeCategory,
                'ending' => $file->MimePart
            )
        ) + $extras + $this->values();

        $this->values($data);

        return true;
    }

    /**
     * Create a new version of the currently loaded image attributes image
     * Will both store the version-information (slug, coords, size) locally
     * as well as notify KeyMedia about the vanity url to make it actually work
     *
     * Usage:
     * <code>
     *   $width = 100;
     *   $height = 100;
     *   $coords = array($x, $y, $x2, $y2);
     *   $handler->addVersion('my-slug-500x500', compact('width', 'height', 'coords'));
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
        $name = $name ?: ezpI18n::tr( 'kernel/classes/datatypes', 'image', 'Default image name' );

        return \eZURLAliasML::convertToAlias($name) . '-' . $postfix;
    }

    /**
     * The path is calculated by using information from the current object and version.
     * If the object is in the node tree it will contain a path that matches the node path,
     * if not it will be placed in the versioned storage repository.
     *
     * @param object $attr The attribute
     * @param object $version The version object
     * @param bool $isImageOwner
     * @return string The storage path for the image.
     */
    public function imagePath($attr, $version, $isImageOwner = null)
    {
        $ini = eZINI::instance('image.ini');
        $useVersion = false;
        if ($isImageOwner === null)
            $isImageOwner = $this->isImageOwner();

        if ($version->attribute('status') === eZContentObjectVersion::STATUS_PUBLISHED || !$isImageOwner)
        {
            $contentObject = $version->attribute('contentobject');
            if ($mainNode = $contentObject->attribute('main_node'))
            {
                $contentImageSubtree = $ini->variable('FileSettings', 'PublishedImages');
                $pathString = $mainNode->pathWithNames();
                $pathString = function_exists('mb_strtolower') ? mb_strtolower($pathString) : strtolower($pathString);
                $pathString = $contentImageSubtree . '/' . $pathString;
            }
            else
            {
                $contentImageSubtree = $ini->variable('FileSettings', 'VersionedImages');
                $pathString = $contentImageSubtree;
                $useVersion = true;
            }
        }
        else
        {
            $contentImageSubtree = $ini->variable('FileSettings', 'VersionedImages');
            $pathString = $contentImageSubtree;
            $useVersion = true;
        }

        $identifierString = $attr->attribute('id') . ($useVersion ? '/' : '-');
        $identifierString .= $attr->attribute('version') . '-' . $attr->attribute('language_code');

        $imagePath = eZSys::storageDirectory() . '/' . $pathString . '/' . $identifierString;
        return $imagePath;
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
            if (!$name) $name = false;
            // Lookup key in my versions
            $key = $name . '-' . implode('x', $size);
            $toScale[] = isset($versions[$key]) ? $versions[$key] : compact('name', 'size');
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
