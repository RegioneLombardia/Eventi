<?php


namespace open20\amos\events\widgets;


use dosamigos\google\maps\overlays\InfoWindow;
use yii\base\Widget;
use dosamigos\google\maps\LatLng;
use dosamigos\google\maps\Map;
use dosamigos\google\maps\overlays\Marker;
use open20\amos\core\utilities\MapsUtility;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;

class EventLocationMapWidget extends Widget
{
    public $dataProvider;
    public $width = '100%';
    public $height = 400;
    public $zoom = 12;
    public $mapType = 'roadmap';

    public function run()
    {
        $this->hideBottonsMapsCss();
        $gmap = $this->dataProvider->models;
        $firstCoordinate = reset($gmap);
        if (count($firstCoordinate)) {
            $lat = $firstCoordinate['lat'];
            $long = $firstCoordinate['lng'];
        }


        $coord = new LatLng(['lat' => $lat, 'lng' => $long]);
        $mapArray = $this->getMapArrayConfig($coord);
        $coordinateInfos = $this->prepareArrayCoordinates($gmap);

        $map = new Map($mapArray);
        foreach ($gmap as $place) {
            $coordPlace = new LatLng(['lat' => $place['lat'], 'lng' => $place['lng']]);
            $event = $place['model'];
            $icon = \Yii::$app->params['platform']['backendUrl'] . "/img/marker_red.png";
            if ($event->event_id) {
                $icon = \Yii::$app->params['platform']['backendUrl'] . "/img/marker_blu.png";
            }
            $marker = new Marker([
                'position' => $coordPlace,
                'title' => $event->title,
                'icon' => $icon
            ]);

            // Provide a shared InfoWindow to the marker
            $content = $this->render('_item_location_map', [
                'event' => $event,
                'place' => $place,
                'coordinateInfos' => $coordinateInfos,
                'firstCoordinate' => $firstCoordinate
            ]);
            $marker->attachInfoWindow(
                new InfoWindow([
                    'content' => $content
                ])
            );

            // Add marker to the map
            $map->addOverlay($marker);
        }
        $zoom = $map->getMarkersFittingZoom();
        $map->options['zoom'] = $zoom;
//        $mapName = $map->getName();
//        $map->appendScript("{$mapName}.setTilt(60);");
//        $map->options['zoom'] = 16.5;

        return $map->display();
    }

    public function hideBottonsMapsCss()
    {
        $this->view->registerCss("

        ");
    }

    /**
     * @param $coord
     * @return array
     */
    public function getMapArrayConfig($coord)
    {


        if ($this->mapType == 'retro') {
            $retrojson = $this->getJsonRetro();
            $jsexpr = new JsExpression($retrojson);
            $this->mapType = 'roadmap';
        }
        else if ($this->mapType == 'aubergine') {
            $retrojson = $this->jsonAubergine();
            $jsexpr = new JsExpression($retrojson);
            $this->mapType = 'roadmap';
        } else {
            $jsexpr = new JsExpression('  {
            featureType: "poi",
            elementType: "labels",
            stylers: [{
              visibility: "off"
            }]
         },
         {
          featureType: "transit",
          elementType: "labels.icon",
          stylers: [{ visibility: "off" }],
        },');
        }

        return [
            'center' => $coord,
            'width' => $this->width,
            'height' => $this->height,
            'zoom' => $this->zoom,
            'mapTypeId' => $this->mapType,
            'mapTypeControl' => false,
//            'streetViewControl' => false,
            'styles' => [$jsexpr],
//            'key' => \Yii::$app->params['googleMapsApiKey']
        ];
    }


    /**
     * @param $places
     * @return array
     */
    public function prepareArrayCoordinates($places)
    {
        $coords = [];
        foreach ($places as $place) {
            $event = $place['model'];
            $coords[$place['lat']][$place['lng']] [] = $event;
        }
        return $coords;
    }

    public function getJsonRetro()
    {
        $retrojson = <<<JSON
  {
    "elementType": "geometry",
    "stylers": [
      {
        "color": "#ebe3cd"
      }
]
},
{
    "elementType": "labels.text.fill",
    "stylers": [
      {
          "color": "#523735"
      }
    ]
  },
{
    "elementType": "labels.text.stroke",
    "stylers": [
      {
          "color": "#f5f1e6"
      }
    ]
  },
{
    "featureType": "administrative",
    "elementType": "geometry.stroke",
    "stylers": [
      {
          "color": "#c9b2a6"
      }
    ]
  },
{
    "featureType": "administrative.land_parcel",
    "elementType": "geometry.stroke",
    "stylers": [
      {
          "color": "#dcd2be"
      }
    ]
  },
{
    "featureType": "administrative.land_parcel",
    "elementType": "labels.text.fill",
    "stylers": [
      {
          "color": "#ae9e90"
      }
    ]
  },
{
    "featureType": "landscape.natural",
    "elementType": "geometry",
    "stylers": [
      {
          "color": "#dfd2ae"
      }
    ]
  },
{
    "featureType": "poi",
    "elementType": "geometry",
    "stylers": [
      {
          "color": "#dfd2ae"
      }
    ]
  },
{
    "featureType": "poi",
    "elementType": "labels.text.fill",
    "stylers": [
      {
          "color": "#93817c"
      }
    ]
  },
{
    "featureType": "poi.park",
    "elementType": "geometry.fill",
    "stylers": [
      {
          "color": "#a5b076"
      }
    ]
  },
{
    "featureType": "poi.park",
    "elementType": "labels.text.fill",
    "stylers": [
      {
          "color": "#447530"
      }
    ]
  },
{
    "featureType": "road",
    "elementType": "geometry",
    "stylers": [
      {
          "color": "#f5f1e6"
      }
    ]
  },
{
    "featureType": "road.arterial",
    "elementType": "geometry",
    "stylers": [
      {
          "color": "#fdfcf8"
      }
    ]
  },
{
    "featureType": "road.highway",
    "elementType": "geometry",
    "stylers": [
      {
          "color": "#f8c967"
      }
    ]
  },
{
    "featureType": "road.highway",
    "elementType": "geometry.stroke",
    "stylers": [
      {
          "color": "#e9bc62"
      }
    ]
  },
{
    "featureType": "road.highway.controlled_access",
    "elementType": "geometry",
    "stylers": [
      {
          "color": "#e98d58"
      }
    ]
  },
{
    "featureType": "road.highway.controlled_access",
    "elementType": "geometry.stroke",
    "stylers": [
      {
          "color": "#db8555"
      }
    ]
  },
{
    "featureType": "road.local",
    "elementType": "labels.text.fill",
    "stylers": [
      {
          "color": "#806b63"
      }
    ]
  },
{
    "featureType": "transit.line",
    "elementType": "geometry",
    "stylers": [
      {
          "color": "#dfd2ae"
      }
    ]
  },
{
    "featureType": "transit.line",
    "elementType": "labels.text.fill",
    "stylers": [
      {
          "color": "#8f7d77"
      }
    ]
  },
{
    "featureType": "transit.line",
    "elementType": "labels.text.stroke",
    "stylers": [
      {
          "color": "#ebe3cd"
      }
    ]
  },
{
    "featureType": "transit.station",
    "elementType": "geometry",
    "stylers": [
      {
          "color": "#dfd2ae"
      }
    ]
  },
{
    "featureType": "water",
    "elementType": "geometry.fill",
    "stylers": [
      {
//          "color": "#b9d3c2"
          "color": "#bad1d4"
      }
    ]
  },
{
    "featureType": "water",
    "elementType": "labels.text.fill",
    "stylers": [
      {
          "color": "#92998d"
      }
    ]
  },
  {
            featureType: "poi",
            elementType: "labels",
            stylers: [{
              visibility: "off"
            }]
         },
         {
          featureType: "transit",
          elementType: "labels.icon",
          stylers: [{ visibility: "off" }],
        }
JSON;
        return $retrojson;
    }


    public function jsonAubergine()
    {
        $json = <<<JSON
  {
    "elementType": "geometry",
    "stylers": [
      {
        "color": "#1d2c4d"
      }
    ]
  },
  {
    "elementType": "labels.text.fill",
    "stylers": [
      {
        "color": "#a6e2d7"
      }
    ]
  },
  {
    "elementType": "labels.text.stroke",
    "stylers": [
      {
        "color": "#1a3646"
      }
    ]
  },
  {
    "featureType": "administrative.country",
    "elementType": "geometry.stroke",
    "stylers": [
      {
        "color": "#4b6878"
      }
    ]
  },
  {
    "featureType": "administrative.land_parcel",
    "elementType": "labels.text.fill",
    "stylers": [
      {
        "color": "#64779e"
      }
    ]
  },
  {
    "featureType": "administrative.province",
    "elementType": "geometry.stroke",
    "stylers": [
      {
        "color": "#4b6878"
      }
    ]
  },
  {
    "featureType": "landscape.man_made",
    "elementType": "geometry.stroke",
    "stylers": [
      {
        "color": "#334e87"
      }
    ]
  },
  {
    "featureType": "landscape.natural",
    "elementType": "geometry",
    "stylers": [
      {
       // "color": "#023e58"
        "color": "#06426f"
      }
    ]
  },
  {
    "featureType": "poi",
    "elementType": "geometry",
    "stylers": [
      {
        "color": "#283d6a"
      }
    ]
  },
  {
    "featureType": "poi",
    "elementType": "labels.text.fill",
    "stylers": [
      {
        "color": "#a5d8e4"
      }
    ]
  },
  {
    "featureType": "poi",
    "elementType": "labels.text.stroke",
    "stylers": [
      {
        "color": "#1d2c4d"
      }
    ]
  },
  {
    "featureType": "poi.park",
    "elementType": "geometry.fill",
    "stylers": [
      {
        //"color": "#023e58"
        "color": "#06426f"
      }
    ]
  },
  {
    "featureType": "poi.park",
    "elementType": "labels.text.fill",
    "stylers": [
      {
        "color": "#3C7680"
      }
    ]
  },
  {
    "featureType": "road",
    "elementType": "geometry",
    "stylers": [
      {
        "color": "#304a7d"
      }
    ]
  },
  {
    "featureType": "road",
    "elementType": "labels.text.fill",
    "stylers": [
      {
        "color": "#98a5be"
      }
    ]
  },
  {
    "featureType": "road",
    "elementType": "labels.text.stroke",
    "stylers": [
      {
        "color": "#1d2c4d"
      }
    ]
  },
  {
    "featureType": "road.highway",
    "elementType": "geometry",
    "stylers": [
      {
        "color": "#2c6675"
      }
    ]
  },
  {
    "featureType": "road.highway",
    "elementType": "geometry.stroke",
    "stylers": [
      {
        "color": "#255763"
      }
    ]
  },
  {
    "featureType": "road.highway",
    "elementType": "labels.text.fill",
    "stylers": [
      {
        "color": "#c3eae3"
      }
    ]
  },
  {
    "featureType": "road.highway",
    "elementType": "labels.text.stroke",
    "stylers": [
      {
        "color": "#023e58"
      }
    ]
  },
  {
    "featureType": "transit",
    "elementType": "labels.text.fill",
    "stylers": [
      {
        "color": "#98a5be"
      }
    ]
  },
  {
    "featureType": "transit",
    "elementType": "labels.text.stroke",
    "stylers": [
      {
        "color": "#1d2c4d"
      }
    ]
  },
  {
    "featureType": "transit.line",
    "elementType": "geometry.fill",
    "stylers": [
      {
        "color": "#283d6a"
      }
    ]
  },
  {
    "featureType": "transit.station",
    "elementType": "geometry",
    "stylers": [
      {
        "color": "#3a4762"
      }
    ]
  },
  {
    "featureType": "water",
    "elementType": "geometry",
    "stylers": [
      {
        "color": "#05183d"
      }
    ]
  },
  {
    "featureType": "water",
    "elementType": "labels.text.fill",
    "stylers": [
      {
        "color": "#4e6d70"
      }
    ]
  },
  {
            featureType: "poi",
            elementType: "labels",
            stylers: [{
              visibility: "off"
            }]
         },
         {
          featureType: "transit",
          elementType: "labels.icon",
          stylers: [{ visibility: "off" }],
        }
JSON;
        return $json;
    }
}