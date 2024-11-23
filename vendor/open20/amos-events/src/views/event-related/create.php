<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    @vendor/open20/amos-events/src/views
 */
/**
 * @var yii\web\View $this
 * @var open20\amos\events\models\EventRelated $model
 */

$this->title = Yii::t('amoscore', 'Crea');
$this->params['breadcrumbs'][] = ['label' => '', 'url' => ['/events']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('amoscore', 'Event Related'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$iconAsset = \open20\amos\layout\assets\BootstrapItaliaCustomSpriteAsset::register($this);
?>
<p><?= \Yii::t('amosevents', 'Stai aggiungendo un evento correlato a <strong>{titleEvent}</strong>', [
        'titleEvent' => $event->title
    ]) ?>
</p>

<div class="d-flex justify-content-between">
    <div class="container-search">
        <a class="btn btn-outline-secondary btn-sm btn-icon mb-3" data-toggle="collapse" href="#form-search-events" role="button" aria-expanded="false" aria-controls="form-search-events">
            <svg class="icon">
                <use xlink:href="<?= $iconAsset->baseUrl ?>/material-sprite.svg#ic_search"></use>
            </svg>
            <span>Cerca</span>
        </a>
    </div>
</div>

<?=   $this->render('_search', ['model' => $modelSearch, 'originAction' => Yii::$app->controller->action->id ]); ?>

<div class="event-related-create">
    <?= $this->render('_form', [
        'event' => $event,
        'model' => $model,
        'fid' => NULL,
        'dataField' => NULL,
        'dataEntity' => NULL,
        'dataProvider' => $dataProvider,
        'modelSearch' => $modelSearch,

    ]) ?>

</div>
