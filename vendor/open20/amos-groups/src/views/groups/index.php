<?php

use open20\amos\core\helpers\Html;
use open20\amos\core\views\DataProviderView;
use yii\widgets\Pjax;
use open20\amos\core\icons\AmosIcons;
use open20\amos\groups\Module;
use yii\bootstrap\Modal;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var \open20\amos\groups\models\search\GroupsSearch $model
 */
$this->title = Yii::t('amosgroups', 'Gruppi');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="groups-index">
    <?php // echo $this->render('_search', ['model' => $model]);  ?>

    <p>
        <?php /* echo         Html::a(Yii::t('cruds', 'Nuovo {modelClass}', [
          'modelClass' => 'Groups',
          ])        , ['create'], ['class' => 'btn btn-amministration-primary']) */ ?>
    </p>
    <?php
    //    $isVisible                     = true;
    $isVisible = false;
    $cwh = Yii::$app->getModule("cwh");
    $community = Yii::$app->getModule("community");
    if (isset($cwh) && isset($community)) {
        $cwh->setCwhScopeFromSession();
        if (!empty($cwh->userEntityRelationTable)) {
            $entityId = $cwh->userEntityRelationTable['entity_id'];
            if (!empty($entityId)) {
                $isVisible = false;
            }
        }
    }
    ?>
    <?php
    echo DataProviderView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $model,
        'currentView' => $currentView,
        'gridView' => [
            'columns' => [
//                'id',
//                'parent_id',
                'name',
                [
                    'attribute' => 'parent_id',
                    'visible' => $isVisible
                ],
                [
                    'value' => function ($model) {
                        return $model->getUsers()->count();
                    },
                    'label' => Module::t('amosgroups', 'Utenti')
                ],

                [
                    'value' => function ($model) {
                        return $model->getGroupsComunications()->count();
                    },
                    'label' => Module::t('amosgroups', 'Comunicazioni')
                ],

                ['attribute' => 'created_at', 'format' => ['datetime', (isset(Yii::$app->modules['datecontrol']['displaySettings']['datetime']))
                    ? Yii::$app->modules['datecontrol']['displaySettings']['datetime'] : 'd-m-Y H:i:s A']],
                [
                    'class' => 'open20\amos\core\views\grid\ActionColumn',
                    'template' => '{comunications}{view}{update}{delete}',
                    'buttons' => [
                        'comunications' => function ($url, $model) {
                            return Html::a(AmosIcons::show('account-box-mail'),
                                Yii::$app->urlManager->createUrl(['/groups/groups-comunication/index', 'idGroup' => $model->id]),
                                [
                                    'class' => 'btn btn-tool-secondary',
                                    'title' => Module::t('amosadmin', 'Vedi comunicazioni del gruppo')
                                ]);
                        },

                        'send' => function ($url, $model) {
                            /** @var \open20\amos\admin\models\UserProfile $model */
                            $utente = Yii::$app->getUser();
                            $community = new \open20\amos\community\models\Community;
                            if ($community->isCommunityManager() || $utente->can('ADMIN')) {
                                return Html::a(AmosIcons::show('email'),
                                    Yii::$app->urlManager->createUrl(['/groups/groups/send-email-to-group', 'idGroup' => $model->id]),
                                    [
                                        'class' => 'btn btn-tool-secondary',
                                        'title' => Module::t('amosadmin', 'Invia una notifica al gruppo')
                                    ]);
                            } else {
                                return '';
                            }
                        }
                    ]
                ],
            ],
        ],
        /* 'listView' => [
          'itemView' => '_item'
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
          'icon' => 'iconaMarker',
          ]
          ],
          'calendarView' => [
          'itemView' => '_calendar',
          'clientOptions' => [
          //'lang'=> 'de'
          ],
          'eventConfig' => [
          //'title' => 'titoloEvento',
          //'start' => 'data_inizio',
          //'end' => 'data_fine',
          //'color' => 'coloreEvento',
          //'url' => 'urlEvento'
          ],
          'array' => false,//se ci sono più eventi legati al singolo record
          //'getEventi' => 'getEvents'//funzione da abilitare e implementare nel model per creare un array di eventi legati al record
          ] */
    ]);
    ?>

</div>