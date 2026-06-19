<?php

namespace Tests\Feature;

use App\Models\Stagiaire;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use ZipArchive;

class StagiaireCrudTest extends TestCase
{
    use RefreshDatabase;

    private array $payload = [
        'civilite' => 'Mlle.',
        'nom' => 'Aya Test',
        'cin' => 'F123456',
        'sujet' => 'Développement d’une application web',
        'niveau' => 4,
        'filiere' => 'Architecture',
        'etablissement' => 'ENA Oujda',
        'date_debut' => '2026-07-01',
        'date_fin' => '2026-07-31',
    ];

    public function test_dashboard_is_accessible_without_authentication(): void
    {
        $this->get('/stagiaires')
            ->assertOk()
            ->assertSee('Tableau de bord');
    }

    public function test_dashboard_rows_link_to_the_complete_stagiaire_details(): void
    {
        $stagiaire = Stagiaire::create([
            ...$this->payload,
            'niveau' => '4ème année',
        ]);

        $this->get('/stagiaires')
            ->assertOk()
            ->assertSee('class="stagiaire-row"', false)
            ->assertSee('data-href="'.route('stagiaires.show', $stagiaire).'"', false)
            ->assertSee('Cliquer pour voir la fiche complète');

        $this->get(route('stagiaires.show', $stagiaire))
            ->assertOk()
            ->assertSee($stagiaire->nom)
            ->assertSee($stagiaire->sujet)
            ->assertSee($stagiaire->filiere)
            ->assertSee($stagiaire->etablissement);
    }

    public function test_a_stagiaire_can_be_created_updated_and_deleted(): void
    {
        $this->post('/stagiaires', $this->payload)
            ->assertRedirect();

        $stagiaire = Stagiaire::firstOrFail();
        $this->assertSame('Aya Test', $stagiaire->nom);
        $this->assertSame('4ème année', $stagiaire->niveau);
        $this->assertSame('Architecture', $stagiaire->filiere);
        $this->assertSame('ENA Oujda', $stagiaire->etablissement);

        $this->put("/stagiaires/{$stagiaire->id}", [
            ...$this->payload,
            'sujet' => 'Sujet modifié',
        ])->assertRedirect();

        $this->assertDatabaseHas('stagiaires', ['sujet' => 'Sujet modifié']);

        $this->delete("/stagiaires/{$stagiaire->id}")
            ->assertRedirect('/stagiaires');

        $this->assertDatabaseCount('stagiaires', 0);
    }

    public function test_first_year_is_formatted_and_a_new_establishment_can_be_added(): void
    {
        $this->post('/stagiaires', [
            ...$this->payload,
            'cin' => 'F111111',
            'niveau' => 1,
        ])->assertRedirect();

        $this->assertDatabaseHas('stagiaires', [
            'cin' => 'F111111',
            'niveau' => '1ère année',
        ]);

        $this->post('/stagiaires', [
            ...$this->payload,
            'cin' => 'F222222',
            'etablissement' => '__autre__',
            'etablissement_autre' => 'Université Mohammed Premier',
        ])->assertRedirect();

        $this->assertDatabaseHas('stagiaires', [
            'cin' => 'F222222',
            'etablissement' => 'Université Mohammed Premier',
        ]);
    }

    public function test_attestation_is_generated_from_the_word_template(): void
    {
        $stagiaire = Stagiaire::create([
            ...$this->payload,
            'niveau' => '4ème année',
        ]);

        $response = $this->get("/stagiaires/{$stagiaire->id}/attestation")
            ->assertOk()
            ->assertDownload('attestation-aya-test-F123456.docx');

        $path = tempnam(sys_get_temp_dir(), 'attestation-').'.docx';
        file_put_contents($path, $response->streamedContent());

        $archive = new ZipArchive;
        $this->assertTrue($archive->open($path));

        $documentXml = $archive->getFromName('word/document.xml');
        $relationships = $archive->getFromName('word/_rels/document.xml.rels');

        $this->assertStringContainsString('w:headerReference', $documentXml);
        $this->assertStringNotContainsString('w:type="page"', $documentXml);
        $this->assertStringContainsString('header2.xml', $relationships);
        $this->assertNotFalse($archive->getFromName('word/header2.xml'));
        $this->assertNotFalse($archive->getFromName('word/media/image1.jpeg'));
        $this->assertStringContainsString('étudiante', $documentXml);
        $this->assertStringContainsString('4ème année d’Architecture', $documentXml);
        $this->assertStringContainsString('du 1 au 31 juillet 2026', $documentXml);
        $this->assertStringContainsString('intéressée', $documentXml);
        $this->assertStringNotContainsString('filière', $documentXml);
        $this->assertStringNotContainsString('sur le sujet', $documentXml);

        $archive->close();
        unlink($path);
    }
}
