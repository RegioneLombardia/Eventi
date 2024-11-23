<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    @vendor/open20/amos-events/src/views 
 */
/**
 * @var yii\web\View $this
 * @var open20\amos\events\models\EventWebexAccounts $model
 * @var open20\amos\admin\models\UserProfile $dataProvider
 */
$this->title = Yii::t('amosevents', 'Assegna utente a profilo Webex ').$modelWebexAccounts->email_webex;
$this->params['breadcrumbs'][] = ['label' => '', 'url' => ['/event-webex']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('amoscore', 'Event Webex'), 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => strip_tags($model), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('amoscore', 'Aggiorna');
?>
<div class="event-location-update">

<?=
$this->render('_form',
        [
            'modelWebexAccounts' => $modelWebexAccounts,
            'modelSearch' => $modelSearch,
            'dataProvider' => $dataProvider,
        ]
);
?>

</div>
