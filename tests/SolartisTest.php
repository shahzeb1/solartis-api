<?php

namespace Tests\Feature;

use Tests\TestCase;

class SolartisTest extends TestCase
{
    /**
     * Test to make sure we can get the rate
     *
     * @return void
     */
    public function test_get_rate()
    {
        $travelers = [];
        array_push($travelers, array("TravelerDOB" => "02/14/1990", "TravelCost" => "300"));
        array_push($travelers, array("TravelerDOB" => "02/14/1990", "TravelCost" => "300"));

        $data = ["departuredate" => "10/16/2018",
            "returndate" => "2/20/2019",
            "destinationcountry" => "France",
            "planname" => "Renter's Collision",
            "statecode" => "CA",
        ];

        $solartis = new \App\Solartis\API;
        $data['plancode'] = $solartis->plan_code($data['planname']);
        $res = $solartis->get_rating($data, $travelers);
        $this->assertEquals($res['RequestStatus'], "SUCCESS");
        $this->assertEquals($res['PremiumInformation']['PlanName'], $data['planname']);
        assert(array_key_exists('TotalGrossPremium', $res['PremiumInformation']));
    }

    /**
     * Test to make sure we can successfully create a
     * customer for Solartis
     *
     * @return void
     */
    public function test_create_customer()
    {

        $data = [
            "departuredate" => "10/16/2018",
            "returndate" => "2/20/2019",
            "destinationcountry" => "France",
            "planname" => "Air Ticket Protector",
            "statecode" => "CA",
            "statename" => "California",
            "plancode" => "1",
        ];

        // Set up the travelers
        $travelers = [];
        $faker = \Faker\Factory::create();
        foreach (range(1, 2) as $index) {
            $traveler = [];
            $traveler['TravelerDOB'] = $faker->date($format = 'm/d/Y', $max = '-12 years');
            $traveler['TravelCost'] = (string) 300;
            $traveler['FirstName'] = $faker->firstName();
            $traveler['LastName'] = $faker->lastName();
            $traveler['AddressLine1'] = $faker->streetAddress();
            $traveler['City'] = $faker->city();
            $traveler['State'] = $faker->state();
            $traveler['StateCode'] = $faker->stateAbbr();
            $traveler['Country'] = "United States";
            $traveler['Zipcode'] = $faker->postcode();
            $traveler['Email'] = $faker->email();
            $traveler['Phone'] = $faker->phoneNumber();
            $traveler['TravelerIndex'] = (string) $index;
            array_push($travelers, $traveler);
        }
        // Reach out to the create_customer API
        $solartis = new \App\Solartis\API;
        $res = $solartis->create_customer($data, $travelers);
        $this->assertEquals($res['RequestStatus'], "SUCCESS");
        assert(array_key_exists('CustomerReferenceNumber', $res['CustomerInformation']));
    }

    /**
     * Test to make sure we can successfully issue a policy
     *
     * @return void
     */
    public function test_issue_policy()
    {
        $faker = \Faker\Factory::create();
        $data = [
            'customernumber' => 'SC1800007161EDV8', // This needs to be generated fresh
            'cardnumber' => '5555555555554444',
            'cvv' => '123',
            'expmonth' => '11',
            'expyear' => '2018',
            'name' => $faker->name(),
            'address' => $faker->streetAddress(),
            'city' => $faker->city(),
            'statecode' => $faker->stateAbbr(),
            'country' => 'United States',
            'zipcode' => $faker->postcode(),
            'email' => $faker->email(),
            'phone' => $faker->phoneNumber(),
        ];

        // Reach out to the create_customer API
        $solartis = new \App\Solartis\API;
        $res = $solartis->issue_policy($data);
        $this->assertEquals($res['PolicyBatch']['PaymentStatus'], "Paid");

        // Should fail the second time we reach out to the API
        $res = $solartis->issue_policy($data);
        $this->assertEquals($res['messageDetail']['messageCode'], "ERR401");
    }
}
