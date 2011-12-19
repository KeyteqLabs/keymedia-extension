<?php

namespace ezr_keymedia\models;

class Image
{
    /**
     * Image values
     * @var array
     */
    protected $data = array();

    /**
     * Find the first object matching criteria (id lookups)
     *
     * @param array $criteria
     * @return \ezr_keymedia\models\Backend
     */
    public function __construct($data = array())
    {
        $this->data = $data;
    }

    /**
     * Get value from image
     * 
     * @param string $key Key to get
     * @return mixed
     */
    public function __get($key)
    {
        switch ($key)
        {
            case 'id': $key = '_id'; break;
            case 'size': return $this->size();
        }
        return isset($this->$key) ? $this->data->$key : null;
    }

    public function size()
    {
        return array($this->file->width, $this->file->height);
    }

    public function __isset($key)
    {
        $exists = array('size');
        if ($key === 'id') $key = '_id';
        return isset($this->data->$key) ?: in_array($key, $exists);
    }

    public function hasAttribute($key)
    {
        return isset($this->$key);
    }
    public function attribute($key)
    {
        return $this->$key;
    }

    public function thumb($width, $height)
    {
        $url = 'http://' . $this->host() . "/{$width}x{$height}/{$this->_id}.jpg";
        return $url;
    }

    public function host($host = null)
    {
        if (is_string($host)) $this->_host = $host;
        return $this->_host;
    }
}
