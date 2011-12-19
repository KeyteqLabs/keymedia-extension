<?php
/**
 * KeyMedia eZ extension
 *
 * @copyright     Copyright 2011, Keyteq AS (http://keyteq.no/labs)
 */

namespace ezr_keymedia\modules\key_media;

use \eZPersistentObject;
use \ezr_keymedia\models\Backend;

use \eZHTTPFile;
use \eZHTTPTool;
use \eZContentObjectAttribute;

/**
 * KeyMedia controller
 * Exposes the HTTP actions for eZr KeyMedia integration
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
        $data = array();
        $data['backends'] = eZPersistentObject::fetchObjectList(Backend::definition());
        return self::response(
            $data,
            array(
                'template' => 'design:dashboard/dashboard.tpl',
                'pagelayout' => static::LAYOUT
            )
        );
    }

    /**
     * Add new mediabase connection
     */
    public function addConnection()
    {
        $data = array();
        if ($this->http->method('post'))
        {
            $ezhttp = $this->http->ez();
            $username = $ezhttp->variable('username', false);
            $host = $ezhttp->variable('host', false);
            $api_key = $ezhttp->variable('api_key', false);
            $redirectTo = $ezhttp->variable('redirect_to', false);

            $data = compact('username', 'host', 'api_key');
            $backend = Backend::create($data);
            $backend->store();
            if ($redirectTo)
            {
                $id = $backend->id;
                header("Location: {$redirectTo}?added={$id}");
            }
        }

        return self::response(
            $data,
            array(
                'template' => 'design:dashboard/add_connection.tpl',
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
        $http = \eZHTTPTool::instance();
        // $id will be id of image in keymeda
        $backend = array_pop($args);
        $id = array_pop($args);
        if ($backend && $id)
        {
            $version = $http->variable('version', array());

            $backend = Backend::first(array('id' => $backend));

            $templates = array(
                'skeleton' => 'design:parts/scaler.tpl',
                'scale' => 'design:parts/scale_version.tpl'
            );
            $data = compact('item') + self::_templates($http, $templates);
        }
        return $data;
    }

    /**
     * eZJSCore method for saving a new scaled version
     */
    public static function saveVersion(array $args = array())
    {
        list($id, $attributeId, $version) = $args;
        if ($id && $attributeId && $version)
        {
            $http = \eZHTTPTool::instance();

            $coords = $http->variable('coords');
            $size = $http->variable('size');
            $name = $http->variable('name');

            // Store information on content object
            $imageAttribute = eZContentObjectAttribute::fetch($attributeId, $version);

            // @var \ezr_keymedia\models\image\Handler
            $handler = $imageAttribute->content();
            $results = $handler->addVersion($name, compact('coords', 'size'));

            $data = compact('results');
        }
        return $data;
    }

    /**
     * eZJSCore method for browsing KeyMedia
     */
    public static function browse($args)
    {
        $http = \eZHTTPTool::instance();
        if ($id = array_pop($args))
        {
            $q = $http->variable('q', '');
            $width = 160;
            $height = 120;
            $offset = 0;
            $limit = 25;
            $id = (int) $id;

            $backend = Backend::first(compact('id'));
            $results = $backend->search($q, array(), compact('width', 'height', 'offset', 'limit'));

            $templates = array(
                'item' => 'design:parts/keymedia_browser_item.tpl'
            );
            $data = compact('results') + self::_templates($http, $templates);
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

        $attributeID = $http->postVariable('AttributeID');
        $contentObjectVersion = $http->postVariable('ContentObjectVersion');
        $contentObjectID = $http->postVariable('ContentObjectID');

        $imageAttribute = eZContentObjectAttribute::fetch($attributeID, $contentObjectVersion);
        $handler = $imageAttribute->content();
        if (!$ok = $handler->uploadFile($httpFile))
            $error = 'Failed upload';

        return compact('ok', 'error');
    }

    /***
     * Render a bunch of templates into an array and return them
     * Defaults to include `skeleton`
     *
     * @param array $templates
     * @return array
     */
    protected static function _templates($http, array $templates = array())
    {
        $defaults = array();
        if ($http->variable('skeleton', false))
            $defaults['skeleton'] = 'design:content/keymedia/browse.tpl';

        $templates += $defaults;

        $tpl = \eZTemplate::factory();
        $result = array();
        foreach ($templates as $name => $path)
        {
            if ($path) $result[$name] = $tpl->fetch($path);
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

}
