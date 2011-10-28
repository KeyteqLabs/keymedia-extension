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
    /** @var string The class used for progress-reporting. */
    protected $progressClass;
    /** @var string The method used for progress reporting. */
    protected $progressMethod;

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
     * Sets the callback used for progressreporting.
     * The method should be static.
     * It should also support the four following arguments:
     * downloadsize, downloadedsize, uploadsize, uploadedsize.
     *
     * @param $class
     * @param $method
     */
    public function setProgressCallback($class, $method)
    {
        $this->progressClass = $class;
        $this->progressMethod = $method;
    }

    /**
     *
     * Uploads an image to KeyMedia
     *
     * @param $fieldname
     * @param array $tags
     * @param array $attributes
     *
     * @return mixed|null
     */
    public function uploadImage($fieldname, $tags = array(), $attributes = array())
    {
        set_time_limit(3600);
        ini_set('max_execution_time', '3600');

        $url = $this->getRequestUrl();

        $filename = $_FILES[$fieldname]['tmp_name'] ?: null;

        if (file_exists($filename))
        {
            $postFields = array
            (
                'image' => '@' . $filename,
                'originalName' => $_FILES[$fieldname]['name'],
                'tags' => serialize($tags),
                'attributes' => serialize($attributes)
            );

            $result = $this->uploadByCurl($url, $postFields);

            return $result;
        }
        else return null;
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

        curl_setopt($ch, CURLOPT_NOPROGRESS, 0);
        curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, array('ezkpmedia\modules\Connector\Connector', 'progressCallback'));
        curl_setopt($ch, CURLOPT_TIMEOUT, 3600);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);

        return $result;
    }

    /**
     *
     * Progress reporter for curl.
     *
     * @param $download_size
     * @param $downloaded_size
     * @param $upload_size
     * @param $uploaded_size
     */
    public function progressCallback($download_size, $downloaded_size, $upload_size, $uploaded_size)
    {
        if (class_exists($this->progressClass))
        {
           if (method_exists($this->progressClass, $this->progressMethod))
           {
               $callback = array($this->progressClass, $this->progressMethod);
               $params = array($download_size, $downloaded_size, $upload_size, $uploaded_size);

               call_user_func_array($callback, $params);
           }
        }
    }

    /**
     *
     * Builds the url for accessing keymedia.
     *
     * @return bool|string
     */
    protected function getRequestUrl()
    {
        $url = $this->mediabaseDomain .'/media/upload';
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
}