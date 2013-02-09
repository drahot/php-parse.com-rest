<?php

namespace Parse\Tests;

use Parse\User;
use Parse\Data\Binary;
use Parse\Data\GeoPoint;

class UserTest extends TestCase
{
	
	public function setup()
	{
		parent::setup();
		$user = new User("hogehage@test_hoge.jp", "12345");
		try {
			$user->login();
			$user->delete();
		} catch (\Exception $e) {
		}
		$user = new User("hotta@digitalize.biz", "12345");
		try {
			$user->login();
			$user->delete();
		} catch (\Exception $e) {
		}
	}	

	public function testUser()
	{
		$user = new User("hogehage@test_hoge.jp", "12345");
		$user->signup();
		try {
			$user->firstName = "Hogeo";
			$user->lastName = "Hage";
			$user->phone = '1234-1236-9999';
			$user->save();
			$this->fail("not raise exception!");
		} catch (\Exception $e) {
			$this->assertInstanceOf('\RuntimeException', $e);
		}
		try {
			$user->delete();
			$this->fail("not raise exception!");
		} catch (\Exception $e) {
			$this->assertInstanceOf('\RuntimeException', $e);
		}

		$user->login();
		$user->firstName = "Hogeo";
		$user->lastName = "Hage";
		$user->phone = "121321-11111";
		try {
			$user->sessionToken = "AAAHKJHK";
			$this->fail("not raise exception!");
		} catch (\Exception $e) {
			$this->assertInstanceOf('\RuntimeException', $e);
		}
		$user->save();

		$user2 = User::get($user->objectId);
		$this->assertEquals($user, $user2);
	}

	public function testDelete()
	{
		$user = new User("hogehige@hogehage.jp", "12345");
		$user->signup();
		try {
			$user->login();
			$user->delete();
		} catch (\Exception $e) {
			$this->fail("fail delete!");
		}
		try {
			$user->login();
			$this->fail("data exists");
		} catch (\Exception $e) {

		}
	}

	public function testResetPassword()
	{
		// $user = new User("hotta@digitalize.biz", "12345");
		// $user->signup();
		// try {
		// 	$user->login();
		// 	$user->resetPassword("hotta@digitalize.biz");
		// } catch (\Exception $e) {
		// 	$this->fail("fail delete!");
		// }
	}

}