<?php

namespace Parse\Tests;

use Parse\Parse;

/**
 * 
 * @author drahot
 */
class ParseTest extends TestCase
{
	
	public function testInitialize()
	{
		Parse::initialize(
			"CkKns64Qlk6hDAiwm1vuKMcXo9iILWI6FUBKaXlL",
			"w4U0sTDR2aL600uSz2uNGkv48vcIGxnH1o2pzN07",
			"BbOdOQqpDcr7xY1eDLduyJzNW04jBdFzdazd5r7C"
		);
		$this->assertEquals("CkKns64Qlk6hDAiwm1vuKMcXo9iILWI6FUBKaXlL", Parse::getAppId());
		$this->assertEquals("w4U0sTDR2aL600uSz2uNGkv48vcIGxnH1o2pzN07", Parse::getRestApiKey());
		$this->assertEquals("BbOdOQqpDcr7xY1eDLduyJzNW04jBdFzdazd5r7C", Parse::getMasterKey());
	}

}