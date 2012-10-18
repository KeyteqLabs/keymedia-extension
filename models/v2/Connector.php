<?php

namespace keymedia\models\v2;

/**
 * KeyMedia connector, makes the module talk the talk that KeyMedia talks
 * Enables the KeyMedia API
 *
 * <code>
 *   $api = new Connector($username, $apiKey, $host);
 *   $api->searchByTags($tags, $operator);
 *   $api->search(array('q' => 'a term'));
 *   $result = $api->uploadMedia($filepath, $filename, $tags, $attributes);
 * </code>
 *
 * @author Henning Kvinnesland (henning@keyteq.no)
 * @author Raymond Julin (raymond@keyteq.no)
 * @since 1.0.0
 */
class Connector extends \keymedia\models\ConnectorBase
{

    /**
     * Generic search, utilize whatever you might want as conditions
     *
     * @param array $conditions
     * @return array
     */
    public function search(array $criteria = array(), array $options = array())
    {
        $payload = $options + $criteria;
        $results = $this->makeRequest('/media.json', $payload);
        if ($results && isset($results->media))
        {
            $hits = $results->media;
            $total = $results->total;
            return (object) compact('hits', 'total');
        }
        return false;
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

        $results = $this->makeRequest('/media.json', $params);
        if ($results && isset($results->media))
        {
            $hits = $results->media;
            $total = $results->total;
            return (object) compact('hits', 'total');
        }
        return false;
    }

    /**
     *
     * Retrieves medias matching one or more tags.
     *
     * @param array $tags A list of tags
     * @param string $operator and (default is or)
     * @param int $limit
     * @param int $offset
     * @param int|false $width
     * @param int|false $height
     *
     * @return mixed
     */
    public function searchByTags($tags = array(), $operator = 'or', $limit = 25, $offset = 0, $width = false, $height = false)
    {
        $criteria = compact('tags');
        $options = compact('operator', 'limit', 'offset');

        $results = $this->search($criteria, $options);
        if ($results && isset($results->media))
        {
            $hits = $results->media;
            $total = $results->total;
            return (object) compact('hits', 'total');
        }
        return false;
    }

    /**
     * Return media information for given media id
     *
     * @param string $id
     * @return object
     */
    public function media($id)
    {
        $response = $this->makeRequest('/media/' . $id . '.json');
        return isset($response->media) ? $response->media : false;
    }

    /**
     * Put tags on an existing media
     *
     * @param $id
     * @param array $tags
     *
     * @return object
     */
    public function tagMedia($id, $tags = array())
    {
        return $this->makeRequest('/media/' . $id . '.json', compact('tags'), 'PUT');
    }

    /**
     * Report media usage to keymedia
     *
     * @param $id
     * @param array $reference
     * @return bool
     */
    public function reportUsage($id, array $reference)
    {
        $reference += compact('id');

        $response = $this->makeRequest('/media/references.json', $reference, 'POST');
        return isset($response->media) ? $response->media : false;
    }

    /**
     * Add new version
     *
     * @param string $id
     * @param string $slug
     * @param array $transformation
     * @return string Relative url to new version
     */
    public function addVersion($id, $slug, array $transformation = array())
    {
        $payload = array('slug' => $slug);

        if (isset($transformation['size']))
        {
            list($width, $height) = $transformation['size'];
            $payload += compact('width', 'height');
        }
        if (isset($transformation['coords']))
            $payload['coords'] = implode(',', $transformation['coords']);

        $url = '/media/' . $id . '/versions.json';
        return $this->makeRequest($url, $payload, 'POST');
    }

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
    public function uploadMedia($filename, $name, $tags = array(), $attributes = array())
    {
        if (!file_exists($filename)) {
            user_error("KeyMedia extension failed uploading $filename");
            return null;
        }

        if (ini_get('max_execution_time') < $this->timeout)
            set_time_limit($this->timeout + 10);

        $file = '@' . $filename;
        $file .= ';type=' . $this->mime($filename);

        // Must send mime type along
        if (isset($attributes['attributes'])) {
            $payload = $attributes + compact('file', 'name', 'tags');
        }
        else {
            $payload = compact('file', 'name', 'tags', 'attributes');
        }

        return $this->makeRequest('/media.json', $payload, 'POST');
    }

    /**
     * Simplify a result set
     *
     * @param object $media
     * @return object
     */
    public function simplify($media)
    {
        $parts = explode('.', $media->name);
        $ending = array_pop($parts);
        $width = 160;
        $height = 120;
        $ending = $media->scalesTo->ending;
        $mimeParts = explode('/', $media->file->type);
        // Should not be hardcoded references to brightcove here
        if (isset($media->brightcove) && isset($media->brightcove->thumb)) {
            $thumbUrl = $media->brightcove->thumb;
        }
        else {
            $thumbUrl = '//' . $media->host . '/' . $width . 'x' . $height . '/' . $media->_id . '.' . $ending;
        }
        $thumb = (object) array('url' => $thumbUrl);
        $file = $media->file;
        return (object) array(
            'id' => $media->_id,
            'tags' => $media->tags,
            'type' => $mimeParts[0],
            'filesize' => $file->size,
            'width' => (int) isset($file->width) ? $file->width : 0,
            'height' => (int) isset($file->height) ? $file->height : 0,
            'thumb' => $thumb,
            'filename' => $media->name,
            'shared' => $media->shared,
            'scalesTo' => $media->scalesTo,
            'host' => $media->host
        );
    }

    protected function signHeader(&$payload)
    {
        // Alphabetic sort
        $secret = $this->apiKey;
        $message = '';
        foreach ($payload as $k => $v)
        {
            if (!is_array($v) && substr($v,0,1) !== '@')
                $message .= $k.$v;
        }

        $signature = hash_hmac('sha1', $message, $secret);

        return array(
            "X-Keymedia-Username: {$this->username}",
            "X-Keymedia-Signature: {$signature}"
        );

    }

    /**
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

        return $url;
    }

    protected function sign($username, $secret, $payload)
    {
        return hash_hmac('sha256', $payload, $secret);
    }
}
