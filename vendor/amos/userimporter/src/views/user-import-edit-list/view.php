<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    @vendor/amos/userimporter/src/views 
 */
use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\datecontrol\DateControl;
use yii\helpers\Url;
use open20\amos\core\module\BaseAmosModule;

/**
* @var yii\web\View $this
* @var amos\userimporter\models\UserImportEditList $model
*/

$this->title = strip_tags($model);
$this->params['breadcrumbs'][] = ['label' => '', 'url' => ['/userimporter']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('amoscore', 'User Import Edit List'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-import-edit-list-view">

    <?= DetailView::widget([
    'model' => $model,    
    'attributes' => [
                'name',
            'surname',
            'fiscal_code',
            'email',
            [
                'attribute'=>'expire_date',
                'format'=>['datetime',(isset(Yii::$app->modules['datecontrol']['displaySettings']['datetime'])) ? Yii::$app->modules['datecontrol']['displaySettings']['datetime'] : 'd-m-Y H:i:s A'],                
            ],
    ],    
    ]) ?>

</div>

<div id="form-actions" class="bk-btnFormContainer pull-right">
    <?= Html::a(BaseAmosModule::t('amoscore', 'Chiudi'), Url::previous(), ['class' => 'btn btn-secondary']); ?></div>
