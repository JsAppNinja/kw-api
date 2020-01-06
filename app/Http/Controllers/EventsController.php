<?php
/**
 * api.kw.dev
 * @SWG\Swagger(
 *     schemes={"http"},
 *     host="localhost:8000",
 *     basePath="/v1",
 *     @SWG\Info(
 *         version="1.0.0",
 *         title="Swagger KW-API",
 *         description="This is KW-API messaging middleware for dispatch data to services / applications",
 *         @SWG\Contact(
 *             email="izhur2001@gmail.com"
 *         ),
 *     ),
 *     @SWG\ExternalDocumentation(
 *         description="Find out more about Swagger",
 *         url="http://swagger.io"
 *     )
 * )
 * @SWG\SecurityScheme(
 *   securityDefinition="apiKey",
 *   type="apiKey",
 *   in="header",
 *   name="apiKey",
 * )
 */

namespace App\Http\Controllers;

use App\Event;
use App\Subscriber;
use App\Jobs\SendNotifyEmail;
use App\Jobs\BroadcastEvent;
//use App\Jobs\PublishEvent;
use App\Services\EventsService;
use App\Services\ApiKeyService;
use App\Http\Requests\AddEventRequest;
use App\Http\Requests\RegisterEventRequest;
use App\Http\Requests\SubscribeEventRequest;
use App\Http\Requests\FilterPageRequest;
use Illuminate\Http\Request;
use App\Http\Requests;
use GuzzleHttp\Client;
use Cache;

class EventsController extends Controller
{

    public function __construct()
    {
        //
    }

    /**
     * The Dashboard controller to allow a human to manage/view the Event BUS
     *
     * @return string
     */
    public function dashboard()
    {
        //TODO build this...
        return 'hello world';
    }

    public function store(RegisterEventRequest $request)
    {
        $this->registerEvent($request);
    }

    /**
     * @SWG\Get(
     *     path="/events",
     *     tags={"events"},
     *     operationId="list event",
     *     summary="list event data",
     *     description="",
     *     consumes={"application/json", "application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="page",
     *         in="query",
     *         description="page data",
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="_filters",
     *         in="query",
     *         description="filters for data",
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="_perPage",
     *         in="query",
     *         description="number of items for every page",
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="_sortField",
     *         in="query",
     *         description="sorting items using specified field",
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="_sortDir",
     *         in="query",
     *         description="sort direction using _sortField",
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Bad Request",
     *     ),
     *     security={
     *       {"apiKey": {}},
     *     }
     * )
     */
    /**
     * list all events paginated 
     *
     * @param App\Http\Requests\FilterPageRequest $request
     * @return Response
     */
    public function index(FilterPageRequest $request)
    {
        $perpage = $request->getPerPage();
        $sortdir = $request->getSortDir();
        $sortfield = $request->getSortField();
        $filters = $request->getFilters();

        $events = new Event;
        if ($filters) {
            foreach ($filters as $key=>$val) {
                $events = $events->where($key,trim($val));
            }
        }
        $events = $events->orderBy($sortfield,$sortdir);
        
        return response()->json($events->paginate($perpage));
    }

    /**
     * @SWG\Get(
     *     path="/events/{eventId}",
     *     tags={"events"},
     *     operationId="show event",
     *     summary="show detail of event data",
     *     description="",
     *     consumes={"application/json", "application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="eventId",
     *         in="path",
     *         description="id of event",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Bad Request",
     *     ),
     *     security={
     *       {"apiKey": {}},
     *     }
     * )
     */
    /**
     * Show detail of an event
     * @param int $eventId
     * @return Response
     */
    public function show($eventId)
    {
        $event = Event::find($eventId);
        if (!$event) {
            throw new \Exception("Not Found", 404);
        }

        return response()->json($event);
    }

    /**
     * @SWG\Put(
     *     path="/events/{eventId}",
     *     tags={"events"},
     *     operationId="update event",
     *     summary="update event data",
     *     description="",
     *     consumes={"application/json", "application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="eventId",
     *         in="path",
     *         description="id of event",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="object",
     *         in="formData",
     *         description="object of event",
     *         required=true,
     *         type="string",
     *         default="lead",
     *     ),
     *     @SWG\Parameter(
     *         name="action",
     *         in="formData",
     *         description="action of event",
     *         required=true,
     *         type="string",
     *         default="create",
     *     ),
     *     @SWG\Parameter(
     *         name="version",
     *         in="formData",
     *         description="version of event",
     *         required=true,
     *         type="string",
     *         default="1",
     *     ),
     *     @SWG\Parameter(
     *         name="jsonSchema",
     *         in="formData",
     *         description="json schema of data event that that needs to dispatch",
     *         required=true,
     *         type="string",
     *         default="",
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Bad Request",
     *     ),
     *     security={
     *       {"apiKey": {}},
     *     }
     * )
     */
    /**
     * Update the specified event in storage.
     *
     * @param  int  $eventId
     * @return Response
     */
    public function update(RegisterEventRequest $request, $eventId)
    {
        $event = Event::find($eventId);
        if (!policy($event)->update($request, $event)) {
            throw new \Exception('Unauthorized for update Event',403);
        }

        // check don't collide with already existing data.
        $event->object = $request->input('object');
        $event->action = $request->input('action');
        $event->version = $request->input('version');
        $event->jsonSchema = $request->input('jsonSchema');
        $event->save();

        return response()->json($event);
    }

    /**
     * @SWG\Delete(
     *     path="/events/{eventId}",
     *     tags={"events"},
     *     operationId="delete event",
     *     summary="delete event data",
     *     description="",
     *     consumes={"application/json", "application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="eventId",
     *         in="path",
     *         description="id of event",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Bad Request",
     *     ),
     *     security={
     *       {"apiKey": {}},
     *     }
     * )
     */
    /**
     * Remove the specified event in storage.
     *
     * @param  int  $eventId
     * @return Response
     */
    public function destroy($eventId,Request $request)
    {
        $event = Event::find($eventId);
        if (!policy($event)->delete($request, $event)) {
            throw new \Exception('Unauthorized for delete Event',403);   
        }

        //Subscriber::where('event_id',$event->id)->delete();
        $event->subscribers()->delete();
        $event->delete();

        return response()->json($event);
    }

    /**
     * @SWG\Post(
     *     path="/events/unsubscribe",
     *     tags={"events"},
     *     operationId="unsubscribe event",
     *     summary="unsubscribe event data",
     *     description="",
     *     consumes={"application/json", "application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="object",
     *         in="formData",
     *         description="object of event",
     *         required=true,
     *         type="string",
     *         default="lead",
     *     ),
     *     @SWG\Parameter(
     *         name="action",
     *         in="formData",
     *         description="action of event",
     *         required=true,
     *         type="string",
     *         default="create",
     *     ),
     *     @SWG\Parameter(
     *         name="version",
     *         in="formData",
     *         description="version of event",
     *         required=true,
     *         type="string",
     *         default="1",
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Bad Request",
     *     ),
     *     security={
     *       {"apiKey": {}},
     *     }
     * )
     */
    /**
     * Unsubscribe event
     * @param int $eventId
     * @return Response
     */
    public function unsubscribeEvent(Request $request)
    {
        $data = $request->only(['object','action','version']);

        $eventObject = app(EventsService::class)->getEvent($data['object'],$data['action'],$data['version']);

        if (!$eventObject) {
            throw new \Exception('Event not found!',404);
        }

        $apiUser = app(ApiKeyService::class)->getApiUser($request->header('apiKey'));

        // check if already subscribing
        if (!$eventObject->subscribers->get($apiUser->id)) {
            throw new \Exception('Not subscribed to this event, so can not unsubscribe.',404);
        }

        $count = $eventObject->subscribers->get($apiUser->id)->delete();
        
        return response()->json([
            'object'    => $data['object'],
            'action'    => $data['action'],
            'version'   => $data['version'],
            'count'     => $count,
        ]);
    }

    /**
     * @SWG\Get(
     *     path="/events/{eventId}/subscribers",
     *     tags={"events"},
     *     operationId="list event subscribers",
     *     summary="list subscribers of an event",
     *     description="list subscribers of an event",
     *     consumes={"application/json", "application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="eventId",
     *         in="path",
     *         description="id of event",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Bad Request",
     *     ),
     *     security={
     *       {"apiKey": {}},
     *     }
     * )
     */
    /**
     * Show detail of an event subscribers
     * @param int $eventId
     * @return Response
     */
    public function subscribers($eventId)
    {
        $event = Event::find($eventId);

        if (!$event) {
            throw new \Exception('Event not found!',404);
        }
        foreach($event->subscribers as $sub) {
            $sub->apiUser;
        }

        return response()->json($event->subscribers);
    }

    /**
     * @SWG\Post(
     *     path="/events/register",
     *     tags={"events"},
     *     operationId="registerEvent",
     *     summary="Register an event from services / application publisher",
     *     description="",
     *     consumes={"application/json", "application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="object",
     *         in="formData",
     *         description="object of event",
     *         required=true,
     *         type="string",
     *         default="lead",
     *     ),
     *     @SWG\Parameter(
     *         name="action",
     *         in="formData",
     *         description="action of event",
     *         required=true,
     *         type="string",
     *         default="create",
     *     ),
     *     @SWG\Parameter(
     *         name="version",
     *         in="formData",
     *         description="version of event",
     *         required=true,
     *         type="string",
     *         default="1",
     *     ),
     *     @SWG\Parameter(
     *         name="jsonSchema",
     *         in="formData",
     *         description="json schema of data event that that needs to dispatch",
     *         required=true,
     *         type="string",
     *         default="",
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Bad Request",
     *     ),
     *     security={
     *       {"apiKey": {}},
     *     }
     * )
     */
    /**
     * Registering new event
     * @param RegisterEventRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function registerEvent(RegisterEventRequest $request)
    {
        
        //TODO: Need to figure out how to solve for application context.
        $data = $request->only(['object','action','version','jsonSchema']);

        $eventsSp = app(EventsService::class);
        $eventsSp->validateSchema($data['jsonSchema']);    
        $event = $eventsSp->getEvent($data['object'],$data['action'],$data['version']);

        if ($event) {
            throw new \Exception('Object, action, and version already registered. Try adding a new version.');
        }

        $apiUser = app(ApiKeyService::class)->getApiUser($request->header('apiKey'));

        //Everything seems to have worked, lets save it to the database.
        $data['apiUser'] = $apiUser->id;
        $event = Event::Create($data);

        return response()->json($event);
    }

    /**
     * @SWG\Post(
     *     path="/events/subscribe",
     *     tags={"events"},
     *     operationId="subscribeEvent",
     *     summary="subscribe an event to get data from publisher",
     *     description="",
     *     consumes={"application/json", "application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="object",
     *         in="formData",
     *         description="object of event",
     *         required=true,
     *         type="string",
     *         default="lead",
     *     ),
     *     @SWG\Parameter(
     *         name="action",
     *         in="formData",
     *         description="action of event",
     *         required=true,
     *         type="string",
     *         default="create",
     *     ),
     *     @SWG\Parameter(
     *         name="version",
     *         in="formData",
     *         description="version of event",
     *         required=true,
     *         type="string",
     *         default="1",
     *     ),
     *     @SWG\Parameter(
     *         name="endPoint",
     *         in="formData",
     *         description="endPoint callback",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Bad Request",
     *     ),
     *     security={
     *       {"apiKey": {}},
     *     }
     * )
     */
    /**
     * Ability to Subscribe for specific event
     * @param SubscribeEventRequest $request
     * @throws \Exception
     */
    public function subscribeEvent(SubscribeEventRequest $request) {
        $data = $request->only(['object','action','version','endPoint']);

        $eventObject = app(EventsService::class)->getEvent($data['object'],$data['action'],$data['version']);

        if (!$eventObject) {
            abort(404,'Event not found!');
        }

        $apiUser = app(ApiKeyService::class)->getApiUser($request->header('apiKey'));

        // check if already subscribing
        if ($eventObject->subscribers->get($apiUser->id)) {
            abort(403,'Already subscribing');
        }

        $data['event_id'] = $eventObject->id;
        $data['api_user_id'] = $apiUser->id;
        $sub = Subscriber::create($data);

        return response()->json($sub);
    }

    /**
     * @SWG\Post(
     *     path="/events/add",
     *     tags={"events"},
     *     operationId="addEvent",
     *     summary="add an event for dispatched to services / application subscriber",
     *     description="",
     *     consumes={"application/json", "application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="object",
     *         in="formData",
     *         description="object of event",
     *         required=true,
     *         type="string",
     *         default="lead",
     *     ),
     *     @SWG\Parameter(
     *         name="action",
     *         in="formData",
     *         description="action of event",
     *         required=true,
     *         type="string",
     *         default="create",
     *     ),
     *     @SWG\Parameter(
     *         name="version",
     *         in="formData",
     *         description="version of event",
     *         required=true,
     *         type="string",
     *         default="1",
     *     ),
     *     @SWG\Parameter(
     *         name="event",
     *         in="formData",
     *         description="Dispatched event data",
     *         required=true,
     *         type="string",
     *         @SWG\Schema(ref="#/definitions/Event"),
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Bad Request",
     *     ),
     *     security={
     *       {"apiKey": {}},
     *     }
     * )
     */
    /**
     *
     * Ability to AddEvents that will be pushed to Queue to be broadcasted
     * @param AddEventRequest $request
     * @throws \Exception
     */
    public function addEvent(AddEventRequest $request)
    {

        $object     = $request->input('object');
        $action     = $request->input('action');
        $version    = $request->input('version');

        $eventsSp = app(EventsService::class);
        $eventObject = $eventsSp->getEvent($object,$action,$version);

        $eventData = $request->input('event');
        $eventsSp->validateEventAgainstSchema($eventData, $eventObject->jsonSchema);
   
        // list subscriber, attach endpoint to every job
        $subs = $eventObject->subscribers;

        $count = 0;
        try {
            // this block made allocation memory error, when rabbitmq not up
            foreach ($subs as $sub) {
                $job = (new BroadcastEvent($eventData, $eventObject,$sub->endPoint));
                //on specific queue
                //$job = (new BroadcastEvent($eventData, $eventObject))->onQueue($object.".".$action);

                $ok = $this->dispatch($job);
                if ($ok) $count++;
            }
        } catch (\Exception $e) {
            throw new \Exception('Failed adding to queue',500);
        }

        return response()->json([
            'object'    => $object,
            'action'    => $action,
            'version'   => $version,
            'count'     => $count,
        ]);
    }

    /**
     * @SWG\Post(
     *     path="/events/{$eventId}/notify",
     *     tags={"events"},
     *     operationId="eventNotify",
     *     summary="notify event subscribers",
     *     description="notify event subscibers by ApiUser.email of subscribers",
     *     consumes={"application/json", "application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="eventId",
     *         in="path",
     *         description="id of event",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="title",
     *         in="formData",
     *         description="title of notification",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="message",
     *         in="formData",
     *         description="message of notification",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Bad Request",
     *     ),
     *     security={
     *       {"apiKey": {}},
     *     }
     * )
     */
    /**
     * Send email notification to all subscribers of event, by ApiUser.email
     * @param Request $request
     */
    public function notify($eventId, Request $request)
    {
        $message = $request->input('message');
        $title = $request->input('title');
        $event = Event::find($eventId);

        if (!$event) {
            abort(404, 'Event not found!');
        }

        $count = 0;
        foreach($event->subscribers as $sub) {
            $sub->apiUser;
            if (!empty($sub->apiUser->email)) {
                $job = (new SendNotifyEmail($sub->apiUser->email, $title,$message));
                //on specific queue
                //$job = (new SendNotifyEmail($sub->apiUser->email, $eventObject,$sub->endPoint))
                //  ->onQueue($object.".".$action);

                $ok = $this->dispatch($job);
                if ($ok) $count++;
            }
        }

        return response()->json([
            'title'=>$title,
            'message'=>$message,
            'eventId'=>$eventId,
            'count'=>$count,
        ]);
    }
}