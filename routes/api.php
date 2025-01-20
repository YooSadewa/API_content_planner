<?php

use App\Http\Controllers\api\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Host route
Route::get('/host', [ApiController::class, 'getHost']);
Route::post('/host/create', [ApiController::class, 'createHost']);
Route::put('/host/update/{id}', [ApiController::class, 'updateHost']);
Route::delete('/host/delete/{id}', [ApiController::class, 'deleteHost']);

//Speaker route
Route::get('/pembicara', [ApiController::class, 'getSpeaker']);
Route::post('/pembicara/create', [ApiController::class, 'createSpeaker']);
Route::put('/pembicara/update/{id}', [ApiController::class, 'updateSpeaker']);
Route::delete('/pembicara/delete/{id}', [ApiController::class, 'deleteSpeaker']);