<?php

namespace ezr_keymedia\modules\connector;

/**
 * KeyMedia connector, makes the module talk the talk that KeyMedia talks
 * Enables the KeyMedia API
 *
 * <code>
 *   $api = new Connector($username, $apiKey, $host);
 *   $api->searchByTags($tags, $operator);
 *   $api->search($q);
 *   $result = $api->uploadMedia($filepath, $filename, $tags, $attributes);
 * </code>
 *
 * @author Henning Kvinnesland / henning@keyteq.no
 * @since 27.10.2011
 *
 */
class Connector
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
     * Search by one or more tags.
     *
     * @param $q
     * @param bool $attributes
     * @param bool $collection
     * @param bool $limit
     * @param bool $offset
     * @param bool $width
     * @param bool $heigth
     * @param bool $externalId
     *
     * @return mixed
     */
    public function search($q, $attributes = false, $collection = false, $limit = false, $offset = false, $width = false, $heigth = false, $externalId = false)
    {
        $params = compact('q', 'limit', 'offset');
        if ($heigth !== false) $params['height'] = array($heigth);
        if ($width !== false) $params['width'] = array($width);

        if ($attributes !== false) $params['attributes'] = $attributes;
        if ($collection !== false) $params['collection'] = $collection;
        if ($externalId !== false) $params['externalId'] = $externalId;

        return $this->makeRequest('search', $params);
    }

    /**
     *
     * Retrieves images matching one or more tags.
     *
     * @param array $tags A list of tags
     * @param bool $operator and (default is or)
     * @param bool $limit
     * @param bool $offset
     * @param bool $width
     * @param bool $height
     *
     * @return mixed
     */
    public function searchByTags($tags = array(), $operator = false, $limit = false, $offset = false, $width = false, $height = false)
    {
        $params = array();
        $params['tag'] = $tags;

        if ($operator !== false) $params['tagsoperator'] = $operator;
        if ($limit !== false) $params['limit'] = $limit;
        if ($offset !== false) $params['offset'] = $offset;
        if ($width !== false) $params['width'] = $width;
        if ($height !== false) $params['heigth'] = $height;

        return $this->makeRequest('tag', $params);
    }

    /**
     *
     * Uploads media to KeyMedia
     *
     * @param $filename
     * @param $originalName
     * @param array $tags
     * @param array $attributes
     *
     * @return mixed|null
     */
    public function uploadMedia($filename, $originalName, $tags = array(), $attributes = array())
    {
        if (ini_get('max_execution_time') < $this->timeout)
            set_time_limit($this->timeout + 10);

        $url = $this->getRequestUrl('upload');

        if (file_exists($filename))
        {
            $postFields = array
            (
                'media' => '@' . $filename,
                'originalName' => $originalName,
                'tags' => serialize($tags),
                'attributes' => serialize($attributes)
            );

            $result = $this->uploadByCurl($url, $postFields);

            return json_decode($result);
        }

        return null;
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
     *
     * Upload alternative by curl.
     *
     * @param $url
     * @param $postFields
     *
     * @return mixed
     */
    protected function uploadByCurl($url, $postFields)
    {
        $ch = curl_init($url);

        if ($this->callback)
        {
            curl_setopt($ch, CURLOPT_NOPROGRESS, 0);
            curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, $this->callback);
        }

        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);

        return $result;
    }

    /**
     *
     * Makes a request and returns the result.
     *
     * @param $action
     * @param $params
     *
     * @return mixed
     */
    protected function makeRequest($action, $params)
    {
        $url = $this->getRequestUrl($action, $params);

        return json_decode(file_get_contents($url));
    }

    /**
     *
     * Builds the url for accessing keymedia.
     *
     * @param $action
     * @param $params
     *
     * @return bool|string
     */
    protected function getRequestUrl($action, $params = array())
    {
        $url = $this->mediabaseDomain .'/media/' . $action . '/';
        if (strpos($url, "http") === false)
            $url = 'http://'.$url;

        $urlArr = parse_url($url);
        $authUrl = $urlArr['scheme'] . '://' . $urlArr['host'] . $urlArr['path'];

        $auth = md5($authUrl . $this->apiKey);

        $params['username'] = $this->username;
        $params['auth'] = $auth;

        $queryString = http_build_query($params);

        if (strpos($url, '?') === false)
            $url .= '?' . $queryString;
        else
            $url .= '&' . $queryString;

        return $url;
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
