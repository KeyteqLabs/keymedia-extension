<?php

namespace ezr_keymedia\models;

class Backend extends \eZPersistentObject
{
    protected static $definition = array(
        'fields' => array(
            'id' => array('name' => 'user_id', 'datatype' => 'integer', 'required' => true),
            'host' => array('name' => 'host', 'datatype' => 'string', 'required' => true),
            'username' => array('name' => 'username', 'datatype' => 'string', 'required' => true),
            'api_key' => array('name' => 'api_key', 'datatype' => 'string', 'required' => true)
        ),
        'keys' => 'id',
        'class_name' => '\\ezr_keymedia\\models\\Backend',
        'name' => 'ezr_keymedia_backends'
    );

    public static function definition()
    {
        return self::$definition;
    }

    public static function create(array $data = array())
    {
        $device = new static($data);
        return $device;
    }
}
