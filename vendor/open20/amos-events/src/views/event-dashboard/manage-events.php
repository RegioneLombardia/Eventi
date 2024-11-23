<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\events\views\event
 * @category   CategoryName
 */

use open20\amos\core\helpers\Html;
use open20\amos\core\icons\AmosIcons;
use open20\amos\core\utilities\ModalUtility;
use open20\amos\core\views\DataProviderView;
use open20\amos\events\AmosEvents;

use yii\web\View;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var open20\amos\events\models\search\EventSearch $model
 * @var string $currentView
 */


/** @var AmosEvents $eventsModule */
$eventsModule = Yii::$app->getModule(AmosEvents::getModuleName());
$this->title = $title;
$this->params['breadcrumbs'][] = $this->title;

$wizardAsset = \open20\amos\events\assets\WizardEventAsset::register($this);

$script = <<< JS
    // Trova l'elemento con la classe specifica
var elemento = document.querySelector('.fa-sort-amount-down-alt');

// Verifica se l'elemento è stato trovato
if (elemento) {
  // Sostituisci il nome di classe esistente con uno nuovo
  elemento.classList.replace('fa-sort-amount-down-alt', 'fa-sort-amount-down');
}
JS;
$this->registerJs($script);
?>
<div class="event-index">
    <?php

    echo $this->render('_search', ['model' => $model, 'originAction' => Yii::$app->session->get('previousUrl')]);

    //    echo $this->render('_search', ['model' => $model,'originAction' => Yii::$app->session->get('previousUrl')]);
    //    echo $this->render('_order', ['model' => $model,'originAction' => Yii::$app->session->get('previousUrl')]);

    //    if (\Yii::$app->user->can('EVENT_CREATE')) {
    //        echo \yii\helpers\Html::a(AmosEvents::t('amosevents', 'Crea nuovo'), '/events/wizard', ['class' => 'btn btn-primary btn-xs pull-right']);
    //    }
    echo DataProviderView::widget([
        'dataProvider' => $dataProvider,
        'currentView' => $currentView,
        'gridView' => [
            'columns' => [
                'title',
                'event_type_id' => [
                    'attribute' => 'event_type_id',
                    'label' => $model->getAttributeLabel('eventType'),
                    'value' => 'eventType.title'
                ],
                'multilanguage' => [
                    'attribute' => 'multilanguage',
                    'label' => $model->getAttributeLabel('Multilingua'),
                    'format' => 'boolean',
                    'value' => function ($model) {
                        if (is_null($model->multilanguage)) {
                            return false;
                        }
                        return $model->multilanguage;
                    },
                ],
                'status' => [
                    'attribute' => 'status',
                    'value' => function ($model) {
                        return $model->getWorkflowBaseStatusLabel();
                    }
                ],
                'begin_date_hour:datetime',
                [
                    'class' => 'open20\amos\core\views\grid\ActionColumn',
                    'template' => '{info}{community}{webex}{view}{duplicate}',
                    'buttons' => [
                        'info' => function ($url, $model) {
                            $userEventCreator = $model->userCreator;
                            $userLastUpdate = $model->getUserLastUpdate();
                            $str =  AmosEvents::t('amosevents', '<strong>Creatore evento</strong>') . ': ' . ($userEventCreator ? $userEventCreator->userProfile->nomeCognome : '') . "<br>";
                            $str .= AmosEvents::t('amosevents', '<strong>Autore ultima modifica</strong>') . ': ' . ($userLastUpdate ? $userLastUpdate->userProfile->nomeCognome : '');

                            return Html::a(AmosIcons::show('info-outline'), 'javascript:void(0)', [
                                'class' => 'btn btn-secondary btn-xs mx-1',
                                'data-container' => 'body', 'data-toggle' => 'popover', 'data-trigger' => 'hover', 'data-html' => 'true', 'title' => 'Info', 'data-content' => $str
                            ]);
                        },
                        'duplicate' => function ($url, $model) {
                            return Html::a(AmosIcons::show('copy'), ['/events/event-dashboard/duplicate', 'id' => $model->id], [
                                'class' => 'btn btn-secondary btn-xs mx-1',
                                'title' => 'Duplica evento',
                            ]);
                        },
                        'view' => function ($url, $model) {
                            return Html::a('Visualizza', ['/events/event-dashboard/view', 'id' => $model->id], [
                                'class' => 'btn btn-secondary btn-xs ml-1',
                                'title' => AmosEvents::t('amosevents', "Gestisci evento")
                            ]);
                        },
                        'community' => function ($url, $model) {
                            return \open20\amos\events\utility\EventsUtility::getButtonCommunity($model);
                        },
                        'webex' => function ($url, $model) {
                            if ($model->eventType->webmeeting_webex && !empty($model->webmeeting_webex_id) && !empty($model->webMeetingWebex)) {
                                $btnWebex = '';
                                if (!$model->webexIsClosed()) {
                                    if ($model->canGoToWebexUrl()) {
                                        $btnWebex = Html::a('Avvia Videoconferenza', $model->webMeetingWebex->web_link, [
                                            'class' => 'btn btn-secondary btn-xs',
                                            'target' => '_blank',
                                            'title' => AmosEvents::t('amosevents', "Avvia la videoconferenza")
                                        ]);
                                    } else {
                                        $btnWebex = Html::a('Avvia Videoconferenza', 'javascript:void(0)', [
                                            'class' => 'btn btn-secondary btn-xs',
                                            'disabled' => true,
                                            'title' => AmosEvents::t('amosevents', "La sessione di videoconferenza non è ancora iniziata. Quando il pulsante sarà attivo sarai rediretto all'area dedicata alla videoconferenza.")
                                        ]);
                                    }
                                }
                                return $btnWebex;
                            }
                            return '';
                        },
                    ]
                ]
            ],
        ],
        'listView' => [
            'itemView' => '_itemListEventBackend',
            'options' => [
                'class' => 'event-card-content',
            ],
            'itemsContainerOptions' => [
                'class' => "row variable-gutters",
                "role" => "listbox",
                "data-role" => "list-view",
            ],
            'itemOptions' => [
                'class' => "col-md-4 col-sm-6 my-4",
            ],
        ],
        /*
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
                'icon' => 'iconaMarker',
                ]
                ],*/
        /**  'calendarView' => [
     * 'itemView' => '_calendar',
     * 'options' => [
     * 'lang' => 'it',
     * ],
     * 'eventConfig' => [
     * 'id' => 'id',
     * 'title' => 'eventTitle',
     * 'start' => 'begin_date_hour',
     * 'end' => 'end_date_hour',
     * 'color' => 'eventColor',
     * 'url' => 'eventUrl'
     * ],
     * 'array' => false,//se ci sono più eventi legati al singolo record
     * //'getEventi' => 'getEvents'//funzione da abilitare e implementare nel model per creare un array di eventi legati al record
     * ]*/
    ]); ?>
</div>