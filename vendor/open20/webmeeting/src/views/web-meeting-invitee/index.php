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

$loginUser = Yii::$app->getUser();
$canUpdate = $loginUser->can(WebMeetingModule::ADMIN_WEBMEETING);

$this->title = WebMeetingModule::_t('#webmeeting_list_invitees');
$this->params['breadcrumbs'][] = $this->title;
$actionColumn = '{view}{delete}';
?>

<div class="web-meeting-invitee-model-index">

<?= DataProviderView::widget([
    'dataProvider' => $dataProvider,
    'currentView' => $currentView,
    'gridView' => [
        'columns' => [
            'webmeeting_id' => [
                'label' => WebMeetingModule::_t('#idx_webmeeting_title'),
                'value' => function ($model) {
                    return empty($model->webMeeting->title)
                        ? '-'
                        : $model->webMeeting->title;
                },
            ],
            'user_id' => [
                'attribute' => 'user_id',
                'label' => WebMeetingModule::_t('#idx_user_invitee'),
                'value' => function ($model) {
                    return empty($model->userProfile->id)
                        ? '-'
                        : Html::a(
                            $model->userProfile->getNomeCognome(),
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
            'co_host' => [
                'attribute' => 'co_host',
                'label' => WebMeetingModule::_t('#idx_is_cohost'),
                'value' => function ($model) {
                    return empty($model->co_host)
                        ? WebMeetingModule::_t('#yes')
                        : WebMeetingModule::_t('#no');
                },
                'format' => 'html'
            ],
            [
                'class' => ActionColumn::class,
                'template' => $actionColumn,
                'buttons' => [
                    'delete' => function ($url, $model) use ($loginUser, $canUpdate) {
                        if (($model->created_by == Yii::$app->user->id) || ($canUpdate == true)) {
                            $action = WebMeetingModule::getDeleteLink($model->id, '-invitee');
                            $options = ModalUtility::getBackToEditPopup(
                                $model,
                                WebMeetingModule::WEBMEETING_MODEL_DELETE,
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

<div id="form-actions" class="bk-btnFormContainer pull-left">
<?= Html::a(
    WebMeetingModule::_t('#close'), 
    WebMeetingModule::getMyIndexLink(),
    ['class' => 'btn btn-secondary']);
?>
</div>
