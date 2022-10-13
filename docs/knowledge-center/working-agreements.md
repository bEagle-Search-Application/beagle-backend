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

* The repository methods that find for a primary key must be call **find** and return exception:
```php
public function find(UserId $userId):User ✅

public function findByUserId(UserId $userId):User ❌
```

* The repository methods that find for a non-primary key must be call **findBy** and return null:
```php
public function findByEmail(UserEmail $userEmail):?User ✅

public function find(UserId $userId):?User ❌
```

* The repository methods that search for any field or fields like a filter must be call **searchBy** and return a collection:
```php
public function searchByName(UserName $userName):UserCollection ✅
```

* The named-constructor that creates a new entity with default params must be call **create**:
```php
public static function create() ✅

public static function createWithDefaultValues() ❌
```

* The named-constructor that creates a new entity with non default params must be call **createWith...**:
```php
public static function createWithDefaultName(string $name) ✅

public static function createWithDefaultNameAndAge(string $name, int $age) ✅
```
