<?php

namespace ezr_keymedia\models;

class Backend extends \eZPersistentObject
{
    protected static $definition = array(
        'fields' => array(
            'id' => array('name' => 'id', 'datatype' => 'integer', 'required' => false),
            'host' => array('name' => 'host', 'datatype' => 'string', 'required' => true),
            'username' => array('name' => 'username', 'datatype' => 'string', 'required' => true),
            'api_key' => array('name' => 'api_key', 'datatype' => 'string', 'required' => true)
        ),
        'keys' => array('id'),
        'class_name' => '\\ezr_keymedia\\models\\Backend',
        'name' => 'ezr_keymedia_backends'
    );

    /**
     * Get data definition for this persistent object
     *
     * @return array
     */
    public static function definition()
    {
        return self::$definition;
    }

    /**
     * Create a brand new un-stored Backend
     *
     * @param array $data
     * @return \ezr_keymedia\models\Backend
     */
    public static function create(array $data = array())
    {
        return new static($data);
    }

    /**
     * Find a list of objects matching criteria
     *
     * @param array $criteria
     * @return array
     */
    public static function find(array $criteria = array())
    {
        return static::fetchObjectList(static::definition(), null, $criteria);
    }

    /**
     * Find the first object matching criteria (id lookups)
     *
     * @param array $criteria
     * @return \ezr_keymedia\models\Backend
     */
    public static function first(array $criteria = array())
    {
        return static::fetchObject(static::definition(), null, $criteria);
    }
}
