<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/', [UserController::class, 'index'])->name('users.index');
Route::resource('users', UserController::class)->except(['create', 'edit']);
Route::post('/users/merge', [UserController::class, 'merge'])->name('users.merge');
Route::post('/users/add-contact', [UserController::class, 'addContact'])->name('users.add-contact');
Route::delete('/users/remove-contact/{contactId}', [UserController::class, 'removeContact'])->name('users.remove-contact');
Route::get('/users/{user}/contacts', [UserController::class, 'getContacts'])->name('users.contacts');
Route::get('/users/{user}/available-contacts', [UserController::class, 'getAvailableContacts'])->name('users.available-contacts');
