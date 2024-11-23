<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\webmeeting
 * @category   WebMeeting View
 */
use open20\webmeeting\WebMeetingModule;

use open20\amos\core\views\DataProviderView;
use open20\amos\core\views\grid\ActionColumn;
use open20\amos\core\utilities\ModalUtility;
use open20\amos\core\utilities\StringUtils;
use open20\amos\core\icons\AmosIcons;

use yii\helpers\Html;

$this->title = WebMeetingModule::_t('#webmeeting_list');
$this->params['breadcrumbs'][] = $this->title;
$actionColumn = '{view}{update}{invitee}{connect}{goto}{delete}';

$loginUser = $this->params['loginUser'];
$canUpdate = $this->params['canUpdate'];
$todayDateTime = $this->params['todayDateTime'];
?>
<div class="web-meeting-model-index">

    <?= $this->render('_search', [
        'model' => $model,
        'backofficeUserListDataProvider' => $this->params['backofficeUserListDataProvider'],
    ]);
    ?>
    <?= $this->render('_order', ['model' => $model]); ?>

    <?= DataProviderView::widget([
        'dataProvider' => $dataProvider,
        'currentView' => $currentView,
        'gridView' => [
            'columns' => [
                [
                    'attribute' => 'start',
                    'label' => WebMeetingModule::_t('#idx_start_date'),
                    'value' => function ($model) {
                        return empty($model->start)
                            ? '-'
                            : Yii::$app->formatter->asDatetime($model->start, null);
                    }
                ],
                [
                    'attribute' => 'end',
                    'label' => WebMeetingModule::_t('#idx_end_date'),
                    'value' => function ($model) {
                        return empty($model->end)
                            ? '-'
                            : Yii::$app->formatter->asDatetime($model->end, null);
                    }
                ],
                'title',
                'agenda' => [
                    'attribute' => 'agenda',
                    'label' => WebMeetingModule::_t('#idx_agenda'),
                    'value' => function ($model) {
                        return empty($model->agenda)
                            ? '-'
                            : StringUtils::shortText($model->agenda, WebMeetingModule::WEBMEETING_SHORT_TEXT_255);
                    },
                    'format' => 'html'
                ],
                'created_by' => [
                    'attribute' => 'created_by',
                    'label' => WebMeetingModule::_t('#idx_created_by'),
                    'value' => function ($model) {
                        return empty($model->createdUserProfile->id)
                            ? '-'
                            : Html::a(
                                $model->createdUserProfile->nomeCognome,
                                ['/admin/user-profile/view', 'id' => $model->createdUserProfile->id],
                                [
                                    'title' => WebMeetingModule::_t('#idx_open_profile', [
                                        'nome_profilo' => $model->createdUserProfile->nomeCognome
                                    ])
                                ]
                            );
                    },
                    'format' => 'html'
                ],
                [
                    'class' => ActionColumn::class,
                    'template' => $actionColumn,
                    'buttons' => [
                        'view' => function ($url, $model) use ($loginUser, $canUpdate) {
                            if (($model->created_by == Yii::$app->user->id) || ($canUpdate == true)) {
                                $action = WebMeetingModule::getViewLink($model->id, '-history');
                                $options = [
                                    'class' => 'btn btn-tools-secondary',
                                    'title' => WebMeetingModule::_t('#view'),
                                ];
                                
                                return Html::a(AmosIcons::show('file'), $action, $options);
                            }
                        },
                    ]
                ],
            ]
        ],
    ]);
    ?>
</div>
