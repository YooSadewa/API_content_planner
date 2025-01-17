<?php

use App\Http\Controllers\api\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/host', [ApiController::class, 'getHost']);
Route::post('/host/create', [ApiController::class, 'createHost']);
Route::put('/host/update/{id}', [ApiController::class, 'updateHost']);
Route::delete('/host/delete/{id}', [ApiController::class, 'deleteHost']);