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

$this->title = WebMeetingModule::_t('#webmeeting_create');
$this->params['breadcrumbs'][] = ['label' => WebMeetingModule::_t('#webmeeting'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="web-meeting-model-create">
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