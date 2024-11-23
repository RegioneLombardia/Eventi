<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    @vendor/open20/amos-events/src/views
 */

use open20\amos\core\helpers\Html;
use yii\bootstrap4\ActiveForm;
use kartik\datecontrol\DateControl;
use open20\amos\core\forms\Tabs;
use open20\amos\core\forms\CloseSaveButtonWidget;
use open20\amos\core\forms\RequiredFieldsTipWidget;
use yii\helpers\Url;
use open20\amos\core\forms\editors\Select;
use yii\helpers\ArrayHelper;
use open20\amos\core\icons\AmosIcons;
use yii\bootstrap\Modal;
use open20\amos\core\forms\TextEditorWidget;
use yii\helpers\Inflector;
use \open20\amos\events\AmosEvents;




/**
 * @var yii\web\View $this
 * @var open20\amos\events\models\EventRelated $model
 * @var yii\widgets\ActiveForm $form
 * @var  $dataProvider
 * @var  \open20\amos\events\models\Event $event
 */


?>
<?php

$this->title = AmosEvents::t('amosevents', "Aggiungi evento correlato");
$this->params['breadcrumbs'][] = $this->title;


?>

<?php
echo open20\amos\core\views\AmosGridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'title',
        'event_type_id' => [
            'attribute' => 'event_type_id',
            'value' => 'eventType.title'
        ],
        'status' => [
            'attribute' => 'status',
            'value' => function ($model) {
                return $model->getWorkflowBaseStatusLabel();
            }
        ],
        'begin_date_hour:datetime',
        [
            'class' => \yii\grid\ActionColumn::className(),
            'template' => '{add}',
            'buttons' => [
                'add' => function ($url, $model) use ($event) {
                    return \yii\helpers\Html::a(AmosEvents::t('amosevents', "Aggiungi"), [
                        '/events/event-related/create',
                        'event_id' => $event->id,
                        'eventToAddId' => $model->id
                    ], [
                        'class' => 'btn btn-xs btn-primary',
                        'title' => AmosEvents::t('amosevents', 'Aggiungi evento in evidenza')
                    ]);
                }
            ]
        ]
    ]
]);
?>
<?php
echo Html::a(AmosEvents::t('amosevents', "Annulla"), [
    '/events/event-dashboard/landing-manage-contents',
    'id' => $event->id,
    '#' => 'container-related-events'
], [
    'class' => 'btn btn-secondary',
    'title' => AmosEvents::t('amosevents', "Annulla")
]) ?>
