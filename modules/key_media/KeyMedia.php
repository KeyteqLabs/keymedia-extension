<?php
/**
 * KeyMedia eZ extension
 *
 * @copyright     Copyright 2011, Keyteq AS (http://keyteq.no/labs)
 */

namespace ezr_keymedia\modules\key_media;

use \eZFunctionHandler;
use \eZHTTPTool;
use \eZPreferences;
use \ezkp\models\content\Object;
use \ezkp\models\content\object\Formatter;
use \ezote\lib\HTTP;

/**
 * Interact with data structures (content objects)
 *
 * @author Bjørnar Helland <bh@keyteq.no>
 * @author Raymond Julin <raymond@keyteq.no>
 * @since 24.10.2011
 *
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
        return self::response(
            $data,
            array(
                'template' => 'design:dashboard/dashboard.tpl',
                'pagelayout' => static::LAYOUT
            )
        );
    }
}
