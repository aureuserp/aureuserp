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

class TrialBalanceExport implements FromArray, WithColumnWidths, WithHeadings, WithStyles
{
    protected $accounts;

    protected Carbon $dateFrom;

    protected Carbon $dateTo;

    protected array $totals;

    protected array $rowMetadata = [];

    public function __construct($accounts, string $dateFrom, string $dateTo, array $totals)
    {
        $this->accounts = is_array($accounts) ? $accounts : $accounts->toArray();
        $this->dateFrom = Carbon::parse($dateFrom);
        $this->dateTo = Carbon::parse($dateTo);
        $this->totals = $totals;
    }

    public function headings(): array
    {
        return [
            ['Trial Balance - From '.$this->dateFrom->format('M d, Y').' to '.$this->dateTo->format('M d, Y')],
            [],
            [
                '',
                'Initial Balance',
                '',
                $this->dateFrom->format('d M Y').' - '.$this->dateTo->format('d M Y'),
                '',
                'End Balance',
                '',
            ],
            [
                'Account',
                'Debit',
                'Credit',
                'Debit',
                'Credit',
                'Debit',
                'Credit',
            ],
        ];
    }

    public function array(): array
    {
        $rows = [];
        $rowIndex = 5;

        collect($this->accounts)->each(function ($account) use (&$rows, &$rowIndex) {
            $rows[] = [
                '        '.($account['code'] ?? '').' '.($account['name'] ?? ''),
                $account['initial_debit'] > 0 ? $account['initial_debit'] : '0.00',
                $account['initial_credit'] > 0 ? $account['initial_credit'] : '0.00',
                $account['period_debit'] > 0 ? $account['period_debit'] : '0.00',
                $account['period_credit'] > 0 ? $account['period_credit'] : '0.00',
                $account['end_debit'] > 0 ? $account['end_debit'] : '0.00',
                $account['end_credit'] > 0 ? $account['end_credit'] : '0.00',
            ];
            $this->rowMetadata[$rowIndex++] = 'account_line';
        });

        $rows[] = array_fill(0, 7, '');
        $rowIndex++;

        $rows[] = [
            'Total',
            $this->totals['initial_debit'],
            $this->totals['initial_credit'],
            $this->totals['period_debit'],
            $this->totals['period_credit'],
            $this->totals['end_debit'],
            $this->totals['end_credit'],
        ];
        $this->rowMetadata[$rowIndex] = 'grand_total';

        return $rows;
    }

    public function columnWidths(): array
    {
        return collect(range('A', 'G'))
            ->mapWithKeys(fn ($col) => [
                $col => match ($col) {
                    'A'     => 35,
                    default => 15,
                },
            ])
            ->all();
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);
        $sheet->mergeCells('A1:G1');

        $sheet->getStyle('B3:C3')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders'   => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'CCCCCC'],
                ],
            ],
        ]);
        $sheet->mergeCells('B3:C3');

        $sheet->getStyle('D3:E3')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders'   => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'CCCCCC'],
                ],
            ],
        ]);
        $sheet->mergeCells('D3:E3');

        $sheet->getStyle('F3:G3')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders'   => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'CCCCCC'],
                ],
            ],
        ]);
        $sheet->mergeCells('F3:G3');

        $sheet->getStyle('A4:G4')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            'borders'   => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_THICK,
                    'color'       => ['rgb' => '666666'],
                ],
            ],
        ]);

        $styleMap = [
            'account_line' => [
                'font'      => ['size' => 12, 'color' => ['rgb' => '666666']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            ],
            'grand_total' => [
                'font'    => ['bold' => true, 'size' => 11],
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
                $sheet->getStyle("A{$rowNum}:G{$rowNum}")->applyFromArray($styleMap[$type]);
            }
        }

        $lastRow = count($this->rowMetadata) + 5;
        $sheet->getStyle("B5:G{$lastRow}")->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle("B5:G{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        return [];
    }
}
