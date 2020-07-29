<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class _Example extends Model
{
    protected $table = '';
    const UPDATED_AT = 'updated_at';
    const CREATED_AT = 'created_at';

    //override save to auto filling some default attribute

    public function save(array $options = [])
    {
        // $this->alias = JString::stringURLSafe($this->title);
        return parent::save($options);
    }

    // public function setGalleryAttribute($value)
    // {
    //     $this->attributes['gallery'] = implode(',', $value);
    // }

    // public function getGalleryAttribute($value)
    // {
    //     return explode(',', $value);
    // }
}
