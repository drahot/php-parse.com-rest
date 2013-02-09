<?php

namespace Parse\Tests;

use Parse\Object;
use Parse\Query\ObjectQuery;

class ObjectQueryTest extends TestCase
{

    private $obj;
    private $obj2;

    public function setup()
    {
        parent::setup();

        $this->obj = new Object("GamePlayer");
        $this->obj->firstName = "Taro";
        $this->obj->lastName = "Okamoto";
        $this->obj->score = 81465;
        $this->obj->save();

        $this->obj2 = new Object("GamePlayer");
        $this->obj2->firstName = "Takeshi";
        $this->obj2->lastName = "Tanaka";
        $this->obj2->score = 10000;
        $this->obj2->save();
    }


    public function testQuery()
    {

        $query = new ObjectQuery("GamePlayer");
        $list = $query->eq("firstName", "Taro")->execute();
        $objResult = $list[0];
        $this->assertEquals($this->obj->firstName, $objResult->firstName);
        $this->assertEquals($this->obj->lastName, $objResult->lastName);
        $this->assertEquals($this->obj->score, $objResult->score);

        $query->reset();
        $list = $query->ne("firstName", "Taro")->execute();
        $objResult = $list[0];
        $this->assertEquals($this->obj2->firstName, $objResult->firstName);
        $this->assertEquals($this->obj2->lastName, $objResult->lastName);
        $this->assertEquals($this->obj2->score, $objResult->score);

        $query->reset();
        $list = $query->gt("score", 9999)->order('objectId')->execute();
        $this->assertEquals(2, count($list));

        $query->reset();
        $list = $query->gte("score", 10000)->order('objectId', true)->execute();
        $this->assertEquals(2, count($list));

        $query->reset();
        $list = $query->gte("score", 10001)->execute();
        $this->assertEquals(1, count($list));

        $query->reset();
        $list = $query->gte("score", 10000000)->execute();
        $this->assertEquals(0, count($list));

        $query->reset();
        $list = $query->lt("score", 10000000)->order('objectId')->execute();
        $this->assertEquals(2, count($list));

        $query->reset();
        $list = $query->lte("score", 81465)->order('objectId')->execute();
        $this->assertEquals(2, count($list));

        $query->reset();
        $list = $query->lte("score", 10000)->order('objectId')->execute();
        $this->assertEquals(1, count($list));

        $query->reset();
        $list = $query->lte("score", 9999)->order('objectId')->execute();
        $this->assertEquals(0, count($list));

        $query->reset();
        $list = $query->in("lastName", array("Okamoto", "Tanaka"))->execute();
        $this->assertEquals(2, count($list));

        $query->reset();
        $list = $query->nin("lastName", array("Okamoto", "Tanaka"))->execute();
        $this->assertEquals(0, count($list));

        $query->reset();
        $list = $query->exists("lastName")->execute();
        $this->assertEquals(2, count($list));
        
        $query->reset();
        $list = $query->exists("lastName", false)->execute();
        $this->assertEquals(0, count($list));

        $query->reset();
        $query->eq("lastName", "Okamoto");
        $newQuery = new ObjectQuery("GamePlayer");      
        $newQuery->eq("lastName", "Tanaka");
        $list = $query->addOrQuery($newQuery)->execute();
        $this->assertEquals(2, count($list));
        
        $query->reset();
        $list = $query->exists("lastName")->count()->execute();
        $this->assertEquals(2, $list['count']);

        $query->reset();
        $list = $query->exists("lastName")->limit(1)->skip(1)->execute();
        $this->assertEquals(1, count($list));
    }   

    public function tearDown()
    {
        $this->obj->delete();
        $this->obj2->delete();
    }
}