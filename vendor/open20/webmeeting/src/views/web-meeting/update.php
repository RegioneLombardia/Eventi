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

$this->title = WebMeetingModule::_t('#update_webmeeting', [
    'name' => $model->title,
]);
$this->params['breadcrumbs'][] = ['label' => WebMeetingModule::_t('#webmeeting'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = WebMeetingModule::_t('amoswebmeeting', '#update');
?>
<div class="web-meeting-model-update">
    <?= $this->render('_form', [
        'model' => $model,
        'userDataProvider' => $userDataProvider,
        'modelUser' => $modelUser,
        'coHostUserEmail' => $coHostUserEmail,
        'timezone' => $timezone,
        'modelInvitees' => $modelInvitees,
        'dualListInvited' => $dualListInvited,
        'invitees' => $invitees,
        'canUpdate' => $canUpdate,
        'scope' => $scope,
        'currentView' => $currentView,
    ]) ?>
</div>
