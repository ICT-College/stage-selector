<?php

namespace App\Model\Table;

use App\Database\Point;
use App\Database\Type\PointType;
use App\Model\Entity\Company;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Database\Expression\Comparison;
use Cake\Database\Expression\FunctionExpression;
use Cake\Database\Expression\QueryExpression;
use Cake\Database\Schema\Table as Schema;
use Cake\Datasource\ConnectionManager;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\Table;
use CvoTechnologies\Gearman\JobAwareTrait;

class CompaniesTable extends Table
{

    use JobAwareTrait;

    public $filterArgs = [
        'name' => [
            'type' => 'like'
        ],
        'address' => [
            'type' => 'value'
        ],
        'postcode' => [
            'type' => 'value'
        ],
        'city' => [
            'type' => 'value'
        ],
        'country' => [
            'type' => 'value'
        ],
        'radius' => [
            'type' => 'finder',
            'field' => 'coordinates',
            'finder' => 'radius'
        ]
    ];

    /**
     * {@inheritDoc}
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->addBehavior('Timestamp');
        $this->addBehavior('Search.Searchable');
    }

    /**
     * Finds a company in a specified radius
     *
     * @param Query $query Query to apply the conditions to
     * @param array $options A set of options to be used by the finder
     *
     * @return Query
     */
    public function findRadius(Query $query, array $options)
    {
        $addressComparisons = [];
        $otherComparisons = [];
        $addressComponents = [];

        foreach ($options as $option => $value) {
            switch ($option) {
                case 'address':
                case 'postcode':
                case 'city':
                case 'country':
                    $addressComponents[$option] = $value;

                    break;
            }
        }

        $query->clause('where')->traverse(function (Comparison $comparison) use (&$addressComparisons, &$otherComparisons) {
            switch ($comparison->getField()) {
                // Add address related conditions to $addressComparisons
                case 'Companies.address':
                case 'Companies.postcode':
                case 'Companies.city':
                case 'Companies.country':
                    $addressComparisons[] = $comparison;

                    break;

                // Add other conditions to $otherComparisons
                default:
                    $otherComparisons[] = $comparison;
            }
        });

        $address = implode(' ', $addressComponents);

        // Override the query conditions with the non address conditions
        $query->where($otherComparisons, [], true);

        $type = new PointType();

        if ($address) {
            $options['coordinates'] = $this->_addressToCoordinates($address);
        }

        if (empty($options['coordinates'])) {
            $query->where($addressComparisons);

            return $query;
        }

        $mainDatabase = ConnectionManager::get('main')->config()['database'];

        // Create the distance calculating condition
        $distanceComparison = new Comparison(new FunctionExpression($mainDatabase . '.DISTANCE', [
            $options['field']['field'] => 'literal',
            $type->toDatabase($options['coordinates'], $this->connection()->driver())
        ]), $options['radius'], null, '<=');

        // Add the address and distance conditions in a OR clause
        $query->where([
            'OR' => [
                $distanceComparison,
                $addressComparisons
            ]
        ]);

        return $query;
    }

    /**
     * Updates coordinates or details when needed
     *
     * @param Event $event The event that was dispatched
     * @param Company $company The company to check the details of
     *
     * @return void
     */
    public function afterSave(Event $event, Company $company)
    {
        $updateCoordinates = false;

        // This is a new company and the coordinates haven't been defined
        if (($company->isNew()) && (!$company->has('coordinates'))) {
            $updateCoordinates = true;
        }
        // This is a existing company and the address has changed
        if ((!$company->isNew()) && (($company->dirty('address')) || $company->dirty('postcode') || $company->dirty('city') || $company->dirty('country'))) {
            $updateCoordinates = true;
        }

        $detailFields = [
            'email',
            'website',
            'telephone'
        ];
        $detailsStored = false;
        foreach ($detailFields as $field) {
            if (!$company->has($field)) {
                continue;
            }
            if (!$company->get($field)) {
                continue;
            }

            $detailsStored = true;
        }

        if (!$detailsStored) {
            $this->updateDetails($company);
        }

        if ($updateCoordinates) {
            $this->updateCoordinates($company);
        }
    }

    /**
     * Start a background job to get the coordinates of a company
     *
     * @param Company $company The company to get the coordinates of
     *
     * @return void
     */
    public function updateCoordinates(Company $company)
    {
        $this->execute('company_coordinates', [
            'company_id' => $company->id,
            'datasource' => $this->connection()->configName()
        ]);
    }

    /**
     * Starts a background job to get more details of a company
     *
     * @param Company $company The company to get the details of
     *
     * @return void
     */
    public function updateDetails(Company $company)
    {
        $this->execute('company_details', [
            'company_id' => $company->id,
            'datasource' => $this->connection()->configName()
        ]);
    }

    /**
     * Do a geocode on the provided address to get the coordinates
     *
     * @param string $address The address to geocode
     *
     * @return Point|bool
     */
    protected function _addressToCoordinates($address)
    {
        $cacheKey = 'address-coordinates-' . md5($address);

        if (Cache::read($cacheKey) !== false) {
            return Cache::read($cacheKey);
        }

        $key = Configure::read('Google.maps.geocoding');

        $response = file_get_contents(
            'https://maps.google.com/maps/api/geocode/json?sensor=false&key=' . $key . '&address=' . urlencode($address)
        );

        $json = json_decode($response);
        if ($json->status !== 'OK') {
            return false;
        }

        $location = $json->results[0]->geometry->location;
        $point = new Point(
            $location->lat,
            $location->lng
        );

        Cache::write($cacheKey, $point);

        return $point;
    }

    /**
     * {@inheritDoc}
     */
    protected function _initializeSchema(Schema $schema)
    {
        $schema->columnType('coordinates', 'point');

        return $schema;
    }
}
