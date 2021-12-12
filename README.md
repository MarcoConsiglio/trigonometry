# trigonometry
A PHP support for angles and trigonometric functions.
[![Latest Stable Version](http://poser.pugx.org/marcoconsiglio/trigonometry/v)](https://packagist.org/packages/marcoconsiglio/trigonometry) [![Total Downloads](http://poser.pugx.org/marcoconsiglio/trigonometry/downloads)](https://packagist.org/packages/marcoconsiglio/trigonometry) [![Latest Unstable Version](http://poser.pugx.org/marcoconsiglio/trigonometry/v/unstable)](https://packagist.org/packages/marcoconsiglio/trigonometry) [![License](http://poser.pugx.org/marcoconsiglio/trigonometry/license)](https://packagist.org/packages/marcoconsiglio/trigonometry) [![PHP Version Require](http://poser.pugx.org/marcoconsiglio/trigonometry/require/php)](https://packagist.org/packages/marcoconsiglio/trigonometry)
# Installation
`composer require marcoconsiglio/trigonometry`

# Usage
## Creating an angle
This creates an angle from its values in degrees, minutes and seconds:
```php
$alfa = Angle::createAngleFromValues(180, 12, 43); // 180° 12' 43"
```

This creates an angle from its textual representation:
```php
$beta = Angle::createAngleFromString("180° 12' 43\""); // Input from the user
```

This create an angle from its decimal representation:
```php
$gamma = Angle::createFromDecimal(180.2119); // 180.2119°
```

This create an angle from its radiant representation:
```php
$gamma = Angle::createFromRadiant(3.1452910063); // deg2rad(180.2119°)
```

You can obtain degrees values separated in an array (simple by default, or associative):
```php
$values = $alfa->getDegrees();
echo $values[0]; // Degrees
echo $values[1]; // Minutes
echo $values[2]; // Seconds
$values = $alfa->getDegrees(true);
echo $value['degrees'];
echo $value['minutes'];
echo $value['seconds'];
```
or in a textual representation:
```php
echo (string) $beta; // 180° 12' 43"
```
## Algebric sum between two angles
$alfa = Angle::createFromDecimal(180.2119)
### Exceptions when creating an angle
Creating an angle by values overflowing the maximum (+/-)360° throws the `AngleOverflowException`

Creating an angle by string overflowing the maximum (+/-)360° throws the `NoMatchException`