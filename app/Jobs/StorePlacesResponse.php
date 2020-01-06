<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Place;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class StorePlacesResponse extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    
    private $data;
    /**
     * StorePlacesResponse constructor.
     * @param array $data
     * @return void
     */
    public function __construct(array  $data)
    {
        //
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
        foreach ($this->data as $data)
        {
            $open_now = (isset($data['opening_hours'])) ? (int) $data['opening_hours']['open_now'] : (int) '0';

            $record = [
                'lat'                   =>  $data['geometry']['location']['lat'],
                'lng'                   =>  $data['geometry']['location']['lng'],
                'icon'                  =>  $data['icon'],
                'places_id'             =>  $data['id'],
                'name'                  =>  $data['name'],
                'open_now'              =>  $open_now,
                'photo_height'          =>  $data['photos'][0]['height'],
                'photo_html_attributes' =>  $data['photos'][0]['html_attributions'][0],
                'photo_reference'       =>  $data['photos'][0]['photo_reference'],
                'photo_width'           =>  $data['photos'][0]['width'],
                'place_id'              =>  $data['place_id'],
                'rating'                =>  $data['rating'],
                'reference'             =>  $data['reference'],
                'scope'                 =>  $data['scope'],
                'types'                 =>  implode(',',$data['types']),
                'vicinity'              =>  $data['vicinity']

            ];

            Place::firstOrCreate($record);
        }
    }
}
