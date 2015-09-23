<?php

namespace Stagemarkt\Soap;

use Cake\Utility\Hash;
use Stagemarkt\Entity\Accreditation;
use Stagemarkt\Entity\AddressCompany;
use Stagemarkt\Entity\Company;
use Stagemarkt\Entity\Position;
use Stagemarkt\Entity\StudyProgram;
use Stagemarkt\Response\SearchResponse;
use Stagemarkt\ResultSet;
use Stagemarkt\Webservice;
use Stagemarkt\WebserviceQuery;

/**
 * Class Search
 * @package Stagemarkt\Soap
 *
 * @method SearchResponse Zoeken(array $parameters)
 */
class Search extends SoapClient implements Webservice
{

    public function search(array $conditions, array $options = [])
    {
        $defaultOptions = [
            'limit' => 10,
            'page' => 1
        ];
        $options = array_merge($defaultOptions, $options);

        $parameters = [
            'AantalResultatenPerPagina' => $options['limit'],
            'Pagina' => $options['page'],
            'ZoekInDeBuurt' => true
        ];

        switch ($conditions['type']) {
            case 'company':
                $parameters += [
                    'LeerplaatsErkenningAanduiding' => 'E'
                ];

                if (isset($conditions['id'])) {
                    $parameters['CodeLeerbedrijf'] = $conditions['id'];
                }

                break;
        }

        return $this->Zoeken($parameters);
    }

    /**
     * {@inheritDoc}
     */
    public function testingUrl()
    {
        return 'http://wl-acc.stagemarkt.nl/webservices/whitelabel/ws_whitelabelzoekenv03.asmx?WSDL';
    }

    /**
     * {@inheritDoc}
     */
    public function liveUrl()
    {
        return 'http://wl-acc.stagemarkt.nl/webservices/whitelabel/ws_whitelabelzoekenv03.asmx?WSDL';
    }

    /**
     * {@inheritDoc}
     */
    public function resultProperty()
    {
        return 'ZoekenResult';
    }

    public function __call($function_name, $arguments)
    {
        $soapResponse = parent::__call($function_name, $arguments);

        $positions = [];
        $companies = [];
        if (isset($soapResponse->Resultaten->Resultaat)) {
            $results = $soapResponse->Resultaten->Resultaat;
            if (!is_array($results)) {
                $results = [$results];
            }

            foreach ($results as $result) {
                if (isset($result->LeerplaatsId)) {
                    $position = new Position([
                        'id' => $result->LeerplaatsId,
                        'company' => new Company([
                            'id' => $result->CodeLeerbedrijf,
                            'address' => new AddressCompany([
                                'address_line' => $result->Vestigingsadres->Straat,
                                'postcode' => $result->Vestigingsadres->Postcode,
                                'city' => $result->Vestigingsadres->Plaats,
                                'country' => $result->Vestigingsadres->Land,
                            ], [
                                'markClean' => true,
                                'markNew' => false,
                            ]),
                            'name' => $result->LeerbedrijfNaam
                        ], [
                            'markClean' => true,
                            'markNew' => false,
                        ]),
                        'study_program' => new StudyProgram([
                            'id' => $result->Opleidingen->Opleiding->Crebonummer,
                            'description' => $result->Opleidingen->Opleiding->Omschrijving,
                        ], [
                            'markClean' => true,
                            'markNew' => false,
                        ]),
                        'learning_pathway' => $result->Leerweg,
                        'kind' => $result->LeerplaatsSoort,
                        'description' => $result->VacatureLeerplaatsOmschrijving,
                        'amount' => $result->LeerplaatsAantal
                    ], [
                        'markClean' => true,
                        'markNew' => false,
                    ]);

                    $positions[] = $position;
                } else {
                    if (isset($companies[$result->CodeLeerbedrijf])) {
                        $companies[$result->CodeLeerbedrijf]->accreditation[] = new Accreditation([
                            'study_program_id' => $result->Erkenning->Crebonummer,
                        ], [
                            'markClean' => true,
                            'markNew' => false,
                        ]);
                        continue;
                    }

                    $company = new Company([
                        'id' => $result->CodeLeerbedrijf,
                        'address' => new AddressCompany([
                            'address_line' => $result->Vestigingsadres->Straat,
                            'postcode' => $result->Vestigingsadres->Postcode,
                            'city' => $result->Vestigingsadres->Plaats,
                            'country' => $result->Vestigingsadres->Land,
                        ], [
                            'markClean' => true,
                            'markNew' => false,
                        ]),
                        'accreditation' => [
                            new Accreditation([
                                'study_program_id' => $result->Erkenning->Crebonummer,
                            ], [
                                'markClean' => true,
                                'markNew' => false,
                            ])
                        ],
                        'name' => $result->LeerbedrijfNaam,
                    ], [
                        'markClean' => true,
                        'markNew' => false,
                    ]);

                    $companies[$company->id] = $company;
                }
            }

            $companies = array_values($companies);
        }

        $response = new SearchResponse();
        $response->setCode($soapResponse->Signaalcode)
            ->positions($positions)
            ->companies($companies)
            ->total($soapResponse->AantalResultatenTotaal);

        return $response;
    }

    public function execute(WebserviceQuery $query)
    {
        if ($query->action() !== WebserviceQuery::ACTION_READ) {
            throw new \BadMethodCallException;
        }

        $response = $this->search($query->conditions(), $query->getOptions());

        switch ($query->conditions()['type']) {
            case 'company':
                $entities = $response->companies();

                break;
        }

        return new ResultSet($entities, $response->total());
    }
}
