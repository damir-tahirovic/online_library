<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ResetPasswordController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PublisherController;

Route::post('/auth/login', [UserController::class, 'login'])->name('user.login');

Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

Route::get('/reset-password/{token}', function (string $token) {
    return ['token' => $token];
})->middleware('guest')->name('password.reset');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/register', [UserController::class, 'register'])->name('user.register');
    Route::post('/auth/logout', [UserController::class, 'logout'])->name('user.logout');

    //Routes for genres
    Route::middleware('checkLibrarian')->group(function () {
        Route::get('genres', [GenreController::class, 'index'])->name('genres.index');
        Route::post('genres', [GenreController::class, 'store'])->name('genres.store');
        Route::get('genres/{id}', [GenreController::class, 'show'])->name('genres.show');
        Route::put('genres/{id}', [GenreController::class, 'update'])->name('genres.update');
        Route::delete('genres/{id}', [GenreController::class, 'destroy'])->name('genres.destroy');
    });

    // pass user id as parameter
    // dont forget to add logged user bearer token
    Route::get('/auth/users/{role}', [UserController::class, 'viewUsers'])->middleware('checkLibrarian')->name('user.index');
    Route::get('/auth/users/{user}', [UserController::class, 'viewUser'])->middleware('checkLibrarian')->name('user.show');
    Route::get('/auth/users/{user}/profile_picture', [UserController::class, 'viewUserProfilePicture'])->middleware('checkLibrarian')->name('user.profilePicture');
    Route::put('/auth/user/update', [UserController::class, 'editUser'])->name('user.edit');

    Route::delete('/auth/user/{user}', [UserController::class, 'delete'])->name('user.delete');

    // Publisher routes
    Route::post('/auth/publishers/create', [PublisherController::class, 'createPublisher'])->name('publishers.create');
    Route::get('/auth/publishers/{id}', [PublisherController::class, 'viewPublisher'])->middleware('checkLibrarian')->name('publishers.view');
    Route::delete('/auth/publishers/{id}', [PublisherController::class, 'deletePublisher'])->middleware('checkLibrarian')->name('publishers.delete');
    Route::get('/auth/publishers', [PublisherController::class, 'viewAllPublishers'])->name('publishers.index');

    // Book routes
    Route::post('/auth/books/create', [BookController::class, 'createBook'])->middleware('checkLibrarian')->name('books.create');
    Route::get('/auth/books/{id}', [BookController::class, 'viewBook'])->middleware('checkLibrarian')->name('books.view');
    Route::get('/auth/books', [BookController::class, 'viewAllBooks'])->middleware('checkLibrarian')->name('books.index');
});


Route::get('/authors', [AuthorController::class, 'index']);
Route::get('/authors/{author}/picture', [AuthorController::class, 'getPicture']);
Route::get('/authors/{author}', [AuthorController::class, 'show']);
