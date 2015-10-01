<?php

namespace App\Model\Entity;

use App\Database\Point;
use Cake\Cache\Cache;
use Cake\Network\Http\Client;
use Cake\ORM\Entity;
use Stagemarkt\Entity\Entity as StagemarktEntity;
use Stagemarkt\Entity\StagemarktBasedEntityTrait;

class Company extends Entity
{

    use StagemarktBasedEntityTrait {
        applyStagemarktEntity as protected _applyStagemarktEntity;
    }

    public function applyStagemarktEntity(StagemarktEntity $entity)
    {
        $this->set([
            'stagemarkt_id' => $entity->id,
            'name' => $entity->name,
            'address' => $entity->address->address,
            'postcode' => $entity->address->postcode,
            'city' => $entity->address->city,
            'coordinates' => $this->_addressToCoordinates($entity->address->address . ' ' . $entity->address->city)
        ]);

        if ($entity->address->country === 'Nederland') {
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
            'http://maps.google.com/maps/api/geocode/json', ['sensor' => false, 'address' => $address]
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
