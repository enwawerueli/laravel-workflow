<?php

namespace EmzD\Workflow\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorkflowRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:workflow,state_machine',
            'supports' => [
                'required',
                'string',
                function (string $attribute, mixed $value, \Closure $fail) {
                    if (!class_exists($value)) {
                        $fail("Class {$value} does not exist");
                    }
                }],
            'marking_property' => [
                'required',
                'string',
                function (string $attribute, mixed $value, \Closure $fail) {
                    $class = $this->request->get('supports');
                    if (!class_exists($class)) {
                        return;
                    }
                    if (!property_exists($class, $value)) {
                        $fail("Class {$class} has no property {$value}");
                    }
                    $err = "Class {$class} has no method %s";
                    if (!method_exists($class, $method = 'get' . ucfirst($value))) {
                        $fail(sprintf($err, $method));
                    }
                    if (!method_exists($class, $method = 'set' . ucfirst($value))) {
                        $fail(sprintf($err, $method));
                    }
                }],
            'metadata' => 'nullable|json',
            'active' => 'boolean'
        ];
        if ($this->isMethod('PATCH')) {
            return array_intersect_key($rules, $this->all());
        }
        return $rules;
    }

    public function validated($key = null, $default = null): mixed
    {
        if (array_key_exists($k = 'metadata', $validated = parent::validated($key, $default))) {
            $validated[$k] = json_decode($validated[$k]);
        }
        return $validated;
    }
}