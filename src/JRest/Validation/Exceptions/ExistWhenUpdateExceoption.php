<?php

namespace JRest\Validation\Exceptions;

use \Respect\Validation\Exceptions\ValidationException;

final class ExistsWhenUpdateException extends ValidationException
{

    protected  $defaultTemplates = [
        self::MODE_DEFAULT  => [
            self::STANDARD => 'has already been taken',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => 'This does not exist',
        ],
    ];
}
