<?php

use App\Models\InventoryItem;

// Delete previously generated dummy items
InventoryItem::where('name', 'like', 'Test Item%')->delete();

$realItems = [
    ['Office Supplies', 'Consumable', 'ream', 'A4 Bond Paper (Sub 20)'],
    ['Office Supplies', 'Consumable', 'pcs', 'Sign Pen (Black, 0.5mm)'],
    ['Office Supplies', 'Consumable', 'pcs', 'Sign Pen (Blue, 0.5mm)'],
    ['Office Supplies', 'Consumable', 'box', 'Paper Clips (Jumbo)'],
    ['Office Supplies', 'Consumable', 'pcs', 'Correction Tape'],
    ['Office Supplies', 'Consumable', 'pcs', 'Sticky Notes (3x3)'],
    ['Office Supplies', 'Consumable', 'pcs', 'Highlighter (Yellow)'],
    ['Office Supplies', 'Non-Consumable', 'pcs', 'Stapler with Remover'],
    ['Office Supplies', 'Non-Consumable', 'pcs', 'Hole Puncher (Heavy Duty)'],
    ['Office Supplies', 'Non-Consumable', 'pcs', 'Whiteboard Marker (Black)'],

    ['IT Equipment & Devices', 'Asset', 'unit', 'Dell Optiplex Desktop'],
    ['IT Equipment & Devices', 'Asset', 'unit', 'Lenovo ThinkPad Laptop'],
    ['IT Equipment & Devices', 'Asset', 'unit', 'Epson L3110 Printer'],
    ['IT Equipment & Devices', 'Non-Consumable', 'pcs', 'Logitech Wireless Mouse'],
    ['IT Equipment & Devices', 'Non-Consumable', 'pcs', 'USB Flash Drive 64GB'],
    ['IT Equipment & Devices', 'Consumable', 'bottle', 'Epson Ink 003 (Black)'],
    ['IT Equipment & Devices', 'Consumable', 'bottle', 'Epson Ink 003 (Cyan)'],
    ['IT Equipment & Devices', 'Non-Consumable', 'pcs', 'HDMI Cable (1.5m)'],
    ['IT Equipment & Devices', 'Asset', 'unit', 'AOC 24-inch Monitor'],
    ['IT Equipment & Devices', 'Asset', 'unit', 'APC UPS 650VA'],

    ['Furniture & Fixtures', 'Asset', 'unit', 'Ergonomic Office Chair'],
    ['Furniture & Fixtures', 'Asset', 'unit', 'Wooden Office Desk'],
    ['Furniture & Fixtures', 'Asset', 'unit', 'Steel Filing Cabinet (4 Drawers)'],
    ['Furniture & Fixtures', 'Asset', 'unit', 'Conference Table'],
    ['Furniture & Fixtures', 'Asset', 'unit', 'Visitor Chair'],

    ['HR Records & Document Materials', 'Consumable', 'pcs', 'Expanding Envelope (Long)'],
    ['HR Records & Document Materials', 'Consumable', 'pcs', 'Brown Envelope (Long)'],
    ['HR Records & Document Materials', 'Consumable', 'box', 'Fastener (Plastic)'],
    ['HR Records & Document Materials', 'Non-Consumable', 'pcs', 'Arch File Folder'],

    ['Forms & HR Documents', 'Consumable', 'pad', 'Leave Application Form'],
    ['Forms & HR Documents', 'Consumable', 'pad', 'Daily Time Record (DTR) Cards'],

    ['Maintenance & Utility Supplies', 'Consumable', 'bottle', 'Disinfectant Spray'],
    ['Maintenance & Utility Supplies', 'Consumable', 'pcs', 'Microfiber Cloth'],
    ['Maintenance & Utility Supplies', 'Consumable', 'roll', 'Tissue Paper (2-ply)'],
    ['Maintenance & Utility Supplies', 'Consumable', 'bottle', 'Glass Cleaner'],
    ['Maintenance & Utility Supplies', 'Non-Consumable', 'pcs', 'Trash Bin'],

    ['Security & Accountability Items', 'Consumable', 'pcs', 'ID Lace / Lanyard'],
    ['Security & Accountability Items', 'Consumable', 'pcs', 'Plastic ID Holder'],
    ['Security & Accountability Items', 'Asset', 'unit', 'Biometric Scanner'],
    ['Security & Accountability Items', 'Asset', 'unit', 'CCTV Camera'],
];

$count = 1;
foreach ($realItems as $item) {
    InventoryItem::create([
        'code' => 'ITM-'.date('Y').'-'.str_pad($count, 3, '0', STR_PAD_LEFT),
        'name' => $item[3],
        'category' => $item[0],
        'type' => $item[1],
        'unit' => $item[2],
        'stock' => rand(10, 100),
        'minimum' => rand(5, 20),
        'date_registered' => now()->subDays(rand(1, 60)),
    ]);
    $count++;
}

echo 'Created '.count($realItems)." realistic items!\n";
