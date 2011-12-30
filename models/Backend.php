<?php

namespace ezr_keymedia\models;

use \stdclass;

class Backend extends \eZPersistentObject
{
    protected $connectors = array(
        '1' => 'ezr_keymedia\\models\\v1\\Connector',
        '2' => 'ezr_keymedia\\models\\v2\\Connector',
    );

    protected $connection = false;

    protected static $definition = array(
        'fields' => array(
            'id' => array('name' => 'id', 'datatype' => 'integer', 'required' => false),
            'host' => array('name' => 'host', 'datatype' => 'string', 'required' => true),
            'username' => array('name' => 'username', 'datatype' => 'string', 'required' => true),
            'api_key' => array('name' => 'api_key', 'datatype' => 'string', 'required' => true),
            'api_version' => array('name' => 'api_version', 'datatype' => 'int', 'required' => true)
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
        $data = array_map('trim', $data);
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
            $results = $con->searchByTerm(
                $q, $criteria['attributes'], $criteria['collection'],
                $options['limit'], $options['offset'], $options['width'], $options['height'],
                $criteria['externalId']
            );

            return $options['format'] === 'simple' ? $this->simplify($results) : $results;
        }

        return false;
    }

    /**
     * Find media tagged by $tagged
     *
     * Example:
     * <code>
     *     $imagesOfCatsWithDogs = $backend->tagged(array('cat','dog'), array('operator' => 'and'));
     * </code>
     *
     * @param array|string $tagged An array of tags, or string for a single tag
     * @param array $options Options to control look-up strategy
     *      - `operator` string _or_ or _and_. Defaults to _and_
     *      - `limit` int Limit number of hits
     * @return array|false
     */
    public function tagged($tagged, array $options = array())
    {
        $options += array(
            'operator' => 'or',
            'limit' => 25,
            'offset' => 0,
            'format' => 'simple'
        );

        // Backwards compliance for old behaviour
        $options += array(
            'width' => false,
            'height' => false
        );

        $tagged = (array) $tagged;

        if ($con = $this->connection())
        {
            $results = $con->searchByTags(
                $tagged, $options['operator'], $options['limit'],
                $options['offset'], $options['width'], $options['height']
            );

            return $options['format'] === 'simple' ? $this->simplify($results) : $results;
        }
    }

    /**
     * Get a single image information
     *
     * @param string $id
     * @return \ezr_keymedia\models\Image
     */
    public function get($id)
    {
        if ($con = $this->connection())
        {
            $image = new Image($con->media($id));
            $image->host($this->host);
            return $image;
        }
        return null;
    }

    /**
     * Create a new version for specified image id
     *
     * @param string $id
     * @param string $name
     * @param array $transformations
     * @return string Relative url to new version
     */
    public function addVersion($id, $name, array $transformations = array())
    {
        // Get connector
        $connection = $this->connection();

        $data = $connection->addVersion($id, $name, $transformations);
        if (!isset($data->error))
            $data->url = join('/', array('', $id, $data->version->slug));
        return $data;
    }

    /**
     * Upload a new media via API
     *
     * @param string $filepath Local file system path to media file
     * @param string $filename File name to use in KeyMedia
     * @param array $tags
     * @param array $data
     *          - `attributes`
     *          - `collection`
     *
     * @return \ezr_keymedia\models\Image|false Image object if a successfull upload
     */
    public function upload($filepath, $filename, array $tags = array(), array $data = array())
    {
        if ($con = $this->connection())
        {
            $data += array('attributes' => array());
            $result = $con->uploadMedia($filepath, $filename, $tags, $data['attributes']);
            if ($result && isset($result->media))
            {
                $image = new Image($result->media);
                $image->host($this->host);
                return $image;
            }
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
        $con = $this->connection();
        $formatted = array();
        foreach ($results as $media)
            $formatted[] = $con->simplify($media);
        return $formatted;
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
            if (!isset($this->connectors[$this->api_version]))
                throw new \Exception("API version {$this->api_version} has no conncetor");

            $class = $this->connectors[$this->api_version];
            $connection = new $class($this->username, $this->api_key, $this->host);
            $this->connection = $connection;
        }
        return $this->connection;
    }
}
