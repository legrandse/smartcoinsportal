<?php

namespace App\Livewire;

use Livewire\Component;

//use Illuminate\Http\Request;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Rule;
use Livewire\Attributes\On;

use App\Jobs\SyncSettingsJob;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use App\Models\Devices;
use App\Models\Settings;

class ControlPanel extends Component
{
	use WithFileUploads;
	
	public $device;
	
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
	
	public $deviceUrl = 'https://smartcoinspython.ngrok.app';
	public $noteReaderUrl = 'https://smartcoinsnotereader.ngrok.app';
	public $collectToggle = false;
	public $stackToggle = false;
	
	public $apiKey;



	public function mount($device) {
		
		$this->apiKey = config('services.noteader.key');

				
		$this->device = $device;
		
		
		
		$this->payconiqToggle = (bool) Settings::where('device',$this->device->serial)
												->where('name', 'payconiq')->value('value');
		$this->stripeToggle = (bool) Settings::where('device',$this->device->serial)
												->where('name', 'stripe')->value('value');
		$this->paypalToggle = (bool) Settings::where('device',$this->device->serial)
												->where('name', 'paypal')->value('value');
		$this->cashToggle = (bool) Settings::where('device',$this->device->serial)
											->where('name', 'cash')->value('value');
		
		$this->magasin = Settings::where('device',$this->device->serial)
								  ->where('name', 'magasin')->value('value');
        
        $this->parity = Settings::where('device',$this->device->serial)
        						->where('name', 'parity')->value('value');
        
        $this->tokenArray = Settings::where('device',$this->device->serial)
        							->where('name', 'tokenArray')->value('value');
        
        $this->ngrok = Settings::where('device',$this->device->serial)
        						->where('name', 'ngrok')->value('value');
		
	}
	
	
	
	
	
	public function validateData(){

            $this->validate([
                'payconiqToggle' => 'required',
                'stripeToggle' => 'required',
                'paypalToggle' => 'required',
                'cashToggle' => 'required',
            ]);
        }
	
	

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
		$settings = Settings::where('device',$this->device->serial)
							->where('name', $settingName)->first();
	
		if ($settings) {
			$settings->value = $newValue;
			$settings->save();
		}
	
		// Appelle ta logique Livewire existante
		$this->updated($settingName, $newValue);
	}
	
	#[On('updateInput')] 
	public function updated($name, $value) 
	{
	    $settings = Settings::where('device',$this->device->serial)
	                        ->where('name', $name)
	                        ->first();

	    if ($settings) {
	        $settings->update(['value' => $value]);
	    } else {
	        $settings = Settings::create([
	            'device' => $this->device->serial,
	            'name'   => $name,
	            'value'  => $value,
	        ]);
	    }

	    Http::post($this->ngrok.'/api/notify-settings-update', [
	        'value' => 'settings',
	        'data' => [
	            'device'=> $settings->device,
	            'name'  => $settings->name,
	            'value' => $settings->value,
	        ]
	    ]);

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
		Http::post($this->deviceUrl . '/shutdown', [						   
						
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
		
		$this->stackToggle = false;
		
		if($this->collectToggle){
			try {
	            
				$response = Http::withToken($this->apiKey)->post($this->noteReaderUrl .'/collect');
				
			} catch (\Exception $e) {
				\Log::error("Erreur lors de la vérification du lecteur de billets : " . $e->getMessage());
				return redirect()->back()->with('error', 'Lecteur de billets - Une erreur est survenue : ' . $e->getMessage());
		    	}
					
		}
		
		session()->flash('success', 'successfully updated.');
	}


	public function stackNote()
	{
		
		$this->collectToggle = false;
		
		if($this->stackToggle){
			try {
				$response = Http::withToken($this->apiKey)->post($this->noteReaderUrl . '/enable', [
					
					'amount' => 0,
					'stacking' => true
				]);
				
			} catch (\Exception $e) {
				\Log::error("Erreur lors de la vérification du lecteur de billets : " . $e->getMessage());
				return redirect()->back()->with('error', 'Lecteur de billets - Une erreur est survenue : ' . $e->getMessage());
		    	}
				
	 
		}
		
		session()->flash('success', 'successfully updated.');
	}

	
    public function render()
    {
    	
    	
        return view('livewire.control-panel');
    }
}
