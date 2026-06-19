<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreStagiaireRequest extends FormRequest
{
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:255'],
            'civilite' => ['required', 'string', 'in:M.,Mlle.'],
            'cin' => ['required', 'string', 'max:30', 'unique:stagiaires,cin'],
            'sujet' => ['required', 'string', 'max:255'],
            'niveau' => ['required', 'integer', 'between:1,5'],
            'filiere' => ['required', 'string', 'max:255'],
            'etablissement' => ['required', 'string', 'max:255'],
            'etablissement_autre' => ['nullable', 'string', 'max:255'],
            'date_debut' => ['required', 'date'],
            'date_fin' => ['required', 'date', 'after_or_equal:date_debut'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Le champ :attribute est obligatoire.',
            'cin.unique' => 'Ce CIN est déjà enregistré.',
            'niveau.integer' => 'Le niveau doit être un nombre entre 1 et 5.',
            'niveau.between' => 'Le niveau doit être compris entre 1 et 5.',
            'date_fin.after_or_equal' => 'La date de fin doit être postérieure ou égale à la date de début.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('etablissement') === '__autre__') {
            $this->merge([
                'etablissement' => trim((string) $this->input('etablissement_autre')),
            ]);
        }
    }
}
