<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\EcritureController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\AbonnementController;
use App\Http\Controllers\Cabinet\DashboardController;
use App\Http\Controllers\Cabinet\UserController;
use App\Http\Controllers\Cabinet\SettingsController;
use App\Http\Controllers\Admin\TenantController;
use App\Http\Controllers\Admin\PlanController;
use Illuminate\Support\Facades\Route;

// ─────────────────────────────────────────────────────────────────────
// LANDING PAGE PUBLIQUE
// ─────────────────────────────────────────────────────────────────────
Route::get('/', [LandingController::class, 'index'])->name('landing');

// ─────────────────────────────────────────────────────────────────────
// AUTHENTIFICATION
// ─────────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/connexion', [LoginController::class, 'showLoginForm'])->name('login');
    Route::get('/inscription', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/inscription', [RegisterController::class, 'register'])->name('register.post');
    Route::post('/connexion', [LoginController::class, 'login'])->name('login.post');
    Route::get('/mot-de-passe-oublie', [LoginController::class, 'showForgotForm'])->name('password.request');
    Route::post('/mot-de-passe-oublie', [LoginController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reinitialiser/{token}', [LoginController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reinitialiser', [LoginController::class, 'reset'])->name('password.update');
});

Route::post('/deconnexion', [LoginController::class, 'logout'])
     ->name('logout')
     ->middleware('auth');

// ─────────────────────────────────────────────────────────────────────
// ROUTE SPÉCIALE — Servir le PDF à n8n.cloud via token signé (30 min)
// RÈGLE 3 : pas d'URL publique directe pour les PDFs
// Cette route est accessible SANS auth (n8n n'est pas authentifié comme user)
// mais AVEC validation du token chiffré
// ─────────────────────────────────────────────────────────────────────
Route::get('/factures/{facture}/pdf', [FactureController::class, 'servirPdf'])
     ->name('factures.pdf');

// ─────────────────────────────────────────────────────────────────────
// ZONE CABINET AUTHENTIFIÉE
// ─────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'check.subscription'])->group(function () {

    // Dashboard
    Route::get('/tableau-de-bord', [DashboardController::class, 'index'])->name('dashboard');

    // ── Factures ──────────────────────────────────────────────────
    Route::prefix('factures')->name('factures.')->group(function () {
        Route::get('/', [FactureController::class, 'index'])->name('index');
        Route::get('/upload', [FactureController::class, 'upload'])->name('upload');
        Route::post('/', [FactureController::class, 'store'])
             ->middleware('check.quota')
             ->name('store');
        Route::get('/{facture}/statut', [FactureController::class, 'statut'])->name('statut');
        Route::get('/{facture}', [FactureController::class, 'show'])->name('show');
        Route::get('/{facture}/revue', [FactureController::class, 'revue'])->name('revue');
        Route::post('/{facture}/valider', [FactureController::class, 'valider'])->name('valider');
        Route::post('/{facture}/rejeter', [FactureController::class, 'rejeter'])->name('rejeter');
    });

    // ── Journal des écritures ─────────────────────────────────────
    Route::get('/ecritures', [EcritureController::class, 'index'])->name('ecritures.index');

    // ── Exports (plan Starter+) ───────────────────────────────────
    Route::get('/export/xlsx', [ExportController::class, 'xlsx'])->name('export.xlsx');
    Route::get('/export/csv', [ExportController::class, 'csv'])->name('export.csv');

    // ── Abonnement ────────────────────────────────────────────────
    Route::prefix('abonnement')->name('abonnement.')->group(function () {
        Route::get('/', [AbonnementController::class, 'index'])->name('index');
        Route::post('/payer', [AbonnementController::class, 'initierPaiement'])->name('payer');
        Route::get('/succes', [AbonnementController::class, 'succes'])->name('succes');
    });

    // ── Administration du cabinet (admin uniquement) ──────────────
    Route::middleware('role:admin,super_admin')->group(function () {
        Route::get('/parametres', [SettingsController::class, 'index'])->name('settings.index');
        Route::put('/parametres', [SettingsController::class, 'update'])->name('settings.update');

        Route::get('/utilisateurs', [UserController::class, 'index'])->name('users.index');
        Route::post('/utilisateurs', [UserController::class, 'store'])->name('users.store');
        Route::put('/utilisateurs/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/utilisateurs/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
});

// ─────────────────────────────────────────────────────────────────────
// ZONE SUPER ADMIN (iCODE)
// ─────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:super_admin'])
     ->prefix('admin')
     ->name('admin.')
     ->group(function () {
         Route::get('/', [TenantController::class, 'dashboard'])->name('dashboard');
         Route::resource('tenants', TenantController::class);
         Route::post('/tenants/{tenant}/suspendre', [TenantController::class, 'suspendre'])->name('tenants.suspendre');
         Route::post('/tenants/{tenant}/activer', [TenantController::class, 'activer'])->name('tenants.activer');
         // Gestion des plans
         Route::resource('plans', PlanController::class)->except(['show']);
     });
