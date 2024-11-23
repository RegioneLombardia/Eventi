<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    @backend/views
 */

use open20\amos\core\helpers\Html;
use open20\amos\core\views\DataProviderView;
use yii\widgets\Pjax;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var open20\amos\groups\models\search\GroupsComunicationSearch $model
 */

$this->title = \open20\amos\groups\Module::t('amosgroups', "Comunicazioni per il gruppo '{group}'", [
    'group' => $group->name
]);
$this->params['breadcrumbs'][] = ['label' => '', 'url' => ['/groups']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="groups-comunication-index">
    <?= $this->render('_search', ['model' => $model, 'originAction' => Yii::$app->controller->action->id]); ?>

    <?= DataProviderView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $model,
        'currentView' => $currentView,
        'gridView' => [
            'columns' => [

//                [
//                    'attribute' => 'groups.name',
//                    'label' => 'Gruppo'
//                ],

                'subject:striptags',
                [
                    'attribute' => 'statusLabel',
                    'label' => \open20\amos\groups\Module::t('amosgroups', 'Stato')
                ],
                [
                    'attribute' => 'sent_at',
                    'format' => 'datetime'
                ],

                [
                    'class' => 'open20\amos\core\views\grid\ActionColumn',
                    'template' => '{duplicate}{send}{update}{delete}',
                    'buttons' => [
                        'send' => function ($url, $model) {
                            if ($model->status == \open20\amos\groups\models\GroupsComunication::STATUS_DRAFT) {
                                return Html::a(\open20\amos\core\icons\AmosIcons::show('email'),
                                    Yii::$app->urlManager->createUrl(['/groups/groups-comunication/send-email', 'id' => $model->id]),
                                    [
                                        'class' => 'btn btn-tool-secondary',
                                        'title' => \open20\amos\groups\Module::t('amosadmin', 'Invia una notifica al gruppo'),
                                        'data-confirm' => "Sei sicuro di inviare la comunicazione al gruppo?"
                                    ]);
                            }
                        }
                        , 'duplicate' => function ($url, $model) {
                            return Html::a(\open20\amos\core\icons\AmosIcons::show('copy'),
                                Yii::$app->urlManager->createUrl(['/groups/groups-comunication/duplicate', 'id' => $model->id]),
                                [
                                    'class' => 'btn btn-tool-secondary',
                                    'title' => \open20\amos\groups\Module::t('amosadmin', 'Duplica comunicazione'),
                                    'data-confirm' => "Sei sicuro di duplicare la comunicazione?"
                                ]);
                        }
                    ]
                ],
            ],
        ],
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

    <?= \yii\helpers\Html::a(\open20\amos\groups\Module::t('amosgroups', 'Chiudi'), ['/groups/groups/index'], [
            'class' => 'btn btn-secondary'
        ]
    ) ?>
</div>
