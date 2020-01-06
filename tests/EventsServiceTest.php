<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Services\EventsService;

class EventServiceTest extends TestCase
{
    /**
     *@var class EventService 
     */
    private $eventService;
    
    public function setUp()
    {
        parent::setUp();
        $this->eventService = new EventsService;
    }
    
    /**
     * 
     * update array jsonShema;
     * create array Event;
     * check.
     */
    public function testValidateEventAgainstSchema()
    {
        
        $this->jsonSchema['properties']['name'] = ['type' => 'string'];
        $this->jsonSchema['required'] = ['name', 'object', 'action', 'createdAt'];
        $arrayJsonSchema = $this->jsonSchema; 
        
        $arrayEvent = ['name'     => 'unitTest',
                       'object'   => 'unitTest',
                       'action'   => 'create',
                       'createdAt'=> 'unitTest'
        ];
        
        $resultCheck = $this->eventService->validateEventAgainstSchema(json_encode($arrayEvent),json_encode($arrayJsonSchema));
        //check
        $this->assertEquals(true, $resultCheck, 'EventsService@ValidateEventAgainstSchema must return (bool)TRUE');
    }

    /**
     * @expectedException Exception
     * @expectedExceptionCode 422
     */
    public function testInvalidEventSchemaFails()
    {

        $this->jsonSchema['properties']['name'] = ['type' => 'string'];
        $this->jsonSchema['required'] = ['name', 'object', 'action', 'createdAt'];
        $arrayJsonSchema = $this->jsonSchema;

        $arrayEvent = [
            'name'     => 'unitTest',
            'action'   => 'create'
        ];

        $resultCheck = $this->eventService->validateEventAgainstSchema(json_encode($arrayEvent),json_encode($arrayJsonSchema));
        //check
        $this->assertFalse($resultCheck, 'EventsService@ValidateEventAgainstSchema must return (bool)FALSE');
    }
    
    /**
     * check ValidateSchema
     * Sent array $this->jsonSchema
     */
    public function testValidateSchema()
    {      
       $this->assertEquals(true,$this->eventService->validateSchema(json_encode($this->jsonSchema)));
    }

    /**
     * check InvalidateSchema
     * Sent array $this->jsonSchema
     * @expectedException Exception
     * @expectedExceptionCode 400
     */
    public function testInvalidateSchema()
    {
        $this->assertEquals(false,$this->eventService->validateSchema('x'));
    }
    
    /**
     * Create new row in events
     * Check getEvent()
     * Sent Object, Action, version
     * clean up row
     */
     public function testGetEvent()
     {
        $event = factory(App\Event::class)->create(['apiUser'=>'UnitTestApi']);
        
        $resultCheckFunction = $this->eventService->getEvent($event->object, 
               $event->action,
               $event->version);

        //check;
        $this->assertEquals(true, is_object($resultCheckFunction), 'EventsService@getEvent must return object');

        //second call, from cache
        $temp = $this->eventService->getEvent($event->object,
                $event->action, 
                $event->version);
        $this->assertEquals(true, $resultCheckFunction->id==$temp->id , 'EventsService@getEvent must return object, from cache');
         
        //clean up cache;
        $this->eventService->forgetEvent($event->object,$event->action,$event->version);
        //clean up row;
        $this->assertEquals(true, $event->delete($event->id));
     }
    
}
