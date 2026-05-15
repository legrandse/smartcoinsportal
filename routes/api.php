<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Validation\ValidationException;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/login', function (Request $request) {
    // 1. Valider les données reçues
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        //'device_name' => 'required', // Le nom du boitier IoT
    ]);

    // 2. Chercher l'utilisateur dans la base du Portail
    $user = User::where('email', $request->email)->first();

    // 3. Vérifier si l'utilisateur existe et si le mot de passe correspond
    if (! $user || ! Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Identifiants incorrects'], 401);
    }

    // 4. Créer le token Sanctum
    $token = $user->createToken($request->email)->plainTextToken;

    // 5. Renvoyer le token au device
    return ['token' => $token];
});