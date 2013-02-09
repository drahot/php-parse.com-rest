<?php

namespace Parse\Tests;

use Parse\Parse;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{

    public function setup()
    {
        Parse::initialize(
            "YOUR APP ID",
            "YOUR REST KEY",
            "YOUR MASTER KEY"
        );
    }

}