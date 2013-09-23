<?php

namespace keymedia\models;

interface RequestInterface
{
    /**
     * Perform request
     * @param string $url
     * @param string $method
     * @param string|array|null $payload
     * @return mixed
     */
    public function perform($url, $method, $payload = null, array $options = array());
}
