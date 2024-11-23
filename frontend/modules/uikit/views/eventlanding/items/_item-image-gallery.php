<?php

use app\modules\cms\helpers\CmsHelper;
use open20\amos\news\AmosNews;
use open20\amos\events\utility\EventsUtility;


$truncate = 250;
?>


<div class="item-image">
<?php
                    $url = \Yii::$app->params['platform']['backendUrl'] . '/img/img_default.jpg';
                    if (!empty($model->fileImage)) {
                        $url =  $model->fileImage->getWebUrl(EventsUtility::getImageCrops('big'), false, true);
                    }

                    $contentImage = CmsHelper::img(
                        $url,
                        [
                            'class' => 'el-image',
                            'alt' => AmosNews::t('amosnews', 'immagine')
                        ]
                    );

                    $modalId = 'gallery-modal-' . $model->id . '-id';
                    echo CmsHelper::a($contentImage, 'javaScript: void(0);', ['onclick' => 'javascript:$("#' . $modalId . '").modal("toggle");']);

                    ?>
</div>
<!-- Modal -->
<div class="modal fade bd-example-modal-lg" role="dialog" id="<?= $modalId ?>">
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
                            <?= $contentImage ?>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>