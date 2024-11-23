<?php

use \yii\helpers\Html;

?>
<div class="search-esplora-eventi">
    <?php
    \yii\bootstrap\ActiveForm::begin(['action' => '/ricerca','method' => 'get']);
    echo Html::textInput('searchtext', null, ['placeholder' => \Yii::t('site',"Cerca eventi...")]);
    echo Html::submitButton("<span class='am am-search'></span>", ['class' => '']);
    \yii\bootstrap\ActiveForm::end();
    ?>
</div>
