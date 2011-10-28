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
    /**
     * Executes the test.
     */
    public function execute()
    {
        $api = new Connector('ea799a1d3ee2d58690c97735d2f2571a', 'keymedia', 'km.no');
        $api->setProgressCallback(array('ezkpmedia\modules\UserTest\UserTest', 'callback'));

        $attributes = array('ok' => 'nei', 'godtbilde' => 'tja', 'ugyldigattributt' => 'hmm?');

        $result = $api->uploadImage('image', array('tag1', 'tag2'), $attributes);

        echo $result;

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