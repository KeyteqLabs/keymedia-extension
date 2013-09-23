<?php

namespace keymedia\tests\models;

use \keymedia\models\Media;

class MediaTest extends \PHPUnit_Framework_TestCase
{
    public function testIdAttribute()
    {
        // Create the Array fixture.
        $fixture = array();
        $media = new Media(array(
            'id' => 1
        ));
        $mediaUnder = new Media(array(
            '_id' => 2
        ));

        $mediaObject = new Media((object) array(
            '_id' => 2
        ));
 
        // Assert that the size of the Array fixture is 0.
        $this->assertEquals(1, $media->id);
        $this->assertNotEquals(2, $media->id);
        $this->assertEquals(2, $mediaUnder->id);
        $this->assertEquals(2, $mediaObject->id);
    }

    public function testDataMethodRespectsIdAttribute()
    {
        $media = new Media(array(
            '_id' => 2
        ));
        $data = $media->data();
        $this->assertEquals(2, $data->id);
        $this->assertEquals(2, $media->id);
    }

    public function testSetHost()
    {
        $media = new Media((object) array(
            'id' => 1
        ));
        $host = 'example.com';
        $media->host($host);
        $data = $media->data();
        $this->assertEquals($host, $data->host);
        $this->assertEquals($host, $media->host());
    }

    public function testFitInside()
    {
        $width = $height = 1000;
        $media = new Media((object) array(
            'file' => (object) compact('width', 'height')
        ));
        $w = $h = 500;
        $expect = Media::fitToBox($w, $h, $width, $height);
        $actual = $media->boxInside($w, $h);

        $this->assertEquals($expect, $actual);
    }
}
