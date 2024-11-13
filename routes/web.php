<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CVController;

Route::get('/', function () {
    return view('welcome');
});

// Route for uploading and parsing CVs
Route::post('/upload-cv', [CVController::class, 'uploadCv'])->name('upload.cv');
Route::get('/cv', [CVController::class, 'cvForm'])->name('cv.form');


//Routes for uploading and parsing batch cvs
Route::get('/batch-cvs', [CVController::class, 'batchCVForm'])->name('batch.cv.form');
Route::post('/batch-cvs-please', [CVController::class, 'dispatchBatchCvJob'])->name('batch.cvs');
