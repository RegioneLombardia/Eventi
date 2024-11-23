<?php

use yii\widgets\ListView;

use yii\data\ActiveDataProvider;
use open20\amos\events\models\EventLanding;

/**
 * @var PreferenceLanding $landing
 * @var string $cssClass
 * @var string $templateLanguage
 * @var string $detailPage
 * @var array $viewFields
 * @var int $blockItemId
 * @var ActiveDataProvider $dataProvider
 */

$queryParams = [];
$model = null;
$landing = null;

$model = \app\modules\cms\utility\CachedEvent::getModelEvent($parms);
$landing = \app\modules\cms\utility\CachedEvent::getModelLanding();

// se esiste qualcosa da visualizzare allora...
if (!is_null($dataProvider) && ($dataProvider->getTotalCount() > 0)) {
    $js = <<<JS
    $('.section-gallery').attr('style',"display:block !important");
JS;
    $this->registerJs($js);
    $title = \open20\amos\events\models\EventLanding::defaultLabelsTitleSections('title_pics');
    if ($landing && !empty($landing->title_pics)) {
        $title = $landing->title_pics;
    }
    /** @var int $layoutId */
// $layoutId = (!is_null($landing)) ? $landing->gallery_type : null;

    $layoutId = (!is_null($landing)) ? $landing->gallery_type : null;
    ?>

    <div class="<?= $cssClass ?>">

        <div class="uk-container py-5 border-light border-top">
            <h3 class="h1">
                <?= $title ?>
            </h3>
        </div>
        <?php $dataProvider->setPagination(false);
        switch ($layoutId) {

            case EventLanding::GALLERY_TYPE_CAROUSEL:
                $tot = count($dataProvider->models);
                ?>
                <div class="uk-container">


                    <div uk-slider class="uk-margin-top slider-carousel">

                        <div class="uk-position-relative">

                            <div class="uk-slider-container uk-light">
                                <ul class="uk-slider-items uk-child-width-1-1 uk-child-width-1-2@s  uk-child-width-1-3@m uk-grid-match uk-grid"
                                    uk-height-viewport="offset-top: true; offset-bottom: 80">
                                    <?php
                                    /** @var \amos\sitemanagement\models\SiteManagementSliderElem $model */
                                    foreach ($dataProvider->models as $model) {
                                        /** @var \open20\amos\attachments\models\File $fileImage */
                                        $fileImage = $model->fileImage;
                                        $urlImage = $fileImage->getWebUrl('social', false, true);
                                        ?>
                                        <li>
                                            <div class="uk-cover-container">
                                                <img src="<?= $urlImage ?>" uk-cover>
                                            </div>
                                        </li>
                                        <?php
                                    } ?>
                                </ul>
                            </div>

                            <div class="uk-hidden@s uk-light">
                                <a class="uk-position-center-left uk-position-small" href="#" uk-slidenav-previous
                                   uk-slider-item="previous"></a>
                                <a class="uk-position-center-right uk-position-small" href="#" uk-slidenav-next
                                   uk-slider-item="next"></a>
                            </div>

                            <div class="uk-visible@s uk-light">
                                <a class="uk-position-center-left uk-position-small uk-hidden-hover" href="#"
                                   uk-slidenav-previous uk-slider-item="previous"></a>
                                <a class="uk-position-center-right uk-position-small uk-hidden-hover" href="#"
                                   uk-slidenav-next uk-slider-item="next"></a>
                            </div>

                        </div>

                        <ul class="uk-slider-nav uk-dotnav uk-flex-center uk-margin"></ul>

                    </div>
                </div>

                <?php
                break; // FINE LAYOUT_CAROUSEL

            case EventLanding::GALLERY_TYPE_CAROUSEL_ZOOM:
                ?>

                <div class="uk-position-relative uk-visible-toggle slider-carousel-zoom" tabindex="-1"
                     uk-slideshow="min-height: 300; max-height: 600;">

                    <ul class="uk-slideshow-items">
                        <?php

                        /** @var \amos\sitemanagement\models\SiteManagementSliderElem $model */
                        foreach ($dataProvider->models as $model) {
                            /** @var \open20\amos\attachments\models\File $fileImage */
                            $fileImage = $model->fileImage;
                            $urlImage = $fileImage->getWebUrl('social', false, true);
                            ?>
                            <li>
                                <img src="<?= $urlImage ?>" uk-cover>
                            </li>
                            <?php
                        }
                        ?>
                    </ul>
                    <div uk-slider class="uk-margin-top slider-carousel">

                        <div class="uk-position-relative">

                            <div class="uk-slider-container uk-light">
                                <ul class="uk-slider-items uk-child-width-1-2 uk-child-width-1-3@s uk-child-width-1-4@m uk-child-width-1-5@l uk-grid-match uk-grid">

                                    <?php
                                    // $totElem = $dataProvider->count();
                                    $i = 0;
                                    /** @var \amos\sitemanagement\models\SiteManagementSliderElem $model */
                                    foreach ($dataProvider->models as $model) {
                                        /** @var \open20\amos\attachments\models\File $fileImage */
                                        $fileImage = $model->fileImage;
                                        $urlImageThumb = $fileImage->getWebUrl('thumb', false, true);
                                        ?>
                                        <li uk-slideshow-item="<?= $i ?>">
                                            <div class="uk-cover-container">
                                                <a href="">
                                                    <img src="<?= $urlImageThumb ?>" alt="">
                                                </a>
                                            </div>
                                        </li>

                                        <?php
                                        $i++;
                                    }
                                    ?>
                                </ul>
                            </div>

                            <div class="uk-hidden@s uk-light">
                                <a class="uk-position-center-left uk-position-small" href="#" uk-slidenav-previous
                                   uk-slider-item="previous"></a>
                                <a class="uk-position-center-right uk-position-small" href="#" uk-slidenav-next
                                   uk-slider-item="next"></a>
                            </div>

                            <div class="uk-visible@s uk-light">
                                <a class="uk-position-center-left uk-position-small uk-hidden-hover" href="#"
                                   uk-slidenav-previous uk-slider-item="previous"></a>
                                <a class="uk-position-center-right uk-position-small uk-hidden-hover" href="#"
                                   uk-slidenav-next uk-slider-item="next"></a>
                            </div>

                        </div>

                        <ul class="uk-slider-nav uk-dotnav uk-flex-center uk-margin"></ul>

                    </div>

                </div>


                <?php
                break; // FINE LAYOUT_CAROUSEL_ZOOM

            case EventLanding::GALLERY_TYPE_MASONRY:
                ?>
                <div class="it-grid-list-wrapper it-image-label-grid it-masonry pb-5 mb-5">
                    <?php
                    $dataProvider->setPagination(false);
                    echo ListView::widget([
                        'id' => 'list-gallery-masonry',
                        'summary' => false,
                        'dataProvider' => $dataProvider,
                        'itemView' => '_itemImageMasonry',
                        'viewParams' => [
                            'detailPage' => $detailPage,
                            'viewFields' => $viewFields,
                            'blockItemId' => $blockItemId,
                        ],
                        'options' => [
                            'class' => 'card-columns',
                        ],
                    ]);
                    ?>
                </div>
                <?php
                break; // FINE LAYOUT_MASONRY

            case EventLanding::GALLERY_TYPE_IMAGE:
                ?>
                <div class="it-grid-list-wrapper it-image-label-grid it-image pb-5 mb-5">
                    <?php
                    echo ListView::widget([
                        'id' => 'list-gallery-single',
                        'summary' => false,
                        'dataProvider' => $dataProvider,
                        'itemView' => '_itemSingleImageGallery',
                        'viewParams' => [
                            'detailPage' => $detailPage,
                            'viewFields' => $viewFields,
                            'blockItemId' => $blockItemId,
                        ],
                        'options' => [
                            'class' => 'single-image',
                        ],
                    ]);
                    ?>
                </div>
                <?php
                break; // FINE LAYOUT_IMAGE

            // PreferenceLandingLayoutImages::LAYOUT_GRID:
            default:
                // INIZIO LAYOUT GRID
                ?>

                <div class="content-gallery uk-container it-grid-list-wrapper pb-5 mb-5 uk-margin-top">

                    <?php
                    //GRID
                    echo ListView::widget([
                        'id' => 'list-gallery',
                        'summary' => false,
                        //                'pager' => [
                        //                    'maxButtonCount'=>0,
                        //                ],
                        'dataProvider' => $dataProvider,
                        'itemView' => '_itemImageGallery',
                        'viewParams' => [
                            'detailPage' => $detailPage,
                            'viewFields' => $viewFields,
                            'blockItemId' => $blockItemId,
                        ],
                        'options' => [
                            // 'uk-grid' => 'masonry: true',
                            'class' => 'list-view uk-child-width-1-2@s uk-child-width-1-3@m uk-grid-medium',
                        ],
                    ]);


                    ?>
                </div>

                <?php
                break; // FINE LAYOUT GRID
        } ?>
    </div>
<?php } else {
    // se non esiste nulla da visualizzare allora in JS viene nascosta la sezione
    $js = "
           // $('.section-gallery').attr('style',\"display:none !important\");
           // $('#menu-gallery').remove();
        ";
    $this->registerJs($js);
}
?>


