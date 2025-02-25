<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use \App\Models\User;


class AuthenticatedSessionController extends Controller
{

	//Inicio de sesión y generación de token.		
	public function store(Request $request): JsonResponse
	{
		$request->validate([
			'email' => ['required', 'email'],
			'password' => ['required'],
		]);

		$user = User::where('email', $request->email)->first();

		if (!$user) {
			return response()->json(['message' => 'El usuario no existe.'], 404);
		}

		if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
			return response()->json(['message' => 'Credenciales inválidas'], 401);
		}

		$user = Auth::user();
		$token = $user->createToken('auth_token')->plainTextToken;

		return response()->json([
			'message' => 'Inicio de sesión exitoso.',
			'user' => $user,
			'token' => $token,
		]);
	}

	/**
	 * Cierre de sesión y revocación de tokens.
	 */
	public function destroy(Request $request): JsonResponse
	{
		// Revocar el token del usuario autenticado
		$request->user()->tokens()->delete();

		return response()->json(['message' => 'Cierre de sesión exitoso.']);
	}
}
