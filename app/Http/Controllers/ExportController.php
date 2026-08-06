<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\StockTransaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use ZipArchive;

class ExportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function exportPDF()
    {
        ini_set('memory_limit', '512M');
        $inventoryItems = InventoryItem::orderBy('code')->get();
        $stockInTotals = StockTransaction::where('type', 'in')
            ->selectRaw('inventory_item_id, SUM(quantity) as total_in')
            ->groupBy('inventory_item_id')
            ->pluck('total_in', 'inventory_item_id');

        $pdf = Pdf::loadView('exports.registry-pdf', [
            'inventoryItems' => $inventoryItems,
            'stockInTotals' => $stockInTotals,
        ])->setPaper('a4', 'landscape')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'defaultFont' => 'Arial',
                'dpi' => 96,
            ]);

        return $pdf->download('phrmdo-inventory-registry.pdf');
    }

    public function exportExcel()
    {
        $rows = InventoryItem::orderBy('code')
            ->get()
            ->map(function ($item) {
                return [
                    $item->code,
                    $item->name,
                    $item->category,
                    $item->type ?? 'Consumable',
                    $item->stock_unit,
                    $item->issue_unit,
                    $item->units_per_stock_unit,
                    $item->display_stock,
                    $item->bulk_equivalent ?? '',
                    $item->minimum,
                    $item->date_registered ? Carbon::parse($item->date_registered)->format('M d, Y') : '',
                ];
            })
            ->prepend([
                'Item Code',
                'Name',
                'Category',
                'Type',
                'Stock Unit',
                'Issue Unit',
                'Units per Stock Unit',
                'Current Stock',
                'Bulk Equivalent',
                'Minimum Stock',
                'Date Registered',
            ])
            ->values()
            ->all();

        $path = $this->buildXlsx($rows);

        return response()
            ->download($path, 'phrmdo-inventory-list.xlsx')
            ->deleteFileAfterSend(true);
    }

    private function buildXlsx(array $rows): string
    {
        $path = tempnam(storage_path('app'), 'inventory-export-');
        $zip = new ZipArchive;
        $zip->open($path, ZipArchive::OVERWRITE);

        $zip->addFromString('[Content_Types].xml', $this->contentTypesXml());
        $zip->addFromString('_rels/.rels', $this->rootRelsXml());
        $zip->addFromString('xl/workbook.xml', $this->workbookXml());
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->workbookRelsXml());
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->worksheetXml($rows));
        $zip->close();

        return $path;
    }

    private function worksheetXml(array $rows): string
    {
        $xmlRows = '';

        foreach ($rows as $rowIndex => $row) {
            $excelRow = $rowIndex + 1;
            $xmlRows .= '<row r="'.$excelRow.'">';

            foreach ($row as $columnIndex => $value) {
                $cell = $this->columnName($columnIndex + 1).$excelRow;
                $xmlRows .= '<c r="'.$cell.'" t="inlineStr"><is><t>'.$this->xml($value).'</t></is></c>';
            }

            $xmlRows .= '</row>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<sheetData>'.$xmlRows.'</sheetData>'
            .'</worksheet>';
    }

    private function columnName(int $number): string
    {
        $name = '';

        while ($number > 0) {
            $number--;
            $name = chr(65 + ($number % 26)).$name;
            $number = intdiv($number, 26);
        }

        return $name;
    }

    private function xml($value): string
    {
        return htmlspecialchars((string) $value, ENT_XML1 | ENT_COMPAT, 'UTF-8');
    }

    private function contentTypesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            .'<Default Extension="xml" ContentType="application/xml"/>'
            .'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            .'<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            .'</Types>';
    }

    private function rootRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            .'</Relationships>';
    }

    private function workbookXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
            .'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            .'<sheets><sheet name="Inventory" sheetId="1" r:id="rId1"/></sheets>'
            .'</workbook>';
    }

    private function workbookRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            .'</Relationships>';
    }
}
