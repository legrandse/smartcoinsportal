<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail; // Si tu veux envoyer un email

class ContactController extends Controller
{
    public function send(Request $request)
    {
        // 1. Validation des données
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        // 2. Traitement (Envoi d'e-mail, sauvegarde en bdd, etc.)
        // Exemple basique d'envoi d'e-mail (pense à configurer ton .env pour le mail)
        
        Mail::raw($validated['message'], function ($message) use ($validated) {
            $message->to('ton-email@example.com')
                    ->subject($validated['subject'])
                    ->from($validated['email'], $validated['name']);
        });
        

        // 3. Réponse JSON (nécessaire car le script JS du template attend du JSON/du texte pour afficher .sent-message)
        return back()->with('success', 'Your message has been sent. Thank you!');
    }
}
