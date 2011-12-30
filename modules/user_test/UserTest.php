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

    protected function searchTest()
    {
        $result = $this->api->search('bity');

        var_dump($result);
    }

    protected function uploadTest()
    {
        $attributes = array('ok' => 'nei', 'godtbilde' => 'tja', 'ugyldigattributt' => 'hmm?');

        $result = $this->api->uploadMedia($_FILES['media']['tmp_name'], $_FILES['media']['name'], array('tag1', 'tag2'), $attributes);

        var_dump($result);
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
