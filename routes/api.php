<?php
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\ShapesController;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

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

Route::get('getNode/{id}', [ApiController::class, 'getNode']);
Route::get('getScenery/{id}', [ApiController::class, 'getScenery']);

Route::post('saveProject', [ApiController::class, 'saveProject']);
Route::post('saveNode', [ApiController::class, 'saveNode']);
Route::post('saveScenery', [ApiController::class, 'saveScenery']);
Route::post('saveSceneryNoPropagation', [ApiController::class, 'saveSceneryNoPropagation']);

Route::put('updateProject/{id}', [ApiController::class, 'updateProject']);
Route::put('updateAllProject/{id}', [ApiController::class, 'updateProject2']);
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

Route::post('sendCode', [ApiController::class, 'sendCode']);
Route::post('checkCode', [ApiController::class, 'checkCode']);
Route::post('changePassword', [ApiController::class, 'changePassword']);
Route::post('testEmail', [ApiController::class, 'testEmail']);


Route::get('/verify-email/{code}', function ($code) {
    $user = User::where('email_verification_code', $code)->first();

    if (!$user) {
        return redirect('http://209.38.31.107/#/login?status=error&message=Invalid verification code');
    }

    // Marcar el email como verificado
    $user->email_verified_at = now();
    $user->email_verification_code = null;
    $user->save();

    // Redirigir al login del frontend con un mensaje de éxito
    return redirect('http://209.38.31.107/#/login?status=success&message=Email successfully verified');
});


Route::post('/resend-verification', function (Request $request) {
    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    if ($user->email_verified_at) {
        return response()->json(['message' => 'Email already verified'], 200);
    }

    // Generar nuevo código
    $newCode = md5(uniqid());
    $user->email_verification_code = $newCode;
    $user->save();

    // Enviar correo con el nuevo código
    Mail::send('verify-email', ['user' => $user], function ($message) use ($user) {
        $message->from('noreply@ztris.com', 'Ztris');
        $message->to($user->email, $user->name);
        $message->subject('Verify your email');
    });

    return response()->json(['message' => 'Verification email sent successfully'], 200);
});



Route::get('list-users', [ApiController::class, 'listUsers']);