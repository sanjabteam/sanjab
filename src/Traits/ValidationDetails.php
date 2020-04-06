<?php

namespace Sanjab\Traits;

use Illuminate\Http\Request;
use Illuminate\Validation\Validator;
use Illuminate\Database\Eloquent\Model;

trait ValidationDetails
{
    /**
     * Validation rules.
     *
     * @param Request $request
     * @param string $type  create|edit
     * @param Model|null $item
     * @return array
     */
    public function validationRules(Request $request, string $type, Model $item = null)
    {
        return [];
    }

    /**
     * Validation attributes.
     *
     * @param Request $request
     * @param string $type  create|edit
     * @param Model|null $item
     * @return array
     */
    public function validationAttributes(Request $request, string $type, Model $item = null)
    {
        return [];
    }

    /**
     * Validation messages.
     *
     * @param Request $request
     * @param string $type  create|edit
     * @param Model|null $item
     * @return array
     */
    public function validationMessages(Request $request, string $type, Model $item = null)
    {
        return [];
    }

    /**
     * Validation after callback.
     *
     * @param \Illuminate\Validation\Validator  $validator
     * @param \Illuminate\Http\Request $request
     * @param string $type  create|edit
     * @param Model|null  $item
     * @return void
     */
    public function validationAfter(Validator $validator, Request $request, string $type, Model $item = null)
    {
    }
}
