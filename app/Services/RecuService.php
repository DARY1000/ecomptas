<?php

namespace App\Services;

use App\Models\Abonnement;
use Barryvdh\DomPDF\Facade\Pdf;

class RecuService
{
    /**
     * Génère le PDF du reçu pour un abonnement payé et retourne son contenu binaire.
     */
    public function generer(Abonnement $abonnement): string
    {
        $abonnement->loadMissing('tenant', 'plan');

        return Pdf::loadView('pdf.recu', [
            'abonnement' => $abonnement,
            'tenant'     => $abonnement->tenant,
            'logoPath'   => config('app.logo_path'),
        ])->output();
    }
}
