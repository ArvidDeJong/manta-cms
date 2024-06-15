<?php

namespace Manta\Services;

use Illuminate\Support\Facades\File;

class Manta
{

    public static function config($name)
    {
        $path = app_path("Livewire/Manta/{$name}/{$name}config.json");

        if (!File::exists($path)) {
            $path = app_path("Livewire/Manta/{$name}/{$name}config_default.json");
            // throw new \Exception("Configuration file not found: $path");
        }

        $json = File::get($path);
        return json_decode($json, true);
    }
}
