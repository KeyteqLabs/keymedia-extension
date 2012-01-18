<?php

namespace ezr_keymedia\tests;

use \ezr_keymedia\models\Image;

class ImageTest extends \PHPUnit_Framework_TestCase
{
    public function testIdAttribute()
    {
        // Create the Array fixture.
        $fixture = array();
        $image = new Image(array(
            'id' => 1
        ));
        $imageUnder = new Image(array(
            '_id' => 2
        ));

        $imageObject = new Image((object) array(
            '_id' => 2
        ));
 
        // Assert that the size of the Array fixture is 0.
        $this->assertEquals(1, $image->id);
        $this->assertNotEquals(2, $image->id);
        $this->assertEquals(2, $imageUnder->id);
        $this->assertEquals(2, $imageObject->id);
    }

    public function testDataMethodRespectsIdAttribute()
    {
        $image = new Image(array(
            '_id' => 2
        ));
        $data = $image->data();
        $this->assertEquals(2, $data->id);
        $this->assertEquals(2, $image->id);
    }

    public function testSetHost()
    {
        $image = new Image((object) array(
            'id' => 1
        ));
        $host = 'example.com';
        $image->host($host);
        $data = $image->data();
        $this->assertEquals($host, $data->host);
        $this->assertEquals($host, $image->host());
    }

    public function testFitInside()
    {
        $width = $height = 1000;
        $image = new Image((object) array(
            'file' => (object) compact('width', 'height')
        ));
        $w = $h = 500;
        $expect = Image::fitToBox($w, $h, $width, $height);
        $actual = $image->boxInside($w, $h);

        $this->assertEquals($expect, $actual);
    }
}
