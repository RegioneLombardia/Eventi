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
use open20\amos\core\icons\AmosIcons;

use yii\helpers\Html;

$this->title = WebMeetingModule::_t('#webmeeting_team_list');
$this->params['breadcrumbs'][] = $this->title;
$actionColumn = '{view}{update}{delete}';
?>

<div class="web-meeting-model-index">

    <?= $this->render('_search', ['model' => $model, ]); ?>
    <?= $this->render('_order', ['model' => $model, ]); ?>

    <?= DataProviderView::widget([
        'dataProvider' => $dataProvider,
        'currentView' => $currentView,
        'gridView' => [
            'columns' => [
                'id',
                'team_id',
                'name',
                'created_by' => [
                    'attribute' => 'created_by',
                    'label' => WebMeetingModule::_t('#idx_created_by'),
                    'value' => function ($model) {
                        return empty($model->created_user_profile->id)
                            ? '-'
                            : Html::a(
                                $model->created_user_profile->getNomeCognome(),
                                ['/admin/user-profile/view', 'id' => $model->created_user_profile->id],
                                ['title' => WebMeetingModule::_t(
                                    'amoswebmeeting',
                                    '#idx_open_profile', ['nome_profilo' => $model->created_user_profile->getNomeCognome()]
                                )]
                            );
                    },
                    'format' => 'html'
                ],
                'created_at',
                'updated_by' => [
                    'attribute' => 'updated_by',
                    'label' => '#idx_update_by',
                    'value' => function ($model) {
                        return empty($model->createdUserProfile->id)
                            ? '-'
                            : Html::a(
                                $model->createdUserProfile->getNomeCognome(),
                                ['/admin/user-profile/view', 'id' => $model->createdUserProfile->id],
                                [
                                    'title' => WebMeetingModule::_t('#idx_open_profile', [
                                        'nome_profilo' => $model->createdUserProfile->getNomeCognome()
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
                            $action = WebMeetingModule::getViewLink($model->id, '-team');
                            $options = [
                                'class' => 'btn btn-tools-secondary',
                                'title' => WebMeetingModule::_t('#view'),
                            ];

                            return Html::a(AmosIcons::show('file'), $action, $options);
                        },
                        'update' => function ($url, $model) use ($loginUser, $canUpdate) {
                            if (($model->created_by == Yii::$app->user->id) || ($canUpdate)) {
                                $action = WebMeetingModule::getUpdateLink($model->id, '-team');
                                $options = ModalUtility::getBackToEditPopup(
                                    $model,
                                    WebMeetingModule::WEBMEETING_TEAM_MODEL_UPDATE,
                                    $action,
                                    [
                                        'class' => 'btn btn-tools-secondary',
                                        'title' => WebMeetingModule::_t('#update'),
                                        'data-pjax' => '0'
                                    ]
                                );
                                return Html::a(AmosIcons::show('edit'), $action, $options);
                            }
                        },
                        'delete' => function ($url, $model) use ($loginUser, $canUpdate) {
                            if (($model->created_by == Yii::$app->user->id) || ($canUpdate)) {
                                $action = WebMeetingModule::getDeleteLink($model->id, '-team');
                                $options = ModalUtility::getBackToEditPopup(
                                    $model,
                                    WebMeetingModule::WEBMEETING_TEAM_MODEL_DELETE,
                                    $action,
                                    [
                                        'class' => 'btn btn-danger-inverse',
                                        'title' => WebMeetingModule::_t('#delete'),
                                        'data-confirm' => WebMeetingModule::_t('#confirm_delete'),
                                        'data-method' => "post",
                                        'data-pjax' => "0"
                                    ]
                                );

                                return Html::a(AmosIcons::show('delete'), $action, $options);
                            }
                        }
                    ]
                ],
            ]
        ],
    ]);
    ?>
</div>
