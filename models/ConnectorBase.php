<?php

namespace ezr_keymedia\models;

/**
 * KeyMedia connector base.
 * Implements shared methods between each API version
 *
 * @author Raymond Julin (raymond@keyteq.no)
 * @since 1.0.0
 */
abstract class ConnectorBase implements ConnectorInterface
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

    /**
     * Makes a request and returns the result.
     *
     * @param $action
     * @param $params
     *
     * @return mixed
     */
    protected function makeRequest($action, array $params = array(), $method = 'GET')
    {
        $headers = array();
        $method = strtoupper($method);
        $params = array_filter($params);

        $url = $this->getRequestUrl($action, $params);

        $ch = curl_init();
        switch ($method)
        {
            case 'POST':
                // Cant send arrays using curl, makes no sense in http
                foreach ($params as &$v)
                    $v = is_array($v) ? implode(',', $v) : $v;
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                break;
            case 'GET':
                $joiner = strpos($url, '?') ? '&' : '?';
                $url .= $joiner . http_build_query($params);
                break;
        }
        curl_setopt($ch, CURLOPT_URL, $url);

        if ($header = $this->signHeader($params))
            $headers += $header;

        if ($this->callback)
        {
            curl_setopt($ch, CURLOPT_NOPROGRESS, 0);
            curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, $this->callback);
        }

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if (is_numeric($this->timeout))
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);

        if ($headers)
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        return json_decode($result);
    }

    /**
     * Find mime type for given local filename
     *
     * @param string $filename
     * @return string
     */
    protected function mime($filename)
    {
        $info = new \finfo(FILEINFO_MIME_TYPE);
        return $info->file($filename);
    }
}
