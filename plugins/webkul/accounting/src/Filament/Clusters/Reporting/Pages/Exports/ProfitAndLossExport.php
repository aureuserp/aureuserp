<?php

namespace Webkul\Accounting\Filament\Clusters\Reporting\Pages\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProfitAndLossExport implements FromCollection, WithColumnWidths, WithStyles
{
    protected array $profitLossData;

    protected Carbon $dateFrom;

    protected Carbon $dateTo;

    protected array $rowMetadata = [];

    public function __construct(array $profitLossData, Carbon $dateFrom, Carbon $dateTo)
    {
        $this->profitLossData = $profitLossData;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    public function collection()
    {
        $rows = collect();

        $rows->push([null, 'From '.$this->dateFrom->format('M d, Y').' to '.$this->dateTo->format('M d, Y')]);
        $rows->push([null, null]);
        $rows->push([null, 'Balance']);
        $rows->push([null, null]);

        $rowIndex = 5;

        foreach ($this->profitLossData['sections'] as $section) {
            $rows->push([
                $section['title'],
                '',
            ]);
            $this->rowMetadata[$rowIndex] = 'section_header';
            $rowIndex++;

            if (! empty($section['accounts'])) {
                foreach ($section['accounts'] as $account) {
                    $accountName = ($account['code'] ? $account['code'].' - ' : '').$account['name'];
                    $rows->push([
                        '            '.$accountName,
                        number_format($account['balance'], 2),
                    ]);
                    $this->rowMetadata[$rowIndex] = 'account_line';
                    $rowIndex++;
                }

                $rows->push([
                    $section['total_label'],
                    number_format($section['total'], 2),
                ]);
                $this->rowMetadata[$rowIndex] = 'section_total';
                $rowIndex++;
            } else {
                $rows->push([
                    '            '.$section['empty_message'],
                    '',
                ]);
                $this->rowMetadata[$rowIndex] = 'empty_message';
                $rowIndex++;
            }

            $rows->push(['', '']);
            $rowIndex++;
        }

        $rows->push([
            $this->profitLossData['is_profit'] ? 'Net Profit' : 'Net Loss',
            number_format(abs($this->profitLossData['net_income']), 2),
        ]);
        $this->rowMetadata[$rowIndex] = 'net_income';

        return $rows;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 50,
            'B' => 20,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = count($this->rowMetadata) + 5;

        $sheet->getStyle('B1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);

        $sheet->getStyle('B3')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            'borders'   => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_THICK,
                    'color'       => ['rgb' => '000000'],
                ],
            ],
        ]);

        $styleMap = [
            'section_header' => [
                'font'      => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '666666']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                'borders'   => [
                    'bottom' => [
                        'borderStyle' => Border::BORDER_DOUBLE,
                        'color'       => ['rgb' => '000000'],
                    ],
                ],
            ],
            'account_line' => [
                'font'      => ['size' => 12, 'color' => ['rgb' => '666666']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            ],
            'section_total' => [
                'font'      => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '666666']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                'borders'   => [
                    'bottom' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => '000000'],
                    ],
                ],
            ],
            'empty_message' => [
                'font'      => ['italic' => true, 'size' => 12, 'color' => ['rgb' => '999999']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            ],
        ];

        foreach ($this->rowMetadata as $rowNum => $type) {
            if ($type === 'net_income') {
                $sheet->getStyle("A{$rowNum}")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '666666']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                ]);
                $sheet->getStyle("B{$rowNum}")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '666666']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                ]);
                $sheet->getStyle("A{$rowNum}:B{$rowNum}")->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => Border::BORDER_DOUBLE,
                            'color'       => ['rgb' => '000000'],
                        ],
                    ],
                ]);
            } elseif (isset($styleMap[$type])) {
                $sheet->getStyle("A{$rowNum}:B{$rowNum}")->applyFromArray($styleMap[$type]);
            }
        }

        $sheet->getStyle("B5:B{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        return [];
    }
}
