<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MochilaController extends Controller
{
    // Obtener todos los accesorios desbloqueados del usuario actual
    public function index(Request $request)
    {
        // Asumiendo que usas algún sistema de autenticación (como Laravel Sanctum o ID simulado temporal)
        // Si manejas el ID del usuario de otra forma en tu app, ajústalo aquí:
        $userId = $request->user() ? $request->user()->id : 1;

        $accesorios = DB::table('usuario_accesorios')
            ->where('user_id', $userId)
            ->pluck('accesorio_slug'); // Devuelve un array con los slugs: ['sombrero_explorador', ...]

        return response()->json($accesorios);
    }

    // Guardar un nuevo accesorio desbloqueado al abrir el cofre
    public function desbloquear(Request $request)
    {
        $request->validate([
            'accesorio_slug' => 'required|string'
        ]);

        $userId = $request->user() ? $request->user()->id : 1;
        $slug = $request->accesorio_slug;

        // Verificamos si ya lo tiene para evitar duplicados
        $existe = DB::table('usuario_accesorios')
            ->where('user_id', $userId)
            ->where('accesorio_slug', $slug)
            ->exists();

        if (!$existe) {
            DB::table('usuario_accesorios')->insert([
                'user_id' => $userId,
                'accesorio_slug' => $slug,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Accesorio guardado en la base de datos con éxito'
        ]);
    }
}
