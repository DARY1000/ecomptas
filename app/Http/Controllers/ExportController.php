<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Services\ExportService;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function __construct(private ExportService $exportService) {}

    /**
     * Export CSV des écritures (compatible Sage, Ciel, etc.)
     */
    public function csv(Request $request)
    {
        $tenant = auth()->user()->tenant;

        // Vérifier que le plan autorise l'export
        $plan = Plan::where('slug', $tenant->plan)->first();
        abort_unless($plan?->export_xlsx, 403, 'L\'export n\'est pas disponible dans votre plan actuel.');

        $csv = $this->exportService->exportCsv(
            tenantId:   $tenant->id,
            dateDebut:  $request->date_debut,
            dateFin:    $request->date_fin,
            journal:    $request->journal
        );

        $filename = 'ecritures_' . now()->format('Y-m') . '.csv';

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Export FEC (Fichier des Écritures Comptables) — format standardisé pour
     * transmission fiscale, avec sous-comptes tiers et numérotation séquentielle.
     */
    public function fec(Request $request)
    {
        $tenant = auth()->user()->tenant;

        $plan = Plan::where('slug', $tenant->plan)->first();
        abort_unless($plan?->export_xlsx, 403, 'L\'export n\'est pas disponible dans votre plan actuel.');

        $fec = $this->exportService->exportFec(
            tenantId:   $tenant->id,
            dateDebut:  $request->date_debut,
            dateFin:    $request->date_fin,
            journal:    $request->journal
        );

        $identifiant = $tenant->ifu ?: $tenant->slug;
        $filename = "{$identifiant}_FEC_" . now()->format('Ymd') . '.txt';

        return response($fec, 200, [
            'Content-Type'        => 'text/plain; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Export XLSX des écritures via maatwebsite/excel.
     */
    public function xlsx(Request $request)
    {
        $tenant = auth()->user()->tenant;

        $plan = Plan::where('slug', $tenant->plan)->first();
        abort_unless($plan?->export_xlsx, 403, 'L\'export n\'est pas disponible dans votre plan actuel.');

        $data = $this->exportService->exportXlsx(
            tenantId:   $tenant->id,
            dateDebut:  $request->date_debut,
            dateFin:    $request->date_fin,
            journal:    $request->journal
        );

        $filename = 'ecritures_' . now()->format('Y-m') . '.xlsx';

        // Génération XLSX simple avec maatwebsite/excel via un export array
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\EcrituresExport($data),
            $filename
        );
    }
}
