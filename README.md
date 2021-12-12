# trigonometry
<img alt="GitHub" src="https://img.shields.io/github/license/marcoconsiglio/trigonometry">
<img alt="GitHub release (latest by date)" src="https://img.shields.io/github/v/release/marcoconsiglio/trigonometry">
<br>
A PHP support for angles and trigonometric functions.

# Installation
`composer require marcoconsiglio/trigonometry`

# Usage
Import this class to represent angles.
```php
use MarcoConsiglio\Trigonometry\Angle;
```
Import this class to sum angles.
```php
use MarcoConsiglio\Trigonometry\Operations\Sum;
```
## Creating an angle
### Degrees, minutes and seconds
This creates an angle from its values in degrees, minutes and seconds:
```php
$alfa = Angle::createAngleFromValues(180, 12, 43); // 180° 12' 43"
```
### Parse a string
This creates an angle from its textual representation:
```php
$beta = Angle::createAngleFromString("180° 12' 43\""); // Input from the user
```

This is possible thank to the regular expression
```php
Angle::ANGLE_REGEX;
```
The regex treat degrees and minutes as integer numbers, but seconds are treated as one digit precision float number.

### Decimal
This create an angle from its decimal representation:
```php
$gamma = Angle::createFromDecimal(180.2119); // 180.2119°
```
### Radiant
This create an angle from its radiant representation:
```php
$gamma = Angle::createFromRadiant(3.1452910063); // deg2rad(180.2119°)
```

### Exceptions when creating an angle
Creating an angle by values overflowing the maximum (+/-)360° throws the `AngleOverflowException`

Creating an angle by string overflowing the maximum (+/-)360° throws the `NoMatchException`

## Getting angle values
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

You can cast the angle to decimal:
```php
echo (string) $alfa->toDecimal(); // 180.2119
```

You can cast the angle to radiant:
```php
echo (string) $alfa->toRadiant(); // 3.1452910063
```

## Negative angles
You can create negative angles too!
```php
$alfa = Angle::createAngleFromValues(180, 12, 43, Angle::COUNTER_CLOCKWISE);
$beta = Angle::createAngleFromString("-180° 12' 43\"");
$gamma = Angle::createFromDecimal(-180.2119); 
$delta = Angle::createFromRadiant(-3.1452910063);
```
### Direction
Positive angle are represented by the class constant
```php
Angle::CLOCKWISE; // 1
```
while negative angle are represented by the opposite class constant:
```php
Angle::COUNTER_CLOCKWISE; // 1
```
You can toggle direction:
```php
$alfa->toggleDirection();
```
You can check if an angle is clockwise or counterclockwise.
```php
$alfa->isClockwise();           // false
$alfa->isCounterClockwise();    // true
```
## Algebric sum between two angles
The `Sum` class extends the `Angle` class, so you immediately obtain the algebric sum
between two angles.
```php
$alfa = Angle::createFromDecimal(180);
$beta = Angle::createFromDecimal(270);
$gamma = new Sum($alfa, $beta);
(string) $gamma; // 90° 0' 0"
```
You can sum negative angles to.

## Tests
By launching this command you can produce a testbook in `/TESTS.md`, a coverage report in `/tests/coverage_report/index.html` and output tests results on the command line:
```bash
vendor/bin/phpunit --testdox
```