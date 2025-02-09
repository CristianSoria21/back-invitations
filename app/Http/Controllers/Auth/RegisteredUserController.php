<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
  /**
   * Registro de usuario y generación de token.
   */

  public function store(Request $request): JsonResponse
  {
    try {
      $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
      ], [
        'name.required' => 'El nombre es obligatorio.',
        'email.required' => 'El correo electrónico es obligatorio.',
        'email.email' => 'El formato del correo electrónico no es válido.',
        'email.unique' => 'El correo electrónico ya está registrado.',
        'password.required' => 'La contraseña es obligatoria.',
        'password.confirmed' => 'Las contraseñas no coinciden.',
        'password.min' => 'La contraseña debe tener al menos 8 caracteres.'
      ]);

      // Crear el usuario
      $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
      ]);

      // Disparar el evento Registered
      event(new Registered($user));

      // Crear un token personal para el usuario
      $token = $user->createToken('auth_token')->plainTextToken;

      return response()->json([
        'message' => 'Usuario registrado exitosamente.',
        'user' => $user,
        'token' => $token,
      ], 201);
    } catch (\Exception $e) {
      return response()->json([
        'message' => 'Error del lado del servidor.',
        'error' => $e->getMessage()
      ], 500);
    }
  }
}
