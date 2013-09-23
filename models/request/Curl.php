<?php

namespace keymedia\models\request;

use keymedia\models\RequestInterface;

class Curl implements RequestInterface
{
    /**
     * @var int
     */
    public $timeout = 1800;

    /**
     * Perform request
     * @param string $url
     * @param string $method
     * @param string|array|null $params
     * @param array $options
     * @return mixed
     */
    public function perform($url, $method, $params = null, array $options = array())
    {
        $options += array(
            'headers' => false,
            'callback' => false,
            'timeout' => $this->timeout
        );
        $ch = curl_init();
        switch ($method) {
            case 'PUT':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
                break;
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                break;
            case 'GET':
                $joiner = strpos($url, '?') ? '&' : '?';
                $url .= $joiner . http_build_query($params);
                break;
        }
        curl_setopt($ch, CURLOPT_URL, $url);

        if ($options['callback']) {
            curl_setopt($ch, CURLOPT_NOPROGRESS, 0);
            curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, $options['callback']);
        }

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        if (is_numeric($options['timeout'])) {
            curl_setopt($ch, CURLOPT_TIMEOUT, $options['timeout']);
        }

        if ($options['headers']) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $options['headers']);
        }

        return curl_exec($ch);
    }
}
