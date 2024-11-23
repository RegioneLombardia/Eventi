<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    @vendor/open20/amos/events/src/views 
 */
use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\datecontrol\DateControl;
use yii\helpers\Url;
use open20\amos\events\AmosEvents;

/**
* @var yii\web\View $this
* @var open20\amos\events\models\EventGroupReferent $model
*/

$this->title = $model->denominazione;
$this->params['breadcrumbs'][] = ['label' => '', 'url' => ['/events']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('amoscore', 'Event Group Referent'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-group-referent-view">

    <?= DetailView::widget([
    'model' => $model,    
    'attributes' => [
             'denominazione',
            'descrizione:html',
            'note:html',
    ],    
    ]) ?>

</div>

<div id="form-actions" class="bk-btnFormContainer pull-right">
    <?= Html::a(Yii::t('amoscore', 'Chiudi'), Url::previous(), ['class' => 'btn btn-secondary']); ?></div>
