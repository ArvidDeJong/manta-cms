<?php

namespace Manta\Traits;

use GuzzleHttp\Client;

trait MantaMapsTrait
{

    // Google maps
    public ?float $DEFAULT_LATITUDE = null;
    public ?float $DEFAULT_LONGITUDE = null;
    public ?int $GOOGLE_MAPS_ZOOM = null;
    public bool $maps_set_center = false;
    public ?string $maps_id = 'default';
    public array $markers = [];
    public ?string $address = null;

    public function getByAddress()
    {
        $this->getCoordinates($this->address);
    }

    function getCoordinates($address = null)
    {
        if ($address == null) {
            $this->dispatch('toastr:error', ['message' => 'Je moet een adres opgeven']);
            return false;
        }

        $apiKey = env('GOOGLE_KEY_PHP');  // Vervang met je eigen API-sleutel
        $client = new Client();
        $response = $client->request('GET', 'https://maps.googleapis.com/maps/api/geocode/json', [
            'query' => [
                'address' => $address,
                'key' => $apiKey
            ]
        ]);

        $data = json_decode($response->getBody(), true);


        if ($data['status'] == 'OK') {
            $location = $data['results'][0]['geometry']['location'];
            $this->DEFAULT_LATITUDE = $location['lat'];
            $this->DEFAULT_LONGITUDE =  $location['lng'];
        }
    }
}
