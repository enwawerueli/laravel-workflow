<?php

namespace EmzD\Workflow\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\SyntaxError;

class TransitionRequest extends FormRequest {
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
            'workflow_id' => 'required|numeric|integer',
            'from' => 'nullable|array',
            // 'from.*' => 'numeric|integer',
            'to' => 'nullable|array',
            // 'to.*' => 'numeric|integer',
            'guard' => [
                'nullable',
                'string',
                function (string $attribute, mixed $value, \Closure $fail) {
                    try {
                        (new ExpressionLanguage())->lint($value, null);
                    } catch (SyntaxError $th) {
                        $fail($th->getMessage());
                    }
                }],
            'metadata' => 'nullable|json'
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