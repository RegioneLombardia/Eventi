<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\documenti\views\documenti
 * @category   CategoryName
 */

use open20\amos\core\forms\ContextMenuWidget;
use open20\amos\core\forms\PublishedByWidget;
use open20\amos\core\helpers\Html;
use open20\amos\documenti\AmosDocumenti;
use open20\amos\notificationmanager\forms\NewsWidget;

/**
 * @var yii\web\View $this
 * @var open20\amos\documenti\models\Documenti $model
 */

/** @var AmosDocumenti $documentsModule */
$documentsModule = AmosDocumenti::instance();
$documentMainFile = $model->getDocumentMainFile();
$modelViewUrl = $model->getFullViewUrl();
$document = $model->getDocumentMainFile();
$documentPresent = ($document != null);
$documentLinkPresent = (!empty($model->link_document));
$visible = isset($statsToolbar) ? $statsToolbar : false;
$enableContentDuplication = $documentsModule->enableContentDuplication;
$enableCatImgInDocView = $documentsModule->enableCatImgInDocView;
$documentCategory = $model->documentiCategorie;
$isFolder = $model->is_folder;
$enableCategories = $documentsModule->enableCategories;
$modelTitleSpecialChars = htmlspecialchars($model->titolo);

$jsCount = <<<JS
    $('.link-document-id').click(function() {
        var idDoc = $(this).attr('data-key');
        $.ajax({
           url: 'increment-count-download-link?id='+idDoc,
           type: 'get',
           success: function (data) {
           }

      });
    })
JS;

$this->registerJs($jsCount);
?>

<div class="document-item-container d-flex border-bottom py-4 w-100">

    <div class="info-doc">
        <div>

            <?= \open20\amos\documenti\utility\DocumentsUtility::getDocumentIcon($model); ?>
            <span class="text-muted small"><?= $docExtension = strtoupper($document->type); ?>
            <?php if ($documentPresent): ?>

                (<?= $model->documentMainFile->formattedSize ?>) - <?= AmosDocumenti::tHtml('amosdocumenti', 'File principale:') ?>
             <?php endif; ?>
             </span>

            <!-- TODO da finire e fare la parte per link documento esterno -->
            <?php
            if ($documentPresent) {
                echo Html::tag('span', ((strlen($documentMainFile->name) > 80) ? substr($documentMainFile->name, 0, 75) . '[...]' : $documentMainFile->name) . '.' . $documentMainFile->type, ['class' => 'text-muted small']);
            } else {
                if ($documentLinkPresent) {
                    echo Html::tag('span', (AmosDocumenti::tHtml('amosdocumenti', 'File esterno')), ['class' => 'text-muted small']);
                }
            }
            ?>

        </div>
        <?php if ($documentLinkPresent) : ?>
            <?= Html::a(
                $model->titolo,
                $model->link_document,
                [
                    'class' => 'link-list-title h5',
                    'target' => 'blank',
                    'title' => AmosDocumenti::t('amosdocumenti', 'Apri il link esterno al documento') . ' ' . $modelTitleSpecialChars
                ]
            )
            ?>
        <?php else : ?>
            <?= Html::a(
                $model->titolo,
                $modelViewUrl,
                [
                    'class' => 'link-list-title h5',
                    'title' => AmosDocumenti::t('amosdocumenti', 'Apri la scheda del documento') . ' ' . $modelTitleSpecialChars
                ]
            )
            ?>
        <?php endif; ?>
        <p class="mb-0 m-t-5 text-muted">
            <?php $stringa = \open20\amos\documenti\models\DocumentiCartellePath::getPath($model); 
                echo AmosDocumenti::t(
                    'amosdocumenti',
                    'Percorso: '
                    ).$stringa. $model->titolo
                ?>
        </p>
        <?php if ($model->descrizione_breve) { ?>
            <p class="mb-0 m-t-5 text-muted">
                <?= htmlspecialchars($model->descrizione_breve) ?>
            </p>
        <?php } ?>
        <div class="small mb-2 m-t-10">
            <?= PublishedByWidget::widget([
                'model' => $model,
                'layout' => (isset(\Yii::$app->params['hideListsContentCreatorName']) && (\Yii::$app->params['hideListsContentCreatorName'] === true) ? '' : '{publisher}') . '{targetAdv}' . ((!$isFolder && $enableCategories) ? '{category}' : '') . (Yii::$app->user->can('ADMIN') ? '{status}' : '')
            ]) ?>
        </div>
        <div>
            <?php
            if ($documentPresent) {
                echo Html::a(
                    AmosDocumenti::t('amosdocumenti', 'Scarica'),
                    [
                        '/attachments/file/download/',
                        'id' => $document->id,
                        'hash' => $document->hash,
                        'download' => true
                    ],
                    [
                        'title' => AmosDocumenti::t('amosdocumenti', 'Scarica il documento') . ' ' . $modelTitleSpecialChars,
                        'class' => 'm-r-10 small uppercase bold',
                    ]
                );
            }

            ?>

            <?php if ($documentLinkPresent) {
                echo Html::a(
                    AmosDocumenti::t('amosdocumenti', '#detail'),
                    $model->link_document,
                    [
                        'title' => AmosDocumenti::t('amosdocumenti', '#see_external_document_detail') . ' ' . $modelTitleSpecialChars,
                        'class' => 'small m-r-10 uppercase bold',
                        'target' => '_blank',
                        'data-key' => $model->id
                    ]
                );
            } else if ($documentPresent) { ?>
                <?= Html::a(
                    AmosDocumenti::t('amosdocumenti', '#detail'),
                    $modelViewUrl,
                    [
                        'class' => 'small uppercase bold',
                        'title' => AmosDocumenti::t('amosdocumenti', '#see_document_detail') . ' ' . $modelTitleSpecialChars
                    ]
                )
                ?>
            <?php } ?>

        </div>
    </div>
    <div class="ml-auto doc-actions d-flex">
        <div>
            <?= NewsWidget::widget(['model' => $model]); ?>
        </div>
        <div>
            <?= ContextMenuWidget::widget([
                'model' => $model,
                'actionModify' => "/documenti/documenti/update?id=" . $model->id,
                'actionDelete' => "/documenti/documenti/delete?id=" . $model->id,
                'modelValidatePermission' => 'DocumentValidate',
                'mainDivClasses' => 'manage-documents'
            ]) ?>
        </div>
    </div>
</div>
