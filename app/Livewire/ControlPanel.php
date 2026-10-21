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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

	#[Rule('image|max:1024')] // 1MB Max
    public $slider;
    
    public $sliders = [];
    	
	public $payconiqToggle;
	public $stripeToggle;
	public $paypalToggle;
	public $cashToggle;
	
	#[Validate('required|integer')]
	public $magasin;
	
	#[Validate('required|numeric|min:1')]
	public $parity;
	
	#[Validate(['required', 'string', 'regex:/^\d+(,\d+)*$/'])]
	public $tokenArray;
	
	#[Validate('required|url')]
	public $ngrok;
	
	//public $deviceUrl = 'https://smartcoinspython.ngrok.app';
	//public $noteReaderUrl = 'https://smartcoinsnotereader.ngrok.app';
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
        
        $this->loadHopperLevels();
        
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
	    $localToggles = ['collectHopperToggle', 'collectToggle', 'stackToggle', 'photo', 'slider', 'sliders'];

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

	    Http::withToken($this->device->serial)->post($this->ngrok.'/api/notify-settings-update', [
	        'value' => 'settings',
	        'data' => [
	            'device'=> $settings->device,
	            'name'  => $settings->name,
	            'value' => $settings->value,
	        ]
	    ]);

	    session()->flash('success', 'successfully updated.');
	}

	//use in mount() and Echo to live refresh
	public function loadHopperLevels(){

		$this->hopperLevels = HopperLevel::where('device',$this->device->serial)
								->orderBy('value_eur')
								->get();

	}
	
	
	
	//store image
	public function save()
    {
        // 1. Sauvegarder l'image sur le disque local du portail
		// (Dans storage/app/public/1/logo/logo.jpg)
		$this->photo->storeAs($this->linkedDevice->device_id .'/logo', 'logo.jpg', 'public');

		// 2. Récupérer le contenu binaire du fichier via la façade Storage
		$fileContent = Storage::disk('public')->get($this->linkedDevice->device_id .'/logo/logo.jpg');

		// 3. Envoyer le contenu directement à l'application
		$response = Http::withToken($this->device->serial)
			->attach(
				'attachment',    // Le nom de la clé côté réception
				$fileContent,    // Le contenu brut de l'image
				'logo.jpg'       // Le nom du fichier transmis
			)
			->post($this->ngrok . '/api/logo');
		
		//$this->redirect('/linked-devices/1/edit');
    }

	public function saveSlider()
	{
		// 1. Valider que le fichier est bien présent et est une image
		$this->validate([
			'slider' => 'required|image|max:2048',
		]);

		// Générer un nom unique avec un timestamp pour éviter les conflits
		$ext = $this->slider->getClientOriginalExtension();
		$fileName = 'slider_' . time() . '.' . $ext;

		// 2. Sauvegarder l'image sur le disque local du portail
		// (Dans storage/app/public/slider/nom_du_fichier.jpg)
		// Note : avec le disque 'public', storeAs prend ('dossier', 'nom', 'disque')
		$this->slider->storeAs($this->linkedDevice->device_id .'/slider', $fileName, 'public');

		// 3. Récupérer le contenu binaire du fichier via la façade Storage
		$fileContent = Storage::disk('public')->get($this->linkedDevice->device_id .'/slider/' . $fileName);

		// 4. Envoyer le contenu directement à l'application distante via l'API
		$response = Http::withToken($this->device->serial)
			->attach(
				'attachment',    // Le nom de la clé côté réception (à adapter si besoin)
				$fileContent,    // Le contenu brut de l'image
				$fileName        // Le nom du fichier transmis
			)
			->post($this->ngrok . '/api/slider');

		// 5. Nettoyage
		$this->reset('slider');
		
		// Optionnel : Gérer la réponse ou lever une erreur si l'API échoue
		if ($response->failed()) {
			// Gérer l'erreur d'envoi si nécessaire
			\Log::error('Échec de l\'envoi du slider : ' . $response->body());
		}

		// $this->dispatch('slider-updated'); // pour rafraîchir le carrousel
	}
    
    public function getSlidersProperty()
	{
	    $files = \Storage::files('public/slider');

	    // Transformer en URL accessibles
	    return collect($files)->map(fn($file) => \Storage::url($file));
	}
	
	public function deleteSlider($path)
	{
	    // 1. Extraire uniquement le nom du fichier (ex: "slider_1711234567.jpg")
		$fileName = basename(parse_url($path, PHP_URL_PATH));

		// 2. Extraire le chemin relatif pour la suppression locale sur le disque public
		$relativePath = $this->linkedDevice->device_id. '/slider/' . $fileName;

		// 3. Supprimer en local si le fichier existe
		if (Storage::disk('public')->exists($relativePath)) {
			Storage::disk('public')->delete($relativePath);

			// 4. Envoyer le nom du fichier dans le payload
			$response = Http::withToken($this->device->serial)->post($this->ngrok . '/api/slider/delete', [
				'filename' => $fileName,
				// ou 'path' => $relativePath (selon ce que votre API attend)
			]);

			if ($response->failed()) {
				\Log::error('Échec de la suppression distante du slider : ' . $response->body());
			}
		}

	   
	}


    
    
	public function shutdownRaspberry()
	{
		Http::withToken($this->device->serial)->post($this->ngrok . '/api/shutdown', [						   
						
						'command' => 'shutdown',					
						
						]);
		
	}

	public function rebootRaspberry()
	{
		Http::withToken($this->device->serial)->post($this->ngrok . '/api/reboot', [						   
						
						'command' => 'reboot',					
						
						]);
		
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
	            
				$response = Http::withToken($this->device->serial)->post($baseUrl);
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

		try {
			$response = Http::withToken($this->device->serial)->post(
				$this->ngrok . '/api/stack-hopper',
				[
					'quantity' => $this->quantity,
					'denomination' => $this->denomination,
				]
			);

			if ($response->failed()) {
				throw new \Exception(
					"Erreur API : " . $response->body()
				);
			}

			session()->flash('success', 'Requête envoyée avec succès !');

		} catch (\Exception $e) {

			Log::error("Erreur lors de l'appel stack-hopper : " . $e->getMessage());

			session()->flash(
				'error',
				'Une erreur est survenue : ' . $e->getMessage()
			);
		}
	}
	
	

	public function collectNotereader()
	{
		$this->stackToggle = false;
		//$this->collectToggle = !$this->collectToggle;
			
		if($this->collectToggle){
			try {
	            
				$response = Http::withToken($this->device->serial)->post($this->ngrok .'/api/collect-notereader');
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


/*	public function stackNote()
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
		
		
		
	}*/

	public function stackNote()
	{
		if ($this->stackToggle) {
        $this->collectToggle = false;
    }
		if($this->stackToggle){
			try {
				$response = Http::withToken($this->device->serial)->post($this->ngrok . '/api/stack-note');
				
			} catch (\Exception $e) {
				\Log::error("Erreur lors de la vérification du lecteur de billets : " . $e->getMessage());
				return redirect()->back()->with('error', 'Lecteur de billets - Une erreur est survenue : ' . $e->getMessage());
		    	}
		}
		else {
			try {
				$response = Http::withToken($this->device->serial)->post($this->noteReaderUrl . '/disable');
				
			} catch (\Exception $e) {
				\Log::error("Erreur lors de la vérification du lecteur de billets : " . $e->getMessage());
				return redirect()->back()->with('error', 'Lecteur de billets - Une erreur est survenue : ' . $e->getMessage());
		    	}
			
		}
		
		session()->flash('success', 'successfully updated.');
	}


	//refresh hopperLevels select in the view
	#[On('echo:hopperLevels,HopperLevelsUpdated')]
	public function refreshHopperLevels()
	{
	    $this->loadHopperLevels();
		

	}
	
	
	//permet de rafraichir la table avec un private channel
	/*public function getListeners()
	{
	    $user = auth()->user();
	    $listeners = [];

	    // On parcourt les appareils liés pour créer un écouteur par canal privé
	    foreach ($user->linkedDevices as $linked) {
	        $serial = $linked->device->serial;
	        
	        // Syntaxe : echo-private:{canal},{événement}
	        // Sans broadcastAs, l'événement est le namespace complet précédé d'un point
	        $listeners["echo-private:transaction.{$serial},.App\Events\TransactionsListener"] = 'refreshHopperLevels';
	    }

	    return $listeners;
	}
*/



	
    public function render()
    {
	   try {
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
	        }
			$files = Storage::disk('public')->files($this->linkedDevice->device_id.'/slider');
    
    		// On génère l'URL publique correcte
    		$this->sliders = collect($files)->map(fn($file) => Storage::disk('public')->url($file))->toArray();

			




	        return view('livewire.control-panel');
    }
}
