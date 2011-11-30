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

use \eZPersistentObject;
use ezr_keymedia\models\Backend;

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
}
