<?php

use open20\amos\layout\utility\AmosIconsBi;

$skipCycle = false;
if(empty($timePeriodStart)){
    $skipCycle = true;
    $timePeriodStart [$n] = 'xxx';
}
?>
<?php foreach((Array) $timePeriodStart as $key => $date){?>
<?php //for ($i = 1; $i <= $tot; $i++) {
    if(!$skipCycle){
        $n = $key;
    }
//    ?>
    <div class="row row-time-period">
        <div class="col-md-3">
            <?= $form->field($modelForm, "timePeriodStart[$n]")->widget(\yii\widgets\MaskedInput::className(), [
                'id' => 'period-start-' . $n,
                'mask' => '99:99',
            ])->label('Ora inizio') ?>

        </div>
        <div class="col-md-3">
            <?= $form->field($modelForm, "timePeriodEnd[$n]")->widget(\yii\widgets\MaskedInput::className(), [
                'id' => 'period-start-' . $n,
                'mask' => '99:99',
            ])->label('Ora Fine') ?>
        </div>
        <div class="col-md-2 mt-3">
            <?= \yii\helpers\Html::a(AmosIconsBi::show('ic_close', 'danger'), '#', ['id' => 'del-period-' . $n, 'class' => 'deleteTime btn btn-icon']) ?>
        </div>
    </div>
<?php } ?>
