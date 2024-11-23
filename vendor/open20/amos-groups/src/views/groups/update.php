<?php

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var \open20\amos\groups\models\Groups $model
*/

$this->title = Yii::t('cruds', 'Aggiorna ') .$model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('cruds', 'Groups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="groups-update">

    <?= $this->render('_form', [
        'model' => $model,
        'dataProvider' => $dataProvider,
        'dataProviderSelected' => $dataProviderSelected,
    ]) ?>

</div>
