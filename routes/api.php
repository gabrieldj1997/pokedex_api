<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

Route::post('/test', function (Request $req) {
    $rawContent = $req->getContent();
    Log::info('Webhook - Rota alcançada.');
    Log::info('Webhook Payload (ALL): ', $req->all());
    return response()->json(['status' => 'received'], 200);
});