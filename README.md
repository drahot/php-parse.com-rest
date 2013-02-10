php-parser.com-rest
===================

PHP Parser.com REST API


SETUP
=========================

### sample of parse initialize ###

```
<?php

use Parse\Parse;

// Initialize 
Parse::Initialize(
	"YOUR APP ID",
	"YOUR REST KEY",
	"YOUR MASTER KEY"	
);

?>
```

EXAMPLE
=========================

```
<?php

use Parse\Parse;
use Parse\Object;
use Parse\Data\Binary;
use Parse\Data\GeoPoint;

$address = new Object("Address");
$address->firstName = "Hogeo";
$address->lastName = "Hage";
$birthDay = new \DateTime("1970/01/01");
$address->birthDay = $birthDay;
$address->address = "Hiroshima, Japan";
$point = new GeoPoint(89.11, 110);
$address->addressLocation = $point;
$address->age = 43;
$binary = new Binary('abcdefgHAAA');
$address->binaryData = $binary;
$address->save();

$address2 = Object::get("Address", $address->objectId)

var_dump($address2);

?>