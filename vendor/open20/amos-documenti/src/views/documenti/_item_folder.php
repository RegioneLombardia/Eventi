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
use open20\amos\core\helpers\Html;
use open20\amos\core\icons\AmosIcons;
use open20\amos\core\forms\PublishedByWidget;
use open20\amos\documenti\AmosDocumenti;
use open20\amos\notificationmanager\forms\NewsWidget;


/**
 * @var yii\web\View $this
 * @var open20\amos\documenti\models\Documenti $model
 */

$modelViewUrl = $model->getFullViewUrl();

?>
<div class="document-item-container item-folder flexbox-column border-bottom py-4 w-100">

    <div class="flexbox align-item-center">
        <?php
        if ($model->drive_file_id) { ?>
            <span class="icon icon-folder icon-sm mdi mdi-folder-google-drive"></span>
        <?php
        } else { ?>
            <span class="icon icon-folder icon-sm mdi mdi-folder"></span>
            <?php
        }
        ?>
        <span class="small uppercase"><?= AmosDocumenti::t('amosdocumenti', 'Cartella') ?></span>

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
                    'labelDeleteConfirm' => AmosDocumenti::t('amosdocumenti', '#confirm_delete_folder')
                    //                    'mainDivClasses' => 'col-xs-1 nop'
                ]) ?>
            </div>

        </div>
    </div>
   
    <?= Html::a(Html::tag('p', htmlspecialchars($model->titolo), ['class' => 'h5 title-one-line']), $modelViewUrl, ['class' => 'link-list-title', 'title' => AmosDocumenti::t('amosdocumenti', 'Vai alla cartella ').$model->titolo]) ?>
                
    <div class="small mb-2">
        <?php $stringa = \open20\amos\documenti\models\DocumentiCartellePath::getPath($model); 
        echo AmosDocumenti::t(
                    'amosdocumenti',
                    'Percorso: '
                    ).$stringa. $model->titolo
                ?>
    </div>
    <div class="small mb-2">
      
     <?= PublishedByWidget::widget([
                'model' => $model,
                'layout' => (isset(\Yii::$app->params['hideListsContentCreatorName']) && (\Yii::$app->params['hideListsContentCreatorName'] === true) ? '' : '{publisher}') . '{targetAdv}' . ((!$isFolder && $enableCategories) ? '{category}' : '') . (Yii::$app->user->can('ADMIN') ? '{status}' : '')
            ]) ?>
    </div>

    <div>
        <?= Html::a(AmosDocumenti::t('amosdocumenti', 'Apri') , $modelViewUrl, ['class' => 'read-more d-inline mr-2 uppercase bold small', 'title' => AmosDocumenti::t('amosdocumenti', 'Apri la cartella ')]) ?>

    </div>
    
</div>