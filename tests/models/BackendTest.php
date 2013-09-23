<?php

namespace keymedia\tests\models;

use \keymedia\models\Backend;
use \keymedia\models\v2\Connector;

class BackendTest extends \PHPUnit_Framework_TestCase
{
    public function testUpload()
    {
        $host = 'localhost';
        $apiVersion = '2';
        $backend = Backend::create(array(
            'id' => 1,
            'host' => $host,
            'username' => 'test',
            'api_key' => 'test',
            'api_version' => $apiVersion
        ));

        $mock = \Mockery::mock('keymedia\\models\\ConnectorInterface', array(
            'search' => 1,
            'setProgressCallback' => 1,
            'searchByTerm' => 1,
            'searchByTags' => 1,
            'uploadMediaFromForm' => 1,
            'getTimeout' => 1,
            'setTimeout' => 1
        ));

        $filepath = __DIR__ . '/../fixtures/image.jpg';
        $filename = 'Bergen.jpg';
        $tags = array('foo', 'bar');

        $mock
            ->shouldReceive('uploadMedia')
            ->with($filepath, $filename, $tags)
            ->andReturnUsing(function($path, $name, $tags) {
                $media = array(
                    'id' => 1,
                    'host' => $host
                );
                return compact('media');
            });

        $backend->setConnector($apiVersion, $mock);
        $media = $backend->upload($filepath, $filename, $tags);

        $this->assertIsA('keymedia\\models\\Media', $media);
    }
}
