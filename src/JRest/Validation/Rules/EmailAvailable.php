<?php

namespace JRest\Validation\Rules;

use JRest\Models\User;
use Respect\Validation\Rules\AbstractRule;

class EmailAvailable extends AbstractRule
{

    public function validate($input): bool
    {
        return !User::where('email', $input)->exists();
    }
}
