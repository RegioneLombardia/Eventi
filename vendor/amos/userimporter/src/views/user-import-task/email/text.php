<?php

use amos\userimporter\Module;

$appName = \Yii::$app->name;
?>


<?php
echo \Yii::t('amosuserimport', "Gentile {NOME} {COGNOME}<br>
        Sei stato invitato a registrarti alla piattaforma <strong>$appName</strong>");
?>

