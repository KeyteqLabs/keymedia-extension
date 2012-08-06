<?php

namespace keymedia\models\media;

use \eZMimeType;
use \eZHTTPFile;
use \eZContentObjectVersion;
use \eZURLAliasML;
use \ezpI18n;
use \keymedia\models\Backend;
use \keymedia\models\Media;
use \Exception;
use \ezote\lib\Inflector;
use \keymedia\models\Box;

class Handler
{
    protected $attr;
    protected $_backend;
    protected $_media;
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
     * Upload media to KeyMedia and connect to this attribute afterwards
     *
     * @param eZHTTPFile|string $file The uploaded media or a local file
     * @param array $tags Tags to add to media in KeyMedia
     * @param string $title Alternative media text
     *
     * @return \keymedia\models\Media|false
     */
    public function uploadFile($file, array $tags = array(), $title = '')
    {
        if ($file instanceof \eZHTTPFile)
            $filepath = $file->Filename;
        elseif (is_string($file))
            $filepath = $file;

        $filename = $this->mediaName($this->attr, $this->version());
        $media = $this->backend()->upload($filepath, $filename, $tags, compact('title'));
        $this->setMedia(array(
            'id' => $media->id,
            'host' => $media->host(),
            'type' => $media->type(),
            'ending' => $media->ending()
        ));
        return $media;
    }

    /**
     * Create a new version of the currently loaded media attributes media
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
     * @return string Returns the media url
     */
    public function addVersion($name, array $transformations = array())
    {
        // Fetch existing values
        $data = $this->values();

        if (!isset($data['id']))
            throw new Exception(__CLASS__ . '::' . __METHOD__ . ' called without an media connection made first');

        $filename = $this->mediaName($this->attr, $this->version(), false, $name);

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
     * The media name will generated from the name of the current version.
     * If this is empty it will use the object name or the alternative text.
     *
     * This ensures that the media has a name which corresponds to the object it belongs to.
     *
     * The normalization ensures that the name only contains filename and URL friendly characters.
     *
     * @param object $attribute
     * @param object $version
     * @param string $language
     * @return string Normalized name for the media.
    */
    public function mediaName($attr, $version, $language = false, $postfix = '')
    {
        // Use either passed language or the attributes language_code
        $language = $language ?: $attr->attribute('language_code');

        // Use version name of default to name
        $name = $version->versionName($language) ?: $version->name($language);
        // Finally fall back ona  default name
        $name = $name ?: ezpI18n::tr( 'kernel/classes/datatypes', 'media', 'Default media name' );
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
        $ok = array('backend', 'thumb', 'filesize', 'mime_type', 'media',
            'toscale', 'minsize', 'mediafits');
        if (in_array(strtolower($name), $ok)) return true;
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
        switch ($name) {
            case 'backend':
                return $this->backend();
            case 'selected':
                return $this->selected();
            case 'toscale':
                return $this->toScale();
            case 'minSize':
                return $this->minSize();
            case 'mediaFits':
                $media = $this->getMedia();
                $box = $this->minSize();
                if ($box && $media)
                    return $box->fits($media->box());
                return false;
            case 'media':
                return $this->getMedia();
            case 'thumb':
                $media = $this->getMedia();
                return $media ? $media->thumb(300, 200) : '';
            case 'filesize':
                $media = $this->getMedia();
                return $media ? $media->file->size : 0;
            case 'type':
                return $this->getMedia()->type();
            case 'mime_type':
                $media = $this->getMedia();
                return $media ? $media->file->type : '';
            default:
                $values = $this->values();
                return $values[$name];
        }
    }

    /**
     * Update the media set in db for the ContentObjectAttribute loaded
     * in the handler at the moment.
     *
     * @param string|array $id Remote id from KeyMedia or all data to save
     * @param string $host Host that will serve the media later on
     * @return bool
     */
    public function setMedia($id, $host = false, $ending = 'jpg')
    {
        if (is_array($id)) {
            $values = $id;
            $id = $values['id'];
        }
        else {
            $values = compact('id', 'host', 'ending');
        }
        if (!$this->hasMedia($id)) {
            $this->values($values);
        }
        return true;
    }

    /**
     * Check if Handlers attribute has media set
     *
     * @param string|null $id
     * @return bool
     */
    public function hasMedia($id = null)
    {
        $values = $this->values();
        $hasId = isset($values['id']);
        if (!$hasId) return false;
        return $id ? $id === $values['id'] : true;
    }

    /**
     * @throws Exception
     * @param string|array|null $format
     * @param boolean $fetchInfo Wether to fetch info from mediabase
     * @return array
     */
    public function media($format = array(300, 200), $quality = null, $fetchInfo = false)
    {
        $attributeValues = $this->values();

        // With no attribute values (keymedia id, host etc) we can't do much
        if (!$attributeValues)
            return null;

        /**
         * Figure out what scale-version to use
         * To do this we need to know that the format actually
         * supports scaling, leaving out videos for example
         */
        $version = null;

        if (!isset($format)) {
            $urls = array_filter($attributeValues['versions'], function($v) {
                return isset($v['url']);
            });
            $version = array_shift($urls);

        }
        elseif (is_string($format)) {
            // String as format means a named format from this attributes
            // initial setup in the GUI. An example can be
            // a named format "main" with "500x500" as its settings
            $versions = isset($attributeValues['versions']) ? $attributeValues['versions'] : false;
            if (!$versions || !isset($versions[$format])) {
                if ($this->generateNamedVersion($format))
                    $attributeValues = $this->values();
            }

            $version = $attributeValues['versions'][$format];
        }

        $result = array();
        if (isset($attributeValues['type'])) {
            $result['type'] = $attributeValues['type'];
        }

        if ($result['type'] === 'video') $fetchInfo = true;

        if ($fetchInfo && ($media = $this->getMedia())) {
            if (!isset($result['type'])) {
                $typeArr = explode('/', $media->file->type);
                $result['type'] = array_shift($typeArr);
            }
            $result['mime-type'] = $media->file->type;
            $result['original'] = $this->fetchMediaInfo();
        }

        // For backwards compatibility default the type as its not always
        // set in the local attributes values
        $result += array('type' => 'image');

        if (is_array($format)) {
            return array(
                'url' => $this->thumb($format[0], $format[1], $quality)
            ) + $result;
        }
        else
        {
            // Build simple reply array
            list($width, $height) = $version['size'];
            $coords = $version['coords'];
            if ($width && $height)
                $ratio = $width / $height;
            $result = compact('width', 'height', 'ratio', 'coords') + $result;

            if ($result['type'] === 'image') {
                // Image specific handling
                switch($attributeValues['ending'])
                {
                    case 'png':
                    case 'jpg':
                    case 'gif':
                        $ext = '.' . $attributeValues['ending'];
                        break;
                    default :
                        $ext = '.jpg';
                }
                $host = !empty($media) ? $media->host() : $attributeValues['host'];
                $url = $this->addQualityToUrl($version['url'], $quality);
                $result['url'] = "http://" . $host . $url . $ext;
            }

            return $result;
        }
        return null;
    }

    /**
     * Remove the media
     *
     * @return mixed
     */
    public function remove()
    {
        $this->attr->setAttribute('data_text', '');
        $this->attributeValues = false;
        return $this->attr->storeData();
    }

    /**
     * Report usage of media back to keymedia
     *
     * @param $contentObject
     * @return bool|\keymedia\models\Media
     */
    public function reportUsage($contentObject)
    {
        $values = $this->values();
        if (empty($values['id']))
            return false;

        $attr = $this->attr;
        $version = $this->version();
        $language = $attr->attribute('language_code');

        // Use version name of default to name
        $name = $version->versionName($language) ? : $version->name($language);
        $mainNode = $contentObject->attribute('main_node');

        /**
         * Find main siteaccess and main domain
         */
        $siteINI = \eZINI::instance('site.ini');
        $defaultAccess = $siteINI->variable('SiteSettings', 'DefaultAccess');
        $defaultSiteINI = \eZINI::instance('site.ini.append.php', 'settings/siteaccess/' . $defaultAccess, null, null, null, true);
        $domain = $defaultSiteINI->variable('SiteSettings', 'SiteURL');

        $urlArr = parse_url($domain);
        $pathArr = array_filter(explode('/', $urlArr['path']));
        $url = isset($urlArr['host']) ? $urlArr['host'] . '/' : '';
        $url .= implode('/', $pathArr) . '/' . $mainNode->attribute('url');

        if (!empty($urlArr['scheme']))
            $url = $urlArr['scheme'] . '://' . $url;
        else
            $url = 'http://' . $url;

        /**
         * Create an external id for Keymedia
         */
        $urlArr = parse_url($url);
        $externalId = md5($urlArr['host'] . '|' . $contentObject->ID . '|' . $attr->ID);

        $reference = array(
            'title' => $name,
            'url' => $url,
            'externalId' => $externalId
        );

        $backend = $this->backend();

        return $backend->reportUsage($values['id'], $reference);
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
            return $this->attributeValues;
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
            if ($id)
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
     * Cached loading of the media for this content object attribute
     *
     * @return \keymedia\models\Media
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
     * Get min size an media must be to be used for this attribute instance
     *
     * @return array $width, $height
     */
    protected function minSize()
    {
        $width = $height = 0;
        foreach ($this->toScale() as $version)
        {
            list($w, $h) = $version['size'];
            if ($w > $width) $width = $w;
            if ($h > $height) $height = $h;
        }
        return new Box($width, $height);
    }

    /**
     * Cached loading of the media for this content object attribute
     *
     * @return \keymedia\models\Media
     */
    protected function getMedia()
    {
        if (!$this->_media)
        {
            if (!($backend = $this->backend()))
                return false;

            $data = $this->attr->attribute(\KeyMedia::FIELD_VALUE);
            $data = json_decode($data);

            if (is_object($data) && isset($data->id) && $data->id)
                $this->_media = $backend->get($data->id);
            else
                $this->_media = false;
        }
        return $this->_media;
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
     * Build a thumb string for the currently selected media
     *
     * @param int $width
     * @param int $height
     * @return string
     */
    protected function thumb($width, $height, $quality = false)
    {
        $data = $this->values();
        $host = isset($data['host']) ? $data['host'] : '';
        $ending = isset($data['ending']) ? $data['ending'] : 'jpg';
        $id = isset($data['id']) ? $data['id'] : '';
        if ($quality)
            $quality = 'q' . $quality;
        return 'http://' . $host . "/{$width}x{$height}{$quality}/{$id}.{$ending}";
    }

    /**
     * Add quality param in url to keymedia file
     *
     * @param $url
     * @param $quality
     * @return string
     */
    protected function addQualityToUrl($url, $quality)
    {
        if (!$quality)
            return $url;

        $quality = 'q' . $quality;
        $pathArr = explode('/', $url);
        $index = $pathArr[0] ? 1 : 2;

        /**
         * Add quality param at index
         */
        array_splice($pathArr, $index, 0, $quality);

        return join('/', $pathArr);
    }

    /**
     * Helper to load a specialized set of media information
     * from the KeyMedia backend
     * Will do a http request so this is expensive
     *
     * @return array
     */
    protected function fetchMediaInfo()
    {
        $media = $this->getMedia();
        if ($media && ($data = $media->data())) {
            return array(
                'size' => $data->file->size,
                'width' => $data->file->width,
                'height' => $data->file->height,
                'name' => isset($data->file->name) ? $data->file->name : null,
                'ratio' => $data->file->ratio,
                'remotes' => $this->toArray($data->remotes)
            );
        }
        return false;
    }

    protected function generateNamedVersion($format)
    {
        $formatSize = $this->formatSize($format);
        if ($formatSize && ($media = $this->getMedia())) {
            list($versionWidth, $versionHeight) = $formatSize;
            $bestFit = $media->boxInside($versionWidth, $versionHeight);
            $bestFit['size'] = array($versionWidth, $versionHeight);

            // Autocreate the best fit version
            self::addVersion($format, $bestFit);

            $attributeValues = $this->values();
            $versions = $attributeValues['versions'];
        }
        return null;
    }
    protected function toArray($object) {
        $new = array();
        foreach ($object as $key => $val)
            $new[$key] = is_object($val) ? $this->toArray($val) : $val;
        return $new;
    }
}