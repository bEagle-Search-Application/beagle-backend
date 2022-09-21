<?php declare(strict_types = 1);

namespace Beagle\Shared\Domain\ValueObjects;

use Illuminate\Support\Str;

class Token
{
    private const TYPE = "Bearer";

    protected function __construct(private ?string $value)
    {
    }

    public function value():?string
    {
        return $this->value;
    }

    public static function fromString(string $token):static
    {
        return new static($token);
    }

    public function equals(Token $token):bool
    {
        return $this->value === $token->value();
    }

    public function clear():self
    {
        return new self(\trim(\str_replace(self::TYPE, "", $this->value)));
    }

    public function clearValue():string
    {
        return $this->clear()->value;
    }

    public function type():string
    {
        return self::TYPE;
    }

    public static function generateRandom64String():self
    {
        return new self(Str::random(64));
    }
}
