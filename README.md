<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>


## Beagle Backend

### Working agreements

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

### Documentation

[Principios SOLID: Qué son, cuáles, y qué beneficios aporta usarlos](https://devexperto.com/principios-solid/)

[Los principios SOLID, ¡explicados!](https://www.youtube.com/watch?v=2X50sKeBAcQ)

[SOLID, principios del diseño orientado a objetos en PHP](https://diego.com.es/solid-principios-del-diseno-orientado-a-objetos-en-php)

[¿Qué es la Arquitectura Hexagonal? | Puertos y Adaptadores](https://www.youtube.com/watch?v=VLhdDYaW-uI)

[Charla Arquitectura Hexagonal](https://www.youtube.com/watch?v=SWw04WfvTro)

[Introducción Arquitectura Hexagonal - DDD](https://www.youtube.com/watch?v=GZ9ic9QSO5U)

[Introducción a Domain Driven Design (DDD)](https://www.youtube.com/watch?v=g9hTQQHPj2I)

[Domain Driven Design: principios, beneficios y elementos — Primera Parte](https://medium.com/@jonathanloscalzo/domain-driven-design-principios-beneficios-y-elementos-primera-parte-aad90f30aa35)

[Pruebas unitarias y Test-Driven Development | Ejemplo desde cero](https://www.youtube.com/watch?v=YuRdaR6wwWU)

[Pruebas de Integración | MOCKS vs STUBS | Dobles de Prueba 🧩](https://www.youtube.com/watch?v=pxOwxsBFYYo)

[Mock, Stub, Fake, Dummy, Spy](https://our-academy.org/posts/el-pequeno-mocker)

