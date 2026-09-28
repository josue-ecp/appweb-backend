<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\MochilaController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\StripeController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Rutas de autenticación
Route::post('/register', [ApiController::class, 'registerUser']);
Route::post('/login', [ApiController::class, 'loginUser']);

// Rutas del usuario y juegos
Route::get('/usuario', [ApiController::class, 'getUsuario']);
Route::post('/usuario/estrellas', [ApiController::class, 'sumarEstrellas']);
Route::get('/materias', [ApiController::class, 'getMaterias']);

Route::get('/trofeos', [ApiController::class, 'getTrofeos']);
Route::post('/trofeos/desbloquear', [ApiController::class, 'desbloquearTrofeo']);

Route::get('/mochila', [MochilaController::class, 'index']);
Route::post('/mochila/desbloquear', [MochilaController::class, 'desbloquear']);

Route::post('/materias', [ApiController::class, 'storeMateria']);
Route::post('/admin/login', [ApiController::class, 'loginAdmin']);
Route::post('/admin/mundo-completo', [ApiController::class, 'storeMundoCompleto']);

Route::get('/crear-admin-prueba', function() {
    // Borramos o actualizamos si ya existe
    DB::table('administradores')->where('correo', 'admin@aventurilandia.com')->delete();

    DB::table('administradores')->insert([
        'nombre' => 'Profesor Admin',
        'correo' => 'admin@aventurilandia.com',
        'contrasena' => Hash::make('admin123'), // Encriptado nativo de Laravel
        'creado_en' => now()
    ]);

    return "¡Administrador creado con éxito y contraseña encriptada correctamente!";
});

// Temporalmente sin middleware estricto de auth para pruebas fluidas de Stripe
Route::post('/stripe/crear-sesion', [StripeController::class, 'crearSesionCheckout']);

Route::post('/stripe/crear-payment-intent', [StripeController::class, 'crearPaymentIntent']);
