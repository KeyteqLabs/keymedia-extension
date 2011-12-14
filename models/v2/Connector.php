<?php

namespace ezr_keymedia\models\v2;

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
 * @author Henning Kvinnesland (henning@keyteq.no)
 * @author Raymond Julin (raymond@keyteq.no)
 * @since 1.0.0
 */
class Connector extends \ezr_keymedia\models\ConnectorBase
{

    /**
     * Generic search, utilize whatever you might want as conditions
     *
     * @param array $conditions
     * @return array
     */
    public function search(array $conditions = array())
    {
    }

    /**
     * Search by one or more tags.
     *
     * @param $q
     * @param bool $attributes
     * @param bool $collection
     * @param bool $limit
     * @param bool $offset
     * @param bool $width
     * @param bool $height
     * @param bool $externalId
     *
     * @return mixed
     */
    public function searchByTerm($q, $attributes = false, $collection = false, $limit = false, $offset = false, $width = false, $height = false, $externalId = false)
    {
        $params = compact('q', 'limit', 'offset');
        if ($width && !is_array($width)) $params['width'] = $width;
        if ($height && !is_array($height)) $params['height'] = $height;

        if ($attributes !== false) $params['attributes'] = $attributes;
        if ($collection !== false) $params['collection'] = $collection;
        if ($externalId !== false) $params['externalId'] = $externalId;

        return $this->makeRequest('/media.json', $params);
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

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        $data = json_decode($result);
        return $data;
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
    protected function getRequestUrl($action, array $payload = array())
    {
        $url = $this->mediabaseDomain . $action;

        // Ensure it has http://
        if (strpos($url, "http") === false) $url = 'http://' . $url;

        /*
        $urlArr = parse_url($url);
        $authUrl = $urlArr['scheme'] . '://' . $urlArr['host'] . $urlArr['path'];
        $auth = md5($authUrl . $this->apiKey);
        $payload += array(
            'username' => $this->username,
            'signature' => $this->sign($this->username, $this->apiKey, $payload)
        );
         */

        if ($payload) $url .= '?' . http_build_query($payload);

        return $url;
    }

    protected function sign($username, $secret, $payload)
    {
        return hash_hmac('sha256', $payload, $secret);
    }
}
