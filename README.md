# Gestion des stagiaires

Application Laravel 12 minimale, sans authentification, pour :

- ajouter, consulter, modifier et supprimer des stagiaires ;
- gérer le nom, le CIN, le sujet, le niveau, l’établissement et la période ;
- générer une attestation Word à partir du modèle fourni.

## Installation

Prérequis : PHP 8.2+, Composer et MySQL.

```powershell
cd C:\Users\4-SIG\Documents\CODES\Stagiaire\app
composer install
Copy-Item .env.example .env
php artisan key:generate
```

Créer la base `Stage` :

```powershell
C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS Stage CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

Si MySQL possède un mot de passe, le renseigner dans `.env`, puis :

```powershell
php artisan migrate
php artisan serve
```

Ouvrir `http://127.0.0.1:8000`.

## Tests

```powershell
php artisan test
```

Le modèle Word utilisé se trouve dans `storage/app/templates/attestation.docx`.
