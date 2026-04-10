<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation de l'upload de factures PDF.
 * Limite : 50 Mo par fichier, max 10 fichiers simultanés.
 * Types acceptés : PDF uniquement (contrôle MIME + extension).
 */
class UploadFactureRequest extends FormRequest
{
    public function authorize(): bool
    {
        // L'utilisateur doit être connecté et ne pas être auditeur
        return auth()->check() && !auth()->user()->estAuditeur();
    }

    public function rules(): array
    {
        return [
            'pdfs'   => [
                'required',
                'array',
                'min:1',
                'max:10', // Max 10 fichiers par upload
            ],
            'pdfs.*' => [
                'required',
                'file',
                'mimes:pdf',        // Extension PDF
                'mimetypes:application/pdf', // MIME type réel
                'max:51200',        // 50 Mo en kilo-octets
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'pdfs.required'       => 'Veuillez sélectionner au moins un fichier PDF.',
            'pdfs.array'          => 'Format de données invalide.',
            'pdfs.max'            => 'Vous ne pouvez pas uploader plus de 10 fichiers à la fois.',
            'pdfs.*.required'     => 'Un fichier est requis.',
            'pdfs.*.file'         => 'Le fichier uploadé est invalide.',
            'pdfs.*.mimes'        => 'Seuls les fichiers PDF sont acceptés.',
            'pdfs.*.mimetypes'    => 'Le type MIME doit être application/pdf.',
            'pdfs.*.max'          => 'Chaque fichier ne doit pas dépasser 50 Mo.',
        ];
    }
}
