<?php

namespace Parse\Tests;

use Parse\File;

class FileTest extends TestCase
{
	
	public function testFile()
	{
		$data = file_get_contents(__DIR__."/websnap8mw5FE_medium.png");
		$response = File::upload("websnap8mw5FE_medium.png", $data);
		$this->assertNotNull($response['url']);
		$this->assertNotNull($response['name']);
		$name = $response['name'];
		File::delete("websnap8mw5FE_medium.png");
	}

}