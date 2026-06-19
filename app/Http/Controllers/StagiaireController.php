<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStagiaireRequest;
use App\Http\Requests\UpdateStagiaireRequest;
use App\Models\Stagiaire;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;
use PhpOffice\PhpWord\TemplateProcessor;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class StagiaireController extends Controller
{
    public function index(): View
    {
        return view('stagiaires.index', [
            'stagiaires' => Stagiaire::latest()->paginate(10),
            'total' => Stagiaire::count(),
            'enCours' => Stagiaire::whereDate('date_debut', '<=', now())
                ->whereDate('date_fin', '>=', now())
                ->count(),
        ]);
    }

    public function create(): View
    {
        return view('stagiaires.create');
    }

    public function store(StoreStagiaireRequest $request): RedirectResponse
    {
        $data = $request->validated();
        unset($data['etablissement_autre']);
        $data['niveau'] = Stagiaire::formatNiveau($data['niveau']);
        $stagiaire = Stagiaire::create($data);

        return redirect()
            ->route('stagiaires.show', $stagiaire)
            ->with('success', 'Le stagiaire a été ajouté avec succès.');
    }

    public function show(Stagiaire $stagiaire): View
    {
        return view('stagiaires.show', compact('stagiaire'));
    }

    public function edit(Stagiaire $stagiaire): View
    {
        return view('stagiaires.edit', compact('stagiaire'));
    }

    public function update(UpdateStagiaireRequest $request, Stagiaire $stagiaire): RedirectResponse
    {
        $data = $request->validated();
        unset($data['etablissement_autre']);
        $data['niveau'] = Stagiaire::formatNiveau($data['niveau']);
        $stagiaire->update($data);

        return redirect()
            ->route('stagiaires.show', $stagiaire)
            ->with('success', 'Les informations ont été mises à jour.');
    }

    public function destroy(Stagiaire $stagiaire): RedirectResponse
    {
        $stagiaire->delete();

        return redirect()
            ->route('stagiaires.index')
            ->with('success', 'Le stagiaire a été supprimé.');
    }

    public function attestation(Stagiaire $stagiaire): BinaryFileResponse
    {
        $templatePath = storage_path('app/templates/attestation.docx');

        abort_unless(File::exists($templatePath), 500, 'Le modèle d’attestation est introuvable.');

        $tempDirectory = storage_path('app/temp');
        File::ensureDirectoryExists($tempDirectory);

        $fileName = 'attestation-'.Str::slug($stagiaire->nom).'-'.$stagiaire->cin.'.docx';
        $outputPath = $tempDirectory.'/'.Str::uuid().'.docx';

        $processor = new TemplateProcessor($templatePath);
        $processor->setValue('attestation_text', $this->attestationText($stagiaire));
        $processor->saveAs($outputPath);

        return response()
            ->download($outputPath, $fileName)
            ->deleteFileAfterSend(true);
    }

    private function attestationText(Stagiaire $stagiaire): string
    {
        $debut = $stagiaire->date_debut->locale('fr')->isoFormat('D MMMM YYYY');
        $fin = $stagiaire->date_fin->locale('fr')->isoFormat('D MMMM YYYY');

        return sprintf(
            'Le Directeur de l’Agence Urbaine d’Oujda atteste par la présente que %s, titulaire de la C.I.N n° %s, stagiaire en %s, filière « %s », à %s, a effectué un stage pratique au sein de cette Agence du %s au %s, sur le sujet « %s ».',
            $stagiaire->nom,
            $stagiaire->cin,
            $stagiaire->niveau,
            $stagiaire->filiere,
            $stagiaire->etablissement,
            $debut,
            $fin,
            $stagiaire->sujet,
        );
    }
}
