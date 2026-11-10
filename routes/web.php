<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuditLogController;

Route::get('/', [AuditLogController::class, 'index'])->name('audit.index');
Route::post('/log', [AuditLogController::class, 'store'])->name('audit.store');
Route::get('/logs', [AuditLogController::class, 'showLogs'])->name('audit.logs');
Route::get('/verify', [AuditLogController::class, 'verify'])->name('audit.verify');
