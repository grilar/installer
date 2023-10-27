<?php

namespace Grilar\Installer\Http\Requests;

use Grilar\ACL\Http\Requests\CreateUserRequest;

class SaveAccountRequest extends CreateUserRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        $rules['username'] = 'required|alpha_dash|min:4|max:30';

        return $rules;
    }
}
