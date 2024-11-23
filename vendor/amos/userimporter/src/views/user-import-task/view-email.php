<?php
/**
 * @var $model \amos\userimporter\models\UserImportTask
 */
use \amos\userimporter\Module;
use yii\helpers\Html;

$this->title = Yii::t('amosuserimporter', "Email di invito per l'importazione {name}",[
    'name' => $model->name
]);
$this->params['breadcrumbs'][] = ['label' => '', 'url' => ['/userimporter']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-3">
        <p>
            <strong><?= Module::t('amosevents', 'Oggetto invito') .': '?></strong>
        </p>
    </div>
    <div class="col-md-9">
        <?= $model->email_subject  ?>
    </div>
</div>


<div class="row">
    <div class="col-md-3">
        <p>
            <strong><?=  Module::t('amosevents', 'Testo invito').': ' ?></strong>
        </p>
    </div>
    <div class="col-md-9">
        <p><?=$model->email_text ?></p>
    </div>
</div>

<div id="form-actions" class="bk-btnFormContainer pull-right">
    <?= Html::a(Yii::t('amoscore', 'Chiudi'), '/userimporter/user-import-task/index', ['class' => 'btn btn-secondary']); ?>
</div>
