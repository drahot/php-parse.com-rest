<?php

namespace Parse\Tests;

use Parse\Parse;

class ParseTest extends TestCase
{
    
    public function testInitialize()
    {
        Parse::initialize(
            "YOUR APP ID",
            "YOUR REST KEY",
            "YOUR MASTER KEY"
        );
        $this->assertEquals("YOUR APP ID", Parse::getAppId());
        $this->assertEquals("YOUR REST KEY", Parse::getRestApiKey());
        $this->assertEquals("YOUR MASTER KEY", Parse::getMasterKey());
    }

}