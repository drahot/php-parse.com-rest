<?php

namespace Parse\Tests;

use Parse\Query\UserQuery;
use Parse\User;

class UserQueryTest extends TestCase
{

    private $user;
    private $user2;

    public function setup()
    {
        $this->user = new User("okamoto@test_hoge.jp", "12345");
        $this->user->signup();
        $this->user->login();
        $this->user->firstName = "Taro";
        $this->user->lastName = "Okamoto";
        $this->user->phone = "121321-11111";
        $this->user->age = 90;
        $this->user->save();

        $this->user2 = new User("yamada@test_hoge.jp", "54321");
        $this->user2->signup();
        $this->user2->login();
        $this->user2->firstName = "Ichiro";
        $this->user2->lastName = "Yamada";
        $this->user2->phone = "080-1234-5678";
        $this->user2->age = 18;
        $this->user2->save();
    }

    public function testUser()
    {
        $query = new UserQuery();
        $list = $query->eq("firstName", "Taro")->execute();
        $userResult = $list[0];
        $this->assertEquals($this->user->firstName, $userResult->firstName);
        $this->assertEquals($this->user->lastName, $userResult->lastName);

        $query->reset();
        $list = $query->ne("firstName", "Taro")->execute();
        $objResult = $list[0];
        $this->assertEquals($this->user2->firstName, $objResult->firstName);
        $this->assertEquals($this->user2->lastName, $objResult->lastName);

        $query->reset();
        $list = $query->gt("age", 18)->execute();
        $objResult = $list[0];
        $this->assertEquals($this->user->firstName, $objResult->firstName);
        $this->assertEquals($this->user->lastName, $objResult->lastName);

        $query->reset();
        $list = $query->lt("age", 80)->execute();
        $objResult = $list[0];
        $this->assertEquals($this->user2->firstName, $objResult->firstName);
        $this->assertEquals($this->user2->lastName, $objResult->lastName);
    }

    public function tearDown()
    {
        $this->user = new User("okamoto@test_hoge.jp", "12345");
        $this->user->login();
        $this->user->delete();
        $this->user2 = new User("yamada@test_hoge.jp", "54321");
        $this->user2->login();
        $this->user2->delete();
    }

}