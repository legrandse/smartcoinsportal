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
use App\Models\HopperLevel;
use App\Models\LinkedDevices;

class ControlPanel extends Component
{
	use WithFileUploads;
	
	public $device;
	public $linkedDevice;
	
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
	public $collectHopperToggle = false;
	public $stackHopperToggle = false;
	
	public $apiKey;

	public $denomination; // ex: 0.50
    public $quantity = 1; // ex: 3
    

    public $hopperLevels = [];

	public function mount($device) {
		
		$this->apiKey = config('device.key');
		
				
		$this->device = Devices::find($device);
		
		$this->linkedDevice = LinkedDevices::where('device_id',$this->device->id)->first();
	
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
        
        $this->hopperLevels = HopperLevel::orderBy('value_eur')->get();
        
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
	    // 1. Liste des propriétés locales qu'on ne veut PAS envoyer à l'API de configuration
	    $localToggles = ['collectHopperToggle', 'collectToggle', 'stackToggle'];

	    if (in_array($name, $localToggles)) {
	        // On arrête la fonction ici pour ces propriétés, Livewire aura quand même
	        // mis à jour la variable PHP, ce qui est parfait pour vos méthodes d'action.
	        return;
	    }

	    // 2. Votre logique existante reste inchangée pour le reste (magasin, parity, ngrok...)
	    $settings = Settings::where('device', $this->device->serial)
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
		$baseUrl = $this->ngrok . '/api/reset';
		try {
			$response = Http::withToken($this->apiKey)->post($baseUrl);
		} catch (\Exception $e) {
			\Log::error("Erreur lors de la vérification du lecteur de billets : " . $e->getMessage());
			return redirect()->back()->with('error', 'Lecteur de billets - Une erreur est survenue : ' . $e->getMessage());
	    	}
			session()->flash('success', 'successfully updated.');


			//$this->redirect('/settings'); 

	}
	
	public function collectHopper()
	{
		
		$baseUrl = $this->ngrok . '/api/collect-hopper';
		if($this->collectHopperToggle){
		
			try {
	            
				$response = Http::withToken($this->apiKey)->post($baseUrl);
				session()->flash('success', 'successfully updated.');
				
			} catch (\Exception $e) {
				\Log::error("Erreur lors de la commande collecte du lecteur de billets : " . $e->getMessage());
				$this->collectHopperToggle = false;
				return redirect()->back()->with('error', 'Lecteur de billets - Une erreur est survenue : ' . $e->getMessage());
		    	}
		}


			//$this->redirect('/settings'); 

	}
	
	
	public function stackHopper()
    {
       $this->validate([
            'quantity' => 'required|integer|min:1',
            'denomination' => 'required|numeric|min:0.1',
        ]);

		if($this->stackHopperToggle){
	        try {
	            $response = Http::withToken($this->apiKey)->post($this->ngrok . '/api/stack-hopper', [
	                'amount' => $this->quantity,
	                'denomination' => $this->denomination,
	            ]);

	            if ($response->failed()) {
	                throw new \Exception("Erreur API : " . $response->body());
	            }

	            session()->flash('success', 'Requête envoyée avec succès !');
	        } catch (\Exception $e) {
	            Log::error("Erreur lors de la vérification du lecteur de billets : " . $e->getMessage());
	            session()->flash('error', 'Une erreur est survenue : ' . $e->getMessage());
	        }
	    
		}    
	        
    }
	
	

	public function collectNotereader()
	{
		$this->stackToggle = false;
		//$this->collectToggle = !$this->collectToggle;
			
		if($this->collectToggle){
			try {
	            
				$response = Http::withToken($this->apiKey)->post($this->ngrok .'/api/collect-notereader');
				session()->flash('success', 'successfully updated.');
			
			} catch (\Exception $e) {
				\Log::error("Erreur lors de la fonction collect du lecteur de billets : " . $e->getMessage());
				return redirect()->back()->with('error', 'Lecteur de billets - Une erreur est survenue : ' . $e->getMessage());
		    	}
					
		}
		/*else {
			try {
				$response = Http::withToken($this->apiKey)->post($this->noteReaderUrl . '/disable');
				
			} catch (\Exception $e) {
				\Log::error("Erreur lors de la vérification du lecteur de billets : " . $e->getMessage());
				return redirect()->back()->with('error', 'Lecteur de billets - Une erreur est survenue : ' . $e->getMessage());
		    	}
			
		}*/
		
		
	}


	public function stackNote()
	{
		
		$this->collectToggle = false;
		//$this->stackToggle = !$this->stackToggle;
		//dd($this->stackToggle);
		if($this->stackToggle){
			try {
				$response = Http::withToken($this->apiKey)->post($this->ngrok . '/api/stack-note');
				session()->flash('success', 'successfully updated.');
				
			} catch (\Exception $e) {
				\Log::error("Erreur lors de la vérification du lecteur de billets : " . $e->getMessage());
				return redirect()->back()->with('error', 'Lecteur de billets - Une erreur est survenue : ' . $e->getMessage());
		    	}
		}
		
		
		
	}

	
    public function render()
    {
	 /*   try {
	            // On tente d'appeler l'URL avec un timeout court (ex: 2 secondes)
	            // pour ne pas bloquer le chargement de la page inutilement
	            $response = Http::timeout(10)->get($this->ngrok);
				//dd($response);
	            if ($response->failed()) {
	                //return view('livewire.device-offline');
	                abort(404, "Le périphérique ne répond pas.");
	            }

	            return view('livewire.control-panel');

	        } catch (\Exception $e) {
	            // En cas de timeout ou de problème DNS, on bascule sur la vue d'erreur
	            //return view('livewire.device-offline');
	            abort(404);
	        }*/
	        return view('livewire.control-panel');
    }
}
