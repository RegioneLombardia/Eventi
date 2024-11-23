<?php

use open20\amos\dashboard\AmosDashboard;
use open20\amos\core\helpers\Html;
use open20\amos\core\icons\AmosIcons;

$object = \Yii::createObject($model['classname']);
?>

<div class="card-widget">
    <div class="chechbox-widget pull-right">
        <label for="<?= \yii\helpers\StringHelper::basename($model['classname']); ?>" class="sr-only"><?= $object->getDescription(); ?></label>
        <input id="<?= \yii\helpers\StringHelper::basename($model['classname']); ?>" type="checkbox" name="amosWidgetsClassnames[]" value="<?= $model['classname']; ?>" <?= (in_array($model['classname'],
    $this->params['widgetSelected']) ? 'checked' : '')
?> />
    </div>
    <div class="dashboard-item">
        <?php
        $object->setUrl('');
        ?>
<?= $object->run(); ?>
    </div>
    <p><?= $object->getDescription(); ?></p>
</div>
