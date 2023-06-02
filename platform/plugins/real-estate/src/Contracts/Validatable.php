<?php

namespace Botble\RealEstate\Contracts;

use Illuminate\Foundation\Http\FormRequest;

trait Validatable
{
    protected FormRequest|array $validator;

    public function rules(): array
    {
        if (is_array($this->validator)) {
            return $this->validator;
        }

        return method_exists($this->validator, 'rules') ? $this->validator->rules() : [];
    }

    public function validator(string|array $validator): static
    {
        if (is_string($validator)) {
            $this->validator = (new $validator());
        } elseif (is_array($validator)) {
            $this->validator = $validator;
        }

        return $this;
    }
}
