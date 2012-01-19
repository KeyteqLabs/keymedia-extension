<?php

namespace keymedia\tests\models\image;

use \keymedia\models\image\Handler;
use \eZContentObjectAttribute;

class HandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testMediaOnEmptyAttributeFailes()
    {
        $values = array();
        $attributeMock = $this->getMock('eZContentObjectAttribute', array('attribute'));
        // First time calling attribute('data_text') it should be empty
        $attributeMock->expects($this->any())
            ->method('attribute')
            ->with('data_text')
            ->will($this->returnValue($values));
        $attributeMock->DataText = json_encode($values);

        $handler = new Handler($attributeMock);
        $media = $handler->media('named-format');
        $this->assertNull($media);
    }

    public function testHasMedia()
    {
        $emptyAttr = $this->getMock('eZContentObjectAttribute', array('attribute'));
        $emptyAttr->expects($this->any())
            ->method('attribute')
            ->will($this->returnValue(json_encode((object)array())));

        $values = array('id' => 123);
        $attr = $this->getMock('eZContentObjectAttribute', array('attribute'));
        $attr->expects($this->any())
            ->method('attribute')
            ->will($this->returnValue(json_encode($values)));

        $handler = new Handler($emptyAttr);
        $this->assertFalse($handler->hasImage());

        $handler = new Handler($attr);
        $this->assertTrue($handler->hasImage());
        $this->assertFalse($handler->hasImage(12));
        $this->assertTrue($handler->hasImage(123));
    }
    
    public function testGetMedia()
    {
        return;
        $values = array(
            'id' => 123,
            'host' => 'test.com',
            'ending' => 'jpg'
        );
        $attributeMock = $this->getMock('eZContentObjectAttribute', array('attribute'));
        // First time calling attribute('data_text') it should be empty
        $attributeMock->expects($this->any())
            ->method('attribute')
            ->with('data_text')
            ->will($this->returnValue($values));

        $handler = new Handler($attributeMock);
        $media = $handler->media(array(100, 100));
        print_r($media);
        $this->assertNotNull($media);
    }
}
