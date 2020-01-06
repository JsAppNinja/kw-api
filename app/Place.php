<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    /**
     * Person Storage Model
     *
     * @property int $id
     * @property string $lat
     * @property string $lng
     * @property string $icon
     * @property string $places_id
     * @property string $name
     * @property int    $open_now
     * @property string $photo_height
     * @property string $photo_html_attributes
     * @property string $photo_reference
     * @property string $photo_width
     * @property string $place_id
     * @property string $rating
     * @property string $reference
     * @property string $scope
     * @property string $types
     * @property string $vicinity
     *
     */
    protected $fillable = [ 'lat', 'lng', 'icon', 'places_id', 'name', 'open_now', 'photo_height',
                            'photo_html_attributes', 'photo_reference', 'photo_width', 'place_id', 'rating',
                            'reference', 'scope', 'types', 'vicinity'];

}
