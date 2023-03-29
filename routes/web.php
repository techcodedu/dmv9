<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CollegeController;
use App\Http\Controllers\CampusExtension;
use App\Http\Controllers\ChancellorController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CampusRecordsController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// Route::get('/college/dashboard', [CollegeController::class, 'dashboard'])->middleware('auth', 'role:college')->name('college.dashboard');
// Route::middleware(['web', 'auth', 'role:college'])->group(function () {
//     Route::get('/college/dashboard', [CollegeController::class, 'dashboard'])->name('college.dashboard');
// });
// Route::middleware(['web', 'auth', 'role:campus_extensions'])->group(function () {
//     Route::get('/campusExtension/dashboard', [CampusExtension::class, 'dashboard'])->name('campus_extensions.dashboard');
// });
Route::middleware(['auth'])->group(function () {

    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('system.dashboard');
        Route::get('/admin/createUser', [AdminController::class, 'create'])->name('users.create');
        Route::post('/admin/saveUser', [AdminController::class, 'store'])->name('users.store');
        Route::get('/users/{id}/edit', [AdminController::class, 'edit'])->name('users.edit');
        Route::put('/users/{id}', [AdminController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [AdminController::class, 'destroy'])->name('users.destroy');

        // Add more routes for campus_extensions role as needed
    });

    Route::middleware(['role:college'])->group(function () {
        Route::get('/college/dashboard', [CollegeController::class, 'dashboard'])->name('college.dashboard');
        Route::get('/college/create-document', [CollegeController::class, 'showCreateDocument'])->name('college.show_create_document');
        Route::post('/college/create-document', [CollegeController::class, 'storeDocument'])->name('college.store_document');
        Route::get('/college/documentHistory', [CollegeController::class, 'documentHistory'])->name('college.document_history');
        Route::get('/document/view/{document_id}', [DocumentController::class, 'view'])->name('document.view');
        Route::get('/document/edit/{document_id}', [DocumentController::class, 'edit'])->name('document.edit');
    });

    Route::middleware(['role:campus_records'])->group(function () {
        Route::get('/campus_records/dashboard', [CampusRecordsController::class, 'dashboard'])->name('campus_records.dashboard');
        Route::get('/campus_records/incoming', [CampusRecordsController::class, 'incomingDocuments'])->name('campus_records.incoming');
        Route::get('/campus_records/download/{file}', [CampusRecordsController::class, 'downloadFile'])->name('download.file');
        Route::post('/campus_records/forward/{id}', [CampusRecordsController::class, 'forwardDocument'])->name('campus_records.forward');
        Route::get('/campus_records/outgoing', [CampusRecordsController::class, 'outgoingDocuments'])->name('campus_records.outgoing');

        Route::get('/campus_records/document_history', [CampusRecordsController::class, 'documentHistory'])->name('document_history');
        Route::get('/campus_records/document_history/data', [CampusRecordsController::class, 'documentHistoryData'])->name('document_history.data');

        // Add more routes for campus_extensions role as needed
    });
     Route::middleware(['role:campus_extensions'])->group(function () {
        Route::get('/campus-extensions/dashboard', [CampusExtension::class, 'dashboard'])->name('campus_extension.dashboard');
        // Add more routes for campus_extensions role as needed
    });

});


// Route::middleware(['auth'])->group(function () {
    
//     Route::get('/campus_extension/dashboard', [CampusExtensionController::class, 'dashboard'])->name('campus_extension.dashboard');
//     Route::get('/chancellor/dashboard', [ChancellorController::class, 'dashboard'])->name('chancellor.dashboard');
// });
