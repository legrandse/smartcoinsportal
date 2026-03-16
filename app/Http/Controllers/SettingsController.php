<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Settings;

class SettingsController extends Controller
{
    public function update(Request $request)
	{
	    $data = $request->validate([
	        'device' => 'required|string',
	        'value'  => 'required|integer',
	    ]);

	    $settings = Settings::where('device', $data['device'])
	        ->where('name', 'magasin')
	        ->firstOrFail();

	    $settings->update([
	        'value' => $data['value']
	    ]);

	    return response()->json([
	        'status' => 'success',
	        'value' => $settings->value
	    ]);
	}
}
