<?php

namespace ezr_keymedia\modules\user_test;

/**
 *
 * Beskrivelse
 *
 *
 *
 * @package App_
 * @subpackage
 *
 * @author Henning Kvinnesland / henning@keyteq.no
 * @since 27.10.2011
 *
 */

use \stdClass;
use \eZHTTPTool;
use \ezr_keymedia\models\Backend;
use \ezr_keymedia\models\v1\Connector as V1;
use \ezr_keymedia\models\v2\Connector as V2;

class UserTest
{
    /** @var Connector API-Connector */
    protected $api;

    /** @var array List of backends */
    protected $backends;

    /**
     * Initializes the api-connector.
     */
    public function __construct()
    {
        $this->backends = Backend::find();
        $this->api = new V1('keymedia', 'keymedia_test', 'keymedia.raymond.keyteq.no');
        $this->api->setProgressCallback(array($this, 'callback'));
    }

    public function tags()
    {
        $http = eZHTTPTool::instance();

        $form = new stdClass;
        $form->action = $_SERVER['SCRIPT_URI'];
        $backends = $this->backends;
        $result = array();

        if ($tags = $http->variable('tags', false))
        {
            $operator = $http->variable('operator', 'and');
            $tags = array_filter(explode(',', $tags));

            $backend = Backend::first(array('id' => $http->variable('backend', 1)));
            if (count($tags) === 1)
                $tags = array_shift($tags);
            $result = $backend->tagged($tags, compact('operator', 'limit'));
        }
        require_once('tags.tpl.php');
        \eZExecution::cleanExit();
    }

    public function upload()
    {
        $http = eZHTTPTool::instance();

        $form = new stdClass;
        $form->action = $_SERVER['SCRIPT_URI'];
        $backends = $this->backends;
        $result = array();

        $file = isset($_FILES['media']) ? $_FILES['media'] : false;
        if ($file)
        {
            $tags = array_filter(explode(',', $http->variable('tags')));
            $backend = Backend::first(array('id' => $http->variable('backend', 1)));

            $result = $backend->upload($file['tmp_name'], $file['name'], $tags);
            var_dump($result);
        }
        require_once('upload.tpl.php');
        \eZExecution::cleanExit();
    }

    /**
     *
     * Test of progress.
     *
     * @param $a
     * @param $b
     * @param $c
     * @param $d
     */
    public function callBack($a, $b, $c, $d)
    {
        trigger_error($a);
        trigger_error($b);
        trigger_error($c);
        trigger_error($d);
    }
}
