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

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = strip_tags($model->title);
$this->params['breadcrumbs'][] = ['label' => '', 'url' => ['/web-meeting']];
$this->params['breadcrumbs'][] = ['label' => 'WebMeeting', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="webmeeting-assets-view">
<?= DetailView::widget([
    'model' => $model,
    'attributes' => [
        'id',
        'title',
        'agenda:ntext',
        'meeting_number',
        'meeting_id',
        'meeting_type',
        'state',
        'timezone',
        'host_user_id',
        'host_display_name',
        'host_email',
        'host_key',
        'site_url',
        'web_link',
        'password',
        'sip_address',
        'dial_in_ip_address',
        'enabled_auto_record_meeting',
        'allow_any_user_to_be_co_host',
        'telephony',
        'created_at',
        'created_by' => [
            'attribute' => 'created_by',
            'label' => WebMeetingModule::_t('#idx_created_by'),
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
    ],
]) ?>
</div>

<div id="form-actions" class="bk-btnFormContainer pull-left">
<?= Html::a(
        WebMeetingModule::_t('#close'), 
        WebMeetingModule::getMyIndexLink(),
        ['class' => 'btn btn-secondary']
    );
?>
</div>
