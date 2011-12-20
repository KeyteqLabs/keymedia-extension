<?php

namespace ezr_keymedia\models\v1;

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
class Connector extends \ezr_keymedia\models\ConnectorBase
{
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

        if (file_exists($filename))
        {
            $payload = array
            (
                'media' => '@' . $filename,
                'originalName' => $originalName,
                'tags' => serialize($tags),
                'attributes' => serialize($attributes)
            );

            $result = $this->makeRequest($this->getRequestUrl('upload'), $payload, 'POST');

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
}
