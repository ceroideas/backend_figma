<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('migrar', [ApiController::class, 'migrar']);

Route::get('getProjects', [ApiController::class, 'getProjects']);
Route::get('getProject/{id}', [ApiController::class, 'getProject']);
Route::post('saveProject', [ApiController::class, 'saveProject']);
Route::post('saveNode', [ApiController::class, 'saveNode']);
Route::post('saveScenery', [ApiController::class, 'saveScenery']);

Route::put('updateProject/{id}', [ApiController::class, 'updateProject']);
Route::put('updateNode/{id}', [ApiController::class, 'updateNode']);
Route::put('updateScenery/{id}', [ApiController::class, 'updateScenery']);