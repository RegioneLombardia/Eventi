<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    @vendor/open20/amos-events/src/views
 */

use open20\amos\core\helpers\Html;
use open20\amos\core\views\DataProviderView;
use yii\widgets\Pjax;
use open20\amos\layout\utility\AmosIconsBi;
use open20\amos\events\AmosEvents;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var open20\amos\events\models\Event $model
 */
$idEvent = $model->id;
$js = <<<JS
//    $(document).on('click', '.event-landing-section-index .move', function(e){
//        e.preventDefault();
//        var row = $(this).parents('tr');
//        var rowToAppend = $(row).clone();
//        var direction = $(this).attr('direction');
//
//        $.ajax({
//           url: $(this).attr('href'),
//           type: 'get',
//           success: function (data) {
//               if(direction === 'up'){
//                   var prev = $(row).prev();
//                   $(prev).before(rowToAppend);
//               }else{
//                   var next = $(row).next();
//                   $(next).after(rowToAppend);
//               }
//               $(this).parents('tr').remove();
//           }
//      });
//
//
//    })
       $.ajax({
          url: '/events/event-landing-section/active-sections-ajax',
          type: 'get',
          data:{id: $idEvent },
          success: function (data) {
              $.each( data, function( key, value ) {
                  if(value == true){
                      $('#'+key).after("<span title='Attiva' class='mdi mdi-check-circle mdi-24px text-success'></span>");
                  } else {
                      $('#'+key).after("<span title='Nascosta' class='mdi mdi-close-circle mdi-24px text-danger'></span>");
                  }
              })
          }
     });

JS;
$this->registerJs($js);

$this->title = Yii::t('amoscore', 'Ordinamento sezioni "{title}"', ['title' => $model->title]);
$this->params['breadcrumbs'][] = ['label' => '', 'url' => ['/events']];
$this->params['breadcrumbs'][] = $this->title;
$eventId = $model->id;
$configSections = \open20\amos\events\models\Event::getConfigLandingSections();

?>
<div class="event-landing-section-index">
    <?= DataProviderView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $model,
        'currentView' => $currentView,
        'gridView' => [
            'columns' => [
                [
                    'width' => '10%',
                    'label' => 'Ordina',
                    'value' => function ($model) use ($max, $min, $eventId) {


                        $buttons = '';
                        if ($model->n_order != $min) {
                            $buttons .= Html::a(
                                AmosIconsBi::show('ic_arrow_upward'),
                                [
                                    '/events/event-landing-section/order',
                                    'id' => $model->id,
                                    'direction' => 'up',
                                    'event_id' => $eventId,
                                ],
                                [
                                    'class' => 'btn btn-xs btn-icon move',
                                    'data-toggle' => 'tooltip',
                                    'direction' => 'up',
                                    'title' => AmosEvents::t('amosevents', 'Sposta sopra')
                                ]
                            );
                        }
                        if ($model->n_order != $max) {
                            $buttons .= Html::a(
                                AmosIconsBi::show('ic_arrow_downward'),
                                [
                                    '/events/event-landing-section/order',
                                    'id' => $model->id,
                                    'direction' => 'down',
                                    'event_id' => $eventId,

                                ],
                                [
                                    'class' => 'btn btn-xs btn-icon move',
                                    'data-toggle' => 'tooltip',
                                    'direction' => 'down',
                                    'title' => AmosEvents::t('amosevents', 'Sposta sotto')
                                ]
                            );
                        }
                        return $buttons;
                    },
                    'format' => 'raw'
                ],
                'n_order',
                [
                    'label' => AmosEvents::t('amosevents', 'Sezione'),
                    'value' => function ($model) use ($configSections) {
                        return $configSections[$model->section]['label'];
                    }
                ],
                [
                    'label' => AmosEvents::t('amosevents', 'Attiva'),
                    'value' => function ($model) use ($configSections) {
                        return Html::hiddenInput($model->section, null, ['id' => $model->section]);
                    },
                    'format' => 'raw'
                ],
            ],
        ]
        /*'listView' => [
        'itemView' => '_item',
        'masonry' => FALSE,

        // Se masonry settato a TRUE decommentare e settare i parametri seguenti 
        // nel CSS settare i seguenti parametri necessari al funzionamento tipo
        // .grid-sizer, .grid-item {width: 50&;}
        // Per i dettagli recarsi sul sito http://masonry.desandro.com                                     

        //'masonrySelector' => '.grid',
        //'masonryOptions' => [
        //    'itemSelector' => '.grid-item',
        //    'columnWidth' => '.grid-sizer',
        //    'percentPosition' => 'true',
        //    'gutter' => '20'
        //]
        ],
        'iconView' => [
        'itemView' => '_icon'
        ],
        'mapView' => [
        'itemView' => '_map',          
        'markerConfig' => [
        'lat' => 'domicilio_lat',
        'lng' => 'domicilio_lon',
        'icon' => 'iconMarker',
        ]
        ],
        'calendarView' => [
        'itemView' => '_calendar',
        'clientOptions' => [
        //'lang'=> 'de'
        ],
        'eventConfig' => [
        //'title' => 'titleEvent',
        //'start' => 'data_inizio',
        //'end' => 'data_fine',
        //'color' => 'colorEvent',
        //'url' => 'urlEvent'
        ],
        'array' => false,//se ci sono più eventi legati al singolo record
        //'getEventi' => 'getEvents'//funzione da abilitare e implementare nel model per creare un array di eventi legati al record
        ]*/
    ]); ?>

</div>
