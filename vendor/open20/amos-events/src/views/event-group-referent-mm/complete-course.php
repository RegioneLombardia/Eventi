<?php

use open20\amos\events\AmosEvents;

$title = AmosEvents::t('amosevents', 'Completa il corso');
$text1 = AmosEvents::t('amosevents', "Prima di diventare operatore è necessario iscriversi e completare il corso di formazione.");
$text2 = AmosEvents::t('amosevents', "Successivamente sarai abilitato da un membro dello staff.");
$buttonText = AmosEvents::t('amosevents', "COMPLETA IL CORSO");


if ($courseCompleted) {
    $title = AmosEvents::t('amosevents', 'Corso completato');
    $text1 = AmosEvents::t('amosevents', "Complimenti, hai  completato positivamente il corso di formazione.");
    $text2 = AmosEvents::t('amosevents', "Verrai presto abilitato da un membro dello staff.");
    $buttonText = AmosEvents::t('amosevents', "RIVEDI I MODULI FORMATIVI");
}

$this->title = $title;

?>
<br>
<div class="col-md-12">
    <h5><?= $text1 ?>
        <br>
        <?= $text2 ?>
    </h5>
</div>
<br>

<?php if (!$courseEnrolled) { ?>
    <div class="col-xs-12 m-t-20">
        <?= \yii\helpers\Html::a(AmosEvents::t('amosevents', "COMPLETA IL CORSO"), ['/moodle/course/enrol-in-course', 'id' => $course_id, 'user_id' => \Yii::$app->user->id, 'send_email' => false], [
            'class' => 'btn btn-primary'
        ]); ?>
    </div>
<?php } else { ?>
    <div class="col-xs-12 m-t-20">
        <?= \yii\helpers\Html::a($buttonText, ['/community/join', 'id' => $course->community_id], [
            'class' => 'btn btn-primary'
        ]); ?>
    </div>
<?php } ?>

