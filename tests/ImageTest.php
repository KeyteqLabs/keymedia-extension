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
}
