<?php

use Illuminate\Support\Facades\File;

if (!function_exists('cms_config')) {
    function cms_config($name)
    {
        $path = app_path("../manta/config/{$name}.json");

        if (!File::exists($path)) {
            $path = app_path("../manta/config/{$name}_default.json");
            // throw new \Exception("Configuration file not found: $path");
        }

        $json = File::get($path);
        return json_decode($json, true);
    }
}

if (!function_exists('module_config')) {
    function module_config($name)
    {
        $path = app_path("/Livewire/Manta/{$name}/{$name}config.json");

        if (!File::exists($path)) {
            $path = app_path("/Livewire/Manta/{$name}/{$name}config_default.json");
            // throw new \Exception("Configuration file not found: $path");
        }

        $json = File::get($path);
        return json_decode($json, true);
    }
}


if (!function_exists('generatePassword')) {
    function generatePassword($length = 8, $includeNumbers = true, $includeLetters = true, $includeSpecialChars = true)
    {
        $numbers = '0123456789';
        $letters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $specialChars = '!@#$%^&*()_-=+;:,.?';

        $characters = '';
        $password = '';

        if ($includeNumbers) {
            $characters .= $numbers;
        }

        if ($includeLetters) {
            $characters .= $letters;
        }

        if ($includeSpecialChars) {
            $characters .= $specialChars;
        }

        if ($characters === '') {
            return 'Please enable at least one character type.';
        }

        $charactersLength = strlen($characters);
        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[rand(0, $charactersLength - 1)];
        }

        return $password;
    }
}


if (!function_exists('getRoutesManta')) {
    function getRoutesManta()
    {
        return collect(Illuminate\Support\Facades\Route::getRoutes())
            ->pluck('action.as')
            ->filter(function ($routeName) {
                return Illuminate\Support\Str::startsWith($routeName, 'website');
            })
            ->unique()
            ->sort()
            ->values()
            ->mapWithKeys(function ($routeName) {
                return [$routeName => $routeName];
            })
            ->toArray();
    }
}


if (!function_exists('translate')) {
    function translate(object $item, ?string $locale = null)
    {
        // Standaardtaal instellen als geen taal is opgegeven
        $locale = $locale ?: config('app.locale');

        // Oorspronkelijk en resultaat in een array initialiseren
        $translation = ['org' => $item, 'result' => $item];

        // Controleren of de opgegeven taal verschilt van de standaardtaal
        if ($locale != config('app.locale')) {
            // Probeer een vertaling te vinden voor het opgegeven item en taal
            $translatedItem = get_class($item)::where(['pid' => $item->id, 'locale' => $locale])->first();

            // Als een vertaling is gevonden, bijwerken van het resultaat
            if ($translatedItem) {
                $translation['result'] = $translatedItem;
            }
        }

        return $translation;
    }
}
