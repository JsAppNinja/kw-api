<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Person;

class StorePersonResponse extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    private $data;

    /**
     * StorePersonResponse constructor.
     * @param array $data
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        foreach($this->data as $data)
        {
            $record =  [
                        'zip' => $data['Zip'],
                        'young_adult_in_household' => $data['YoungAdultInHousehold'],
                        'working_woman' => $data['WorkingWoman'],
                        'timestamp' => $data['TimeStamp'],
                        'state' => $data['State'],
                        'soho_indicator' => $data['SOHOIndicator'],
                        'address' => $data['Address'],
                        'social_presence' => $data['SocialPresence'],
                        'single_parent' => $data['SingleParent'],
                        'senior_adult_in_household' => $data['SeniorAdultInHousehold'],
                        'religion' => $data['Religion'],
                        'presence_of_children' => $data['PresenceOfChildren'],
                        'occupation_detail' => $data['OccupationDetail'],
                        'occupation' => $data['Occupation'],
                        'number_of_children' => $data['NumberOfChildren'],
                        'marital_status_in_household' => $data['MaritalStatusInHousehold'],
                        'last_name' => $data['LastName'],
                        'language' => $data['Language'],
                        'home_owner_renter' => $data['HomeOwnerRenter'],
                        'gender' => $data['Gender'],
                        'first_name' => $data['FirstName'],
                        'ethnic_group' => $data['EthnicGroup'],
                        'education' => $data['Education'],
                        'dob' => $data['DOB'],
                        'city' => $data['City'],
                        'business_owner' => $data['BusinessOwner'],
                        'age_range' => $data['AgeRange']
            ];

            Person::firstOrCreate($record);
        }
    }
}
