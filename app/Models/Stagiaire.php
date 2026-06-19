<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stagiaire extends Model
{
    protected $fillable = [
        'civilite',
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

    public function attestationText(): string
    {
        $etudiant = $this->civilite === 'M.' ? 'étudiant' : 'étudiante';

        return sprintf(
            'Le Directeur de l’Agence Urbaine d’Oujda atteste par la présente que %s %s titulaire de la C.I.N n° %s %s en %s %s, a effectué un stage pratique au sein de cette Agence %s.',
            $this->civilite,
            self::formatNomAttestation($this->nom),
            $this->cin,
            $etudiant,
            self::formatNiveauFiliere($this->niveau, $this->filiere),
            self::formatEtablissement($this->etablissement),
            self::formatPeriodeStage($this->date_debut, $this->date_fin),
        );
    }

    public function attestationClosingText(): string
    {
        $interesse = $this->civilite === 'M.' ? 'intéressé' : 'intéressée';

        return "La présente attestation est délivrée à l’{$interesse} sur sa demande pour servir et valoir ce que de droit.";
    }

    public static function formatNomAttestation(string $nom): string
    {
        $parts = preg_split('/\s+/', trim($nom)) ?: [];

        if ($parts === []) {
            return '';
        }

        if (count($parts) === 1) {
            return mb_strtoupper($parts[0]);
        }

        $lastName = array_pop($parts);

        return implode(' ', $parts).' '.mb_strtoupper($lastName);
    }

    public static function formatNiveauFiliere(string $niveau, string $filiere): string
    {
        $filiere = trim($filiere);
        $liaison = preg_match('/^[aeiouhéèêëàâùûüîïôö]/iu', $filiere) === 1
            ? "d’{$filiere}"
            : "de {$filiere}";

        return "{$niveau} {$liaison}";
    }

    public static function formatEtablissement(string $etablissement): string
    {
        $etablissement = trim($etablissement);

        if (str_starts_with(mb_strtolower($etablissement), 'université')) {
            return "à l’{$etablissement}";
        }

        return "à {$etablissement}";
    }

    public static function formatPeriodeStage(\DateTimeInterface $debut, \DateTimeInterface $fin): string
    {
        $debut = \Illuminate\Support\Carbon::parse($debut)->locale('fr');
        $fin = \Illuminate\Support\Carbon::parse($fin)->locale('fr');

        if ($debut->month === $fin->month && $debut->year === $fin->year) {
            return sprintf(
                'du %s au %s %s',
                $debut->isoFormat('D'),
                $fin->isoFormat('D'),
                $fin->isoFormat('MMMM YYYY'),
            );
        }

        return sprintf(
            'du %s au %s',
            $debut->isoFormat('D MMMM YYYY'),
            $fin->isoFormat('D MMMM YYYY'),
        );
    }
}
