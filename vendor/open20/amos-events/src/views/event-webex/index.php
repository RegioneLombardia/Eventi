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
use open20\amos\layout\utility\AmosIconsBi;
use open20\amos\events\AmosEvents;
use yii\widgets\Pjax;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var open20\amos\events\models\search\EventLocationSearch $model
 */
$this->title = Yii::t('amoscore', 'Webex Accounts');
$this->params['breadcrumbs'][] = ['label' => '', 'url' => ['/events']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-location-index">


    <?=
    DataProviderView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $model,
        'currentView' => $currentView,
        'gridView' => [
            'columns' => [
                'email_webex',
                'user' => [
                    'attribute' => AmosEvents::t('amosevents', '#user_id'),
                    'value' => function ($model) {
                        if ($model->user_id && is_null($model->deleted_at)) {
                            $profile = \open20\amos\admin\models\UserProfile::find()
                                            ->andWhere(['user_id' => $model->user_id])->one();
                            return $profile->nome . " " . $profile->cognome;
                        }

                        return AmosEvents::t('amosevents', '#non_assegnato');
                    }
                ],
                'email' => [
                    'attribute' => AmosEvents::t('amosevents', '#user_email'),
                    'value' => function ($model) {
                        if ($model->user_id && is_null($model->deleted_at)) {
                            $profile = \open20\amos\core\user\User::find()
                                            ->andWhere(['id' => $model->user_id])->one();
                            return $profile->email;
                        }

                        return AmosEvents::t('amosevents', '#non_assegnato');
                    }
                ],
                [
                    'class' => 'open20\amos\core\views\grid\ActionColumn',
                    'template' => '{update}{delete}',
                    'buttons' => [
                        'delete' => function ($url, $model) {
                            if ($model->user_id && is_null($model->deleted_at)) {
                                $createUrlParams = [
                                    '/events/event-webex/delete',
                                    'id' => $model->id,
                                ];
                                return Html::a(AmosIconsBi::show('ic_delete', 'danger'), \Yii::$app->urlManager->createUrl($createUrlParams), [
                                            'class' => "btn btn-xs btn-icon",
                                            'data-toggle' => 'tooltip',
                                            'title' => AmosEvents::t('amosevents', 'Elimina')]);
                            }
                            return '';
                        },
                        'update' => function ($url, $model) {
                            $createUrlParams = [
                                '/events/event-webex/update',
                                'id' => $model->id,
                            ];

                            return Html::a(AmosIconsBi::show('ic_edit', 'secondary'), \Yii::$app->urlManager->createUrl($createUrlParams), [
                                'class' => "btn btn-xs btn-icon",
                                'data-toggle' => 'tooltip',
                                'title' => AmosEvents::t('amosevents', 'Modifica')]);
                        },
                        'delete' => function ($url, $model) {
                            if (!empty($model->user_id)) {
                                return \yii\helpers\Html::a(AmosIconsBi::show('ic_delete', 'danger'), $url, [
                                    'class' => 'btn btn-xs btn-icon',
                                    'data-confirm' => AmosEvents::t('amosevents', "Sei sicuro di eliminare l'assegnazione?"),
                                    'title' => AmosEvents::t('amosevents', "Elimina assgnazione"),
                                ], true);
                            }
                        }
                    ],
                ],
            ],
        ],
    ]);
    ?>

</div>
