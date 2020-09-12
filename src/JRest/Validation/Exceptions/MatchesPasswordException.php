<?php

namespace JRest\Validation\Exceptions;

use \Respect\Validation\Exceptions\ValidationException;

final class MatchesPasswordException extends ValidationException
{

    protected $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => 'is invalid',
        ],
    ];
}
