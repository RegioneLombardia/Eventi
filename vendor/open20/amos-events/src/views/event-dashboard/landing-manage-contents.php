<?php
/**
 * @var $eventLanding EventLanding
 * @var $model \open20\amos\events\models\Event
 * @var $dataProviderRelatedEvents
 */

use open20\amos\events\AmosEvents;
use open20\amos\core\forms\WizardPrevAndContinueButtonWidget;
use open20\amos\events\models\EventLandingRating;
use open20\amos\layout\assets\BootstrapItaliaCustomSpriteAsset;
use open20\amos\events\assets\WizardEventAsset;
use open20\amos\core\forms\bs4\ActiveForm;
use open20\amos\core\icons\AmosIcons;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use open20\amos\events\models\EventLanding;

$this->title = AmosEvents::t('amosevents', "Gestisci i contenuti della landing page");
$this->params['breadcrumbs'][] = ['label' => Yii::$app->session->get('previousTitle'), 'url' => Yii::$app->session->get('previousUrl')];
$this->params['breadcrumbs'][] = $this->title;

$module = \Yii::$app->getModule('events');
$wizardAsset = WizardEventAsset::register($this);
$spriteAsset = BootstrapItaliaCustomSpriteAsset::register($this);
$eventId = $model->id;
$js = <<<JS
    var changedValues = false;
    $('.save-all-data').click(function(e){
        e.preventDefault();
        $('#save_and_redirect_to').val($(this).attr('href'));
        console.log(changedValues);
        if(changedValues){
                   $('#confirm-before-save').modal('show');
        }
        else {
            window.location.href = $(this).attr('href');
        }
    });

    $('#confirm-before-save').on('hidden.bs.modal', function () {
            $('#save_and_redirect_to').val('');
    });
    
    $('#annulla-save').click(function(e){
        e.preventDefault();
        $('#save_and_redirect_to').val('');
        $('#confirm-before-save').modal('hide');
    });
    
    $('input[type="text"], #gallery-type-id').change(function(){
        changedValues = true;
    });
    
    $('#map-style-id').on('change',function(){
         $.ajax({
           url: '/events/event-location/ajax-view-map',
           data: {eventId: '$eventId', mapStyle: $(this).val()},
           type: 'get',
           success: function (data) {
                $('#location-container').html(data);
                if(typeof(initMap_event_place_id) === typeof(Function)){
                    initMap_event_place_id();
                   // console.log('entrato');
                }
           }
      });
    });
    
    
    
JS;
$this->registerJs($js);

$tinyMCECallback = <<< JS
    function (editor) {
        editor.on('change', function () {
               changedValues = true;
        });
    }
JS;

echo \open20\amos\events\widgets\ChangeLanguage::widget(['model' => $model]);

$form = ActiveForm::begin(); ?>

    <h3><?= AmosEvents::t('amosevents', 'Evento "{title}"', ['title' => $model->getTitle()]) ?></h3>
    <div class="row">
        <div class="col-md-12">
            <?php if (empty($eventLanding->title_presentation)) {
                $eventLanding->title_presentation = '';
            } ?>
            <?= $form->field($eventLanding, 'title_presentation')
                ->label(AmosEvents::t('amosevents', "Nome sezione presentazione")); ?>
        </div>
        <div class="col-md-12">
            <?= $form->field($eventLanding, 'description')->widget(\open20\amos\core\forms\TextEditorWidget::className(), [
                'options' => ['placeholder' => AmosEvents::t('amosevents', 'Scrivi una breve presentazione del tuo evento. Usa un massimo di 250 caratteri.')],
                'clientOptions' => [
                    'toolbar' => \open20\amos\events\utility\EventsUtility::getToolbarTextEditor(),
                    'setup' => new \yii\web\JsExpression($tinyMCECallback),
                ]
            ]) ?>
        </div>
    </div>
    <br>

    <div class="row">
        <div class="col-md-12">
            <?php if (empty($eventLanding->title_schedule)) {
                $eventLanding->title_schedule = EventLanding::defaultLabelsTitleSections('title_schedule');
            } ?>
            <?= $form->field($eventLanding, 'title_schedule')
                ->label(AmosEvents::t('amosevents', "Nome sezione programma")); ?>
        </div>
        <div class="col-md-12">
            <?= $form->field($eventLanding, 'schedule')->widget(\open20\amos\core\forms\TextEditorWidget::className(), [
                'options' => ['placeholder' => AmosEvents::t('amosevents', 'Scrivi il programma del tuo evento. Usa un massimo di 1500 caratteri.')],
                'clientOptions' => [
                    'toolbar' => \open20\amos\events\utility\EventsUtility::getToolbarTextEditor(),
                    'setup' => new \yii\web\JsExpression($tinyMCECallback),

                ]
            ]) ?>
        </div>
    </div>


<?php echo $form->field($eventLanding, 'event_id')->hiddenInput()->Label(false); ?>


    <div>
        <h3><?= AmosEvents::t('amosevents', "Protagonisti") ?></h3>
        <div class="row mt-1">
            <div class="col-md-6">
                <?php if (empty($eventLanding->title_protagonists)) {
                    $eventLanding->title_protagonists = EventLanding::defaultLabelsTitleSections('title_protagonists');
                } ?>
                <?= $form->field($eventLanding, 'title_protagonists')
                    ->label(AmosEvents::t('amosevents', "Nome sezione")); ?>
            </div>
            <div class="col-md-12">
                <?= $form->field($eventLanding, 'description_protagonists')->widget(\open20\amos\core\forms\TextEditorWidget::className(), [
                    'options' => ['placeholder' => AmosEvents::t('amosevents', 'Scrivi un testo di introduzione')],
                    'clientOptions' => [
                        'toolbar' => \open20\amos\events\utility\EventsUtility::getToolbarTextEditor(),
                        'setup' => new \yii\web\JsExpression($tinyMCECallback),

                    ]
                ]) ?>
            </div>
        </div>

        <div>
            <?php if (!$eventLanding->isNewRecord) { ?>
                <?php echo Html::a(AmosEvents::t('amosevents', 'Aggiungi protagonista'), [
                    '/events/event-landing-protagonist/create', 'idLanding' => $eventLanding->id], [
                    'class' => 'btn btn-secondary btn-sm mb-3 save-all-data'
                ]) ?>
                <?= \open20\amos\core\views\AmosGridView::widget(
                    [
                        'dataProvider' => $dataProviderProtagonists,
                        'columns' => [
                            'immagine' => [
                                'label' => AmosEvents::t('amosevents', 'Immagine'),
                                'format' => 'html',
                                'value' => function ($model) {
                                    $url = '/img/defaultProfiloM.png';
                                    if (!is_null($model->image)) {
                                        $url = $model->image->getWebUrl('table_small', false, true);
                                    }
                                    $contentImage = Html::img($url, ['class' => 'gridview-image', 'alt' => AmosEvents::t('amosnews', 'Immagine della notizia')]);

                                    return Html::a($contentImage, []);
                                }
                            ],
                            'name',
                            'surname',
                            'company',
                            [
                                'class' => \open20\amos\core\views\grid\ActionColumn::className(),
                                'controller' => 'event-landing-protagonist',
                                'template' => '{update}{delete}',
                                'buttons' => [
                                    'update' => function ($url, $model) {
                                        $spriteAsset = BootstrapItaliaCustomSpriteAsset::register($this);
                                        $iconModifica = "<use xlink:href=" . $spriteAsset->baseUrl . "/material-sprite.svg#ic_edit></use>";
                                        $svgIconModifica = Html::tag('svg', $iconModifica, ['class' => 'icon icon-white']);
                                        $spanSvgIconModifica = Html::tag('span', $svgIconModifica, ['class' => 'rounded-icon rounded-secondary p-1']) . Html::tag('span', AmosEvents::t('amosevents', 'Modifica'), ['class' => 'sr-only']);
                                        return Html::a($spanSvgIconModifica, $url, ['class' => 'btn btn-xs btn-icon', 'data-toggle' => 'tooltip', 'title' => 'Modifica'], true);
                                    },
                                    'delete' => function ($url, $model) {
                                        $spriteAsset = BootstrapItaliaCustomSpriteAsset::register($this);
                                        $iconDelete = "<use xlink:href=" . $spriteAsset->baseUrl . "/material-sprite.svg#ic_close></use>";
                                        $svgIconDelete = Html::tag('svg', $iconDelete, ['class' => 'icon icon-white']);
                                        $spanSvgIconDelete = Html::tag('span', $svgIconDelete, ['class' => 'rounded-icon rounded-danger p-1']) . Html::tag('span', AmosEvents::t('amosevents', 'Elimina'), ['class' => 'sr-only']);
                                        return Html::a($spanSvgIconDelete, $url, ['class' => 'btn btn-xs btn-icon', 'data-toggle' => 'tooltip', 'title' => 'Elimina'], true);
                                    }
                                ]
                            ]
                        ]
                    ]
                ) ?>
            <?php } else { ?>
                <p><?= AmosEvents::t('amosevents', 'Per inserire i protagonisti è necessario salvare i dati') ?></p>
            <?php } ?>
        </div>
    </div>

    <hr class="dashed my-5">

    <div>
        <h3><?= AmosEvents::t('amosevents', "Pics") ?></h3>
        <div class="row mt-1">
            <div class="col-md-6">
                <?php if (empty($eventLanding->title_pics)) {
                    $eventLanding->title_pics = EventLanding::defaultLabelsTitleSections('title_pics');
                } ?>
                <?= $form->field($eventLanding, 'title_pics')
                    ->label(AmosEvents::t('amosevents', "Nome sezione")); ?>
            </div>
            <div class="col-md-6">
                <?php if (empty($eventLanding->gallery_type)) {
                    $eventLanding->gallery_type = 1;
                } ?>
                <?= $form->field($eventLanding, 'gallery_type')->widget(Select2::className(), [
                    'data' => $eventLanding->getLabelTypeGallery(),
                    'options' => [
                        'id' => 'gallery-type-id',
                    ]
                ]); ?>
            </div>
        </div>

        <div>
            <?php if (!$eventLanding->isNewRecord) { ?>
                <?= $this->render('_sliders', [
                    'model' => $model,
                    'landing' => $eventLanding,
                    'dataProviderSliderElemImage' => $dataProviderSliderElemImage,
                ]) ?>
            <?php } else { ?>
                <p><?= AmosEvents::t('amosevents', 'Per inserire le immagini nella gallery è necessario salvare i dati.') ?></p>
            <?php } ?>
        </div>

    </div>

    <hr class="dashed my-5">
    <div>
        <h3><?= AmosEvents::t('amosevents', "Video") ?></h3>
        <div class="row mt-1">
            <div class="col-md-6">
                <?php if (empty($eventLanding->title_video)) {
                    $eventLanding->title_video = EventLanding::defaultLabelsTitleSections('title_video');
                } ?>
                <?= $form->field($eventLanding, 'title_video')
                    ->label(AmosEvents::t('amosevents', "Nome sezione")); ?>
            </div>
        </div>
        <div>
            <?php if (!$eventLanding->isNewRecord) { ?>
                <?= $this->render('_sliders_video', [
                    'model' => $model,
                    'landing' => $eventLanding,
                    'dataProviderSliderElemVideo' => $dataProviderSliderElemVideo

                ]) ?>
            <?php } else { ?>
                <p><?= AmosEvents::t('amosevents', 'Per inserire i video è necessario salvare i dati.') ?></p>
            <?php } ?>
        </div>
    </div>

    <hr class="dashed my-5">
    <div>
        <h3><?= AmosEvents::t('amosevents', "Instagram") ?></h3>
        <div class="row mt-1">
            <div class="col-md-6">
                <?php if (empty($eventLanding->title_instagram_video)) {
                    $eventLanding->title_instagram_video = EventLanding::defaultLabelsTitleSections('title_instagram_video');
                } ?>
                <?= $form->field($eventLanding, 'title_instagram_video')
                    ->label(AmosEvents::t('amosevents', "Nome sezione")); ?>
            </div>
        </div>
        <div>
            <?php if (!$eventLanding->isNewRecord) { ?>
                <?= $this->render('_sliders_instagram', [
                    'model' => $model,
                    'landing' => $eventLanding,
                    'dataProviderSliderElemInstagram' => $dataProviderSliderElemInstagram

                ]) ?>
            <?php } else { ?>
                <p><?= AmosEvents::t('amosevents', 'Per inserire i video è necessario salvare i dati.') ?></p>
            <?php } ?>
        </div>
    </div>

    <hr class="dashed my-5">

    <div>
        <h3><?= AmosEvents::t('amosevents', "Info") ?></h3>
        <div class="row mt-1">
            <div class="col-md-6">
                <?php if (empty($eventLanding->title_info)) {
                    $eventLanding->title_info = EventLanding::defaultLabelsTitleSections('title_info');
                } ?>
                <?= $form->field($eventLanding, 'title_info')
                    ->label(AmosEvents::t('amosevents', "Nome sezione")); ?>
            </div>
        </div>
        <div>
            <?php if (!$eventLanding->isNewRecord) { ?>
                <?= $this->render('_parts/_news', [
                    'model' => $model,
                    'landing' => $eventLanding,
                    'dataProviderNews' => $dataProviderNews
                ]) ?>
            <?php } else { ?>
                <p><?= AmosEvents::t('amosevents', 'Per aggiungere le notizie é necessario salvare i dati.') ?></p>
            <?php } ?>
        </div>
    </div>
    <hr class="dashed my-5">

    <div>
        <h3><?= AmosEvents::t('amosevents', "Documenti") ?></h3>
        <div class="row mt-1">
            <div class="col-md-6">
                <?php if (empty($eventLanding->title_documents)) {
                    $eventLanding->title_documents = EventLanding::defaultLabelsTitleSections('title_documents');
                } ?>
                <?= $form->field($eventLanding, 'title_documents')
                    ->label(AmosEvents::t('amosevents', "Nome sezione")); ?>
            </div>
        </div>
        <div>
            <?php if (!$eventLanding->isNewRecord) { ?>
                <?= $this->render('_parts/_documents', [
                    'model' => $model,
                    'landing' => $eventLanding,
                    'dataProviderDocs' => $dataProviderDocs
                ]) ?>
            <?php } else { ?>
                <p><?= AmosEvents::t('amosevents', 'Per aggiungere i documenti é necessario salvare i dati.') ?></p>
            <?php } ?>
        </div>
    </div>

    <hr class="dashed my-5">

    <div>
        <h3><?= AmosEvents::t('amosevents', "Mappa") ?></h3>
        <div class="row mt-1">
            <div class="col-md-6">
                <?php if (empty($eventLanding->title_maps)) {
                    $eventLanding->title_maps = EventLanding::defaultLabelsTitleSections('title_maps');
                } ?>
                <?= $form->field($eventLanding, 'title_maps')
                    ->label(AmosEvents::t('amosevents', "Nome sezione")); ?>
            </div>
            <?php if ($module->enableCustomMaps) { ?>
                <div class="col-md-6">
                    <?= $form->field($eventLanding, 'map_style')->widget(Select2::className(), [
                        'data' => [
                            'roadmap' => 'Standard',
                            'satellite' => 'Satellitare',
                            'terrain' => 'Terrena',
                            'aubergine' => 'Aubergine',
                            'retro' => 'Retrò',
                        ],
                        'options' => ['id' => 'map-style-id']
                    ])->label(AmosEvents::t('amosevents', "Stile mappa")); ?>
                </div>
            <?php } ?>
        </div>
        <?php if ($module->enableCustomMaps) { ?>
            <div id="location-container" class="row">
                <?= $this->render('@vendor/open20/amos-events/src/views/event-location/view-map', [
                    'model' => $model,
                    'eventLanding' => $eventLanding
                ]); ?>
            </div>
        <?php } ?>

    </div>

    <hr class="dashed my-5">


    <h3><?= AmosEvents::t('amosevents', "Richiesta informazioni evento") ?></h3>
    <div class="row mt-1">
        <div class="col-md-6">
            <?php if (empty($eventLanding->title_request_info)) {
                $eventLanding->title_request_info = EventLanding::defaultLabelsTitleSections('title_request_info');
            } ?>
            <?= $form->field($eventLanding, 'title_request_info')->label(AmosEvents::t('amosevents', "Nome sezione")); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($eventLanding, 'contact_info_organizator')->widget(\open20\amos\core\forms\TextEditorWidget::className(), [
                'options' => ['placeholder' => AmosEvents::t('amosevents', 'Scrivi le informazioni di contatto dell’organizzatore evento')],
                'clientOptions' => [
                    'toolbar' => \open20\amos\events\utility\EventsUtility::getToolbarTextEditor(),
                    'setup' => new \yii\web\JsExpression($tinyMCECallback),

                ]
            ])->label(AmosEvents::t('amosevents', "Informazioni di contatto dell’organizzatore evento")) ?>
        </div>
    </div>

    <hr class="dashed my-5">


    <h3><?= AmosEvents::t('amosevents', "Eventi correlati") ?></h3>
    <div id="container-related-events" class="row mt-1">
        <div class="col-md-6">
            <?php if (empty($eventLanding->title_related_events)) {
                $eventLanding->title_related_events = EventLanding::defaultLabelsTitleSections('title_related_events');
            } ?>
            <?= $form->field($eventLanding, 'title_related_events')->label(AmosEvents::t('amosevents', "Nome sezione")); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?= $this->render('_parts/_related_events', [
                'dataProviderRelatedEvents' => $dataProviderRelatedEvents,
                'model' => $model
            ]); ?>
        </div>
    </div>

<?php
/**
 * Streaming enabled for Informative and Public and open to all
 */
?>
<?php if ($model->eventType->event_type == \open20\amos\events\models\EventType::TYPE_INFORMATIVE || ($model->eventType->event_type == \open20\amos\events\models\EventType::TYPE_OPEN && $model->eventType->limited_seats == false)) { ?>
    <hr class="dashed my-5">

    <div id="streaming">
        <h3><?= AmosEvents::t('amosevents', "Streaming") ?></h3>
        <div>
            <?php if (!$eventLanding->isNewRecord) { ?>
                <?= $this->render('_parts/_streaming', [
                    'model' => $model,
                    'landing' => $eventLanding,
                    'form' => $form
                ]) ?>
            <?php } else { ?>
                <p><?= AmosEvents::t('amosevents', 'Per aggiungere lo streaming é necessario salvare i dati.') ?></p>
            <?php } ?>
        </div>
    </div>
<?php } ?>

    <hr class="dashed my-5">

    <!-- RATING -->
    <div id="rating">
        <?php
        $countRatings = EventLandingRating::find()->andWhere(['event_id' => $model->id])->count();
        $averageRating = empty($countRatings) ? '-' : round((EventLandingRating::find()->andWhere(['event_id' => $model->id])->sum('rating') / $countRatings), 2);
        ?>
        <h3><?= AmosEvents::t('amosevents', "Rating") ?></h3>
        <div>

            <div class="row">
                <div class="col-md-3">
                    <?php echo
                    $form->field($eventLanding, 'enable_rating')->widget(kartik\widgets\SwitchInput::className(), [
                        'options' => ['id' => 'switch-enable-rating'],
                        'pluginOptions' => [
                            //'size' => 'mini',
                            'onText' => 'Si',
                            'offText' => 'No'
                        ],
                    ])->label(AmosEvents::t('amosevents', 'Abilita nella landing page'));
                    ?>
                </div>
                <div class="col-md-9">
                    <?php if (empty($eventLanding->rating_title)) {
                        $eventLanding->rating_title = EventLanding::defaultLabelsTitleSections('rating_title');
                    } ?>
                    <?= $form->field($eventLanding, 'rating_title')
                        ->textInput()
                        ->label(AmosEvents::t('amosevents', 'Titolo visualizzato in landing'));
                    ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <?php if (empty($eventLanding->rating_description)) {
                        $eventLanding->rating_description = AmosEvents::t('amosevents', 'Come valuti la tua esperienza con il servizio di Eventi Regione Lombardia?');
                    } ?>
                    <?= $form->field($eventLanding, 'rating_description')
                        ->textarea(['rows' => 5])
                        ->label(AmosEvents::t('amosevents', 'Descrizione'));
                    ?>
                </div>
            </div>

            <!--        <hr class="dotted">-->
            <div class="row results-rating" style="background-color:#dde2e7; padding:10px">
                <div class="col-md-12">
                    <h5><?= AmosEvents::t('amosevents', 'Risultati') ?></h5>
                    <div>
                        <strong><?= AmosEvents::t('amosevents', 'Voto medio') ?>
                            :</strong> <?= $averageRating . ' / 5' ?>
                    </div>
                    <div>
                        <strong><?= AmosEvents::t('amosevents', 'Nr. partecipanti') ?>:</strong> <?= $countRatings ?>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <hr class="dashed my-5">

    <!-- SHARE PAGE -->
    <div id="share-page">
        <h3><?= AmosEvents::t('amosevents', "Condividi") ?></h3>
        <div>

            <div class="row">
                <div class="col-md-3">
                    <?php echo
                    $form->field($eventLanding, 'is_social_share_enabled')->widget(kartik\widgets\SwitchInput::className(), [
                        'options' => ['id' => 'switch-enable-share'],
                        'pluginOptions' => [
                            //'size' => 'mini',
                            'onText' => 'Si',
                            'offText' => 'No'
                        ],
                    ])->label(AmosEvents::t('amosevents', 'Abilita nella landing page'));
                    ?>
                </div>
                <div class="col-md-9">
                    <?php if (empty($eventLanding->share_title)) {
                        $eventLanding->share_title = EventLanding::defaultLabelsTitleSections('share_title');
                    } ?>
                    <?= $form->field($eventLanding, 'share_title')
                        ->textInput()
                        ->label(AmosEvents::t('amosevents', 'Titolo visualizzato in landing'));
                    ?>
                </div>
            </div>

            <h4><?= AmosEvents::t('amosevents', "Social tag") ?></h4>
            <p><?= AmosEvents::t('amosevents', "I Social tag consentono di inserire informazioni specifiche da utilizzare nella condivisione del contenuto sui social.<br>Se non vengono difiniti verranno usati quelli di default dell'evento") ?></p>
            <?php $seoData = $model->getContentSeoData(); ?>

            <div class="row">
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-12">
                            <?= $form->field($seoData, 'meta_title')->label('Og title') ?>
                        </div>
                        <div class="col-md-12">
                            <?= $form->field($seoData, 'og_description')->label('Og description') ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <?=
                    $form->field($eventLanding, 'ogImageFile')->widget(
                        \open20\amos\attachments\components\CropInput::classname(),
                        [
                            'jcropOptions' => ['aspectRatio' => '1.7', 'placeholder' => AmosEvents::t('amosevents', 'Per una visualizzazione ottimale, carica un\'immagine rettangolare 1920x1080 pixel.')],
                            'options' => [
                                'placeholder' => AmosEvents::t('amosevents', 'Per una visualizzazione ottimale, carica un\'immagine rettangolare 1920x1080 pixel.')
                            ]
                        ]
                    )->label(AmosEvents::t('amosevents', "Og image"))
                    ?>
                </div>

            </div>
        </div>
    </div>


<?php echo Html::hiddenInput('save_and_redirect_to', null, ['id' => 'save_and_redirect_to']) ?>
    <div class="buttons mt-5 d-flex">
        <?= Html::submitButton(AmosEvents::t('amosevents', 'Save'), ['class' => 'btn btn-primary ml-auto']) ?>
    </div>

<?php \yii\bootstrap4\Modal::begin([
    'id' => 'confirm-before-save',
    'size' => \yii\bootstrap4\Modal::SIZE_LARGE,

]); ?>

    <h5><?= AmosEvents::t('amosevents', "Verranno salvate le modifiche apportate alla scheda, confermi?") ?></h5>
    <div class="col-md-12 mt-5">
        <?= Html::a(AmosEvents::t('amosevents', "Annulla"), '', [
                'class' => 'btn btn-secondary',
                'id' => 'annulla-save'
            ]
        ); ?>
        <?= Html::submitButton(AmosEvents::t('amosevents', "Conferma"), [
                'class' => 'btn btn-primary pull-right',
                'id' => 'confirm-save'
            ]
        ); ?>
    </div>
<?php \yii\bootstrap4\Modal::end(); ?>
<?php ActiveForm::end(); ?>