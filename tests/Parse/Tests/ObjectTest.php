<?php

namespace Parse\Tests;

use Parse\Object;
use Parse\Data\Binary;
use Parse\Data\GeoPoint;

class ObjectTest extends TestCase
{
    
    public function testSaveAndGet()
    {
        $obj = new Object("Address");
        $obj->firstName = "Hogeo";
        $obj->lastName = "Hage";
        $birthDay = new \DateTime("1970/01/01");
        $obj->birthDay = $birthDay;
        $obj->address = "Hiroshima, Japan";
        $point = new GeoPoint(89.11, 110);
        $obj->addressLocation = $point;
        $obj->age = 43;
        $this->assertEquals("Hogeo", $obj->firstName);
        $this->assertEquals("Hage", $obj->lastName);
        $this->assertEquals($birthDay, $obj->birthDay);
        $this->assertEquals("Hiroshima, Japan", $obj->address);
        $this->assertEquals($point, $obj->addressLocation);
        $destPoint = $obj->addressLocation;
        $this->assertEquals(89.11, $destPoint->getLatitude());
        $this->assertEquals(110, $destPoint->getLongitude());
        $this->assertEquals(43, $obj->age);
        $binary = new Binary('12jlkj(999');
        $obj->binaryData = $binary;
        $this->assertEquals($binary, $obj->binaryData);
        $this->assertNull($obj->objectId);
        $this->assertNull($obj->createdAt);
        $this->assertNull($obj->updatedAt);     
        try {
            $obj->objectId = "AX7980XPZA001";
            fail("No Error!");
        } catch (\Exception $e) {
            $this->assertInstanceOf('\RuntimeException', $e);                                   
        }       
        try {
            $obj->createdAt = new \DateTime();
            $this->fail("No Error!");
        } catch (\Exception $e) {
            $this->assertInstanceOf('\RuntimeException', $e);               
        }       
        try {
            $obj->updatedAt = new \DateTime();
            $this->fail("No Error!");
        } catch (\Exception $e) {
            $this->assertInstanceOf('\RuntimeException', $e);
        }
        $this->assertTrue($obj->hasProperty('age'));
        $this->assertFalse($obj->hasProperty('bloodType'));
        $obj->bloodType = 'AB';
        $this->assertTrue($obj->hasProperty('bloodType'));
        $obj->removeProperty('bloodType');
        $this->assertFalse($obj->hasProperty('bloodType'));

        $obj->save();
        $this->assertNotNull($obj->objectId);
        $this->assertNotNull($obj->createdAt);
        $this->assertNotNull($obj->updatedAt);

        $obj2 = Object::get('Address', $obj->objectId);
        $this->assertEquals($obj, $obj2);
        $obj->firstName = 'Jimmy';
        $obj->lastName = 'Page';
        $obj->age = 67;
        $obj->addressLocation->setLatitude(80);
        $obj->addressLocation->setLongitude(81);
        $obj->save();
        $obj2 = Object::get('Address', $obj->objectId);
        $this->assertEquals($obj, $obj2);

        $obj->increment('age', 3);
        $this->assertEquals(70, $obj->age);
        $obj->increment('age');
        $this->assertEquals(71, $obj->age);
        $obj->decrement('age', 3);
        $this->assertEquals(68, $obj->age);
        $obj->decrement('age');
        $this->assertEquals(67, $obj->age);

        $objectId = $obj->objectId;
        $updatedAt = $obj->updatedAt;
        $obj->refresh();
        $this->assertEquals($objectId, $obj->objectId);
        $this->assertEquals($updatedAt, $obj->updatedAt);

        $obj->delete();
        $this->assertTrue($obj->isDeleted());
        $this->assertNull($obj->objectId);
        $this->assertNull($obj->createdAt);
        $this->assertNull($obj->updatedAt);
    }

    public function testRelationship()
    {
        $post = new Object("Post");
        $post->title = "I'm lonely";
        $post->content = "Where should we go for lunch?";
        $post->save();

        $comment = new Object("Comment");
        $comment->content = "Let's do Okonomiyaki";
        $comment->parent = $post;
        $comment->save();

        $this->assertNotNull($post->objectId);
        $this->assertNotNull($comment->objectId);

        $post2 = Object::get("Post", $post->objectId);
        $comment2 = Object::get("Comment", $comment->objectId);
        $this->assertEquals($comment2->parent->objectId, $post->objectId);
        $this->assertEquals($comment2->parent->title, "I'm lonely");
    }

}