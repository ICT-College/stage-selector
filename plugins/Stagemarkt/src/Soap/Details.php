<?php

namespace Stagemarkt\Soap;

use Cake\I18n\Time;
use Muffin\Webservice\WebserviceQuery;
use Stagemarkt\Model\Resource\AddressCompany;
use Stagemarkt\Model\Resource\Company;
use Stagemarkt\Model\Resource\ContactpersonCompany;
use Stagemarkt\Model\Resource\Position;
use Stagemarkt\Model\Resource\QualificationPart;
use Stagemarkt\Model\Resource\StudyProgram;
use Stagemarkt\Response\DetailsResponse;

class Details extends StagemarktService
{

    /**
     * @param array $conditions
     * @return DetailsResponse
     */
    public function details(array $conditions)
    {
        $parameters = [];

        if ($conditions['type'] === 'company') {
            $parameters['CodeLeerbedrijf'] = $conditions['id'];
        }
        if ($conditions['type'] === 'position') {
            $parameters['LeerplaatsId'] = $conditions['id'];
        }

        return $this->GeefDetails($parameters);
    }

    /**
     * {@inheritDoc}
     */
    public function testingUrl()
    {
        return 'http://wl-acc.stagemarkt.nl/webservices/whitelabel/ws_vacaturedetails.asmx?WSDL';
    }

    /**
     * {@inheritDoc}
     */
    public function liveUrl()
    {
        return 'http://wl-acc.stagemarkt.nl/webservices/whitelabel/ws_vacaturedetails.asmx?WSDL';
    }

    /**
     * {@inheritDoc}
     */
    public function resultProperty()
    {
        return 'GeefDetailsResult';
    }

    public function __call($function_name, $arguments)
    {
        $soapResponse = parent::__call($function_name, $arguments);

        $position = new Position([
            'id' => $arguments[0]['LeerplaatsId'],
            'company' => new Company([
                'id' => $soapResponse->CodeLeerbedrijf,
                'address' => new AddressCompany([
                    'address' => $soapResponse->Vestigingsadres->Straat,
                    'postcode' => $soapResponse->Vestigingsadres->Postcode,
                    'city' => $soapResponse->Vestigingsadres->Plaats,
                    'country' => $soapResponse->Vestigingsadres->Land,
                ], [
                    'markClean' => true,
                    'markNew' => false,
                ]),
                'correspondence_address' => new AddressCompany([
                    'address' => $soapResponse->Correspondentieadres->Straat,
                    'postcode' => $soapResponse->Correspondentieadres->Postcode,
                    'city' => $soapResponse->Correspondentieadres->Plaats,
                    'country' => $soapResponse->Correspondentieadres->Land,
                ], [
                    'markClean' => true,
                    'markNew' => false,
                ]),
                'condactperson' => new ContactpersonCompany([
                    'name' => $soapResponse->Contactpersoon->Naam,
                    'email' => $soapResponse->Contactpersoon->Email,
                    'telephone' => $soapResponse->Contactpersoon->Telefoonnummer,
                ], [
                    'markClean' => true,
                    'markNew' => false,
                ]),
                'name' => $soapResponse->Naam,
                'website' => @$soapResponse->WebsiteUrl,
                'email' => @$soapResponse->Email,
                'telephone' => @$soapResponse->Telefoonnummer,
                'branch' => @$soapResponse->Branche,
            ], [
                'markClean' => true,
                'markNew' => false,
            ]),
            'study_program' => new StudyProgram([
                'id' => $soapResponse->Opleidingen->Opleiding->Crebonummer,
                'description' => $soapResponse->Opleidingen->Opleiding->Omschrijving,
            ], [
                'markClean' => true,
                'markNew' => false,
            ]),
            'description' => ($soapResponse->Omschrijving) ? $soapResponse->Omschrijving : null,
            'start' => new Time($soapResponse->Startdatum, new \DateTimeZone('Europe/Amsterdam')),
            'end' => new Time($soapResponse->Einddatum, new \DateTimeZone('Europe/Amsterdam')),
        ], [
            'markClean' => true,
            'markNew' => false,
        ]);

        $qualificationParts = [];
        foreach ($soapResponse->Kwalificatieonderdelen->Kwalificatieonderdeel as $qualificationPart) {
            $index = substr($qualificationPart->Omschrijving, 0, 2);
            $description = $qualificationPart->Omschrijving;
            if (!is_numeric($index)) {
                $index = mt_rand(1, 9999);
            } else {
                $description = substr($description, 3);
            }

            $qualificationParts[(int) $index] = new QualificationPart([
                'type' => $qualificationPart->Type,
                'description' => $description
            ], [
                'markClean' => true,
                'markNew' => false,
            ]);
        }
        ksort($qualificationParts);

        $position['qualification_parts'] = array_values($qualificationParts);

        $response = new DetailsResponse();
        $response->setCode($soapResponse->Signaalcode)
            ->position($position);

        return $response;
    }

    public function execute(WebserviceQuery $query)
    {
        if ($query->action() !== WebserviceQuery::ACTION_READ) {
            throw new \BadMethodCallException;
        }

        $response = $this->details($query->conditions(), $query->getOptions());

        switch ($query->conditions()['type']) {
            case 'company':
                $entity = $response->company();

                break;
            case 'position':
                $entity = $response->position();

                break;
        }

        return new ResultSet([$entity], 1);
    }
}
