<?php

namespace App\Jobs;

use App\Event;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use GuzzleHttp\Client;

class BroadcastEvent extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $eventData;
    protected $eventObject;
    protected $endPoint;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($eventData, $eventObject, $endPoint=null)
    {
        $this->eventData    = $eventData;
        $this->eventObject  = $eventObject;
        if ($endPoint) $this->endPoint = $endPoint;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // TODO: logging ??
        $client = new Client();
        $res = $client->request('POST', $this->endPoint, [
            'json' => json_decode($this->eventData)
        ]);
        $code = $res->getStatusCode(); 
        //$body = $res->getBody()->getContents();
        
        if ($code=='200') {
            return true;
        } 
        return false;
    }

    public function failed()
    {

    }
}
