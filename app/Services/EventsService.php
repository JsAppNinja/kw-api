<?php
/**
 * User: joshteam
 * Date: 6/24/16
 * Time: 11:20 PM
 */

namespace App\Services;

use JsonSchema\Validator;
use App\Event;
use Cache;
use App\ApiUser;

class EventsService
{
    /**
     * validate jsonschema and event data
     * @param $event event data
     * @param $schema jsonSchema
     * @return bool
     * @throws \Exception
     */
    public function validateEventAgainstSchema($event, $schema)
    {
        if (!$this->isJson($event)) {
          throw new \Exception('event passed is not valid json',400);
        }

        if ($this->validateSchema($schema)) {
          // then check 
          $refResolver = new \JsonSchema\RefResolver(new \JsonSchema\Uri\UriRetriever(), new \JsonSchema\Uri\UriResolver());
          $schemaResolved = $refResolver->resolve("data:text/plain,".urlencode($schema));
          $eventDecoded = json_decode($event);
          
          $validator = new \JsonSchema\Validator();
          $validator->check($eventDecoded, $schemaResolved);

          if ($validator->isValid()) {
            return true;
          } else {
            throw new \Exception('Event passed does not adhere to JsonSchema defined.',422);
          }
        }
        return false;
    }

    /**
     * validate jsonschema
     * @param $schema jsonSchema
     * @return bool
     * @throws \Exception
     */
    public function validateSchema($schema)
    {
        if (!$this->isJson($schema)) {
          throw new \Exception('schema is not json',400);
        }

        $schema = json_decode($schema);

        if(!isset($schema->properties->object)) {
          throw new \Exception('object not set in jsonSchema',400);
        }
        if(!isset($schema->properties->action)) {
          throw new \Exception('action not set in jsonSchema');
        }
        if(!isset($schema->properties->createdAt)) {
          throw new \Exception('createdAt not set in jsonSchema');
        }

        if(!in_array('object', $schema->required)) {
          throw new \Exception('object must be required in jsonSchema');
        }

        if(!in_array('action', $schema->required)) {
          throw new \Exception('action must be required in jsonSchema');
        }

        if(!in_array('createdAt', $schema->required)) {
          throw new \Exception('createdAt must be required in jsonSchema');
        }

        return true;

    }    

    /**
     * check is string json?
     * @return boolean
     */
    public function isJson($string) 
    {
      json_decode($string);
      return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * get event by object,action,version including all subscriber and cached forever.
     * @param $object string object value
     * @param $action string action value
     * @param $version string version value
     * @return Event object event with subscribers
     */
    public function getEvent($object,$action,$version)
    {
      // return $event;
      return Event::getEvent($object,$action,$version);
    }

    /**
     * forget event cache by object,action.
     * @param $object string object value
     * @param $action string action value
     * @param $version string version value
     */
    public function forgetEvent($object,$action,$version)
    {
      $key = Event::class.".".$object."_".$action."_".$version;
      Cache::forget($key);
    }

    /**
     * @return \stdClass
     */
    private function getBaseJsonSchema()
    {
        return json_decode('
        {
          "$schema": "http://json-schema.org/draft-04/schema#",
          "type": "object",
          "properties": {
            "object": {
              "type": "string"
            },
            "action": {
              "type": "string"
            },
            "createdAt": {
              "type": "string"
            }
          },
          "required": [
            "object",
            "action",
            "createdAt"
          ]
        }
        
        ');
    }

}