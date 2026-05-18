<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LinkedDevices;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
    	$user = auth()->user();
    	$linkedDevice = $user->linkedDevices->device_id->first();
		dd($linkedDevice);
        return view('admin.index');
    }
    
    
    
    
    public function devicesSettings()
    {
        return view('admin.devices.settings');
    }
    
    
}
