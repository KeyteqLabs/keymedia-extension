<?php

namespace ezkpmedia\modules\Connector;

/**
 *
 * Beskrivelse
 *
 * Transfers images to KeyMedia.
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

    /**
     * Returns an instance of the API.
     *
     * @param $apiKey
     * @param $username
     * @param $mediabaseDomain
     */
    public function __construct($apiKey, $username, $mediabaseDomain)
    {
        $this->apiKey = $apiKey;
        $this->username = $username;
        $this->mediabaseDomain = $mediabaseDomain;
    }

    /**
     *
     * Uploads an image to KeyMedia
     *
     * @param $path
     */
    public function uploadImage($path)
    {
        $url = $this->getRequestUrl();

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_POSTFIELDS)
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);

        echo $res;
    }

    /**
     *
     * Builds the url for accessing keymedia.
     *
     * @return bool|string
     */
    protected function getRequestUrl()
    {
        $url = $this->mediabaseDomain .'/media/' . '4ea94bc30c507cf80f000003/info';
        if (strpos($url, "http") === false)
            $url = 'http://'.$url;

        $urlArr = parse_url($url);
        $authUrl = $urlArr['scheme'] . '://' . $urlArr['host'] . $urlArr['path'];

        $auth = md5($authUrl . $this->apiKey);

        $params['username'] = $this->username;
        $params['auth'] = $auth;
        //$params['XDEBUG_SESSION_START'] = true;
        $queryString = http_build_query($params);

        if (strpos($url, '?') === false)
            $url .= '?' . $queryString;
        else
            $url .= '&' . $queryString;

        return $url;
    }
}