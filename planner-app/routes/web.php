<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Web\TaskWebController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/tasks'));

Route::get('/tasks', [TaskWebController::class, 'index'])->name('tasks.index');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/tasks/flash', function (Illuminate\Http\Request $request) {
    if ($request->ajax() && $request->filled('message')) {
        session()->flash('success', $request->message);
        return response()->noContent();
    }
    abort(403);
});

require __DIR__.'/auth.php';
