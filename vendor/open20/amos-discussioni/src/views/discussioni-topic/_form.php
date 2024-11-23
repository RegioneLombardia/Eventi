<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\discussioni
 * @category   CategoryName
 */

use open20\amos\attachments\components\AttachmentsInput;
use open20\amos\attachments\components\AttachmentsList;
use open20\amos\attachments\components\CropInput;
use open20\amos\core\forms\AccordionWidget;
use open20\amos\core\forms\ActiveForm;
use open20\amos\core\forms\CreatedUpdatedWidget;
use open20\amos\core\forms\editors\Select;
use open20\amos\core\forms\TextEditorWidget;
use open20\amos\core\helpers\Html;
use open20\amos\cwh\widgets\DestinatariPlusTagWidget;
use open20\amos\discussioni\AmosDiscussioni;
use open20\amos\discussioni\models\DiscussioniTopic;
use open20\amos\news\AmosNews;
use open20\amos\report\widgets\ReportFlagWidget;
use open20\amos\seo\widgets\SeoWidget;
use open20\amos\workflow\widgets\WorkflowTransitionButtonsWidget;
use open20\amos\workflow\widgets\WorkflowTransitionStateDescriptorWidget;

use yii\web\View;
use yii\widgets\ActiveForm as ActiveForm2;

/**
 * @var View $this
 * @var DiscussioniTopic $model
 * @var ActiveForm2 $form
 */

$form = ActiveForm::begin([
    'options' => ['enctype' => 'multipart/form-data'] // important
]);
$customView = Yii::$app->getViewPath() . '/imageField.php';
$utenteConnesso = Yii::$app->getUser();

$appController = Yii::$app->controller;
$disableStandardWorkflow = $appController->discussioniModule->disableStandardWorkflow;

$module = \Yii::$app->getModule('discussioni');
?>

<?php if (!$model->isNewRecord) : ?>
    <?= WorkflowTransitionStateDescriptorWidget::widget([
        'form' => $form,
        'model' => $model,
        'workflowId' => DiscussioniTopic::DISCUSSIONI_WORKFLOW,
        'classDivIcon' => '',
        'classDivMessage' => 'message',
        'viewWidgetOnNewRecord' => false
    ]); ?>
<?php endif; ?>

<div class="discussioni-form">
    <div class="row">
        <!--contenuti multimediali-->
        <div class="col-xs-12 section-form">
            <h2 class="subtitle-form"><?= AmosDiscussioni::t('amosdiscussioni', 'Immagine principale') ?></h2>
            <div>
                <?= $form->field($model, 'discussionsTopicImage')->widget(CropInput::classname(), [
                    'jcropOptions' => ['aspectRatio' => '1.7']
                ])->label(AmosDiscussioni::t('amosdiscussioni', '#image_field'))->hint(AmosDiscussioni::t('amosdiscussioni', '#image_field_hint')) ?>
            </div>
        </div>
        <!--informazioni generali-->
        <div class="col-xs-12 section-form">
            <?= Html::tag(
                'h2',
                AmosDiscussioni::t('amosdiscussioni', 'Informazioni di base')
                    . CreatedUpdatedWidget::widget(['model' => $model, 'isTooltip' => true])
                    . ReportFlagWidget::widget([
                        'model' => $model,
                    ]),
                ['class' => 'subtitle-form']
            )
            ?>


            <div>
                <?= $form->field($model, 'titolo')->textInput(['maxlength' => true, 'placeholder' => AmosDiscussioni::t('amosdiscussioni', '#title_field_plceholder')])->hint(AmosDiscussioni::t('amosdiscussioni', '#title_field_hint')) ?>
                <?= $form->field($model, 'testo')->widget(
                    TextEditorWidget::className(),
                    [
                        'clientOptions' => [
                            'placeholder' => AmosDiscussioni::t('amosdiscussioni', '#description_field_placeholder'),
                            'lang' => substr(Yii::$app->language, 0, 2)
                        ]
                    ]
                ) ?>
            </div>
        </div>
        <!--documenti e allegati-->
        <div class="col-xs-12 section-form">

            <div>
                <h2 class="subtitle-form"><?= AmosDiscussioni::t('amosdiscussioni', '#attachments_title') ?></h2>

                <?= $form->field(
                    $model,
                    'discussionsAttachments'
                )->widget(AttachmentsInput::classname(), [
                    'options' => [ // Options of the Kartik's FileInput widget
                        'multiple' => true, // If you want to allow multiple upload, default to false
                    ],
                    'pluginOptions' => [ // Plugin options of the Kartik's FileInput widget
                        'maxFileCount' => 100, // Client max files,
                        'showPreview' => false
                    ]
                ])->label(AmosDiscussioni::t('amosdiscussioni', '#attachments_field'))->hint(AmosDiscussioni::t('amosdiscussioni', '#attachments_field_hint')) ?>
                <?= AttachmentsList::widget([
                    'model' => $model,
                    'attribute' => 'discussionsAttachments'
                ]) ?>
            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 section-form">
        <div class="section-modalita-pubblicazione">
            <?= Html::tag('h2', AmosDiscussioni::t('amosdiscussioni', '#settings_receiver_title'), ['class' => 'subtitle-form']) ?>
            <div class="row">
                        <div class="col-xs-12">
                            <div class="content-mod-pubb">
            <?php

            $showReceiverSection = false;

            $moduleCwh = Yii::$app->getModule('cwh');
            $showReceiverSection = isset($moduleCwh) ? true : null;
            $scope = null;
            if (!empty($moduleCwh)) {
                $scope = $moduleCwh->getCwhScope();
            }

            $moduleTag = Yii::$app->getModule('tag');
            $showReceiverSection = isset($moduleTag) ? true : null;

            if ($showReceiverSection) : ?>
                <div>
                    <?= DestinatariPlusTagWidget::widget([
                        'model' => $model,
                        'moduleCwh' => $moduleCwh,
                        'scope' => $scope
                    ]);
                    ?>
                </div>
            <?php endif; ?>
                            </div>
                        </div>
            <?php if (AmosDiscussioni::instance()->enable_foreground && Yii::$app->user->can(AmosDiscussioni::instance()->foreground_permission)) : ?>
                <div class="col-xs-12">
                    <div class="row">
                        <h3 class="subtitle-section-form"><?= AmosNews::t('amosdiscussioni', "#pubblication_on_portal") ?>
                            <em>(<?= AmosNews::t('amosdiscussioni', "#choose_publish_on_portal") ?>)</em>
                        </h3>
                        <div class="col-md-6">
                            <?= $form->field($model, 'primo_piano')->widget(
                                Select::className(),
                                [
                                    'auto_fill' => true,
                                    'data' => Html::getBooleanFieldsValues(),
                                    'options' => [
                                        'prompt' => AmosNews::t('amosdiscussioni', 'Seleziona'),
                                        'disabled' => false,
                                    ],
                                ]
                            );
                            ?>
                        </div>
                        <div class="col-md-6">
                            <?php if (AmosDiscussioni::instance()->site_publish_enabled) : ?>
                                <?=
                                $form->field($model, 'in_evidenza')->widget(Select::className(), [
                                    'auto_fill' => true,
                                    'data' => Html::getBooleanFieldsValues(),
                                    'options' => [
                                        'prompt' => AmosDiscussioni::t('amosnews', 'Seleziona'),
                                        // 'disabled' => ($model->primo_piano == 1 ? false : true)
                                    ]
                                ]);
                                ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            </div>
        </div>
        </div>

        <div class="col-xs-12 note_asterisk">
            <span><?= AmosDiscussioni::t('amosdiscussioni', '#required_field') ?></span>
        </div>

        <!-- <div class="col-md-12 col-sm-12 hidden">
            < ?= $form->field($model, 'in_evidenza')->checkbox() ?>
        </div> -->
















        <?php
        $isCommunityManager = false;
        if (\Yii::$app->getModule('community')) {
            $isCommunityManager = \open20\amos\community\utilities\CommunityUtil::isLoggedCommunityManager();
        }

        if ((Yii::$app->user->can('ADMIN') || $isCommunityManager) && (isset($module->disableComments) && $module->disableComments)) : ?>
            <div class="col-xs-12">
                <?= Html::tag('h2', AmosDiscussioni::t('amosdiscussioni', '#settings_advance'), ['class' => 'subtitle-form']) ?>
                <?php

                echo Html::tag(
                    'div',
                    $form->field($model, 'close_comment_thread')->inline()->radioList(
                        [
                            true => AmosDiscussioni::t('amosdiscussioni', '#force_ok'),
                            false => AmosDiscussioni::t('amosdiscussioni', '#force_ko')
                        ],
                        ['class' => 'comment-choice']
                    )->label(
                        AmosDiscussioni::t('amosdiscussioni', '#close_comment_thread'),
                        ['class' => 'col-md-8 col-xs-12']
                    )
                );
                ?>

            </div>
            <div class="clearfix"></div>
        <?php endif; ?>
    </div>

    <div class="row">
        <?php
        $moduleSeo = Yii::$app->getModule('seo');
        if (isset($moduleSeo)) : ?>
            <div class="col-xs-12">
                <?= AccordionWidget::widget([
                    'items' => [
                        [
                            'header' => AmosDiscussioni::t('amosdiscussioni', '#settings_seo_title'),
                            'content' => SeoWidget::widget([
                                'contentModel' => $model,
                            ]),
                        ]
                    ],
                    'headerOptions' => ['tag' => 'h2'],
                    'options' =>  Yii::$app->user->can('ADMIN') ? [] : ['style' => 'display:none;'],
                    'clientOptions' => [
                        'collapsible' => true,
                        'active' => 'false',
                        'icons' => [
                            'header' => 'ui-icon-amos am am-plus-square',
                            'activeHeader' => 'ui-icon-amos am am-minus-square',
                        ]
                    ],
                ]);
                ?>
            </div>
        <?php endif; ?>

        <?php
        $config = [
            'model' => $model,
            'urlClose' => Yii::$app->session->get('previousUrl')
        ];

        $hideDraftStatuses = true;
        $hideDraftStatus = [];

        if ((Yii::$app->user->can('DiscussionValidate', ['model' => $model])) || (Yii::$app->user->can('ADMIN'))) {
            $hideDraftStatuses = false;
        }

        if ($hideDraftStatuses) {
            $hideDraftStatus[] = DiscussioniTopic::DISCUSSIONI_WORKFLOW_STATUS_ATTIVA;
        }

        $statusToRenderToHide = $model->getStatusToRenderToHide();

        $draftButtons = [];
        if ($disableStandardWorkflow == false) {
            $draftButtons = [
                DiscussioniTopic::DISCUSSIONI_WORKFLOW_STATUS_DAVALIDARE => [
                    'button' => Html::submitButton(Yii::t('amoscommunity', 'Salva'), ['class' => 'btn btn-workflow']),
                    'description' => AmosDiscussioni::t('amosdiscussioni', 'le modifiche e mantieni la discussione in "richiesta di pubblicazione"'),
                ],
                DiscussioniTopic::DISCUSSIONI_WORKFLOW_STATUS_ATTIVA => [
                    'button' => Html::submitButton(Yii::t('amoscommunity', 'Salva'), ['class' => 'btn btn-workflow']),
                    'description' => AmosDiscussioni::t('amosdiscussioni', 'le modifiche e mantieni la discussione "pubblicata"'),
                ],
                'default' => [
                    'button' => Html::submitButton(AmosDiscussioni::t('amosdiscussioni', 'Salva in bozza'), ['class' => 'btn btn-workflow']),
                    'description' => AmosDiscussioni::t('amosdiscussioni', 'potrai richiedere la pubblicazione in seguito'),
                ]
            ];
        }
        $workflowInitialStatusId = $model->getWorkflowSource()->getWorkflow(DiscussioniTopic::DISCUSSIONI_WORKFLOW)->getInitialStatusId();
        echo WorkflowTransitionButtonsWidget::widget([
            // parametri ereditati da verioni precedenti del widget WorkflowTransition
            'form' => $form,
            'model' => $model,
            'workflowId' => DiscussioniTopic::DISCUSSIONI_WORKFLOW,
            'viewWidgetOnNewRecord' => true,
            'closeButton' => Html::a(
                AmosDiscussioni::t('amosdiscussioni', 'Annulla'),
                Yii::$app->session->get('previousUrl'),
                ['class' => 'btn btn-secondary']
            ),
            // fisso lo stato iniziale per generazione pulsanti e comportamenti
            // "fake" in fase di creazione (il record non e' ancora inserito nel db)
            'initialStatusName' => end(explode('/', $workflowInitialStatusId)),
            'initialStatus' => $workflowInitialStatusId,

            // Stati da renderizzare obbligatoriamente in fase di creazione (quando il record non e' ancora inserito nel db)
            'statusToRender' => $statusToRenderToHide['statusToRender'],
            'hideSaveDraftStatus' => $statusToRenderToHide['hideDraftStatus'],

            'draftButtons' => $draftButtons
        ]); ?>
    </div>

    <?php ActiveForm::end(); ?>

    <div class="clearfix"></div>
</div>