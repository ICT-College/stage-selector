<?php

namespace App\Model\Entity;

use App\Database\Point;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\Network\Http\Client;
use Cake\ORM\Entity;
use Muffin\Webservice\Model\Resource;
use Muffin\Webservice\Model\ResourceBasedEntityInterface;
use Muffin\Webservice\Model\ResourceBasedEntityTrait;

class Company extends Entity implements ResourceBasedEntityInterface
{

    use ResourceBasedEntityTrait {
        applyResource as protected _applyResource;
    }

    /**
     * {@inheritDoc}
     */
    public function applyResource(Resource $resource)
    {
        $properties = [
            'stagemarkt_id' => $resource->id,
            'name' => $resource->name,
            'address' => $resource->address->address,
            'postcode' => $resource->address->postcode,
            'city' => $resource->address->city
        ];
        switch ($resource->address->country) {
            case 'Nederland':
            case 'NEDERLAND':
                $properties['country'] = 'NL';
                break;
            case 'China':
                $properties['country'] = 'CN';
                break;
            case 'Spanje':
                $properties['country'] = 'ES';
                break;
            case 'Verenigd Koninkrijk':
                $properties['country'] = 'GB';
                break;
            case 'Turkije':
                $properties['country'] = 'TR';
                break;
            case 'Curaçao':
                $properties['country'] = 'CW';
                break;
            case 'Finland':
                $properties['country'] = 'FI';
                break;
            case 'Noorwegen':
                $properties['country'] = 'NO';
                break;
            case 'Aruba':
                $properties['country'] = 'AW';
                break;
            case 'India':
                $properties['country'] = 'IN';
                break;
            case 'Australië':
                $properties['country'] = 'AU';
                break;
            case 'Zuid-Afrika':
                $properties['country'] = 'ZA';
                break;
            default:
                Log::warning(__('Unknown country {0} for company', $resource->address->country));
        }

        foreach ($properties as $property => $value) {
            if ($this->get($property) === $value) {
                continue;
            }

            $this->set($property, $value);
        }
    }

    /**
     * Turns an address into a point class
     *
     * @param string $address Address to geocode
     * @return Point|bool Either a point class or false in case of an error
     */
    public function addressToCoordinates($address)
    {
        $cacheKey = 'address-coordinates-' . md5($address);

        if (Cache::read($cacheKey) !== false) {
            return Cache::read($cacheKey);
        }

        $key = Configure::read('Google.maps.geocoding');

        $client = new Client;
        $response = $client->get(
            'https://maps.google.com/maps/api/geocode/json', ['sensor' => false, 'address' => $address, 'key' => $key]
        );

        if (!$response->isOk()) {
            return false;
        }

        if ($response->json['status'] !== 'OK') {
            return false;
        }

        $location = $response->json['results'][0]['geometry']['location'];
        $point = new Point(
            $location['lat'],
            $location['lng']
        );

        Cache::write($cacheKey, $point);

        return $point;
    }
}
