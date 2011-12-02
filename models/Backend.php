<?php

namespace ezr_keymedia\models;

class Backend extends \eZPersistentObject
{
    protected $connection = false;

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

    /**
     * Perform search against backend
     *
     * @param string $q Search term
     * @param array $criteria
     *          - `attributes`
     *          - `collection`
     *          - `externalId`
     * @param array $options
     *          - `width`
     *          - `height`
     *          - `offset`
     *          - `limit`
     *
     * @return array|false Array if success, false if falsy connection
     */
    public function search($q, array $criteria = array(), array $options = array())
    {
        $criteria += array(
            'attributes' => false,
            'collection' => false,
            'externalId' => false
        );
        $options += array(
            'width' => false,
            'height' => false,
            'offset' => 0,
            'limit' => 10,
            'format' => 'simple'
        );
        if ($con = $this->connection())
        {
            $results = $con->search(
                $q, $criteria['attributes'], $criteria['collection'],
                $options['limit'], $options['offset'], $options['width'], $options['height'],
                $criteria['externalId']
            );

            return $options['format'] === 'simple' ? $this->simplify($results) : $results;
        }

        return false;
    }

    /**
     * Simplify results from searches
     *
     * @param array $results
     * @return array
     */
    protected function simplify($results)
    {
        foreach ($results->hits as &$r)
        {
            $images = (array) $r->images;
            $thumb = array_shift($images);
            $r = array(
                'id' => $r->id,
                'shared' => $r->shared,
                'filesize' => $r->filesize,
                'width' => (int) $r->width,
                'height' => (int) $r->height,
                'filename' => $r->originalFilename
            ) + compact('thumb', 'images');
        }
        return $results;
    }

    /**
     * Get connection to mediabase
     * 
     * @return \ezr_keymedia\models\Connector
     */
    public function connection()
    {
        if (!is_object($this->connection))
        {
            $connection = new Connector($this->username, $this->api_key, $this->host);
            $this->connection = $connection;
        }
        return $this->connection;
    }
}
