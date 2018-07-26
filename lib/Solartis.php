<?php
namespace App\Solartis;

use Ixudra\Curl\Facades\Curl;

class API
{
    private $APIURL = "https://travelapihk.solartis.net/DroolsV4_2/DroolsService/FireEventV2";

    /**
     * Get the rate
     * @param Array $data contains the data needed for the info
     * @param Array $travelers contains an array of the traveleres
     * @return Response an object which contains the response
     */
    public function get_rating(array $data, array $travelers)
    {
        $JSON = '{
            "ServiceRequestDetail": {
              "ServiceRequestVersion": "1.0",
              "ServiceResponseVersion": "1.0",
              "OwnerId": "15",
              "ResponseType": "JSON",
              "RegionCode": "US",
              "Token": "' . env('API_KEY_SOLARTIS_TRAVEL') . '",
              "UserName": "travelagent",
              "LanguageCode": "en"
            },
            "QuoteInformation": {
              "ProductID": "619",
              "ProductVerID": "706",
              "ProductNumber": "ILT",
              "ProductVerNumber": "1.0",
              "ProducerCode": "86201",
              "OwnerId": "15",
              "PlanCode": "' . $data['plancode'] . '",
              "PlanName": "' . $data['planname'] . '",
              "DepartureDate": "' . $data['departuredate'] . '",
              "ReturnDate": "' . $data['returndate'] . '",
              "DepositDate": "' . $data['departuredate'] . '",
              "DestinationCountry": "' . $data['destinationcountry'] . '",
              "PolicyEffectiveDate": "' . $data['departuredate'] . '",
              "RentalStartDate" : "' . $data['departuredate'] . '",
              "RentalEndDate" : "' . $data['departuredate'] . '",
              "RentalLimit" : "35000",
              "NumberOfRentalCars" : "1",
              "TripCancellationCoverage": "With Trip Cancellation",
              "StateCode": "' . $data['statecode'] . '",
              "QuoteType": "New Business",
              "EventName": "InvokeRatingV2"
            }
          }';
        $j = json_decode($JSON);
        $j->QuoteInformation->TravelerList = $travelers;
        $j = json_encode($j);
        $response = Curl::to($this->APIURL)
            ->withHeader('Token: ' . env('API_KEY_SOLARTIS_TRAVEL'))
            ->withHeader('EventName: InvokeRatingV2')
            ->withData($j)
            ->post();
        return json_decode($response, true);
    }

    /**
     * Create a customer
     * @param Array $data contains the data needed for the info
     * @param Array $travelers contains an array of the traveleres
     * @return Response an object which contains the response
     */
    public function create_customer(array $data, array $travelers)
    {
        $JSON = '{
            "ServiceRequestDetail": {
              "ServiceRequestVersion": "1.0",
              "ServiceResponseVersion": "1.0",
              "OwnerId": "15",
              "ResponseType": "JSON",
              "RegionCode": "US",
              "Token": "' . env('API_KEY_SOLARTIS_TRAVEL') . '",
              "UserName": "travelagent",
              "LanguageCode": "en"
            },
            "CustomerInformation": {
              "ProductVerID": "706",
              "ProductID": "619",
              "ProductNumber": "ILT",
              "ProductVerNumber": "1.0",
              "ProducerCode": "86201",
              "OwnerId": "15",
              "PlanCode": "' . $data['plancode'] . '",
              "PlanName": "' . $data['planname'] . '",
              "DepartureDate": "' . $data['departuredate'] . '",
              "ReturnDate": "' . $data['returndate'] . '",
              "DepositDate": "' . $data['departuredate'] . '",
              "DestinationCountry": "' . $data['destinationcountry'] . '",
              "PolicyEffectiveDate": "' . $data['departuredate'] . '",
              "StateCode": "' . $data['statecode'] . '",
              "StateName": "' . $data['statename'] . '",
              "QuoteType": "New Business",
              "EventName": "CreateCustomer"
            }
          }';
        $j = json_decode($JSON);
        $j->CustomerInformation->TravelerList = $travelers;
        $j = json_encode($j);
        $response = Curl::to($this->APIURL)
            ->withHeader('Token: ' . env('API_KEY_SOLARTIS_TRAVEL'))
            ->withHeader('EventName: CreateCustomer')
            ->withData($j)
            ->post();
        return json_decode($response, true);
    }

    /**
     * Issue a policy
     * @param Array $data contains the data needed for the info
     * @return Response an object which contains the response
     */
    public function issue_policy($data)
    {
        $JSON = '{
            "ServiceRequestDetail": {
              "ServiceRequestVersion": "1.0",
              "ServiceResponseVersion": "1.0",
              "OwnerId": "15",
              "ResponseType": "JSON",
              "RegionCode": "US",
              "Token": "' . env('API_KEY_SOLARTIS_TRAVEL') . '",
              "UserName": "travelagent",
              "LanguageCode": "en"
            },
            "PolicyInformation": {
              "ProductVerID": "706",
              "ProductID": "619",
              "ProductNumber": "ILT",
              "ProductVerNumber": "1.0",
              "ProducerCode": "86201",
              "OwnerId": "15",
              "CustomerNumber": "' . $data["customernumber"] . '",
              "RoleID": "5",
              "RoleName": "Agent",
              "RoleType": "User",
              "EventName": "Pay_Issue",
              "CardNumber": "' . $data["cardnumber"] . '",
              "CVV": "' . $data["cvv"] . '",
              "ExpiryMonth": "' . $data["expmonth"] . '",
              "ExpiryYear": "' . $data["expyear"] . '",
              "PayerName": "' . $data["name"] . '",
              "PayerAddress1": "' . $data["address"] . '",
              "PayerCity": "' . $data["city"] . '",
              "PayerState": "' . $data["statecode"] . '",
              "PayerCountry": "' . $data["country"] . '",
              "PayerZipcode": "' . $data["zipcode"] . '",
              "PayerEmail": "' . $data["email"] . '",
              "PayerPhone": "' . $data["phone"] . '",
              "PaymentMethod": "Credit Card",
              "CardType": "MasterCard"
            }
          }';
        $response = Curl::to($this->APIURL)
            ->withHeader('Token: ' . env('API_KEY_SOLARTIS_TRAVEL'))
            ->withHeader('EventName: Pay_Issue')
            ->withData($JSON)
            ->post();
        return json_decode($response, true);
    }

    /**
     * Helper function to get the plancode
     * @param String $planname
     * @return Int PlanCode
     */
    public function plan_code(string $planname)
    {
        switch ($planname) {
            case "Air Ticket Protector":
                return 1;
                break;
            case "Classic Plus":
                return 2;
                break;
            case "Premier":
                return 3;
                break;
            case "Premier Annual":
                return 4;
                break;
            case "Basic Annual":
                return 9;
                break;
            case "Medical Only Annual":
                return 10;
                break;
            case "Renter's Collision":
                return 11;
                break;
        }
    }
}
