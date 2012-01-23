<?php

namespace keymedia\models;

/**
 * KeyMedia connector interface
 * Exists because we have several versions of the KeyMedia API but we still want
 * to maintain a similar experience from a SDK-point-of-view
 *
 * @author Raymond Julin (raymond@keyteq.no)
 * @since 1.0.0
 */

interface ConnectorInterface
{
    /**
     * Returns an instance of the API.
     *
     * @param $apiKey
     * @param $username
     * @param $mediabaseDomain
     */
    public function __construct($username, $apiKey, $mediabaseDomain);

    /**
     * Perform generic search on backend
     *
     * @param array $criteria
     * @param array $options
     * @return array
     */
    public function search(array $criteria = array(), array $options = array());

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
    public function setProgressCallback($callback);

    /**
     *
     * Search by one or more tags.
     *
     * @param $q
     * @param mixed $attributes
     * @param mixed $collection
     * @param mixed $limit
     * @param mixed $offset
     * @param mixed $width
     * @param mixed $height
     * @param mixed $externalId
     *
     * @return mixed
     */
    public function searchByTerm($q, $attributes = false, $collection = false, $limit = false, $offset = false, $width = false, $height = false, $externalId = false);

    /**
     *
     * Retrieves images matching one or more tags.
     *
     * @param array $tags A list of tags
     * @param mixed $operator and (default is or)
     * @param mixed $limit
     * @param mixed $offset
     * @param mixed $width
     * @param mixed $height
     *
     * @return mixed
     */
    public function searchByTags($tags = array(), $operator = false, $limit = false, $offset = false, $width = false, $height = false);

    /**
     * Uploads media to KeyMedia
     *
     * @param $filename
     * @param $originalName
     * @param array $tags
     * @param array $attributes
     *
     * @return mixed|null
     */
    public function uploadMedia($filename, $originalName, $tags = array(), $attributes = array());

    /**
     * Uploads media
     *
     * @param $fieldname
     * @param array $tags
     * @param array $attributes
     *
     * @return mixed|null
     */
    public function uploadMediaFromForm($fieldname, $tags = array(), $attributes = array());

    /**
     * @return int
     */
    public function getTimeout();

    /**
     * @param int $timeout
     */
    public function setTimeout($timeout);
}
