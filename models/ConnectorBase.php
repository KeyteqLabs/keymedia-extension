<?php

namespace ezr_keymedia\models;

/**
 * KeyMedia connector base.
 * Implements shared methods between each API version
 *
 * @author Raymond Julin (raymond@keyteq.no)
 * @since 1.0.0
 */
class ConnectorBase implements ConnectorInterface
{
    /** @var string Required for accessing KeyMedia */
    protected $apiKey;
    /** @var string Your peronal username */
    protected $username;
    /** @var string The address identifying your KeyMedia installation. */
    protected $mediabaseDomain;
    /** @var string The callback used for progress-reporting. */
    protected $callback;
    /** @var int Timout in minutes before the request is cancelled */
    protected $timeout;

    /**
     * Returns an instance of the API.
     *
     * @param $apiKey
     * @param $username
     * @param $mediabaseDomain
     */
    public function __construct($username, $apiKey, $mediabaseDomain)
    {
        $this->username = $username;
        $this->apiKey = $apiKey;
        $this->mediabaseDomain = $mediabaseDomain;

        $this->timeout = 30;
    }

    /**
     *
     * Sets the callback used for progressreporting.
     * Check PHPDoc for documentation regarding callbacks.
     * @link http://php.net/manual/en/language.pseudo-types.php
     * It should support the four following arguments:
     * downloadsize, downloadedsize, uploadsize, uploadedsize.
     *
     * @param $callback
     *
     * @return bool
     */
    public function setProgressCallback($callback)
    {
        $this->callback = is_callable($callback) ? $callback : false;

        return (bool)$this->callback;
    }

    /**
     *
     * Uploads media
     *
     * @param $fieldname
     * @param array $tags
     * @param array $attributes
     *
     * @return mixed|null
     */
    public function uploadMediaFromForm($fieldname, $tags = array(), $attributes = array())
    {
        $filename = $_FILES[$fieldname]['tmp_name'];
        $originalName = $_FILES[$fieldname]['name'];

        return $this->uploadMedia($filename, $originalName, $tags, $attributes);
    }

    /**
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @param int $timeout
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }
}
