<?php

namespace keymedia\models;

class Box
{
    private $width = 0;
    private $height = 0;

    /**
     * Create a new box
     *
     * @param int $width
     * @param int $height
     */
    public function __construct($width, $height)
    {
        $this->width = $width;
        $this->height = $height;
    }

    /**
     * Get width
     * @return int
     */
    public function width()
    {
        return $this->width;
    }

    /**
     * Get height
     * @return int
     */
    public function height()
    {
        return $this->height;
    }

    /**
     * Check if another box fits in this box
     *
     * @param Box $box
     * @return bool
     */
    public function fits(Box $box)
    {
        return (
            $box->width() >= $this->width() &&
            $box->height() >= $this->height()
        );
    }
}
