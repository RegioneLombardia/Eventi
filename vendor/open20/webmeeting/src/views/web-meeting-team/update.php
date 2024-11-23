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

$this->title = WebMeetingModule::_t('#update_webmeeting_team', [
    'name' => $model->title,
]);
$this->params['breadcrumbs'][] = ['label' => WebMeetingModule::_t('#webmeeting_team'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = WebMeetingModule::_t('#update');
?>
<div class="web-meeting-model-update">

    <?= $this->render('_form', ['model' => $model, ]) ?>

</div>
