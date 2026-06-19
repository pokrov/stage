<?php

use App\Http\Controllers\StagiaireController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/stagiaires');
Route::get('/stagiaires/{stagiaire}/attestation', [StagiaireController::class, 'attestation'])
    ->name('stagiaires.attestation');
Route::resource('stagiaires', StagiaireController::class);
