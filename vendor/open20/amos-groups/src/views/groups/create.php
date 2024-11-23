<?php

use open20\amos\core\helpers\Html;

/**
* @var yii\web\View $this
* @var backend\modules\pateradmin\models\Groups $model
*/

$this->title = Yii::t('amosgroups', 'Create Group');
$this->params['breadcrumbs'][] = ['label' => Yii::t('cruds', 'Groups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="groups-create">
    <?= $this->render('_form', [
        'model' => $model,
        'dataProvider' => $dataProvider
    ]) ?>

</div>
