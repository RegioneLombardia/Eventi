<?php

use app\modules\backendobjects\frontend\Module;
use kartik\datecontrol\DateControl;
use luya\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\ListView;
use open20\amos\events\AmosEvents;

?>
<?php if (!is_null($dataProvider)) {
    if ($dataProvider->getTotalCount() > 0) {
        $js = <<<JS
    $('.section-documenti').attr('style',"display:block !important");
JS;
        $this->registerJs($js);
        $model = \app\modules\cms\utility\CachedEvent::getModelEvent($parms);
        $landing = \app\modules\cms\utility\CachedEvent::getModelLanding();

        $title = \open20\amos\events\models\EventLanding::defaultLabelsTitleSections('title_documents');
        if ($landing && !empty($landing->title_documents)) {
            $title = $landing->title_documents;
        }
        ?>
        <div>
            <div class="uk-container">
                <div class="document-header">

                    <h2><?= $title ?></h2>
                </div>
                <div class="attachments-detail">
                    <div class="uk-child-width-1-3@s" uk-grid uk-height-match="target: > div > .uk-card">
                        <?php foreach ($dataProvider->models as $documento) {
                            $i++;
                            $url = null;
                            /** @var  $documentFile \open20\amos\attachments\models\File */
                            $documentFile = $documento->getDocumentMainFile();
                            if ($documentFile) {
                                $type = $documentFile->type;
                                $url = $documentFile->getWebUrl('square_medium', false, true);


                                ?>
                                <?php
                                $dimScale = ['B', 'Kb', 'Mb', 'Gb', 'Tb'];
                                $sizeFile = intval($documentFile->size);
                                $power = $sizeFile > 0 ? floor(log($sizeFile, 1024)) : 0;
                                $size = number_format($sizeFile / pow(1024, $power), 0, '.', ',') . ' ' . $dimScale[$power];
                                ?>

                                <div>
                                    <div class="event-allegato-info uk-card uk-card-default uk-card-body">
                                        <div class="event-allegato-name">
                                            <strong><?= $documento->titolo ?></strong>
                                        </div>
                                        <div class="event-allegato-kb small">.<?= $type ?> (<?= $size ?>)</div>
                                        <div class="event-allegato-cta">
                                            <a href="<?= $url ?>" class="download-link"
                                               title="<?= AmosEvents::t('amosevents', 'Scarica') ?> <?= $documento->titolo ?>">
                                                <?= AmosEvents::t('amosevents', 'Scarica') ?>
                                                <span class="am am-download"></span>

                                            </a>


                                        </div>
                                    </div>

                                </div>


                            <?php } else if (!empty($documento->link_document)) {
                                $url = $documento->link_document;
                                ?>

                                <li class="attachments-detail__item el-item">
                                    <div class="event-allegato-info">
                                        <div class="event-allegato-name">
                                            <?= \open20\amos\core\utilities\StringUtils::shortText($documento->titolo, 80) ?>
                                        </div>

                                    </div>
                                    <a href="<?= $url ?>" class="icon-download-link" title="Scarica documento">
                                        <span class="am am-download"></span>
                                    </a>
                                </li>


                            <?php } ?>


                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    } else {
        $js = <<<JS
    // $('.section-documenti').attr('style',"display:none !important");
    // $('#menu-documenti').remove();
JS;
        $this->registerJs($js);
    }
}
?>