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
use \ezr_keymedia\modules\connector\Connector;

class UserTest
{
    /** @var Connector API-Connector */
    protected $api;

    /**
     * Initializes the api-connector.
     */
    public function __construct()
    {
        $this->api = new Connector('keymedia', 'keymedia_test', 'keymedia.raymond.keyteq.no');
        $this->api->setProgressCallback(array($this, 'callback'));
    }

    /**
     * Executes the test.
     */
    public function execute()
    {
        $this->uploadTest();

        //$this->searchTest();

        \eZExecution::cleanExit();
    }

    public function tags()
    {
        $http = eZHTTPTool::instance();

        $form = new stdClass;
        $form->action = $_SERVER['SCRIPT_URI'];

        if ($tags = $http->variable('tags', false))
        {
            $operator = $http->variable('operator', 'and');
            $tags = array_filter(explode(',', $tags));
            $result = $this->api->searchByTags($tags, strtolower($operator));
            foreach ($result as &$r)
            {
                $images = (array) $r->images;
                $r->image = current($images);
            }
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

    /**
     * Dummy form.
     */
    public function viewForm()
    {
        $form = new \stdClass(array(
            'action' => '/eng/ezote/delegate/ezkpmedia/UserTest/execute'
        ));
        require_once('form.tpl.php');

        \eZExecution::cleanExit();
    }
}
