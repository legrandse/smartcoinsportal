<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Settings;
use App\Models\SettingsSecondary;

class SyncSettingsJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Exemple : Synchroniser la table `users`
        Settings::chunk(100, function ($settings) {
            foreach ($settings as $setting) {
                SettingsSecondary::updateOrCreate(
                    ['id' => $setting->id], // Correspondance par ID
                    [
                        'name' => $setting->name,
                        'value' => $setting->value,
                        
                    ]
                );
            }
        });
    }
}
