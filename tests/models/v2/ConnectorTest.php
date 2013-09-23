<?php

namespace keymedia\tests\models\v2;

use keymedia\models\v2\Connector;

use \Mockery as m;

class ConnectorTest extends \PHPUnit_Framework_TestCase
{
    protected $username = 'username';
    protected $apiKey = 'key';
    protected $host = 'localhost';

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testUploadMediaFailsOnInvalidFile() {
        $mock = m::mock('keymedia\\models\\RequestInterface');
        $connector = new Connector($this->username, $this->apiKey, $this->host, $mock);

        $filepath = __DIR__ . '/../fixtures/no-image.jpg';
        $filename = 'Bergen.jpg';
        $tags = array('foo', 'bar');
        // Triggers a user error
        $res = $connector->uploadMedia($filepath, $filename, $tags);

        $this->assertEquals(null, $res);
    }

    /**
     * Test that calls to uploadMedia passes correctly
     * massaged data to the Request object
     */
    public function testUploadMedia()
    {
        $filepath = realpath(__DIR__ . '/../../fixtures/image.jpg');
        $filename = 'Bergen.jpg';
        $tags = array('foo', 'bar');

        $mock = m::mock('keymedia\\models\\RequestInterface');
        $mock->shouldReceive('perform')
            ->andSet('timeout', 10)
            ->andReturnUsing(function($url, $method, $payload, $options) use ($filepath, $filename, $tags) {
                if (!preg_match('/^http:\/\/[a-z0-9]+\/.+/', $url)) { return false; }
                if ($method !== 'POST') { return false; }
                if ($payload['file'] !== '@' . $filepath) { return false; }
                if ($payload['name'] !== $filename) { return false; }
                if ($payload['tags'] !== implode(',', $tags)) { return false; }
                return true;
            });

        $connector = new Connector($this->username, $this->apiKey, $this->host, $mock);
        $res = $connector->uploadMedia($filepath, $filename, $tags);
    }
}
