<?php

use app\modules\cms\helpers\CmsHelper;
use open20\amos\news\AmosNews;
$modalId = 'gallery-modal-' . $model->id . '-id';


$truncate = 250;
?>

<div class="col-12">
    <div class="it-grid-item-wrapper">
        <a href="<?= 'javascript:$(\'#' . $modalId . '\').modal(\'toggle\');' ?>">
            <div class="img-responsive-wrapper">
                <div class="img-responsive">
                    <div class="img-wrapper">
                    <?php
                            $url = \Yii::$app->params['platform']['backendUrl'] . '/img/img_default.jpg';
                            if (!empty($model->fileImage)) {
                                $url =  $model->fileImage->getWebUrl('social', false, true);
                            }

                            echo $imgHtml = CmsHelper::img(
                                $url,
                                [
                                    // 'class' => 'el-image',
                                    'alt' => !empty($model->title) ? $model->title : AmosNews::t('amosnews', 'immagine')
                                ]
                            );
                        ?>
                    </div>
                </div>
            </div>
<!--              <span class="it-griditem-text-wrapper">
                <span class="it-griditem-text">Didascalia/title</span>
            </span>
-->     </a>
    </div>
    <!-- Modal -->
    <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" id="<?= $modalId ?>">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header pt-1 pr-1">
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span class="mdi mdi-close"></span>
                    </button>
                </div>
                <div class="modal-body pt-0">
                    <div class="img-responsive-wrapper">
                        <div class="img-responsive">
                            <div class="img-wrapper">
                                <?= $imgHtml ?>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>

</div>

