<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Validator;

class Model extends BaseModel
{
    protected $rules = [];

    public function validate($rulesToIgnore = [])
    {
        $validator = Validator::make($this->attributes, array_except($this->rules, $rulesToIgnore));

        if ($validator->fails()) {
            response_with_errors(400, $validator->errors())->throwResponse();
        }
    }

    public function getAttributes(array $attributes = [])
    {
        if (empty($attributes)) {
            return $this->attributes;
        }

        return array_only($this->attributes, $attributes);
    }
}
