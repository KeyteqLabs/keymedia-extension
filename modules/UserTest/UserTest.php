<?php

namespace ezkpmedia\modules\UserTest;

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

use \ezkpmedia\modules\Connector\Connector;

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
        $this->api->setProgressCallback(array('ezkpmedia\modules\UserTest\UserTest', 'callback'));
    }

    /**
     * Executes the test.
     */
    public function execute()
    {
        //$this->uploadTest();

        //$this->searchTest();

        $this->tagTest();

        \eZExecution::cleanExit();
    }

    protected function tagTest()
    {
        $tags = array('meh', 'arkitektur');

        $result = $this->api->searchByTags($tags, 'and');

        var_dump($result);
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
    public static function callBack($a, $b, $c, $d)
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
        require_once('form.tpl');

        \eZExecution::cleanExit();
    }
}