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
        $api->uploadImage('');

        \eZExecution::cleanExit();
    }

    /**
     * Dummy form.
     */
    public function viewForm()
    {
        $tpl = \eZTemplate::factory();

        echo $tpl->fetch('form.tpl');

        \eZExecution::cleanExit();
    }
}