<?php

namespace keymedia\tests\models\media;

use \keymedia\models\media\Handler;
use \eZContentObjectAttribute;

use \Mockery as m;

class HandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testMediaOnEmptyAttributeFailes()
    {
        $mock = m::mock('eZContentObjectAttribute');
        $mock->shouldReceive('attribute')
            ->with('data_text')
            ->andReturn(array());

        $handler = new Handler($mock);
        $media = $handler->media('named-format');
        $this->assertNull($media);

        $mock = m::mock('eZContentObjectAttribute');
        $mock->shouldReceive('attribute')
            ->andReturn(json_encode((object)array()));
        $handler = new Handler($mock);
        $this->assertFalse($handler->hasMedia());
    }

    public function testHasMedia()
    {
        $values = array('id' => 123);
        $mock = m::mock('eZContentObjectAttribute');
        $mock->shouldReceive('attribute')
            ->andReturn(json_encode($values));

        $handler = new Handler($mock);
        $this->assertTrue($handler->hasMedia());
        $this->assertFalse($handler->hasMedia(12));
        $this->assertTrue($handler->hasMedia(123));
    }
}
