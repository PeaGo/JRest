<?php

namespace JRest\Models;

use Illuminate\Database\Eloquent\Model;

class JModel extends Model
{
    protected $table = '';
    protected $guarded = [];
    const UPDATED_AT = 'updated_at';
    const CREATED_AT = 'created_at';

    //override save to auto filling some default attribute

    public function save(array $options = [])
    {
        return parent::save($options);
    }
}
