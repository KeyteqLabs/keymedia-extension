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
     * Caches id attribute
     * @var string|null
     */
    protected $_idAttribute = null;

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
            case 'id': $key = $this->idAttribute(); break;
            case 'size': return $this->size();
        }
        return isset($this->$key) ? $this->data[$key] : null;
    }

    public function size()
    {
        return array($this->file->width, $this->file->height);
    }

    public function __isset($key)
    {
        $exists = array('size');
        if ($key === 'id') $key = $this->idAttribute();
        return isset($this->data[$key]) ?: in_array($key, $exists);
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

    /**
     * @static
     * @param $width
     * @param $height
     * @param $boxWidth
     * @param $boxHeight
     * @return array
     */
    public static function fitToBox($width, $height, $boxWidth, $boxHeight)
    {

        $formatW = $width;
        $formatH = $height;

        $formatRatio = $formatW / $formatH;

        $outerW = $boxWidth;
        $outerH = $boxHeight;

        $outerRatio = $outerW / $outerH;

        $width = null;
        $height = null;

        $x = 0;
        $y = 0;

        // Identical ratio - no change needed
        if ($formatRatio == $outerRatio) {

            $width = $outerW;
            $height = $outerH;


        }
        else if ($formatRatio == 1) {
            if ($outerRatio > 1)
            {
                $width = $outerH;
                $height = $outerH;
                $x = floor(($outerW - $width) / 2);
                $y = 0;
            }
            elseif ($outerRatio < 1)
            {
                $width = $outerW;
                $height = $outerW;
                $x = 0;
                $y = floor(($outerH - $height) / 2);
            }
        }
        else if ($formatRatio < 1) {
            if ($outerRatio == 1) {
                $height = $outerH;
                $width = (int)($height * $formatRatio);
                $x = floor(($outerW - $width) / 2);
                $y = 0;

            }
            else
            {
                // UNSURE !!!
                if ($outerRatio > $formatRatio) {
                    $width = (int)($outerH * $formatRatio);
                    $height = (int)($width / $formatRatio);
                    $x = floor(($outerW - $width) / 2);
                    $y = floor(($outerH - $height) / 2);
                }
                elseif ($outerRatio < $formatRatio)
                {
                    $height = (int)($outerW / $formatRatio);
                    $width = (int)($height * $formatRatio);

                    $x = floor(($outerW - $width) / 2);
                    $y = floor(($outerH - $height) / 2);
                }
            }
        }
        else if ($formatRatio > 1) {
            if ($outerRatio == 1) {
                $width = $outerW;
                $height = (int)($width / $formatRatio);
                $x = 0;
                $y = floor(($outerH - $height) / 2);
            }
            else
            {

                if ($outerRatio > $formatRatio) {
                    $width = (int)($outerH * $formatRatio);
                    $height = (int)($width / $formatRatio);
                    $x = floor(($outerW - $width) / 2);
                    $y = floor(($outerH - $height) / 2);
                }
                elseif ($outerRatio < $formatRatio)
                {
                    $height = (int)($outerW / $formatRatio);
                    $width = (int)($height * $formatRatio);

                    $x = floor(($outerW - $width) / 2);
                    $y = floor(($outerH - $height) / 2);
                }
            }
        }

        return array(
            'coords' => array(
                $x,$y,$x + $width,$y + $height,
            )
        );

    }

    /**
     * Figure out what attribute is the id attribute
     *
     * @return string
     */
    protected function idAttribute()
    {
        if (!$this->_idAttribute)
        {
            if (isset($this->data['id']))
                $this->_idAttribute = 'id';
            elseif (isset($this->data['_id']))
                $this->_idAttribute = '_id';
        }

        return $this->_idAttribute;

    }
}
