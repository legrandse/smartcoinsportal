<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Devices;
use App\Models\LinkedDevices;
use Illuminate\Validation\ValidationException;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/login', function (Request $request) {
    // 1. Validation de la requête
	$request->validate([
	    'email'    => 'required|email',
	    'password' => 'required',
	    'device'   => 'required', // Le numéro de série du boîtier IoT
	]);

	// 2. Chercher l'utilisateur
	$user = User::where('email', $request->email)->first();

	// 3. Vérifier si l'utilisateur existe et si le mot de passe correspond
	if (! $user || ! Hash::check($request->password, $user->password)) {
	    return response()->json(['message' => 'Identifiants incorrects'], 401);
	}

	// 4. Identifier et vérifier si le device (n° de série) est bien lié à cet utilisateur
	$deviceExistsForUser = $user->linkedDevices()
	    ->whereHas('device', function ($query) use ($request) {
	        $query->where('serial', $request->device);
	    })
	    ->exists();

	if (! $deviceExistsForUser) {
	    return response()->json([
	        'message' => 'Cet appareil n\'est pas associé à votre compte.'
	    ], 403); // 403 Forbidden : Authentifié, mais pas le droit d'accéder à ce device
	}

	// 5. Créer le token Sanctum 
	// Petite astuce : au lieu de nommer le token avec l'email, on le nomme souvent avec le nom/numéro du device !
	$token = $user->createToken($request->device)->plainTextToken;

	// 6. Renvoyer le token au device
	return response()->json(['token' => $token], 200);
});

Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
    // Supprime le token actuel utilisé pour cette requête
    $request->user()->currentAccessToken()->delete();
    return response()->json(['message' => 'Token supprimé']);
});