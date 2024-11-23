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

$this->title = Yii::t('amoscore', 'Crea lista');
$this->params['titleSection']  = $this->title;

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-import-edit-list-create">
    <?= $this->render('_form', [
    'model' => $model,
    'fid' => NULL,
    'dataField' => NULL,
    'dataEntity' => NULL,
    ]) ?>

</div>
