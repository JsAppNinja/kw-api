<?php
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
Route::model('lists', 'App\RoutingList');
Route::model('agents', 'App\ListAgent');
Route::model('leads', 'App\ListLead');

Route::get('/', function () {
    return view('home');
});

Route::group(array('prefix' => 'v1', 'middleware' => 'api.log.activity'), function()
{

	Route::get('api_users/{apiKey}/check','ApiUsersController@check');

    Route::group(['middleware' => 'api.check'], function() {
    	Route::resource('events', 'EventsController',['only' => ['store', 'update', 'destroy']]);
    	Route::resource('events', 'EventsController',['only' => ['index', 'show']]);
    	Route::resource('api_users', 'ApiUsersController',['only' => ['index', 'show', 'store', 'update', 'destroy']]);
    	Route::resource('subscribers', 'SubscriberController',['only' => ['index', 'show']]);

    	Route::get('events/{eventId}/subscribers', 'EventsController@subscribers');
		  Route::post('events/{eventId}/notify', 'EventsController@notify');

    	Route::post('events/register', 'EventsController@registerEvent');
		  Route::post('events/add', 'EventsController@addEvent');
		  Route::post('events/subscribe', 'EventsController@subscribeEvent');
		  Route::post('events/unsubscribe', 'EventsController@unsubscribeEvent');


		  Route::get('api_users/{id}/toggle','ApiUsersController@toggle');

		  Route::get('mq/overview','QueuesController@overview');
		  Route::get('mq/nodes','QueuesController@nodes');
		  Route::get('mq/exchanges','QueuesController@exchanges');
		  Route::get('mq/queues','QueuesController@queues');
      Route::post('communications/send_text', 'CommunicationsController@sendText');
		  Route::get('demographics/get_demographics', 'DemographicController@getDemographics');
		  Route::get('places/get_places', 'PointOfInterestController@getPlaces');

      Route::resource('lists', 'ListsController', ['only' => ['store','index','show']]);
      Route::resource('lists.agents', 'ListAgentsController', ['only' => ['index', 'show', 'store','destroy']]);
      Route::post('lists/{lists}/agents/bulkadd', 'ListAgentsController@bulkstore');
      Route::post('lists/{lists}/assign', 'ListsController@assignAgent');
      Route::get('lists/{lists}/stats', 'ListsController@showStats');
      Route::get('lists/{lists}/agents/{agents}/stats', 'ListAgentsController@showStats');

		  Route::group(['prefix' => 'people/social', 'as' => 'people::'], function () {
  			Route::get('lookupEmail', ['as' => 'lookupEmail','uses' => 'PeopleController@lookupEmail']);
  			Route::get('lookupPhone', ['as' => 'lookupPhone','uses' => 'PeopleController@lookupPhone']);
  			Route::get('lookup', ['as' => 'lookup', 'uses' => 'PeopleController@lookup']);
  		});
    });

    Route::resource('leads', 'ListLeadsController', ['only' => ['index','show']]);
    Route::post('leads/{leads}/mark', 'ListLeadsController@markComplete');

	//Route::resource('users', 'UsersController',['only' => ['index', 'show', 'store', 'update', 'destroy']]);
});

Route::auth();
/*
// Authentication Routes...
Route::get('login', 'Auth\AuthController@showLoginForm');
Route::post('login', 'Auth\AuthController@login');
Route::get('logout', 'Auth\AuthController@logout');

// Registration Routes...
Route::get('register', 'Auth\AuthController@showRegistrationForm');
Route::post('register', 'Auth\AuthController@register');

// Password Reset Routes...
Route::get('password/reset/{token?}', 'Auth\PasswordController@showResetForm');
Route::post('password/email', 'Auth\PasswordController@sendResetLinkEmail');
Route::post('password/reset', 'Auth\PasswordController@reset');
*/

//Route::get('/home', 'HomeController@index');
//Route::get('events/dashboard', 'EventsController@dashboard');
