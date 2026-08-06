<?php

use App\Models\StockTransaction;
use Illuminate\Contracts\Console\Kernel;

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$txs = StockTransaction::where('type', 'out')
    ->orderBy('created_at', 'desc')
    ->take(20)
    ->get(['id', 'inventory_item_id', 'type', 'quantity', 'reference', 'created_at']);

foreach ($txs as $tx) {
    echo "ID:{$tx->id} | item_id:{$tx->inventory_item_id} | qty:{$tx->quantity} | ref:[{$tx->reference}] | date:{$tx->created_at}\n";
}
