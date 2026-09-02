<?php

use App\Http\Controllers\Api\YoutubeInterviewController;
use App\Http\Controllers\Api\YoutubeCoverageController;
use Illuminate\Support\Facades\Route;

Route::get('/youtube/interviews', [YoutubeInterviewController::class, 'index']);

Route::get('/youtube/coverage', [YoutubeCoverageController::class, 'index']);
