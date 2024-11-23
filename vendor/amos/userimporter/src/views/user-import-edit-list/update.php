<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    @vendor/amos/userimporter/src/views 
 */
/**
* @var yii\web\View $this
* @var amos\userimporter\models\UserImportEditList $model
*/

$this->title = Yii::t('amoscore', 'Aggiorna', [
    'modelClass' => 'User Import Edit List',
]);
//$this->params['breadcrumbs'][] = ['label' => '', 'url' => ['/userimporter']];
//$this->params['breadcrumbs'][] = ['label' => Yii::t('amoscore', 'User Import Edit List'), 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => strip_tags($model), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][]= Yii::t('amoscore', 'Aggiorna');
?>
<div class="user-import-edit-list-update">

    <?= $this->render('_form', [
    'model' => $model,
    'fid' => NULL,
    'dataField' => NULL,
    'dataEntity' => NULL,
    ]) ?>

</div>
