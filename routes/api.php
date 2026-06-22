<?php

use App\Http\Controllers\Api\PegawaiController;
use Illuminate\Support\Facades\Route;

Route::get('pegawai', [PegawaiController::class, 'index']);
