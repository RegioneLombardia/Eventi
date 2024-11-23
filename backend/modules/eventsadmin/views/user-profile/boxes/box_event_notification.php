<?php
$modelsEnabled = $moduleCwh->modelsEnabled;
?>
<?php
$notificationConf = \open20\amos\notificationmanager\models\NotificationConf::find()
    ->andWhere(['user_id' => $model->user->id])->one();
$values = [];
$n_confs = 0;
$notifications_enabled = false;
if($notificationConf){
    $notifications_enabled = $notificationConf->notifications_enabled;
   foreach ($notificationConf->notificationConfContents as $conf){
       $values[$conf->models_classname_id]['push'] = $conf->push_notification;
       $values[$conf->models_classname_id]['email'] = $conf->email;
       $n_confs ++;
   }
}
?>
<div class="col-md-12">
    <?php foreach ($modelsEnabled as $classname) {
        if ($classname != 'open20\amos\events\models\Event') {
            $grammar = null;
            $modelClassname = \open20\amos\core\models\ModelsClassname::find()->andWhere(['classname' => $classname])->one();
            $object = new $classname;
            if($object) {
                $grammar = $object->getGrammar();
            }

            if ($modelClassname && $grammar) { ?>
                <p><?= \Yii::t('app', "Vuoi ricevere notifiche relative alla pubblicazione di {x} nelle community d'evento?",[
                        'x' => $grammar->getIndefiniteArticle(). ' '. $grammar->getModelSingularLabel()
                    ]) ?></p>
                <div class="row">
                    <div class="col-md-6">
                        <label><?= \Yii::t('app', "Notifica email") ?></label>
                        <?= \kartik\widgets\SwitchInput::widget([
                            'name' => "notificationContent[$modelClassname->id][email]",
                            'value' => ($n_confs > 0) ? ((!empty($values[$modelClassname->id]['email'])) ? $values[$modelClassname->id]['email'] : false) : true,
                            'options' => ['id' => "id-notification-content-$modelClassname->id-email"],
                            'pluginOptions' => [
                                'size' => 'mini',
                                //                    'onText' => AmosIcons::show('check-circle'),
                                //                    'offText' => AmosIcons::show('circle-o')
                                'onText' => 'Si',
                                'offText' => 'No'
                            ],
                        ]) ?>
                    </div>
                    <div class="col-md-6">
                        <label><?= \Yii::t('app', "Notifica push (App mobile)") ?></label>

                        <?= \kartik\widgets\SwitchInput::widget([
                            'name' => "notificationContent[$modelClassname->id][push]",
                            'value' => ($n_confs > 0) ? ((!empty($values[$modelClassname->id]['push'])) ? $values[$modelClassname->id]['push'] : false):true,
                            'options' => ['class' => 'pull-right', 'id' => "id-notification-content-$modelClassname->id-push"],
                            'pluginOptions' => [
                                'size' => 'mini',
//                                                    'onText' => \open20\amos\core\icons\AmosIcons::show('check-circle'),
//                                                    'offText' => \open20\amos\core\icons\AmosIcons::show('circle-o')
                                'onText' => 'Si',
                                'offText' => 'No'
                            ],
                        ]) ?>
                    </div>
                </div>
                <?= \yii\helpers\Html::hiddenInput("notificationContent[$modelClassname->id][enable]", true)?>
            <?php }
        }
    } ?>
    <div class="row">
        <p><?= \Yii::t('app', "Vuoi ricevere notifiche relativa alla variazioni (data e luogo) di un evento a cui ti sei registrato?") ?></p>
        <div class="col-md-6">
            <label><?= \Yii::t('app', "Notifica email") ?></label>
            <?= \kartik\widgets\SwitchInput::widget([
                'name' => "EventChangeAttributes[email]",
                'value' => $model->enable_email_change_event,
                'options' => ['id' => "id-event-change-attributes-email"],
                'pluginOptions' => [
                    'size' => 'mini',
                    'onText' => 'Si',
                    'offText' => 'No'
                ],
            ]) ?>
        </div>

        <div class="col-md-6">
            <label><?= \Yii::t('app', "Notifica push (App mobile)") ?></label>

            <?= \kartik\widgets\SwitchInput::widget([
                'name' => "EventChangeAttributes[push]",
                'value' => $model->enable_push_change_event,
                'options' => ['id' => "id-event-change-attributes-push"],
                'pluginOptions' => [
                    'size' => 'mini',
                    'onText' => 'Si',
                    'offText' => 'No'
                ],
            ]) ?>
        </div>
    </div>
</div>

