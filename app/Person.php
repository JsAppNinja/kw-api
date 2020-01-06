<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    /**
     * Person Storage Model
     *
     * @property int    $id
     * @property string $zip
     * @property string $young_adult_in_household
     * @property string $working_woman
     * @property string $timestamp
     * @property string $state
     * @property string $soho_indicator
     * @property string $address
     * @property string $social_presence
     * @property string $single_parent
     * @property string $senior_adult_in_household
     * @property string $religion
     * @property string $presence_of_children
     * @property string $occupation_detail
     * @property string $occupation
     * @property string $number_of_children
     * @property string $marital_status_in_household
     * @property string $last_name
     * @property string $language
     * @property string $home_owner_renter
     * @property string $gender
     * @property string $first_name
     * @property string $ethnic_group
     * @property string $education
     * @property string $dob
     * @property string $city
     * @property string $business_owner
     * @property string $age_range
     *
     */
    protected $table = 'persons';
    protected $fillable = ['zip', 'young_adult_in_household','working_woman',
                           'timestamp', 'state', 'soho_indicator', 'address',
                           'social_presence', 'single_parent', 'senior_adult_in_household',
                           'religion', 'presence_of_children', 'occupation_detail', 'occupation',
                           'number_of_children', 'marital_status_in_household', 'last_name', 'language',
                           'home_owner_renter', 'gender', 'first_name', 'ethnic_group', 'education', 'dob',
                           'city', 'business_owner', 'age_range'];

}