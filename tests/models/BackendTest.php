<?php

namespace keymedia\tests\models;

use \keymedia\models\Backend;
use \keymedia\models\v2\Connector;
use \keymedia\models\ConnectorInterface;

class BackendTest extends \PHPUnit_Framework_TestCase
{
    protected $backend;
    protected $host = 'localhost';
    protected $apiVersion = '2';

    public function setUp()
    {
        $this->backend = Backend::create(array(
            'id' => 1,
            'host' => $this->host,
            'username' => 'test',
            'api_key' => 'test',
            'api_version' => $this->apiVersion
        ));
    }

    public function testGetConnection()
    {
        $con = $this->backend->connection();
        $this->assertTrue($con instanceof ConnectorInterface);
    }

    public function testUpload()
    {
        $host = $this->host;
        $filepath = __DIR__ . '/../fixtures/image.jpg';
        $filename = 'Bergen.jpg';
        $tags = array('foo', 'bar');

        $mock = \Mockery::mock('keymedia\\models\\ConnectorInterface', array(
            'search' => 1,
            'searchByTerm' => 1,
            'searchByTags' => 1
        ));

        $mock->shouldReceive('uploadMedia')
            ->with($filepath, $filename, $tags, \Mockery::any())
            ->andReturnUsing(function($path, $name, $tags) use($host) {
                $media = array('id' => 1, 'host' => $host);
                $result = new \stdClass;
                $result->media = $media;
                $result->host = $host;
                return $result;
            });

        $this->backend->setConnector($this->apiVersion, $mock);
        $media = $this->backend->upload($filepath, $filename, $tags);

        $this->assertInstanceOf('keymedia\\models\\Media', $media);
    }
}
