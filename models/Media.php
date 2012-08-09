<?php

namespace keymedia\models;

class Media
{
    /**
     * Media values
     * @var array
     */
    protected $_data = array();

    /**
     * Cached id attribute
     * @var string|false
     */
    protected $_idAttribute = false;

    /**
     * Find the first object matching criteria (id lookups)
     *
     * @param array $criteria
     * @return \keymedia\models\Backend
     */
    public function __construct($data = array())
    {
        if ($data)
            $this->_data = (object) $data;
        else
            $this->_data = (object) array();
    }

    /**
     * Return the value of an attribute in this media
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
            case 'ending': return $this->ending();
            case 'box': return $this->box();
        }
        return isset($this->$key) ? $this->_data->$key : null;
    }

    /**
     * Return the value of an attribute
     * This is used in eZ templates for dynamic resolving of attributes:
     * `{$object.property}` turns into `$object->attribute('property')`
     *
     * Acts as an alias to __get
     *
     * @param string $key
     * @return mixed
     */
    public function attribute($key)
    {
        if ($key === 'data') return $this->data();
        return $this->$key;
    }

    /**
     * Check if a value exists in the data for this object
     *
     * @param string $key
     * @return bool
     */
    public function __isset($key)
    {
        $exists = array('size', 'ending', 'box');
        if ($key === 'id') $key = $this->idAttribute();
        return isset($this->_data->$key) ?: in_array($key, $exists);
    }

    /**
     * Alias for __isset as needed for eZ templates
     *
     * @param string $key
     * @return bool
     */
    public function hasAttribute($key)
    {
        $special = array('data');
        return isset($this->$key) ?: in_array($key, $special);
    }

    /**
     * Build thumb string for current media with given width and height
     * 
     * @param int $width
     * @param int $height
     * @return string
     */
    public function thumb($width, $height)
    {
        return '//' . $this->host() . "/{$width}x{$height}/{$this->id}.jpg";
    }

    /**
     * Return size information in an array(width, height)
     *
     * @return array Dimensions, like {300, 200}
     */
    public function size()
    {
        return array($this->file->width, $this->file->height);
    }

    /**
     * Get or set the host to use for media urls
     *
     * @param string|null $host
     * @return string
     */
    public function host($host = null)
    {
        if (is_string($host)) $this->_data->host = $host;
        return isset($this->host) ? $this->_data->host : '';
    }

    public function type()
    {
        $mimeParts = explode('/', $this->file->type);
        return $mimeParts[0];
    }

    /**
     * Get media Box (dimensions of media)
     * @return keymedia\models\Box
     */
    public function box()
    {
        return new Box($this->file->width, $this->file->height);
    }

    /**
     * Get a file ending to use for this media
     * Defaults to jpg if no ending is found
     */
    public function ending()
    {
        if (isset($this->scalesTo))
            return $this->scalesTo->ending;
        return 'jpg';
    }

    /**
     * Find a sized box inside this medias original size
     * Used to calculate an initial version
     *
     * @param int $width Minimal crop width
     * @param int $height Minimal crop height
     */
    public function boxInside($width, $height)
    {
        return self::fitToBox($width, $height, $this->file->width, $this->file->height);
    }

    /**
     * @static
     * @param $width Width of box to fit
     * @param $height Height of box to fit
     * @param $boxWidth Width of original media
     * @param $boxHeight Height of original media
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
     * Fetch media data
     *
     * @return object
     */
    public function data()
    {
        if (!$this->_data)
        {
            return false;
        }
        $idAttribute = $this->idAttribute();
        $data = clone $this->_data;
        if ($idAttribute !== 'id')
        {
            $data->id = $this->_data->$idAttribute;
            unset($data->$idAttribute);
        }
        return $data;
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
            if (isset($this->_data->id))
                $this->_idAttribute = 'id';
            elseif (isset($this->_data->_id))
                $this->_idAttribute = '_id';
        }

        return $this->_idAttribute;

    }
}
