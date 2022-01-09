# trigonometry
<img alt="GitHub" src="https://img.shields.io/github/license/marcoconsiglio/trigonometry">
<img alt="GitHub release (latest by date)" src="https://img.shields.io/github/v/release/marcoconsiglio/trigonometry">
<a href="https://codecov.io/gh/MarcoConsiglio/trigonometry">
  <img src="https://codecov.io/gh/MarcoConsiglio/trigonometry/branch/dev/graph/badge.svg?token=SYS9COU0XZ"/>
</a>
<br>

Mutation testing <br>
<img alt="MSI" src="https://img.shields.io/badge/Mutations%20Score%20Indicator-82%25-green">
<img alt="MCC" src="https://img.shields.io/badge/Mutation%20Code%20Coverage-98%25-brightgreen">
<img alt="CC MSI" src="https://img.shields.io/badge/Covered%20Code%20MSI-83%25-green">
<br><br>
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
$alfa = Angle::createFromValues(180, 12, 43, Angle::CLOCKWISE); // 180° 12' 43"
$alfa = new Angle(new FromDegrees(180, 12, 43, Angle::CLOCKWISE))
```
### Parse a string
This creates an angle from its textual representation:
```php
$beta = Angle::createFromString("180° 12' 43\""); // Input from the user
$beta = new Angle(new FromString("180° 12' 43\""));
```

This is possible thank to the regular expression
```php
Angle::ANGLE_REGEX;
```
The regex treat degrees and minutes as integer numbers, but seconds are treated as a float number.

### Decimal
This create an angle from its decimal representation:
```php
$gamma = Angle::createFromDecimal(180.2119); // 180.2119°
$gamma = new Angle(new FromDecimal(180.2119));
```
### Radiant
This create an angle from its radiant representation:
```php
$delta = Angle::createFromRadiant(3.1452910063); // deg2rad(180.2119°)
$delta = new Angle(FromRadiant(3.1452910063));
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
There is read-only properties too:
```php
$alfa->degrees;   // 180
$alfa->minutes;   // 12
$alfa->seconds;   // 43
$alfa->direction; // Angle::CLOCKWISE (1)
```

You can cast the angle to decimal:
```php
$alfa->toDecimal(); // 180.2119
```

You can cast the angle to radiant:
```php
$alfa->toRadiant(); // 3.1452910063
```

## Negative angles
You can create negative angles too!
```php
$alfa = Angle::createFromValues(180, 12, 43, Angle::COUNTER_CLOCKWISE);
$beta = Angle::createFromString("-180° 12' 43\"");
$gamma = Angle::createFromDecimal(-180.2119); 
$delta = Angle::createFromRadiant(-3.1452910063);
```

## Comparison
You can compare an angle with a numeric value, numeric string or another `Angle` object.
### $\alpha > \beta$ (greater than)
```php
$alfa = Angle::createFromDecimal(180);
$beta = Angle::createFromDecimal(90);
$gamma = Angle::createFromDecimal(360);
$alfa->isGreaterThan(90);       // true     180 >  90
$alfa->gt("90");                // true     180 >  90
$alfa->isGreaterThan($gamma);   // false    180 > 360
$alfa->gt($gamma);              // false    180 > 360
```

### $\alpha \ge \beta$ (greater than or equal)
```php
$alfa = Angle::createFromDecimal(180);
$beta = Angle::createFromDecimal(90);
$gamma = Angle::createFromDecimal(90);
$alfa->isGreaterThanOrEqual(90);        // true 180 >=  90
$alfa->gte("180");                      // true 180 >= 180
$beta->isGreaterThanOrEqual($gamma);    // true  90 >=  90
$beta->gte(90);                         // true  90 >=  90
```

### $\alpha < \beta$ (less than)
```php
$alfa = Angle::createFromDecimal(90);
$beta = Angle::createFromDecimal(180);
$alfa->isLessThan(180);     // true  90 < 180
$alfa->lt(180);             // true  90 < 180
$alfa->isLessThan($beta);   // true  90 < 180
$beta->lt($alfa);           // true 180 < 90
```
### $\alpha \le \beta$ (less than or equal)
```php
$alfa = Angle::createFromDecimal(90);
$beta = Angle::createFromDecimal(180);
$alfa->isLessThanOrEqual(180);      // true
$alfa->lte(90);                     // true
$alfa->isLessThanOrEqual($beta);    // true
$alfa->lte($beta);                  // true
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
between two angles, passing in its constructor a FromAngles builder, which is a SumBuilder.
```php
$alfa = Angle::createFromDecimal(180);
$beta = Angle::createFromDecimal(270);
$gamma = new Sum(new FromAngles($alfa, $beta));
(string) $gamma; // 90° 0' 0"
```
Note that if the sum is more than +360° or less than -360°, the resulting angle will be corrected to remain between these limits.

# Code documentation
## UML Diagrams
You can find a class diagram at `docs/classes.png`.
![UML class diagram](https://github.com/MarcoConsiglio/trigonometry/blob/dev/docs/classes.png)
## phpDoc
You can read the code documentation at `docs/index.html`.