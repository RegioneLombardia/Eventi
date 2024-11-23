<?php

use open20\amos\attachments\components\AttachmentsInput;
use \open20\amos\documenti\AmosDocumenti;
use yii\helpers\Html;
use open20\amos\core\icons\AmosIcons;

$enabledGoogleDrive = $module && $module->enableGoogleDrive;
$documentsModule = AmosDocumenti::instance();
$js = <<<JS

    $('#type-main-document-id').change(function(){
        if($(this).val() ==='1'){
            $('#main-file-container').show();
            $('#link-document-container').hide();
            $('#link-document-id').val('');
        } else {
            $('#main-file-container').hide();
            $('#link-document-container').show();
        }
    });
JS;

$this->registerJs($js); ?>
<?php if(!empty($model->link_document)){
    $hideExternalLink = '';
    $hideMainDocument = 'display:none';
    $model->typeMainDocument = 2;
}else{
    $hideExternalLink = 'display:none';
    $hideMainDocument = '';
    $model->typeMainDocument = 1;
}?>

<?= $form->field($model, 'typeMainDocument')->widget(\kartik\select2\Select2::className(), [
    'data' => [
        1 => AmosDocumenti::t('amosdocumenti', 'File'),
        2 => AmosDocumenti::t('amosdocumenti', 'Link esterno')],
    'options' => ['id' => 'type-main-document-id']
])->label(AmosDocumenti::t('amosdocumenti', 'tipo di documento'))
?>
<?php if ($enabledGoogleDrive && !empty($model->drive_file_id)) { ?>
    <div class="documents-view">
        <?= $this->render('_download_box', [
            'model' => $model,
            'isFolder' => $isFolder,
            'controller' => \Yii::$app->controller,
            'showNewVersionButton' => false
        ]) ?>
    </div>
<?php } else { ?>
    <?php if ($enabledGoogleDrive) { ?>
        <?= \open20\amos\documenti\widgets\GoogleDriveWidget::widget([
            'model' => $model,
            'form' => $form
        ]); ?>
    <?php } ?>

    <?php if (!$isFolder) { ?>
        <div id="container-document-mainfile" class="col-xs-12 nop">

            <div id="main-file-container" style="<?=$hideMainDocument?>">
                <?= $form->field($model,
                    'documentMainFile')->widget(AttachmentsInput::classname(), [
                    'options' => [
                        'multiple' => FALSE,
                    ],
                    'pluginOptions' => [ // Plugin options of the Kartik's FileInput widget
                        'maxFileCount' => 1,
                        'showRemove' => false,
                        'indicatorNew' => false,
                        'allowedPreviewTypes' => false,
                        'previewFileIconSettings' => false,
                        'overwriteInitial' => false,
//                            'layoutTemplates' => false,
                    ],
                    'enableGoogleDrive' => $enabledGoogleDrive,
                ])->label(AmosDocumenti::t('amosdocumenti', '#image_field'))->hint(AmosDocumenti::t('amosdocumenti', '#image_field_hint').$documentsModule->whiteListFilesExtensions) ?>
            </div>

            <div id="link-document-container" style="<?=$hideExternalLink?>">
                <?= $form->field($model, 'link_document')->textInput([
                    'maxlength' => true,
                    'placeholder' => AmosDocumenti::t('amosdocumenti', '#link_document_field_placeholder'),
                    'id' => 'link-document-id'
                ])
                    ->hint(AmosDocumenti::t('amosdocumenti', '#link_document_field_hint'))
                ?>
            </div>

            <?php if (!empty($documento)): ?>
                <?= $documento->filename ?>
                <?= Html::a(AmosIcons::show('download', ['class' => 'btn btn-tools-secondary']), ['/documenti/documenti/download-documento-principale', 'id' => $model->id], [
                    'title' => 'Download file',
                    'class' => 'bk-btnImport'
                ]); ?>
            <?php endif; ?>

        </div>
    <?php } ?>
<?php } ?>