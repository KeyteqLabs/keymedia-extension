<?php
/**
 * KeyMedia eZ extension
 *
 * @copyright     Copyright 2011, Keyteq AS (http://keyteq.no/labs)
 */

namespace keymedia\modules\key_media;

use \eZPersistentObject;
use \keymedia\models\Backend;

use \eZHTTPFile;
use \eZHTTPTool;
use \eZContentObjectAttribute;

/**
 * KeyMedia controller
 * Exposes the HTTP actions for KeyMedia integration
 *
 * @author Raymond Julin <raymond@keyteq.no>
 * @since 1.0.0
 */
class KeyMedia extends \ezote\lib\Controller
{

    const LAYOUT = 'pagelayout.tpl';

    /**
     * Renders the KeyMedia dashboard
     *
     * @return \ezote\lib\Response
     */
    public function dashboard()
    {
        return self::response(
            array('backends' => $this->backends()),
            array(
                'template' => 'design:dashboard/dashboard.tpl',
                'left_menu' => 'design:dashboard/left_menu.tpl',
                'pagelayout' => static::LAYOUT
            )
        );
    }

    public function connection($id = null)
    {
        // Edit existing
        if ($id)
        {
            $backend = Backend::first(compact('id'));
        }

        if ($this->http->method('post'))
        {
            if (!isset($backend)) $backend = Backend::create();

            $ezhttp = $this->http->ez();

            $username = $ezhttp->variable('username', false);
            $host = $ezhttp->variable('host', false);
            $api_key = $ezhttp->variable('api_key', false);
            $api_version = $ezhttp->variable('api_version', false);

            $data = compact('id', 'username', 'host', 'api_key', 'api_version');

            $this->save($backend, $data);

            if ($redirectTo = $ezhttp->variable('redirect_to', false))
            {
                $id = $backend->attribute('id');
                header("Location: {$redirectTo}/{$id}");
            }
        }

        $backends = $this->backends();
        return self::response(
            compact('backend', 'backends'),
            array(
                'template' => 'design:dashboard/connection.tpl',
                'left_menu' => 'design:dashboard/left_menu.tpl',
                'pagelayout' => static::LAYOUT
            )
        );
    }

    /**
     * eZJSCore method for providing scaler GUI tools
     * @param array $args
     */
    public static function scaler($args)
    {
        return self::_templates(array(
            'skeleton' => 'design:parts/scaler.tpl',
            'scale' => 'design:parts/scale_version.tpl'
        ));
    }

    /**
     * eZJSCore method for saving a new scaled version
     */
    public static function saveVersion(array $args = array())
    {
        list($attributeId, $version) = $args;
        if ($attributeId && $version)
        {
            $http = \eZHTTPTool::instance();

            $coords = $http->variable('coords');
            $size = $http->variable('size');
            $name = $http->variable('name');

            // Store information on content object
            $imageAttribute = eZContentObjectAttribute::fetch($attributeId, $version);

            // @var \keymedia\models\image\Handler
            $handler = $imageAttribute->content();
            $data = $handler->addVersion($name, compact('coords', 'size'));
        }
        return $data;
    }

    /**
     * eZJSCore method for browsing KeyMedia
     */
    public static function browse(array $args = array())
    {
        list($attributeId, $version) = $args;
        if ($attributeId && $version)
        {
            $http = \eZHTTPTool::instance();
            $q = $http->variable('q', '');
            $width = 160;
            $height = 120;
            $offset = 0;
            $limit = 25;

            $attribute = eZContentObjectAttribute::fetch($attributeId, $version);
            $handler = $attribute->content();
            $box = $handler->attribute('minSize');
            $minWidth = $box->width();
            $minHeight = $box->height();
            $backend = $handler->attribute('backend');
            $results = $backend->search($q, compact('minWidth', 'minHeight'), compact('width', 'height', 'offset', 'limit'));

            $data = compact('results');
        }
        return $data;
    }

    /**
     * Upload an image from disk
     */
    public static function upload()
    {
        $http = eZHTTPTool::instance();
        $httpFile = eZHTTPFile::fetch('file');

        $attributeId = $http->postVariable('AttributeID');
        $version = $http->postVariable('ContentObjectVersion');

        $attribute = eZContentObjectAttribute::fetch($attributeId, $version);
        $handler = $attribute->content();
        if (!$image = $handler->uploadFile($httpFile))
            return array('error' => 'Failed upload');

        $tpl = \eZTemplate::factory();
        $tpl->setVariable('attribute', $attribute);
        return array(
            'image' => $image->data(),
            'html' => $tpl->fetch('design:parts/edit_preview.tpl'),
            'ok' => true
        );
    }


    /**
     * eZJSCore method for adding tags to a remote media
     */
    public static function tag(array $args = array())
    {
        list($attributeId, $version) = $args;
        if ($attributeId && $version)
        {
            $attribute = eZContentObjectAttribute::fetch($attributeId, $version);
            $handler = $attribute->content();
            $backend = $handler->attribute('backend');
            $http = \eZHTTPTool::instance();
            $tags = (array) $http->variable('tags');
            $id = $http->variable('id');
            $image = $backend->tag(compact('id'), $tags);
            return $image->data();
        }
        return false;
    }

    /***
     * Render a bunch of templates into an array and return them
     * Defaults to include `skeleton`
     *
     * @param array $templates
     * @return array
     */
    protected static function _templates(array $templates = array())
    {
        $tpl = \eZTemplate::factory();
        $result = array();
        foreach ($templates as $name => $path)
        {
            if ($path)
                $result[$name] = $tpl->fetch($path);
        }

        return $result;
    }

    /**
     * Cache time for retunrned data, only currently used by ezjscPacker
     * Taken from the "interface" `ezjscServerFunctions`
     *
     * @param string $functionName
     * @return int Uniq timestamp (can return -1 to signal that $functionName is not cacheable)
     */
    public static function getCacheTime($functionName)
    {
        return -1;
    }

    protected function backends()
    {
        return Backend::find();
    }

    /**
     * Add new mediabase connection
     */
    protected function save($backend, array $data = array())
    {
        $keys = array('id', 'host', 'username', 'api_key', 'api_version');

        foreach ($keys as $key)
        {
            if (isset($data[$key]))
                $backend->setAttribute($key, $data[$key]);
        }

        return $backend->store();
    }

}
