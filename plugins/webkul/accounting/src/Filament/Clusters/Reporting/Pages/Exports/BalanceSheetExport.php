<?php

namespace Webkul\Accounting\Filament\Clusters\Reporting\Pages\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BalanceSheetExport implements FromCollection, WithColumnWidths, WithStyles
{
    protected array $balanceSheetData;

    protected Carbon $date;

    protected array $rowMetadata = [];

    public function __construct(array $balanceSheetData, Carbon $date)
    {
        $this->balanceSheetData = $balanceSheetData;
        $this->date = $date;
    }

    public function collection()
    {
        $rows = collect([
            [null, 'As of '.$this->date->format('M d, Y')],
            [null, null],
            [null, 'Balance'],
            [null, null],
        ]);

        $rowIndex = 5;

        foreach ($this->balanceSheetData['sections'] as $section) {
            $rows->push([$section['title'], '']);
            $this->rowMetadata[$rowIndex++] = 'section_header';

            foreach ($section['subsections'] as $subsection) {
                $hasAccounts = ! empty($subsection['accounts']);
                $showSubsection = $hasAccounts || ! isset($subsection['show_if_empty']) || $subsection['show_if_empty'];

                if ($showSubsection) {
                    $rows->push(['            '.$subsection['title'], '']);
                    $this->rowMetadata[$rowIndex++] = 'subsection_header';

                    if ($hasAccounts) {
                        collect($subsection['accounts'])->each(function ($account) use (&$rows, &$rowIndex) {
                            $accountName = ($account['code'] ? $account['code'].' - ' : '').$account['name'];
                            $rows->push([
                                '                        '.$accountName,
                                number_format($account['balance'], 2),
                            ]);
                            $this->rowMetadata[$rowIndex++] = 'account_line';
                        });

                        $rows->push([
                            '            '.$subsection['total_label'],
                            number_format($subsection['total'], 2),
                        ]);
                        $this->rowMetadata[$rowIndex++] = 'subsection_total';
                    }
                }
            }

            $rows->push([
                $section['total_label'],
                number_format($section['total'], 2),
            ]);
            $this->rowMetadata[$rowIndex++] = 'section_total';

            $rows->push(['', '']);
            $rowIndex++;
        }

        $rows->push([
            $this->balanceSheetData['grand_total_label'],
            number_format($this->balanceSheetData['grand_total'], 2),
        ]);
        $this->rowMetadata[$rowIndex] = 'grand_total';

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
            'subsection_header' => [
                'font'      => ['size' => 12, 'color' => ['rgb' => '666666']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            ],
            'account_line' => [
                'font'      => ['size' => 12, 'color' => ['rgb' => '666666']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            ],
            'subsection_total' => [
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
        ];

        foreach ($this->rowMetadata as $rowNum => $type) {
            if ($type === 'grand_total') {
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
