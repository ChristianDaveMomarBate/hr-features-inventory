<?php
$categories = ['Office Supplies', 'IT Equipment & Devices', 'Furniture & Fixtures'];
$types = ['Consumable', 'Non-Consumable', 'Asset'];
$units = ['pcs', 'box', 'unit'];

for($i=1; $i<=30; $i++) {
    \App\Models\InventoryItem::create([
        'code' => 'TEST-'.str_pad($i, 3, '0', STR_PAD_LEFT),
        'name' => 'Test Item '.$i,
        'category' => $categories[array_rand($categories)],
        'type' => $types[array_rand($types)],
        'unit' => $units[array_rand($units)],
        'stock' => rand(5, 50),
        'minimum' => rand(5, 15),
        'date_registered' => now()->subDays(rand(1, 30))
    ]);
}
echo "Created 30 items!\n";
