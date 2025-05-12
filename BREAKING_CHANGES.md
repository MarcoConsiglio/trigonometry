# Breaking changes check
v1.1.1 --> v2.0.0
## Legend
```diff
- Previous version code
+ Newest version code
```
## Methods
### Signature change
```php
abstract class AngleBuilder implements AngleBuilderInterface {}
```
```diff
- abstract public function fetchData(): array;
+ public function fetchData(): array {}
```
### Public Visibility Change
### Public Parameters Change
#### Added Parameter 
```php
class AngleOverflowException extends Exception {}
```
```diff
- public function __construct() {}
+ public function __construct(string $message) {}
```
#### Removed Parameter
```php
abstract class AngleBuilder implements AngleBuilderInterface {}
```
```php
class FromDecimal extends AngleBuilder {}
```
```php
class FromDegrees extends AngleBuilder {}
```
```php
class FromRadiant extends AngleBuilder {}
```
```php
class FromString extends AngleBuilder {}
```
```diff
- abstract public function checkOverflow($data);
+ abstract public function checkOverflow();
```
```diff
- abstract public function calcDegrees($data);
+ abstract public function calcDegrees();
```
```diff
- abstract public function calcMinutes($data);
+ abstract public function calcMinutes();
```
```diff
- abstract public function calcSeconds($data);
+ abstract public function calcSeconds();
```
```diff
- abstract public function calcSign($data);
+ abstract public function calcSign();
```
<br>

```php
class Sum extends Angle {}
```
```diff
- public function __construct(Angle $first, Angle $second)
+ public function __construct(SumBuilder $builder)
```

#### Parameter Renamed
```php
class NoMatchException extends Exception {}
```
```diff
- public function __construct(string $subject_string) {}
+ public function __construct(string $angle) {}
```
#### Default value Removed
### Public method Removed
```php
class Sum extends Angle {}
```
```diff
- public function calcSeconds() {}
- public function calcSign() {}
```
## Public Constants and Public Properties
### Name Changed
### Default Value Changed
```php
class Angle implements AngleInterface
```
```diff
- public const MAX_MINUTES = self::MAX_DEGREES * 60;
+ public const MAX_MINUTES = 60;
```
```diff
- public const MAX_SECONDS = self::MAX_MINUTES * 60;
+ public const MAX_SECONDS = 60;
```
### Constant or Property Removed
## Classes
### Class Removed
### Class Renamed
### Interface Unimplement
## Interfaces
### Interface Renamed