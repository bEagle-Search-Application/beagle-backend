<?php declare(strict_types = 1);

namespace Beagle\Shared\Domain;

enum TokenType
{
    case ACCESS;
    case REFRESH;
}
