<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    @vendor/open20/amos/events/src/views 
 */
use open20\amos\core\helpers\Html;
use open20\amos\core\views\DataProviderView;
use yii\widgets\Pjax;
use open20\amos\events\AmosEvents;
use open20\amos\core\icons\AmosIcons;
use open20\amos\layout\utility\AmosIconsBi;


/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var open20\amos\events\models\search\EventGroupReferentSearch $model
 */
$this->title                   = Yii::t('amoscore', 'Direzioni Generali');
$this->params['breadcrumbs'][] = ['label' => '', 'url' => ['/events']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-group-referent-index">
    <?= $this->render('_search', ['model' => $model, 'originAction' => Yii::$app->controller->action->id]);
    ?>

    <?=
    DataProviderView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $model,
        'currentView' => $currentView,
        'gridView' => [
            'columns' => [
                'denominazione',
                'descrizione:striptags',
                'note:striptags',
                [
                    'class' => 'open20\amos\core\views\grid\ActionColumn',
                    'template' => '{team}{view}{update}{delete}',
                    'buttons' => [
                        'team' => function ($url, $model) {
                            $createUrlParams = [
                                '/events/event-group-referent-mm/index',
                                'group_id' => $model['id']
                            ];
                            return Html::a(AmosIconsBi::show('ic_supervisor_account', 'primary'), \Yii::$app->urlManager->createUrl($createUrlParams), [
                                    'class' => "btn btn-xs btn-icon",
                                    'data-toggle' => 'tooltip',
                                    'title' => AmosEvents::t('amosevents', 'Associa utenti')], true);

                            return $btn;
                        },
                        'view' => function($url, $model){
                            return Html::a(AmosIconsBi::show('ic_insert_drive_file', 'primary'), $url, [
                                'class' => "btn btn-xs btn-icon",
                                'data-toggle' => 'tooltip',
                                'title' => AmosEvents::t('amosevents', 'Leggi')], true);
                        },
                        'update' => function($url, $model){
                            return Html::a(AmosIconsBi::show('ic_edit', 'primary'), $url, [
                                'class' => "btn btn-xs btn-icon",
                                'data-toggle' => 'tooltip',
                                'title' => AmosEvents::t('amosevents', 'Modifica')], true);
                        },
                        'delete' => function($url, $model){
                            return Html::a(AmosIconsBi::show('ic_delete', 'danger'), $url, [
                                'class' => "btn btn-xs btn-icon",
                                'data-toggle' => 'tooltip',
                                'title' => AmosEvents::t('amosevents', 'Elimina')], true);
                        }

                    ],
                ],
            ],
        ],
        /* 'listView' => [
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
          ] */
    ]);
    ?>

</div>
