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

class GeneralLedgerExport implements FromArray, WithColumnWidths, WithHeadings, WithStyles
{
    protected $accounts;

    protected Carbon $dateFrom;

    protected Carbon $dateTo;

    protected $getAccountMovesCallback;

    protected array $expandedAccounts;

    protected array $rowMetadata = [];

    public function __construct($accounts, Carbon $dateFrom, Carbon $dateTo, callable $getAccountMovesCallback, array $expandedAccounts = [])
    {
        $this->accounts = $accounts;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->getAccountMovesCallback = $getAccountMovesCallback;
        $this->expandedAccounts = $expandedAccounts;
    }

    public function headings(): array
    {
        return [
            ['General Ledger - From '.$this->dateFrom->format('M d, Y').' to '.$this->dateTo->format('M d, Y')],
            [],
            ['Account', 'Date', 'Communication', 'Partner', 'Debit', 'Credit', 'Balance'],
        ];
    }

    public function array(): array
    {
        $rows = [];
        $rowIndex = 4;

        $totals = collect(['debit', 'credit'])
            ->mapWithKeys(fn ($key) => [$key => 0])
            ->all();

        foreach ($this->accounts as $account) {
            $totals['debit'] += $account->period_debit;
            $totals['credit'] += $account->period_credit;

            $rows[] = [
                $account->code.' '.$account->name,
                '',
                '',
                '',
                $account->period_debit,
                $account->period_credit,
                $account->ending_balance,
            ];
            $this->rowMetadata[$rowIndex++] = 'account_header';

            if (in_array($account->id, $this->expandedAccounts)) {
                if ($account->opening_balance != 0) {
                    $rows[] = [
                        '        Opening Balance',
                        $this->dateFrom->format('M d, Y'),
                        '',
                        '',
                        $account->opening_balance > 0 ? $account->opening_balance : '',
                        $account->opening_balance < 0 ? abs($account->opening_balance) : '',
                        $account->opening_balance,
                    ];
                    $this->rowMetadata[$rowIndex++] = 'opening_balance';
                }

                $moves = ($this->getAccountMovesCallback)($account->id);
                $runningBalance = $account->opening_balance;

                collect($moves)->each(function ($move) use (&$rows, &$rowIndex, &$runningBalance) {
                    $runningBalance += $move['debit'] - $move['credit'];
                    $rows[] = [
                        '        '.$move['move_name'],
                        Carbon::parse($move['date'])->format('M d, Y'),
                        ($move['move_type'] ?? null) == 'entry' ? ($move['name'] ?? '') : '',
                        $move['partner_name'] ?? '',
                        $move['debit'] > 0 ? $move['debit'] : '',
                        $move['credit'] > 0 ? $move['credit'] : '',
                        $runningBalance,
                    ];
                    $this->rowMetadata[$rowIndex++] = 'move_line';
                });
            }
        }

        $rows[] = [
            'Total General Ledger',
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
        return collect(range('A', 'G'))
            ->mapWithKeys(fn ($col) => [
                $col => match ($col) {
                    'A' => 35,
                    'B' => 15,
                    'C', 'D' => 25,
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

        $sheet->getStyle('A3:G3')->applyFromArray([
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
            'account_header' => [
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
            'account_total' => [
                'font'    => ['bold' => true],
                'borders' => [
                    'bottom' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => '000000'],
                    ],
                ],
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

        $lastRow = count($this->rowMetadata) + 4;
        $sheet->getStyle("E4:G{$lastRow}")->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle("E4:G{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        return [];
    }
}
