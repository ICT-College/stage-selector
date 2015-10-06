<?php

namespace App\Model\Entity;

use App\Database\Point;
use Cake\Cache\Cache;
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

    public function applyResource(Resource $resource)
    {
        $coordinates = $this->_addressToCoordinates(
            $resource->address->address . ' ' . $resource->address->city
        );
        if (!$coordinates) {
            $coordinates = null;
        }

        $this->set([
            'stagemarkt_id' => $resource->id,
            'name' => $resource->name,
            'address' => $resource->address->address,
            'postcode' => $resource->address->postcode,
            'city' => $resource->address->city,
            'coordinates' => $coordinates
        ]);

        if ($resource->address->country === 'Nederland') {
            $this->set('country', 'NL');
        }
    }

    protected function _addressToCoordinates($address)
    {
        $cacheKey = 'address-coordinates-' . md5($address);

        if (Cache::read($cacheKey) !== false) {
            return Cache::read($cacheKey);
        }

        $client = new Client;
        $response = $client->get(
            'https://maps.google.com/maps/api/geocode/json', ['sensor' => false, 'address' => $address]
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
