<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\ShapesController;

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

Route::get('getProjects/{id}', [ApiController::class, 'getProjects']);
Route::get('getProject/{id}', [ApiController::class, 'getProject']);

Route::get('getNode/{id}', [ApiController::class, 'getNode']);
Route::get('getScenery/{id}', [ApiController::class, 'getScenery']);

Route::post('saveProject', [ApiController::class, 'saveProject']);
Route::post('saveNode', [ApiController::class, 'saveNode']);
Route::post('saveScenery', [ApiController::class, 'saveScenery']);
Route::post('saveSceneryNoPropagation', [ApiController::class, 'saveSceneryNoPropagation']);

Route::put('updateProject/{id}', [ApiController::class, 'updateProject']);
Route::put('updateNode/{id}', [ApiController::class, 'updateNode']);
Route::put('updateScenery/{id}', [ApiController::class, 'updateScenery']);

Route::delete('deleteProject/{id}', [ApiController::class, 'deleteProject']);
Route::delete('deleteNode/{id}', [ApiController::class, 'deleteNode']);
Route::delete('deleteScenery/{id}', [ApiController::class, 'deleteScenery']);

Route::put('savePosition/{id}', [ApiController::class, 'savePosition']);
Route::put('saveZoom/{id}', [ApiController::class, 'saveZoom']);
Route::put('saveUnite/{id}', [ApiController::class, 'saveUnite']);

Route::get('getSimulations/{id}', [ApiController::class, 'getSimulations']);
Route::get('getSimulation/{id}', [ApiController::class, 'getSimulation']);
Route::post('saveSimulation', [ApiController::class, 'saveSimulation']);
Route::put('updateSimulation/{id}', [ApiController::class, 'updateSimulation']);
Route::delete('deleteSimulation/{id}', [ApiController::class, 'deleteSimulation']);

Route::put('setHiddenTable', [ApiController::class, 'setHiddenTable']);
Route::put('setHiddenNode', [ApiController::class, 'setHiddenNode']);

Route::post('definitelyNotEval', [ApiController::class, 'definitelyNotEval']);

Route::post('generateSimulation', [ShapesController::class, 'generateSimulation']);

Route::post('uploadProject/{id}', [ApiController::class, 'uploadProject']);

Route::post('register', [ApiController::class, 'register']);
Route::post('login', [ApiController::class, 'login']);