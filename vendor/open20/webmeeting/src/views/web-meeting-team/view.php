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
$this->params['breadcrumbs'][] = ['label' => '', 'url' => ['/web-meeting-team']];
$this->params['breadcrumbs'][] = ['label' => 'WebMeeting', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="webmeeting-assets-view">
<?= DetailView::widget([
    'model' => $model,
    'attributes' => [
        'id',
        'title' => 'title',
        'description:ntext',
        'created_at'
        ],
]) ?>
</div>

<div id="form-actions" class="bk-btnFormContainer pull-left">
<?= Html::a(
        WebMeetingModule::_t('#close'), 
        WebMeetingModule::getMyIndexLink('-team'),
        ['class' => 'btn btn-secondary']
    );
?>
</div>
