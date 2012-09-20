<?php

namespace keymedia\models;

use \stdclass;
use \Exception;

class Backend extends \eZPersistentObject
{
    protected $connectors = array(
        '1' => 'keymedia\\models\\v1\\Connector',
        '2' => 'keymedia\\models\\v2\\Connector',
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
        'class_name' => '\\keymedia\\models\\Backend',
        'name' => 'keymedia_backends'
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
     * @return \keymedia\models\Backend
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
     * @return \keymedia\models\Backend
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
            'externalId' => false,
            'q' => $q
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
            // Donts end format to backend, just used here
            $format = $options['format'];
            unset($options['format']);
            $results = $con->search($criteria, $options);
            return $this->_format($results, $format);
        }

        return false;
    }

    /**
     * Find media tagged by $tagged
     *
     * Example:
     * <code>
     *     $mediasOfCatsWithDogs = $backend->tagged(array('cat','dog'), array('operator' => 'and'));
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
            return $this->_format($results, $options['format']);
        }
    }

    /**
     * Tag a media
     *
     * @param array $criteria
     * @param array $tags
     * @return 
     */
    public function tag(array $criteria = array(), $tags = array())
    {
        if ($con = $this->connection())
        {
            $payload = compact('tags');
            if (!isset($criteria['id']))
                throw new Exception('The only supported criteria is the id field');

            $result = $con->tagMedia($criteria['id'], $tags);
            if ($result && isset($result->media))
            {
                $media = new Media($result->media);
                $media->host($this->host);
                return $media;
            }
        }

        return false;
    }

    /**
     * Format results equally for all getter methods
     *
     * @param object $results
     * @param string $format
     * @return object
     */
    protected function _format($results, $format = 'simple')
    {
        $results->hits = ($format === 'simple') ? $this->simplify($results->hits) : $results->hits;
        return $results;
    }

    /**
     * Get a single media information
     *
     * @param string $id
     * @return \keymedia\models\Media
     */
    public function get($id)
    {
        $con = $this->connection();
        if ($con) {
            $data = $con->media($id);
            if ($data) {
                if (!isset($data->host)) $data->host = $this->host;
                return new Media($data);
            }
        }
        return null;
    }

    /**
     * Create a new version for specified media id
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
     * @return \keymedia\models\Media|false Media object if a successfull upload
     */
    public function upload($filepath, $filename, array $tags = array(), array $data = array())
    {
        if ($con = $this->connection())
        {
            $data += array('attributes' => array());
            $result = $con->uploadMedia($filepath, $filename, $tags, $data['attributes']);
            if ($result && isset($result->media))
            {
                $media = new Media($result->media);
                $host = isset($result->host) ? $result->host : $this->host;
                $media->host($host);
                return $media;
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
     * @return \keymedia\models\Connector
     */
    public function connection()
    {
        if (!is_object($this->connection))
        {
            if (!isset($this->connectors[$this->api_version]))
                throw new Exception("API version {$this->api_version} has no conncetor");

            $class = $this->connectors[$this->api_version];
            $connection = new $class($this->username, $this->api_key, $this->host);
            $this->connection = $connection;
        }
        return $this->connection;
    }

    /**
     * Report media usage to keymedia
     *
     * @param $id
     * @param array $reference
     * @return bool|Media
     */
    public function reportUsage($id, array $reference)
    {
        if ($con = $this->connection())
        {
            $result = $con->reportUsage($id, $reference);
            if ($result && isset($result->media))
            {
                $media = new Media($result->media);
                $media->host($this->host);
                return $media;
            }
        }

        return false;
    }
}
