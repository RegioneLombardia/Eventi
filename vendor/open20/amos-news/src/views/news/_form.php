<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\news\views\news
 * @category   CategoryName
 */

use amos\sitemanagement\models\SiteManagementSliderElem;

use open20\amos\attachments\components\AttachmentsInput;
use open20\amos\attachments\components\AttachmentsList;
use open20\amos\attachments\components\CropInput;
use open20\amos\core\forms\AccordionWidget;
use open20\amos\core\forms\ActiveForm;
use open20\amos\core\forms\editors\Select;
use open20\amos\core\forms\RequiredFieldsTipWidget;
use open20\amos\core\forms\TextEditorWidget;
use open20\amos\core\helpers\Html;
use open20\amos\news\AmosNews;
use open20\amos\news\models\News;
use open20\amos\news\models\NewsContentType;
use open20\amos\news\utility\NewsUtility;
use open20\amos\workflow\widgets\WorkflowTransitionButtonsWidget;
use open20\amos\workflow\widgets\WorkflowTransitionStateDescriptorWidget;
use open20\amos\core\icons\AmosIcons;

use kartik\datecontrol\DateControl;
use kartik\select2\Select2;

use open20\agid\organizationalunit\models\AgidOrganizationalUnit;
use open20\agid\person\models\AgidPersonType;

use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * @var yii\web\View $this
 * @var News $model
 * @var yii\widgets\ActiveForm $form
 * @var amos\sitemanagement\Module|null $siteManagementModule
 */
$dateErrorMessage = AmosNews::t('error', "Controllare data");
/** @var AmosNews $newsModule */
$newsModule = \Yii::$app->getModule(AmosNews::getModuleName());
$enableOtherNewsCategories = $newsModule->enableOtherNewsCategories;

$todayDate = date('d-m-Y');
$tomorrowDate = (new DateTime('tomorrow'))->format('d-m-Y');
$moduleNotify = \Yii::$app->getModule('notify');


$moduleSeo = \Yii::$app->getModule('seo');
$hideSeoModuleClass = $newsModule->hideSeoModule ? ' hidden' : '';

$reportModule = \Yii::$app->getModule('report');

// ENABLE AGID FIELDS
$enableAgid = $newsModule->enableAgid;
$rtePlugins = $newsModule->rtePlugins;
$rteToolbar = $newsModule->rteToolbar;

$textAlertCategory = AmosNews::t('news', "Questa categoria è già stata scelta come categoria principale.");

$js2 = <<<JS
  $(document).ready(function () {
    if($("#news_categorie_id-id option").length == 2){
      $($("#news_categorie_id-id option").parent().parent().parent()).hide();
    }
  });

JS;

$jsOtherCategory = <<<JS

     $(document).on('select2:selecting', '#news_categorie_mm_id-id', function(e){
          var selected = e.params.args.data.id;
          var current =  $('#news_categorie_id-id').val();
          
         if(current === selected){
            alert("$textAlertCategory");
            return false;
         }
     });
     
     $('#news_categorie_id-id').change(function(){
        var current = $(this).val();
        var currentText = $(this).text();
        
         var a = $('#news_categorie_mm_id-id').val();
         var text = String(a);
         var otherCategories = text.split(',');
         if(otherCategories.includes(current)){
             otherCategories.forEach(function (item, index) {
                if(item == current){
                    otherCategories.splice(index, 1);
                    $('#news_categorie_mm_id-id').val(otherCategories.join(','));
                    $('#news_categorie_mm_id-id').trigger('change');
                }
            });
         }
    });

JS;

$this->registerJs($js2);
if ($enableOtherNewsCategories) {
    $this->registerJs($jsOtherCategory);
}


$customView = Yii::$app->getViewPath() . '/imageField.php';

echo WorkflowTransitionStateDescriptorWidget::widget([
    'form' => $form,
    'model' => $model,
    'workflowId' => News::NEWS_WORKFLOW,
    'classDivIcon' => '',
    'classDivMessage' => 'message',
    'viewWidgetOnNewRecord' => false
]);

$disableStandardWorkflow = $newsModule->disableStandardWorkflow;

$hidePreviewDeleteButton = false;
if ($enableAgid) {
    $newsImageElementId = Html::getInputId($model, 'newsImage');
    $hidePreviewDeleteButton = true;
    $jsAgid = <<<JS
        function addRequiredAsterisk(fieldName) {
            $('.field-' + fieldName).addClass('required');
        }
        addRequiredAsterisk('$newsImageElementId');
JS;
    $this->registerJs($jsAgid);
    $newsImageLabel = AmosNews::t('amosnews', '#image_field_required');
} else {
    $newsImageLabel = $model->getAttributeLabel('newsImage');
}

$form = ActiveForm::begin([
    'options' => ['enctype' => 'multipart/form-data']
]);
?>

<div class="news-form">
    <div class="row">
        <div class="col-xs-12">
            <?php
            $reportFlagWidget = '';
            if (isset($reportModule) && in_array($model->className(), $reportModule->modelsEnabled)) {
                $reportFlagWidget = \open20\amos\report\widgets\ReportFlagWidget::widget([
                    'model' => $model,
                ]);
            }
            ?>
        </div>

        <!--contenuti multimediali-->
        <div class="col-xs-12 section-form">
            <h2 class="subtitle-form"><?= AmosNews::t('amosnews', 'Immagine principale') ?></h2>
            <div class="row">
                <div class="col-xs-12">
                    <?= $form->field($model, 'newsImage')->widget(CropInput::class, [
                        'hidePreviewDeleteButton' => $hidePreviewDeleteButton,
                        'jcropOptions' => ['aspectRatio' => '1.7']
                    ])
                        ->label($newsImageLabel)
                        ->hint(AmosNews::t('amosnews', '#image_field_hint'))
                    ?>
                </div>
            </div>
        </div>

        <!--nome e categoria-->
        <div class="col-xs-12 section-form">
            <h2 class="subtitle-form"><?= AmosNews::t('amosnews', 'Informazioni di base') ?></h2>
            <div class="row">
                <div class="col-xs-12">
                    <?= $form->field($model, 'titolo')->textInput([
                        'maxlength' => true,
                        'placeholder' => AmosNews::t('amosnews', '#title_field_plceholder')
                    ])
                        ->hint(AmosNews::t('amosnews', '#title_field_hint'))
                    ?>
                </div>

                <?php if (!$enableAgid) : ?>
                    <div class="col-xs-12">
                        <?= $form->field($model, 'sottotitolo')->textInput([
                            'maxlength' => true,
                            'placeholder' => AmosNews::t('amosnews', '#subtitle_field_plceholder')
                        ])
                            ->hint(AmosNews::t('amosnews', '#subtitle_field_hint'))
                        ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- AGID FIELD -->
            <div class="row">
                <?php if ($enableAgid && $newsModule->enableAgidNewsContentType) : ?>
                    <div class="col-xs-12">
                        <?= $form->field($model, 'news_content_type_id')->widget(Select2::class, [
                            'data' => ArrayHelper::map(
                                NewsContentType::find()
                                    ->asArray()
                                    ->all(),
                                'id',
                                'name'
                            ),
                            'language' => substr(Yii::$app->language, 0, 2),
                            'options' => [
                                'id' => 'news_content_type_id',
                                'multiple' => false,
                                'placeholder' => AmosNews::t('amosnews', 'Seleziona') . ' ...',
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ]
                        ])
                            ->hint(AmosNews::t('amosnews', '#news_content_type_id'))
                            ->label(AmosNews::t('amosnews', '#news_content_type_id'));
                        ?>
                    </div>
                <?php endif; ?>

                <div class="col-xs-12">
                    <?php
                    $labelCategory = $enableOtherNewsCategories ? AmosNews::t('amosnews', "Main category") : $model->attributeLabels()['news_categorie_id'];
                    if ($model->isNewRecord && empty($model->news_categorie_id) && !empty(AmosNews::instance()->defaultCategory)) {
                        $model->news_categorie_id = AmosNews::instance()->defaultCategory;
                    }

                    $newsCategoriesQuery = NewsUtility::getNewsCategories()->orderBy('titolo');
                    $otherNewsCategoriesQuery = clone $newsCategoriesQuery;
                    if ($enableOtherNewsCategories) {
                        $otherNewsCategoriesQuery->andWhere(['not in', 'news_categorie.id', $model->news_categorie_id]);
                    }
                    $newsCategories = $newsCategoriesQuery->all();
                    $newsCategoryId = $model->news_categorie_id;
                    
                    if (!$model->news_categorie_id && (count($newsCategories) == 1)) {
                        $newsCategoryId = $newsCategories[0]->id;
                    }
                    ?>
                    <?=
                    $form->field($model, 'news_categorie_id')->widget(
                        Select::class,
                        [
                            'auto_fill' => true,
                            'options' => [
                                'placeholder' => AmosNews::t('amosnews', '#category_field_placeholder'),
                                'id' => 'news_categorie_id-id',
                                'disabled' => false,
                                'value' => $newsCategoryId
                            ],
                            'data' => ArrayHelper::map($newsCategories, 'id', 'titolo'),
                        ]
                    )->label($labelCategory)
                    ?>
                </div>

                <?php if ($enableOtherNewsCategories) { ?>
                    <div class="col-md-12">
                        <?= $form->field($model, 'otherCategories')->widget(Select2::className(), [
                            'options' => [
                                'placeholder' => AmosNews::t('amosnews', '#category_field_placeholder'),
                                'id' => 'news_categorie_mm_id-id',
                                'multiple' => true,
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ],
                            'data' => ArrayHelper::map($otherNewsCategoriesQuery->all(), 'id', 'titolo')
                        ])->label(AmosNews::t('amosnews', 'Other categories')) ?>
                    </div>
                <?php } ?>
            </div>
        </div>

        <!--testo della notizia-->
        <div class="col-xs-12 section-form">
            <h2 class="subtitle-form"><?= AmosNews::t('amosnews', 'Testi') ?></h2>
            <div class="row">
                <div class="col-xs-12">
                    <?= $form->field($model, 'descrizione_breve')->textarea([
                        'maxlength' => 160,
                        'placeholder' => AmosNews::t('amosnews', '#abstract_field_placeholder')
                    ])
                        ->hint(AmosNews::t('amosnews', '#abstract_field_hint'))
                    ?>
                </div>

                <div class="col-xs-12">
                    <?php
                    $clientOption = [
                        'lang' => substr(Yii::$app->language, 0, 2),
                        'plugins' => $rtePlugins,
                        'toolbar' => $rteToolbar,
                    ];
                    $hint = '';
                    if ($newsModule->textEditorClientOptions) {
                        $hint = AmosIcons::show('info-outline') . ' ' . AmosNews::t('amosnews', '#formatted_text');
                        if (empty($newsModule->arrayTextEditorClients) || in_array(\Yii::$app->user->id, $newsModule->arrayTextEditorClients)) {
                            $clientOption['paste_as_text'] = false;
                        }
                        if (!empty($newsModule->arrayTextEditorClients) && in_array(\Yii::$app->user->id, $newsModule->arrayTextEditorClients)) {
                            $hint = AmosIcons::show('info-outline') . ' ' . AmosNews::t('amosnews', '#formatted_text_for_you');
                        } else {
                            $hint = '';
                        }
                    }
                    echo $form->field($model, 'descrizione')->widget(
                        TextEditorWidget::class,
                        [
                            'options' => ['placeholder' => AmosNews::t('amosnews', 'Inserisci...')],
                            'clientOptions' => $clientOption,
                        ]
                    )->hint($hint);
                    ?>
                </div>
            </div>
        </div>

        <!--documenti e allegati-->
        <div class="col-xs-12 section-form">
            <h2 class="subtitle-form"><?= AmosNews::t('amosnews', 'Allegati') ?></h2>
            <div class="row">
                <!-- AGID FIELD -->
                <?php if (($enableAgid) && ($enableAgidAllegati)) : ?>
                    <div class="col-xs-12">
                        <?=
                        $form->field($model, 'news_documento_id')->widget(
                            Select2::class,
                            [
                                'data' => ArrayHelper::map(
                                    \open20\amos\documenti\models\Documenti::find()
                                        ->andwhere(['status' => 'DocumentiWorkflow/VALIDATO'])
                                        ->andWhere(['deleted_at' => null])
                                        ->all(),
                                    'id',
                                    'titolo'
                                ),
                                'language' => substr(Yii::$app->language, 0, 2),
                                'options' => [
                                    'id' => 'news_documento_id',
                                    'multiple' => false,
                                    'placeholder' => AmosNews::t('amosnews', 'Seleziona') . ' ...',
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                ]
                            ]
                        )->hint(AmosNews::t('amosnews', '#news_documento_id'));
                        ?>
                    </div>
                <?php endif; ?>

                <div class="col-xs-12">
                    <?= $form->field($model, 'attachments')->widget(AttachmentsInput::class, [
                        'options' => [ // Options of the Kartik's FileInput widget
                            'multiple' => true, // If you want to allow multiple upload, default to false
                        ],
                        'pluginOptions' => [ // Plugin options of the Kartik's FileInput widget
                            'maxFileCount' => 100, // Client max files
                            'showPreview' => false
                        ]
                    ])
                        ->hint(AmosNews::t('amosnews', '#attachments_field_hint'))
                        ->label(AmosNews::t('amosnews', '#attachments_title'));
                    ?>
                    <?= AttachmentsList::widget([
                        'model' => $model,
                        'attribute' => 'attachments'
                    ])
                    ?>
                </div>
            </div>
        </div>

        <!--referenti-->
        <!-- AGID FIELD -->
        <?php if (($enableAgid) && ($enableAgidReferenti)) : ?>
            <div class="col-xs-12 section-form">
                <h2 class="subtitle-form"><?= AmosNews::t('amosnews', 'Referenti') ?></h2>
                <div class="row">

                    <div class="col-xs-12">
                        <!-- AGID FIELD -->
                        <?=
                        $form->field($model, 'edited_by_agid_organizational_unit_id')->widget(
                            Select2::class,
                            [
                                'data' => ArrayHelper::map(
                                    AgidOrganizationalUnit::find()
                                        ->asArray()
                                        ->all(),
                                    'id',
                                    'name'
                                ),
                                'language' => substr(Yii::$app->language, 0, 2),
                                'options' => [
                                    'id' => 'edited_by_agid_organizational_unit_id',
                                    'multiple' => false,
                                    'placeholder' => AmosNews::t('amosnews', 'Seleziona') . ' ...',
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                ]
                            ]
                        )->hint(AmosNews::t('amosnews', '#edited_by_agid_organizational_unit_id'));
                        ?>
                    </div>
                    <div class="col-xs-12">
                        <!-- AGID FIELD -->
                        <?php
                        foreach ($model->newsAgidPersonMm as $key => $value) {
                            $news_agid_person_mm[] = $value->agid_person_id;
                        }

                        echo $form->field($model, 'news_agid_person_mm[]')->widget(
                            \kartik\select2\Select2::class,
                            [
                                'data' => ArrayHelper::map(
                                    \open20\agid\person\models\AgidPerson::find()
                                        ->select(["id, name, surname, CONCAT(surname, ' ', name) AS surnameName"])
                                        ->andWhere([
                                            'agid_person_type_id' => AgidPersonType::find()
                                                ->andWhere(['name' => "Politica"])
                                                ->one()
                                                ->id
                                        ])
                                        ->andWhere(['deleted_at' => null])
                                        ->all(),
                                    'id',
                                    'surnameName'
                                ),
                                'options' => [
                                    'placeholder' => Yii::t('amosnews', 'Seleziona...'),
                                    'multiple' => true,
                                    'value' => isset($news_agid_person_mm) ? $news_agid_person_mm : null,
                                ],
                            ]
                        )->label(Yii::t('amosnews', 'Persone'))
                        ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- AGID FIELD -->
        <?php if ($enableAgid) : ?>
            <?php
            if (!is_null($siteManagementModule)) {
                $siteManagementModule::setExternalPreviousSessionKeys(
                    Url::current(),
                    $model->getGrammar()->getModelSingularLabel() . ' ' . $model->getTitle()
                );
            }
            ?>
            <div class="row">
                <div class="col-xs-12">
                    <?php
                    if (!$model->isNewRecord) {
                        $max = SiteManagementSliderElem::find()
                            ->andWhere(['slider_id' => $model->image_site_management_slider_id])
                            ->max("site_management_slider_elem.order");
                        $min = SiteManagementSliderElem::find()
                            ->andWhere(['slider_id' => $model->image_site_management_slider_id])
                            ->min("site_management_slider_elem.order");
                        $newsModel = $model;
                    ?>
                        <h3><?= AmosNews::t('amosnews', 'Galleria immagini') ?></h3>
                        <?=
                        Html::a(
                            AmosNews::t('amosnews', 'Aggiungi immagine'),
                            [
                                '/sitemanagement/site-management-slider-elem/create',
                                'id' => $slider_image->id,
                                'slider_type' => SiteManagementSliderElem::TYPE_IMG,
                                "image",
                                'urlRedirect' => urlencode('/news/news/update?id=' . $newsModel->id . '#tab-immagine'),
                                'useCrop' => true,
                                'cropRatio' => 1.7
                            ],
                            [
                                'class' => 'btn btn-navigation-primary',
                                'data-confirm' => AmosNews::t('amosnews', 'Stai per lasciare la pagina, assicurarsi di aver salvato dati. Proseguire?')
                            ]
                        );
                        ?>
                        <?php
                        $gridColumns = [
                            'order',
                            'type' => [
                                'attribute' => 'type',
                                'value' => function ($model) {
                                    return $model->getLabelType();
                                },
                                'enableSorting' => false
                            ],
                            'title' => [
                                'attribute' => 'title',
                                'enableSorting' => false
                            ],
                            [
                                'class' => \open20\amos\core\views\grid\ActionColumn::class,
                                'controller' => 'site-management-slider-elem',
                                'template' => '{update}{delete}',
                                'buttons' => [
                                    'update' => function ($url, $model) use ($newsModel) {
                                        return Html::a(
                                            \open20\amos\core\icons\AmosIcons::show('edit'),
                                            [
                                                '/sitemanagement/site-management-slider-elem/update',
                                                'id' => $model->id,
                                                'urlRedirect' => urlencode('/news/news/update?id=' . $newsModel->id . '#tab-immagine')
                                            ],
                                            [
                                                'class' => 'btn btn-navigation-primary'
                                            ]
                                        );
                                    },
                                    'view' => function ($url, $model) use ($newsModel) {
                                        return Html::a(
                                            \open20\amos\core\icons\AmosIcons::show('file'),
                                            [
                                                '/sitemanagement/site-management-slider-elem/view',
                                                'id' => $model->id,
                                                'urlRedirect' => urlencode('/news/news/update?id=' . $newsModel->id . '#tab-immagine')
                                            ],
                                            [
                                                'class' => 'btn btn-navigation-primary'
                                            ]
                                        );
                                    },
                                    'delete' => function ($url, $model) use ($newsModel) {
                                        return Html::a(
                                            \open20\amos\core\icons\AmosIcons::show('delete'),
                                            [
                                                '/sitemanagement/site-management-slider-elem/delete',
                                                'id' => $model->id, 'urlRedirect' => urlencode('/news/news/update?id=' . $newsModel->id . '#tab-immagine')
                                            ],
                                            [
                                                'class' => 'btn btn-danger-inverse'
                                            ]
                                        );
                                    }
                                ]
                            ]
                        ];

                        if ($readOnly) {
                            array_pop($gridColumns);
                        }
                        echo \open20\amos\core\views\AmosGridView::widget([
                            'dataProvider' => $dataProviderSliderElemImage,
                            'columns' => $gridColumns
                        ]);
                        ?>
                    <?php
                    }
                    ?>
                </div>
                <div class="col-xs-12">
                    <?php if (!$model->isNewRecord) : ?>
                        <h3><?= AmosNews::t('amosnews', 'Video notizia') ?></h3>
                        <?=
                        Html::a(
                            \Yii::t('amosnews', 'Aggiungi video'),
                            [
                                '/sitemanagement/site-management-slider-elem/create',
                                'id' => $slider_video->id,
                                'slider_type' => SiteManagementSliderElem::TYPE_VIDEO,
                                'urlRedirect' => urlencode('/news/news/update?id=' . $newsModel->id . '#tab-video'),
                                'useCrop' => true,
                                'cropRatio' => 1.7
                            ],
                            [
                                'class' => 'btn btn-navigation-primary',
                                'data-confirm' => AmosNews::t('amosnews', 'Stai per lasciare la pagina, assicurarsi di aver salvato dati. Proseguire?')
                            ]
                        );
                        ?>
                        <?php
                        $gridColumns1 = [
                            'order',
                            'type' => [
                                'attribute' => 'type',
                                'value' => function ($model) {
                                    return $model->getLabelType();
                                },
                                'enableSorting' => false
                            ],
                            'title' => [
                                'attribute' => 'title',
                                'enableSorting' => false
                            ],
                            [
                                'class' => \open20\amos\core\views\grid\ActionColumn::class,
                                'controller' => 'site-management-slider-elem',
                                'template' => '{update}{delete}',
                                'buttons' => [
                                    'update' => function ($url, $model) use ($newsModel) {
                                        return Html::a(
                                            \open20\amos\core\icons\AmosIcons::show('edit'),
                                            [
                                                '/sitemanagement/site-management-slider-elem/update',
                                                'id' => $model->id,
                                                'urlRedirect' => urlencode('/news/news/update?id=' . $newsModel->id . '#tab-video')
                                            ],
                                            [
                                                'class' => 'btn btn-navigation-primary'
                                            ]
                                        );
                                    },
                                    'view' => function ($url, $model) use ($newsModel) {
                                        return Html::a(
                                            \open20\amos\core\icons\AmosIcons::show('file'),
                                            [
                                                '/sitemanagement/site-management-slider-elem/view',
                                                'id' => $model->id,
                                                'urlRedirect' => urlencode('/news/news/update?id=' . $newsModel->id . '#tab-video')
                                            ],
                                            [
                                                'class' => 'btn btn-navigation-primary'
                                            ]
                                        );
                                    },
                                    'delete' => function ($url, $model) use ($newsModel) {
                                        return Html::a(
                                            \open20\amos\core\icons\AmosIcons::show('delete'),
                                            [
                                                '/sitemanagement/site-management-slider-elem/delete',
                                                'id' => $model->id, 'urlRedirect' => urlencode('/news/news/update?id=' . $newsModel->id . '#tab-video')
                                            ],
                                            [
                                                'class' => 'btn btn-danger-inverse'
                                            ]
                                        );
                                    }
                                ]
                            ]
                        ];

                        if ($readOnly) {
                            array_pop($gridColumns);
                        }
                        echo \open20\amos\core\views\AmosGridView::widget([
                            'dataProvider' => $dataProviderSliderElemVideo,
                            'columns' => $gridColumns1
                        ]);
                        ?>

                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!--altre informazioni-->
        <!-- AGID FIELD -->
        <?php if ($enableAgid) : ?>
            <div class="col-xs-12">
                <h2 class="subtitle-form"><?= AmosNews::t('amosnews', 'Altre informazioni') ?></h2>
                <div class="row">

                    <div class="col-md-6 col-xs-12">
                        <!-- AGID FIELD -->
                        <?php
                        if ($model->isNewRecord) {
                            $model->date_news = date("Y-m-d");
                        }
                        ?>
                        <?=
                        $form->field($model, 'date_news')->widget(
                            DateControl::class,
                            [
                                'value' => $model->isNewRecord ? date("Y-m-d") : ''
                            ]
                        )
                            ->hint(AmosNews::t('amosnews', 'date_news'))
                            ->label(AmosNews::t('amosnews', 'date_news'));
                        ?>
                    </div>
                    <div class="col-md-6 col-xs-12">
                        <!-- AGID FIELD -->
                        <?=
                        $form->field($model, 'news_expiration_date')->widget(DateControl::className(), [])
                            ->hint(AmosNews::t('amosnews', 'news_expiration_date'))
                            ->label(AmosNews::t('amosnews', 'news_expiration_date'));
                        ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 col-xs-12">

                        <!-- NEWS AGID FIELD -->
                        <?php if ($newsModule->enableAgidNewsRelatedNews == true) : ?>
                            <?php

                            foreach ($model->newsRelatedNewsMm as $key => $value) {
                                $news_related_news_mm[] = $value->related_news_id;
                            }

                            $query = \open20\amos\news\models\News::find()
                                ->andwhere(['status' => News::NEWS_WORKFLOW_STATUS_VALIDATO])
                                ->andWhere(['deleted_at' => null]);
                            if (!empty($model->id)) {
                                $query->andWhere(['!=', 'id', $model->id]);
                            }

                            echo $form->field($model, 'news_related_news_mm[]')->widget(
                                Select2::class,
                                [
                                    'data' => ArrayHelper::map($query->all(), 'id', 'titolo'),
                                    'language' => substr(Yii::$app->language, 0, 2),
                                    'options' => [
                                        'id' => 'news_related_news_mm',
                                        'multiple' => true,
                                        'placeholder' => AmosNews::t('amosnews', 'Seleziona') . ' ...',
                                        'value' => isset($news_related_news_mm) ? $news_related_news_mm : null,
                                    ],
                                    'pluginOptions' => [
                                        'allowClear' => true,
                                    ]
                                ]
                            )->hint(AmosNews::t('amosnews', 'news_related_news_mm'));
                            ?>
                    </div>
                <?php endif; ?>

                <div class="col-md-6 col-xs-12">
                    <?php if ($newsModule->enableAgidNewsRelatedDocumenti == true) : ?>
                        <?php
                        foreach ($model->newsRelatedDocumentiMm as $key => $value) {
                            $news_related_documenti_mm[] = $value->related_documenti_id;
                        }
                        ?>
                        <?=
                        $form->field($model, 'news_related_documenti_mm[]')->widget(
                            Select2::class,
                            [
                                'data' => ArrayHelper::map(
                                    \open20\amos\documenti\models\Documenti::find()
                                        ->andwhere(['status' => 'DocumentiWorkflow/VALIDATO'])
                                        ->andWhere(['deleted_at' => null])
                                        ->all(),
                                    'id',
                                    'titolo'
                                ),
                                'language' => substr(Yii::$app->language, 0, 2),
                                'options' => [
                                    'id' => 'news_related_documenti_mm',
                                    'multiple' => true,
                                    'placeholder' => 'Seleziona ...',
                                    'value' => isset($news_related_documenti_mm) ? $news_related_documenti_mm : null,
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                ]
                            ]
                        )->hint(AmosNews::t('amosnews', 'news_related_documenti_mm'));
                        ?>
                    <?php endif; ?>
                </div>
                </div>
                <div class="row">

                    <div class="col-md-6 col-xs-12">
                        <?php if ($newsModule->enableAgidNewsRelatedAgidService == true) : ?>
                            <?php
                            foreach ($model->newsRelatedAgidServiceMm as $key => $value) {
                                $news_related_agid_service_mm[] = $value->related_agid_service_id;
                            }
                            ?>
                            <?=
                            $form->field($model, 'news_related_agid_service_mm[]')->widget(
                                Select2::class,
                                [
                                    'data' => ArrayHelper::map(
                                        \open20\agid\service\models\AgidService::find()
                                            ->andWhere(['status' => 'AgidServiceWorkflow/VALIDATED'])
                                            ->andWhere(['deleted_at' => null])
                                            ->all(),
                                        'id',
                                        'name'
                                    ),
                                    'language' => substr(Yii::$app->language, 0, 2),
                                    'options' => [
                                        'id' => 'news_related_agid_service_mm',
                                        'multiple' => true,
                                        'placeholder' => AmosNews::t('amosnews', 'Seleziona') . ' ...',
                                        'value' => isset($news_related_agid_service_mm)
                                            ? $news_related_agid_service_mm
                                            : null,
                                    ],
                                    'pluginOptions' => [
                                        'allowClear' => true,
                                    ]
                                ]
                            )->hint(AmosNews::t('amosnews', 'news_related_agid_service_mm'));
                            ?>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6 col-xs-12">
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="row">
        <?php
        $showReceiverSection = false;

        $moduleCwh = \Yii::$app->getModule('cwh');
        isset($moduleCwh) ? $showReceiverSection = true : null;
        isset($moduleCwh) ? $scope = $moduleCwh->getCwhScope() : null;

        $pubblicatedForCommunity = false;
        if (!$model->isNewRecord && isset($moduleCwh)) {
            if (!empty($model->validatori)) {
                foreach ($model->validatori as $validatore) {
                    $network = \open20\amos\cwh\utility\CwhUtil::getNetworkFromId($validatore);
                    if ($network instanceof \open20\amos\community\models\Community) {
                        $pubblicatedForCommunity = true;
                    }
                }
            }
        }
        if ($model->isNewRecord) {
            if (!empty($scope)) {
                $pubblicatedForCommunity = true;
            }
        }

        $moduleTag = \Yii::$app->getModule('tag');
        $tagTitle = ($enableAgid ? AmosNews::t('amosnews', '#tag') : AmosNews::t('amosnews', '#tags_title'));
        if (isset($moduleTag)) : ?>
            <div class="col-xs-12 section-form">
                <div class="section-modalita-pubblicazione">
                    <?=
                    Html::tag('h2', $tagTitle, ['class' => 'subtitle-form'])
                    ?>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="content-mod-pubb">
                                <?php
                                echo \open20\amos\cwh\widgets\DestinatariPlusTagWidget::widget([
                                    'model' => $model,
                                    'moduleCwh' => $moduleCwh,
                                    'scope' => $scope
                                ]);
                                ?>
                            </div>
                        </div>

                        <?php
                        $isCommunityManager = \open20\amos\community\utilities\CommunityUtil::isLoggedCommunityManager();
                        $publish_enabled = !empty(Yii::$app->getModule('news')->params['publication_always_enabled'])
                            || (\Yii::$app->user->can('NEWS_PUBLISHER_FRONTEND')
                                && empty($scope)
                                && !$pubblicatedForCommunity);

                        $adminOrSuperUser = false;
                        if (\Yii::$app->user->can('ADMIN') || \Yii::$app->user->can('ADMIN_ARIA')) {
                            $adminOrSuperUser = true;
                        }

                        if ($newsModule->request_publish_on_hp == true) {
                            $isCommunityManager = $model->isCommunityManagerLoggedUserInThisNews();

                            if(!$adminOrSuperUser)
                            {
                                $publish_enabled = true;
                                $model->primo_piano = 0;
                                $model->in_evidenza = 0;
                            }
                        }

                        if ($publish_enabled) {
                            if (
                                Yii::$app->getModule('news')->params['site_publish_enabled']
                                || Yii::$app->getModule('news')->params['site_featured_enabled']
                            ) {
                        ?>
                                <div class="col-xs-12">
                                    <div class="row">
                                        <?php if (Yii::$app->getModule('news')->params['site_publish_enabled']) { ?>
                                            <h3 class="subtitle-section-form"><?= AmosNews::t('amosnews', "Pubblication on the portal mode") ?>
                                                <em>(<?= AmosNews::t('amosnews', "Choose if you want to publish the news also on the portal") ?>
                                                    )</em>
                                            </h3>
                                        <?php
                                            if (empty($model->primo_piano)) {
                                                $model->primo_piano = 0;
                                            }
                                            if (empty($model->in_evidenza)) {
                                                $model->in_evidenza = 0;
                                            }
                                            $primoPiano = '<div class="col-md-6 col-xs-12">'
                                                . $form->field($model, 'primo_piano')->widget(
                                                    Select::class,
                                                    [
                                                        'auto_fill' => true,
                                                        'data' => [
                                                            '0' => AmosNews::t('amosnews', 'No'),
                                                            '1' => AmosNews::t('amosnews', 'Si')
                                                        ],
                                                        'options' => [
                                                            'prompt' => AmosNews::t('amosnews', 'Seleziona'),
                                                            'disabled' => false,
                                                            'onchange' => "
                if($(this).val() == 1) $('#news-in_evidenza').prop('disabled', false);
                if($(this).val() == 0) {
                    $('#news-in_evidenza').prop('disabled', true);
                    $('#news-in_evidenza').val(0);
                }"
                                                        ],
                                                    ]
                                                ) .
                                                '</div>';
                                            if ($publish_enabled) {
                                                echo $primoPiano;
                                            }
                                        }

                                        if (Yii::$app->getModule('news')->params['site_featured_enabled']) {
                                            $inEvidenza = '<div class="col-md-6 col-xs-12">'
                                                . $form->field($model, 'in_evidenza')->widget(
                                                    Select::class,
                                                    [
                                                        'auto_fill' => true,
                                                        'data' => [
                                                            '0' => AmosNews::t('amosnews', 'No'),
                                                            '1' => AmosNews::t('amosnews', 'Si')
                                                        ],
                                                        'options' => [
                                                            'prompt' => AmosNews::t('amosnews', 'Seleziona'),
                                                            'disabled' => (isset($enableAgid) && true == $enableAgid) ? false : ($model->primo_piano
                                                                == 1 ? false : true)
                                                        ]
                                                    ]
                                                )
                                                . '</div>';
                                            if ($publish_enabled) {
                                                if ($newsModule->request_publish_on_hp && (!$adminOrSuperUser)) {
                                                    // se request_publish_on_hp ma non sono un uttente abilitato a mettere in evidenza... tolgo il campo!
                                                    echo '';
                                                } else {
                                                    echo $inEvidenza;
                                                }

                                            }
                                        }
                                        ?>
                                    </div>
                                </div>

                        <?php
                            }
                        }
                        if ($newsModule->request_publish_on_hp) {
                            $request_hp = '<div class="col-md-6 col-xs-12">'
                                . $form->field($model, 'request_publish_on_hp')->widget(Select::class, [
                                    'auto_fill' => true,
                                    'data' => [
                                        '0' => AmosNews::t('amosnews', 'No'),
                                        '1' => AmosNews::t('amosnews', 'Si')
                                    ],
                                    'options' => [
                                        'prompt' => AmosNews::t('amosnews', 'Seleziona'),
                                        'disabled' => $adminOrSuperUser? true: false
                                    ],
                                ])->label(AmosNews::t('amosnews', '#placeholder_for_choose_to_publish_on_hp'))
                            . '</div>';

                                // solo se sono community manager posso richiedere ad un admin oppure ad un super user la pubbicazione in evideza!
                                if ($isCommunityManager) {
                                    echo $request_hp;
                                }
                        }
                        ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php
        if (\Yii::$app->getModule('correlations')) {
            echo open2\amos\correlations\widget\ManageCorrelationsButtonWidget::widget([
                'model' => $model
            ]);
        }
        ?>
        <div class="col-xs-12"><?= RequiredFieldsTipWidget::widget() ?></div>

    </div>

    <div class="row">
        <div class="col-xs-12 section-form">
            <?php
            if ($newsModule->hidePubblicationDate == false) {
                $publicationDate = Html::tag(
                    'div',
                    $form->field($model, 'data_pubblicazione')->widget(
                        DateControl::class,
                        [
                            'type' => DateControl::FORMAT_DATE
                        ]
                    )
                        ->hint(AmosNews::t('amosnews', '#start_publication_date_hint')),
                    ['class' => 'col-md-4 col-xs-12']
                )
                    . Html::tag(
                        'div',
                        $form->field($model, 'data_rimozione')->widget(
                            DateControl::className(),
                            [
                                'type' => DateControl::FORMAT_DATE
                            ]
                        )
                            ->hint(AmosNews::t('amosnews', '#end_publication_date_hint')),
                        ['class' => 'col-md-4 col-xs-12']
                    );
            } else {
                $publicationDate = '';
            }

            if ($model->isNewRecord) { //default enable comment
                $model->comments_enabled = $newsModule->defaultEnableComments;
            }

            $enableComments = Html::tag(
                'div',
                $form->field($model, 'comments_enabled')->inline()->radioList(
                    [
                        '1' => AmosNews::t('amosnews', '#comments_ok'),
                        '0' => AmosNews::t('amosnews', '#comments_no')
                    ],
                    ['class' => 'comment-choice']
                ),
                ['class' => 'col-md-4 col-xs-12']
            );
            ?>
            <?php
            $contentLanguage = '';
            if ($moduleNotify && !empty($moduleNotify->enableNotificationContentLanguage) && $moduleNotify->enableNotificationContentLanguage) {
            ?>
                <?php
                $contentLanguage = "<div class=\"col-xs-6 nop\">" . \open20\amos\notificationmanager\widgets\NotifyContentLanguageWidget::widget([
                    'model' => $model
                ]) . "</div>"
                ?>
            <?php } ?>

            <?=
            AccordionWidget::widget([
                'items' => [
                    [
                        'header' => AmosNews::t('amosnews', '#settings_optional'),
                        'content' => $publicationDate . $enableComments . $contentLanguage . '<div class="clearfix"></div>',
                    ]
                ],
                'headerOptions' => ['tag' => 'h2'],
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

            <?php if ($moduleSeo) : ?>
                <div class="<?= $hideSeoModuleClass ?>">
                    <?= AccordionWidget::widget([
                        'items' => [
                            [
                                'header' => AmosNews::t('amosnews', '#settings_seo_title'),
                                'content' => \open20\amos\seo\widgets\SeoWidget::widget([
                                    'contentModel' => $model,
                                ]),
                            ]
                        ],
                        'headerOptions' => ['tag' => 'h2'],
                        'options' => Yii::$app->user->can('ADMIN') ? [] : ['style' => 'display:none;'],
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
        </div>

        <?php
        $config = [
            'model' => $model,
            'urlClose' => Yii::$app->session->get('previousUrl'),
            'buttonClassSave' => 'btn btn-workflow',
        ];

        $statusToRenderToHide = $model->getStatusToRenderToHide();

        $draftButtons = [];
        if ($disableStandardWorkflow == false) {
            $draftButtons = [
                News::NEWS_WORKFLOW_STATUS_DAVALIDARE => [
                    'button' => Html::submitButton(AmosNews::t('amosnews', 'Salva'), ['class' => 'btn btn-workflow']),
                    'description' => AmosNews::t(
                        'amosnews',
                        'le modifiche e mantieni la notizia in "richiesta di pubblicazione"'
                    )
                ],
                News::NEWS_WORKFLOW_STATUS_VALIDATO => [
                    'button' => \open20\amos\core\helpers\Html::submitButton(
                        AmosNews::t('amosnews', 'Salva'),
                        ['class' => 'btn btn-workflow']
                    ),
                    'description' => AmosNews::t('amosnews', 'le modifiche e mantieni la notizia "pubblicata"'),
                ],
                'default' => [
                    'button' => Html::submitButton(
                        AmosNews::t('amosnews', 'Salva in bozza'),
                        ['class' => 'btn btn-workflow']
                    ),
                    'description' => AmosNews::t('amosnews', 'potrai richiedere la pubblicazione in seguito'),
                ]
            ];
        }
        $closeButtonUrl = !empty(\Yii::$app->request->get('urlRedirect')) ? \Yii::$app->request->get('urlRedirect') : Yii::$app->session->get('previousUrl');

        echo WorkflowTransitionButtonsWidget::widget([
            'form' => $form,
            'model' => $model,
            'workflowId' => News::NEWS_WORKFLOW,
            'viewWidgetOnNewRecord' => true,
            //'closeSaveButtonWidget' => CloseSaveButtonWidget::widget($config),
            'closeButton' => Html::a(
                AmosNews::t('amosnews', 'Annulla'),
                $closeButtonUrl,
                ['class' => 'btn btn-secondary']
            ),
            'initialStatusName' => "BOZZA",
            'initialStatus' => News::NEWS_WORKFLOW_STATUS_BOZZA,
            'statusToRender' => $statusToRenderToHide['statusToRender'],
            //POII-1147 gli utenti validatore/facilitatore o ADMIN possono sempre salvare la news => parametro a false
            //altrimenti se stato VALIDATO => pulsante salva nascosto
            'hideSaveDraftStatus' => $statusToRenderToHide['hideDraftStatus'],
            'draftButtons' => $draftButtons
        ]);
        ?>
    </div>
</div>
<?php ActiveForm::end(); ?>