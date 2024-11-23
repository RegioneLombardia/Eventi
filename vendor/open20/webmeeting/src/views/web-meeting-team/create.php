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

$this->title = WebMeetingModule::_t('#webmeeting_team_create');
$this->params['breadcrumbs'][] = ['label' => WebMeetingModule::_t('#webmeeting_team'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="web-meeting-team-model-create">
    <?= $this->render('_form', ['model' => $model,]) ?>
</div>