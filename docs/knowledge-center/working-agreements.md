# Working agreements

* All the files must start with:
```php
php declare(strict_types=1);
```

* All the classes by default are final:
```php
final class Person ✅

class Person ❌
```

* All names with **CamelCase**:
```php
$searchByName; ✅

$searchbyname; ❌

$search_by_name; ❌ 
```

* Descriptive names on variables and methods:
```php
$modelRepository = $this->modelRepository; ✅

$a = $this->modelRepository; ️❌

public function searchVideoGamesById(); ✅

public function search(); ❌
```

* Getters and setters without prefix **get** and **set**:
```php
public function name(); ✅

public function getName(); ❌

public function updateAge(); ✅

public function setAge(); ❌
```

* Use annotations if is only necessary:
```php
/** @param VideoGames[] $games */ ✅
public array $games; 

/**
* @param int $numberOne
* @param int $number2        ❌
* @return int
 */
public function sum(int $numberOne, int $number2): int 
{
 //...
}
```

* Indicate return value:
```php
public function sum(int $numberOne, int $number2): int ✅

public function sum(int $numberOne, int $number2) ❌
```

* Space between symbols and operators:
```php
'Hi! My name is' + $name + 'and Im' + $age ✅

'Hi! My name is'+$name+'and Im'+$age ❌
```

* When a method receives more than two input properties, these properties will be in a different lines:
```php
$persona = new Persona(  ✅
    $brazos,
    $piernas,
    $ojos
);

$persona = new Persona($brazos,$piernas,$ojos)); ❌
```

* Test names on third person:
```php
public function testItThrowsException() ✅

public function throwsException() ❌
```

---
