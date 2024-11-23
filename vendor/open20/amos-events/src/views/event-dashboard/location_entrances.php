<?php
use open20\amos\core\forms\bs4\ActiveForm;
use yii\helpers\Html;
use open20\amos\events\AmosEvents;

$this->title = \open20\amos\events\AmosEvents::t('amosevents', 'Location');
$this->params['breadcrumbs'][] = $this->title;

?>

<?php
echo \open20\amos\events\widgets\ChangeLanguage::widget(['model' => $model]);

echo \yii\helpers\Html::a(\open20\amos\events\AmosEvents::t('amosevents', 'Indietro'), ['/events/event-dashboard/info-event', 'id' => $model->id],[
    'class' => 'btn btn-secondary mt-5',
    'title' => \open20\amos\events\AmosEvents::t('amosevents', 'Indietro'),
]);
$form = ActiveForm::begin();

echo $this->render('@vendor/open20/amos-events/src/views/event-dashboard/_parts/_personalized_location',[
    'eventLocationEntrance' => $eventLocationEntrance,
    'eventLocation' => $eventLocation,
    'form' => $form
]);

?>
    <div class="buttons">
        <?php echo Html::submitButton(AmosEvents::t('amosevents', 'Salva'), ['class' => 'btn btn-primary pull-right']) ?>

    </div>
<?php
ActiveForm::end();
