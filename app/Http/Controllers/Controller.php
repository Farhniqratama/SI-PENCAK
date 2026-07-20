<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

abstract class Controller
{
    protected $validator;

    public function __get($name)
    {
        if ($name === 'request') {
            return new class(request()) {
                public function __construct(private Request $request)
                {
                }

                public function getVar($key = null, $default = null)
                {
                    return $key === null ? $this->request->all() : $this->request->input($key, $default);
                }

                public function getJSON()
                {
                    return json_decode($this->request->getContent() ?: '{}');
                }

                public function isAJAX(): bool
                {
                    return $this->request->ajax() || $this->request->expectsJson();
                }
            };
        }

        if ($name === 'response') {
            return new class {
                public function setJSON(array $data)
                {
                    return response()->json($data);
                }

                public function setStatusCode(int $status)
                {
                    return response('', $status);
                }
            };
        }

        return null;
    }

    protected function validate(array $rules, array $messages = [], array $attributes = []): bool
    {
        $rules = collect($rules)->map(function ($rule) {
            return str_replace(
                ['min_length[', 'matches[', ']'],
                ['min:', 'same:', ''],
                $rule
            );
        })->all();

        $this->validator = Validator::make(request()->all(), $rules, $messages, $attributes);

        return ! $this->validator->fails();
    }
}
