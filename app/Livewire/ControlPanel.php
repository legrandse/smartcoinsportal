<?php

namespace App\Livewire;

use Livewire\Component;

use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Rule;
use Livewire\Attributes\On;

use App\Jobs\SyncSettingsJob;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use App\Models\Settings;

class ControlPanel extends Component
{
	use WithFileUploads;
	
	
	
	#[Rule('image|max:1024')] // 1MB Max
    public $photo;
    	
	public $payconiqToggle;
	public $stripeToggle;
	public $paypalToggle;
	public $cashToggle;
	
	#[Validate('required')]
	public $magasin;
	
	#[Validate('required')]
	public $parity;
	
	#[Validate('required')]
	public $tokenArray;
	
	#[Validate('required')]
	public $ngrok;
	
	public $noteReaderUrl = 'https://smartcoinspython.ngrok.app';
	public $apiKey;



	public function mount() {
		
		$this->apiKey = config('services.noteader.key');

		
		
		$this->payconiqToggle = (bool) Settings::where('name', 'payconiq')->value('value');
		$this->stripeToggle = (bool) Settings::where('name', 'stripe')->value('value');
		$this->paypalToggle = (bool) Settings::where('name', 'paypal')->value('value');
		$this->cashToggle = (bool) Settings::where('name', 'cash')->value('value');
		
		$this->magasin = Settings::find(8)->value;
        
        $this->parity = Settings::find(5)->value;
        
        $this->tokenArray = Settings::find(6)->value;
        
        $this->ngrok = Settings::find(7)->value;
		
	}
	
	
	
	
	
	public function validateData(){

            $this->validate([
                'payconiqToggle' => 'required',
                'stripeToggle' => 'required',
                'paypalToggle' => 'required',
                'cashToggle' => 'required',
            ]);
        }
	
	/*
	public function savePayconiqValue() {
		//$this->validateData();
		$this->payconiqToggle = !$this->payconiqToggle;
		$settings = Settings::find(1);
        $settings->value = $this->payconiqToggle ? $this->payconiqToggle : 0;
        $settings->save();
	}
	
	public function saveStripeValue() {
		//$this->validateData();
		$this->stripeToggle = !$this->stripeToggle;
		$settings = Settings::find(2);
        $settings->value = $this->stripeToggle ? $this->stripeToggle : 0;
        $settings->save();
	}
	
	public function savePaypalValue() {
		//$this->validateData();
		$this->paypalToggle = !$this->paypalToggle;
		$settings = Settings::find(3);
        $settings->value = $this->paypalToggle ? $this->paypalToggle : 0;
        $settings->save();
	}
	
	public function saveCashValue() {
		//$this->validateData();
		$this->cashToggle = !$this->cashToggle;
		$settings = Settings::find(4);
        $settings->value = $this->cashToggle ? $this->cashToggle : 0;
        $settings->save();
	}*/

	public function toggleSetting($property)
	{
		    // Map des propriétés Livewire => noms dans la DB
			$propertyToSettingName = [
				'payconiqToggle' => 'payconiq',
				'stripeToggle' => 'stripe',
				'paypalToggle' => 'paypal',
				'cashToggle' => 'cash',
			];
	
		if (!array_key_exists($property, $propertyToSettingName) || !property_exists($this, $property)) {
			return;
		}
	
		$newValue = !$this->$property;
		$this->$property = $newValue;
	
		$settingName = $propertyToSettingName[$property];
		$settings = Settings::where('name', $settingName)->first();
	
		if ($settings) {
			$settings->value = $newValue;
			$settings->save();
		}
	
		// Appelle ta logique Livewire existante
		$this->updated($settingName, $newValue);
	}
	
	#[On('updateInput')] 
	public function updated($name,$value) {
		//dd($name,$value);
		$settings = Settings::where('name', $name)->first();
		$settings->update([
	        
	        	'value' => $value
	        
	        ]);
        
        
        //$job = SyncSettingsJob::dispatch();
        $response =  Http::post('https://smartcoins.ngrok.app/api/notify-settings-update', [
		    'value' => 'settings',
		    'data' => [
                'name' => $settings->name,
                'value' => $settings->value,
            ]  // Ajout en tant que paramètre GET
		]);
       // dd($response->status(), $response->body());
              
        
        
        //Log::info("SyncJob : " . $job);
        session()->flash('success', 'successfully updated.');
	}
	

	
	
	
	//store image
	public function save()
    {
        $this->photo->storeAs('public/logo/logo.jpg');
        $this->redirect('/settings'); 
    }
    
    
	public function shutdownRaspberry()
	{
		Http::post($this->noteReaderUrl . '/shutdown', [						   
						
						'command' => 'shutdown',					
						
							
						]);
		//$service = new LaravelPython();
		//dd($service);
		//$result = $service->run('/home/pi/rpiWebServer/shutdown.py');
		//\Python::run('/home/pi/rpiWebServer/shutdown.py');
	}
	
	public function resetNotereader()
	{
		//dd('hello');
		$baseUrl = $this->noteReaderUrl . '/reset';
		try {
			$response = Http::withToken($this->apiKey)->post($baseUrl);
		} catch (\Exception $e) {
			\Log::error("Erreur lors de la vérification du lecteur de billets : " . $e->getMessage());
			return redirect()->back()->with('error', 'Lecteur de billets - Une erreur est survenue : ' . $e->getMessage());
	    	}
			session()->flash('success', 'successfully updated.');


			//$this->redirect('/settings'); 

	}

	public function collectNotereader()
	{
		//dd('hello');
		$baseUrl = $this->noteReaderUrl . '/collect';
		try {
			$response = Http::withToken($this->apiKey)->post($baseUrl);
		} catch (\Exception $e) {
			\Log::error("Erreur lors de la vérification du lecteur de billets : " . $e->getMessage());
			return redirect()->back()->with('error', 'Lecteur de billets - Une erreur est survenue : ' . $e->getMessage());
	    	}
			session()->flash('success', 'successfully updated.');


			//$this->redirect('/settings'); 

	}


	public function stackNote()
	{
		//dd('hello');
		$baseUrl = $this->noteReaderUrl . '/stack';
		try {
			$response = Http::withToken($this->apiKey)->post($baseUrl);
		} catch (\Exception $e) {
			\Log::error("Erreur lors de la vérification du lecteur de billets : " . $e->getMessage());
			return redirect()->back()->with('error', 'Lecteur de billets - Une erreur est survenue : ' . $e->getMessage());
	    	}
			session()->flash('success', 'successfully updated.');


			//$this->redirect('/settings'); 

	}

	
    public function render()
    {
    	
    	
        return view('livewire.control-panel');
    }
}
