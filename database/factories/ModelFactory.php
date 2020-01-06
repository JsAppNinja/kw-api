<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->safeEmail,
        'password' => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\ApiUser::class, function (Faker\Generator $faker) {
    return [
        'apiKey' => $faker->md5,
        //'isActive' => $faker->boolean,
        'company' => $faker->company,
        'application' => $faker->domainName,
        'email'=> $faker->safeEmail,
    ];
});


$factory->define(App\Event::class, function (Faker\Generator $faker) {

    $nouns = array('lead', 'interaction', 'user');
    $actions = array('create', 'update', 'delete');

    return [
        'apiUser'   => $faker->numberBetween(1,10),
        'object'    => $nouns[ array_rand($nouns) ],
        'action'    =>$actions[ array_rand($actions) ],
        'version'   => $faker->numberBetween(1,10),
        'jsonSchema' => '{"$schema":"http://json-schema.org/draft-04/schema#","type":"object","properties":{"name":{"type":"string"},"object":{"type":"string"},"action":{"type":"string"},"createdAt":{"type":"string"}},"required":["name","object","action","createdAt"]}'
    ];
});

$factory->define(App\Subscriber::class, function (Faker\Generator $faker) {

    $nouns = array('lead', 'interaction', 'user');
    $actions = array('create', 'update', 'delete');

    return [
        'event_id'    => $faker->numberBetween(1,10),
        'api_user_id' => $faker->numberBetween(1,10),
        'object'      => $nouns[ array_rand($nouns) ],
        'action'      => $actions[ array_rand($actions) ],
        'endPoint'    => $faker->domainName,
        'version'     => $faker->numberBetween(1,10)
    ];
});
