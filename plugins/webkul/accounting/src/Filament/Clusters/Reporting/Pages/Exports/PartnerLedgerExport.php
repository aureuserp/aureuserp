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

class PartnerLedgerExport implements FromArray, WithColumnWidths, WithHeadings, WithStyles
{
    protected $partners;

    protected Carbon $dateFrom;

    protected Carbon $dateTo;

    protected $getPartnerMovesCallback;

    protected array $expandedPartners;

    protected array $rowMetadata = [];

    public function __construct($partners, string $dateFrom, string $dateTo, callable $getPartnerMovesCallback, array $expandedPartners = [])
    {
        $this->partners = is_array($partners) ? $partners : $partners->toArray();
        $this->dateFrom = Carbon::parse($dateFrom);
        $this->dateTo = Carbon::parse($dateTo);
        $this->getPartnerMovesCallback = $getPartnerMovesCallback;
        $this->expandedPartners = $expandedPartners;
    }

    public function headings(): array
    {
        return [
            ['Partner Ledger - From '.$this->dateFrom->format('M d, Y').' to '.$this->dateTo->format('M d, Y')],
            [],
            ['Partner', 'Journal', 'Account', 'Invoice Date', 'Due Date', 'Debit', 'Credit', 'Balance'],
        ];
    }

    public function array(): array
    {
        $rows = [];
        $rowIndex = 4;

        $totals = collect(['debit', 'credit'])
            ->mapWithKeys(fn ($key) => [$key => 0])
            ->all();

        foreach ($this->partners as $partner) {
            $totals['debit'] += $partner['period_debit'];
            $totals['credit'] += $partner['period_credit'];

            $rows[] = [
                $partner['name'],
                '',
                '',
                '',
                '',
                $partner['period_debit'],
                $partner['period_credit'],
                $partner['ending_balance'],
            ];
            $this->rowMetadata[$rowIndex++] = 'partner_header';

            if (in_array($partner['id'], $this->expandedPartners)) {
                if ($partner['opening_balance'] != 0) {
                    $rows[] = [
                        '        Opening Balance',
                        $this->dateFrom->format('M d, Y'),
                        '',
                        '',
                        '',
                        $partner['opening_balance'] > 0 ? $partner['opening_balance'] : '',
                        $partner['opening_balance'] < 0 ? abs($partner['opening_balance']) : '',
                        $partner['opening_balance'],
                    ];
                    $this->rowMetadata[$rowIndex++] = 'opening_balance';
                }

                $moves = ($this->getPartnerMovesCallback)($partner['id']);
                $runningBalance = $partner['opening_balance'];

                collect($moves)->each(function ($move) use (&$rows, &$rowIndex, &$runningBalance) {
                    $runningBalance += $move['debit'] - $move['credit'];
                    $rows[] = [
                        '        '.$move['move_name'].($move['ref'] ? ' ('.$move['ref'].')' : ''),
                        $move['journal_name'] ?? '',
                        ($move['account_code'] ? $move['account_code'].' ' : '').$move['account_name'],
                        Carbon::parse($move['invoice_date'])->format('M d, Y'),
                        Carbon::parse($move['invoice_date_due'])->format('M d, Y'),
                        $move['debit'] > 0 ? $move['debit'] : '',
                        $move['credit'] > 0 ? $move['credit'] : '',
                        $runningBalance,
                    ];
                    $this->rowMetadata[$rowIndex++] = 'move_line';
                });
            }
        }

        $rows[] = [
            'Total Partner Ledger',
            '',
            '',
            '',
            '',
            $totals['debit'],
            $totals['credit'],
            '',
        ];
        $this->rowMetadata[$rowIndex] = 'grand_total';

        return $rows;
    }

    public function columnWidths(): array
    {
        return collect(range('A', 'H'))
            ->mapWithKeys(fn ($col) => [
                $col => match ($col) {
                    'A'     => 35,
                    'B'     => 20,
                    'C'     => 30,
                    default => 15,
                },
            ])
            ->all();
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:H1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);
        $sheet->mergeCells('A1:H1');

        $sheet->getStyle('A3:H3')->applyFromArray([
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
            'partner_header' => [
                'font'      => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '666666']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                'borders'   => [
                    'bottom' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => '000000'],
                    ],
                ],
            ],
            'opening_balance' => [
                'font'      => ['italic' => true, 'size' => 12, 'color' => ['rgb' => '666666']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            ],
            'move_line' => [
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
                $sheet->getStyle("A{$rowNum}:H{$rowNum}")->applyFromArray($styleMap[$type]);
            }
        }

        $lastRow = count($this->rowMetadata) + 4;
        $sheet->getStyle("F4:H{$lastRow}")->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle("F4:H{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        return [];
    }
}
