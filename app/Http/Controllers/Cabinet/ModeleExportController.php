<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Jobs\AnalyserModeleExport;
use App\Services\ModeleExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ModeleExportController extends Controller
{
    public function __construct(private ModeleExportService $service) {}

    public function upload(Request $request)
    {
        $tenant = auth()->user()->tenant;

        abort_unless($tenant->planActuel()?->modeles_export, 403, "Cette fonctionnalité n'est pas disponible dans votre plan actuel.");

        $request->validate([
            'modele' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            $modele = $this->service->importer($tenant, $request->file('modele'));
        } catch (\Throwable $e) {
            Log::error('ModeleExportController: import échoué', ['tenant_id' => $tenant->id, 'error' => $e->getMessage()]);
            return back()->withErrors(['modele' => "Impossible d'importer ce fichier : " . $e->getMessage()]);
        }

        AnalyserModeleExport::dispatch($modele);

        return back()->with('success', "Modèle importé — l'analyse est en cours et sera visible dans quelques instants.");
    }

    /**
     * Statut d'analyse du modèle, interrogé en polling par la page Paramètres
     * pour remplacer le spinner par le badge "Analysé" sans que l'utilisateur
     * ait à rafraîchir la page lui-même.
     */
    public function statut(Request $request)
    {
        $modele = auth()->user()->tenant->modeleExport;

        if (!$modele) {
            return response()->json(['existe' => false]);
        }

        return response()->json([
            'existe'      => true,
            'analyse_le'  => $modele->analyse_le?->toIso8601String(),
            'colonnes'    => count($modele->structure['colonnes'] ?? []),
            'notes_style' => $modele->notes_style,
        ]);
    }

    public function destroy(Request $request)
    {
        $tenant = auth()->user()->tenant;
        $modele = $tenant->modeleExport;

        abort_unless($modele, 404);

        $this->service->supprimer($modele);

        return back()->with('success', 'Modèle supprimé.');
    }

    public function exporter(Request $request)
    {
        $tenant = auth()->user()->tenant;
        $modele = $tenant->modeleExport;

        abort_unless($tenant->planActuel()?->modeles_export, 403, "Cette fonctionnalité n'est pas disponible dans votre plan actuel.");
        abort_unless($modele, 404, 'Aucun modèle importé.');

        $contenu = $this->service->genererExport(
            $tenant,
            $modele,
            $request->date_debut,
            $request->date_fin,
            $request->journal
        );

        $filename = pathinfo($modele->nom_original, PATHINFO_FILENAME) . '_' . now()->format('Y-m') . '.' . $modele->extension;
        $mime = $modele->extension === 'csv' ? 'text/csv; charset=UTF-8' : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

        return response($contenu, 200, [
            'Content-Type'        => $mime,
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
