<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Export Excel des écritures comptables SYSCOHADA
 * Compatible avec les logiciels comptables (Sage, Ciel, etc.)
 */
class EcrituresExport implements FromArray, WithStyles, WithTitle
{
    public function __construct(private array $data) {}

    public function array(): array
    {
        return $this->data;
    }

    public function title(): string
    {
        return 'Écritures SYSCOHADA';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Ligne d'en-tête en gras avec fond bleu
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType'   => 'solid',
                    'startColor' => ['rgb' => '1E40AF'],
                ],
            ],
        ];
    }
}
