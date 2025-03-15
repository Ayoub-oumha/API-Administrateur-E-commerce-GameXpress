<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // La gestion des permissions se fait au niveau des routes
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($this->route('id'))
            ],
            'roles' => 'sometimes|array',
            'roles.*' => 'exists:roles,name'
        ];
        
        // Pour la création d'utilisateur, exiger un mot de passe
        if ($this->isMethod('POST')) {
            $rules['password'] = ['required', Password::defaults()];
        }
        
        // Pour la mise à jour, rendre le mot de passe optionnel
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['password'] = ['sometimes', Password::defaults()];
            $rules['name'] = 'sometimes|string|max:255';
            $rules['email'] = [
                'sometimes',
                'email',
                Rule::unique('users')->ignore($this->route('id'))
            ];
        }
        
        return $rules;
    }
}