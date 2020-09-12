<?php

namespace JRest\Validation\Exceptions;

use \Respect\Validation\Exceptions\ValidationException;

final class EmailAvailableException extends ValidationException
{

    protected $defaultTemplates = [
        self::MODE_DEFAULT  => [
            self::STANDARD => 'Email already exists.',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => 'Email does not exist',
        ],
    ];
}
