<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    @backend/views
 */
/**
 * @var yii\web\View $this
 * @var open20\amos\groups\models\GroupsComunication $model
 */

$this->title = Yii::t('amospodcast', 'Aggiorna comunicazione');
$this->params['breadcrumbs'][] = ['label' => '', 'url' => ['/groups']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('amoscore', 'Groups Comunication'), 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => strip_tags($model), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('amoscore', 'Aggiorna');
?>
<div class="groups-comunication-update">

    <?= $this->render('_form', [
        'group' => $group,
        'model' => $model,
        'fid' => NULL,
        'dataField' => NULL,
        'dataEntity' => NULL,
    ]) ?>

</div>
