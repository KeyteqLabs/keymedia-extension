<?php

namespace keymedia\models;

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


    /**
     * Returns an instance of the API.
     *
     * @param string $apiKey
     * @param string $username
     * @param string $mediabaseDomain
     * @param RequestInterface $request
     */
    public function __construct($username, $apiKey, $mediabaseDomain, RequestInterface $request)
    {
        $this->username = $username;
        $this->apiKey = $apiKey;
        $this->mediabaseDomain = $mediabaseDomain;

        $this->request = $request;
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
        return $this->request->timeout;
    }

    /**
     * @param int $timeout
     */
    public function setTimeout($timeout)
    {
        $this->request->timeout = $timeout;
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
        ksort($params);
        $headers = array();
        $method = strtoupper($method);
        $params = array_filter($params);

        foreach ($params as $k => $v) {
            $params[$k] = is_array($v) ? implode(',', $v) : $v;
        }

        $url = $this->getRequestUrl($action, $params);

        $options = array(
            'headers' => $this->signHeader($params),
            'callback' => $this->callback
        );
        $result = $this->request->perform($url, $method, $params, $options);
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
