<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stagiaire extends Model
{
    protected $fillable = [
        'nom',
        'cin',
        'sujet',
        'niveau',
        'filiere',
        'etablissement',
        'date_debut',
        'date_fin',
    ];

    protected function casts(): array
    {
        return [
            'date_debut' => 'date',
            'date_fin' => 'date',
        ];
    }

    public static function formatNiveau(int|string $niveau): string
    {
        $annee = (int) $niveau;

        return $annee === 1 ? '1ère année' : $annee.'ème année';
    }
}
