<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ApiController extends Controller
{
    // 1. Registro de nuevo usuario (guarda todos los datos en la BD)
    public function registerUser(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'correo' => 'required|email|unique:usuarios,correo',
            'contrasena' => 'required|string|min:4',
            'avatar' => 'nullable|string|max:50',
        ]);

        $nuevoId = DB::table('usuarios')->insertGetId([
            'nombre' => $request->nombre,
            'correo' => $request->correo,
            'contrasena' => Hash::make($request->contrasena),
            'avatar' => $request->avatar ?? 'fox',
            'nivel' => 1,
            'estrellas' => 10, // Estrellas de bienvenida por registrarse
            'dias_racha' => 1,
            'creado_en' => now(),
            'actualizado_en' => now(),
        ]);

        $usuario = DB::table('usuarios')->where('id', $nuevoId)->first();

        return response()->json([
            'success' => true,
            'message' => '¡Cuenta creada con éxito!',
            'usuario' => $usuario
        ]);
    }

    // 2. Inicio de sesión (Login para cuentas existentes)
    public function loginUser(Request $request)
    {
        $request->validate([
            'correo' => 'required|email',
            'contrasena' => 'required|string',
        ]);

        $usuario = DB::table('usuarios')->where('correo', $request->correo)->first();

        if (!$usuario || !Hash::check($request->contrasena, $usuario->contrasena)) {
            return response()->json([
                'success' => false,
                'message' => 'Correo o contraseña incorrectos.'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => '¡Bienvenido de nuevo!',
            'usuario' => $usuario
        ]);
    }

    // Obtener datos del usuario (estrellas, nivel, racha) filtrando por user_id
    public function getUsuario(Request $request)
    {
        $userId = $request->query('user_id');

        if ($userId) {
            $usuario = DB::table('usuarios')->where('id', $userId)->first();
        } else {
            $usuario = DB::table('usuarios')->first();
        }

        return response()->json($usuario);
    }

    // Obtener la lista de materias (mundos)
    public function getMaterias()
    {
        $materias = DB::table('materias')->get();
        return response()->json($materias);
    }

    // Actualizar estrellas del usuario al completar misiones filtrando por user_id
    public function sumarEstrellas(Request $request)
    {
        $request->validate([
            'estrellas' => 'required|integer',
            'user_id' => 'required|integer',
        ]);

        $usuario = DB::table('usuarios')->where('id', $request->user_id)->first();

        if ($usuario) {
            $nuevasEstrellas = $usuario->estrellas + $request->estrellas;
            DB::table('usuarios')->where('id', $usuario->id)->update([
                'estrellas' => $nuevasEstrellas,
                'actualizado_en' => now()
            ]);

            return response()->json([
                'success' => true,
                'estrellas_totales' => $nuevasEstrellas
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Usuario no encontrado'], 404);
    }

    // Obtener los trofeos desbloqueados del usuario filtrando por user_id
    public function getTrofeos(Request $request)
    {
        $userId = $request->query('user_id');

        if (!$userId) {
            return response()->json([]);
        }

        $trofeos = DB::table('usuario_trofeos')
            ->where('usuario_id', $userId)
            ->pluck('trofeo_slug');

        return response()->json($trofeos);
    }

    // Desbloquear un trofeo y otorgar su recompensa de estrellas al usuario correcto
    public function desbloquearTrofeo(Request $request)
    {
        $request->validate([
            'trofeo_slug' => 'required|string',
            'recompensa' => 'required|integer',
            'user_id' => 'required|integer'
        ]);

        $usuario = DB::table('usuarios')->where('id', $request->user_id)->first();

        if (!$usuario) {
            return response()->json(['success' => false, 'message' => 'Usuario no encontrado'], 404);
        }

        // Verificamos si ya lo tiene desbloqueado
        $existe = DB::table('usuario_trofeos')
            ->where('usuario_id', $usuario->id)
            ->where('trofeo_slug', $request->trofeo_slug)
            ->exists();

        if (!$existe) {
            // 1. Guardamos el trofeo
            DB::table('usuario_trofeos')->insert([
                'usuario_id' => $usuario->id,
                'trofeo_slug' => $request->trofeo_slug,
                'creado_en' => now()
            ]);

            // 2. Sumamos las estrellas de recompensa al usuario correcto
            $nuevasEstrellas = $usuario->estrellas + $request->recompensa;
            DB::table('usuarios')->where('id', $usuario->id)->update([
                'estrellas' => $nuevasEstrellas,
                'actualizado_en' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => '¡Trofeo desbloqueado y estrellas sumadas!',
                'estrellas_totales' => $nuevasEstrellas
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'El trofeo ya estaba desbloqueado anteriormente.'
        ]);
    }


  // Crear una nueva materia desde el panel administrativo
public function storeMateria(Request $request)
{
    $request->validate([
        'slug' => 'required|string|unique:materias,slug',
        'titulo' => 'required|string|max:255',
        'descripcion' => 'required|string',
        'total_misiones' => 'required|integer'
    ]);

    $materiasId = DB::table('materias')->insertGetId([
        'slug' => $request->slug,
        'titulo' => $request->titulo,
        'descripcion' => $request->descripcion,
        'total_misiones' => $request->total_misiones,
        'creado_en' => now(),
        'actualizado_en' => now()
    ]);

    return response()->json([
        'success' => true,
        'message' => '¡Materia creada con éxito!',
        'id' => $materiasId
    ]);
}

 // Inicio de sesión para administradores / profesores (Validación segura con Hash)
public function loginAdmin(Request $request)
{
    $request->validate([
        'correo' => 'required|email',
        'contrasena' => 'required|string',
    ]);

    $admin = DB::table('administradores')->where('correo', $request->correo)->first();

    if (!$admin || !Hash::check($request->contrasena, $admin->contrasena)) {
        return response()->json([
            'success' => false,
            'message' => 'Credenciales de profesor incorrectas.'
        ], 401);
    }

    return response()->json([
        'success' => true,
        'message' => '¡Bienvenido al Panel Administrativo!',
        'admin' => $admin
    ]);
}

// Guardar preguntas asociadas a una materia
public function storeMundoCompleto(Request $request)
{
    $request->validate([
        'slug' => 'required|string|unique:materias,slug',
        'titulo' => 'required|string|max:255',
        'etiqueta_superior' => 'required|string|max:255',
        'descripcion' => 'required|string',
        'preguntas' => 'required|array|min:1',
    ]);

    DB::transaction(function () use ($request) {
        // 1. Guardar la materia
        DB::table('materias')->insert([
            'slug' => $request->slug,
            'titulo' => $request->titulo,
            'etiqueta_superior' => $request->etiqueta_superior,
            'descripcion' => $request->descripcion,
            'total_misiones' => count($request->preguntas),
            'creado_en' => now(),
            'actualizado_en' => now()
        ]);

        // 2. Guardar todas las preguntas asociadas
        foreach ($request->preguntas as $p) {
            DB::table('preguntas')->insert([
                'materia_slug' => $request->slug,
                'pregunta' => $p['pregunta'],
                'opcion_1' => $p['opcion_1'],
                'opcion_2' => $p['opcion_2'],
                'opcion_3' => $p['opcion_3'],
                'respuesta_correcta' => $p['respuesta_correcta'],
                'creado_en' => now()
            ]);
        }
    });

    return response()->json([
        'success' => true,
        'message' => '¡Mundo y sus 10 preguntas creados con éxito!'
    ]);
}
}
