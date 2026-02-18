<?php

namespace Webkul\Accounting\Filament\Clusters\Reporting\Pages\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AgedReceivableExport implements FromArray, WithColumnWidths, WithHeadings, WithStyles
{
    protected array $partners;

    protected Carbon $asOfDate;

    protected int $period;

    protected array $rowMetadata = [];

    protected string $basis;

    protected array $expandedPartners;

    public function __construct(array $partners, string $asOfDate, int $period, string $basis = 'due_date', array $expandedPartners = [])
    {
        $this->partners = $partners;
        $this->asOfDate = Carbon::parse($asOfDate);
        $this->period = $period;
        $this->basis = $basis;
        $this->expandedPartners = $expandedPartners;
    }

    public function headings(): array
    {
        return [
            ['Aged Receivable - As of '.$this->asOfDate->format('m/d/Y')],
            [],
            [
                'Partner',
                'Invoice Date',
                'At Date',
                '1-'.$this->period,
                ($this->period + 1).'-'.($this->period * 2),
                (($this->period * 2) + 1).'-'.($this->period * 3),
                (($this->period * 3) + 1).'-'.($this->period * 4),
                'Older',
                'Total',
            ],
        ];
    }

    public function array(): array
    {
        $rows = [];
        $rowIndex = 4;

        $totals = collect(['at_date', 'period_1', 'period_2', 'period_3', 'period_4', 'older', 'total'])
            ->mapWithKeys(fn ($key) => [$key => 0])
            ->all();

        foreach ($this->partners as $partnerId => $partner) {
            $rows[] = [
                $partner['partner_name'],
                '',
                ...collect(['at_date', 'period_1', 'period_2', 'period_3', 'period_4', 'older'])
                    ->map(fn ($key) => $partner[$key] != 0 ? $partner[$key] : '')
                    ->toArray(),
                $partner['total'],
            ];
            $this->rowMetadata[$rowIndex++] = 'partner_header';

            if (in_array($partnerId, $this->expandedPartners) && ! empty($partner['lines'])) {
                foreach ($partner['lines'] as $line) {
                    $rows[] = [
                        '        '.$line['move_name'],
                        Carbon::parse($line['invoice_date'])->format('m/d/Y'),
                        ...collect(['at_date', 'period_1', 'period_2', 'period_3', 'period_4', 'older'])
                            ->map(fn ($key) => $line[$key] != 0 ? $line[$key] : '')
                            ->toArray(),
                        '',
                    ];
                    $this->rowMetadata[$rowIndex++] = 'line';
                }
            }

            collect(['at_date', 'period_1', 'period_2', 'period_3', 'period_4', 'older', 'total'])
                ->each(fn ($key) => $totals[$key] += $partner[$key]);
        }

        $rows[] = [
            'Total Aged Receivable',
            '',
            ...collect(['at_date', 'period_1', 'period_2', 'period_3', 'period_4', 'older'])
                ->map(fn ($key) => $totals[$key] != 0 ? $totals[$key] : '')
                ->toArray(),
            $totals['total'],
        ];
        $this->rowMetadata[$rowIndex] = 'grand_total';

        return $rows;
    }

    public function columnWidths(): array
    {
        return collect(range('A', 'I'))
            ->mapWithKeys(fn ($col) => [$col => $col === 'A' ? 50 : 15])
            ->all();
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);
        $sheet->mergeCells('A1:I1');

        $sheet->getStyle('A3:I3')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            'borders'   => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_THICK,
                    'color'       => ['rgb' => '666666'],
                ],
            ],
        ]);

        $styleMap = [
            'partner_header' => [
                'font'      => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '666666']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                'borders'   => [
                    'bottom' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => '000000'],
                    ],
                ],
            ],
            'line' => [
                'font'      => ['size' => 12, 'color' => ['rgb' => '666666']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            ],
            'grand_total' => [
                'font'    => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '666666']],
                'borders' => [
                    'top' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => '000000'],
                    ],
                ],
            ],
        ];

        foreach ($this->rowMetadata as $rowNum => $type) {
            if (isset($styleMap[$type])) {
                $sheet->getStyle("A{$rowNum}:I{$rowNum}")->applyFromArray($styleMap[$type]);
            }
        }

        $lastRow = count($this->rowMetadata) + 4;
        $sheet->getStyle("C4:I{$lastRow}")->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle("C4:I{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        return [];
    }
}
