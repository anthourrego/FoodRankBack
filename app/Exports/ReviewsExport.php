<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ReviewsExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting, WithStyles
{
    protected $reviews;

    public function __construct($reviews)
    {
        $this->reviews = $reviews;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->reviews;
    }

    /**
     * Encabezados del Excel
     */
    public function headings(): array
    {
        return [
            'ID',
            'Restaurante',
            'Producto',
            'Calificación',
            'Comentario',
            'IP',
            'Fecha',
        ];
    }

    /**
     * Mapear los datos para cada fila
     */
    public function map($review): array
    {
        // Si $review es un array, lo convertimos a objeto para acceder con notación de objeto
        $review = is_array($review) ? (object) $review : $review;
        
        return [
            $review->id ?? '',
            $review->restaurant_name ?? '',
            $review->product_name ?? '',
            $review->rating ?? '',
            $review->comment ?? '',
            $review->ip ?? '',
            $review->created_at ? \Carbon\Carbon::parse($review->created_at)->format('d/m/Y H:i:s') : '',
        ];
    }

    /**
     * Formato de columnas
     */
    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_NUMBER,
            'D' => NumberFormat::FORMAT_NUMBER,
            'G' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }

    /**
     * Estilos para el Excel
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Estilo para la primera fila (encabezados)
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => 'E2E8F0',
                    ],
                ],
            ],
        ];
    }
}

