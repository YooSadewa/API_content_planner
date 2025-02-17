<?php

use App\Http\Controllers\api\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Host route
Route::get('/host', [ApiController::class, 'getHost']);
Route::post('/host/create', [ApiController::class, 'createHost']);
Route::put('/host/update/{id}', [ApiController::class, 'updateHost']);
Route::patch('/host/delete/{id}', [ApiController::class, 'deleteHost']);

//Speaker route
Route::get('/pembicara', [ApiController::class, 'getSpeaker']);
Route::post('/pembicara/create', [ApiController::class, 'createSpeaker']);
Route::put('/pembicara/update/{id}', [ApiController::class, 'updateSpeaker']);
Route::patch('/pembicara/delete/{id}', [ApiController::class, 'deleteSpeaker']);

//Podcast route
Route::get('/podcast', [ApiController::class, 'getPodcast']);
Route::post('/podcast/create', [ApiController::class, 'createPodcast']);
Route::put('/podcast/update/{id}', [ApiController::class, 'updatePodcast']);
Route::delete('/podcast/delete/{id}', [ApiController::class, 'deletePodcast']);
Route::put('/podcast/upload/{id}', [ApiController::class, 'uploadPodcast']);

//Quote route
Route::get('/quote', [ApiController::class, 'getQuote']);
Route::post('/quote/create', [ApiController::class, 'createQuote']);
Route::put('/quote/update/{id}', [ApiController::class, 'updateQuote']);
Route::delete('/quote/delete/{id}', [ApiController::class, 'deleteQuote']);

//Inspiring People route
Route::get('/inspiringpeople', [ApiController::class, 'getInspPeople']);
Route::post('/inspiringpeople/create', [ApiController::class, 'createInspPeople']);
Route::put('/inspiringpeople/update/{id}', [ApiController::class, 'updateInspPeople']);
Route::delete('/inspiringpeople/delete/{id}', [ApiController::class, 'deleteInspPeople']);

//Ide Konten Foto route
Route::get('/idekontenfoto', [ApiController::class, 'getIdeKontenFoto']);
Route::post('/idekontenfoto/create', [ApiController::class, 'createIdeKontenFoto']);
Route::put('/idekontenfoto/update/{id}', [ApiController::class, 'updateIdeKontenFoto']);
Route::delete('/idekontenfoto/delete/{id}', [ApiController::class, 'deleteIdeKontenFoto']);

//Ide Konten Video route
Route::get('/idekontenvideo', [ApiController::class, 'getIdeKontenVideo']);
Route::post('/idekontenvideo/create', [ApiController::class, 'createIdeKontenVideo']);
Route::put('/idekontenvideo/upload/{id}', [ApiController::class, 'confirmUploadKontenVideo']);
Route::put('/idekontenvideo/update/{id}', [ApiController::class, 'updateIdeKontenVideo']);
Route::delete('/idekontenvideo/delete/{id}', [ApiController::class, 'deleteIdeKontenVideo']);