<?php
/**
 * @var $model \open20\amos\events\models\Event
 * @var $eventLanding \open20\amos\events\models\EventLanding
 */

use yii\widgets\ListView;
use open20\amos\layout\assets\IsotopeAsset;

?>
<div id="layoutsection-05a"
     class="section-news news-section news-section__homepage green-blu-news uk-section-default uk-visible@xl uk-section"
     style="display:block !important">
    <div class="uk-container">
        <div class="uk-container uk-grid-margin">
            <div class="vertical-title uk-grid uk-grid-stack" uk-grid="">
                <div id="aa2" class="uk-width-1-1@m uk-first-column">
                    <div id="listNewsHp-container">
                        <div class="">
                            <div class="clearfix"></div>
                            <div class="slider-news-home">
                                <?php
                                $title =  \open20\amos\events\models\EventLanding::defaultLabelsTitleSections('title_info');
                                if($eventLanding && !empty($eventLanding->title_info)){
                                    $title = $eventLanding->title_info;
                                }
                                ?>
                                <h2><?= $title ?></h2>

                                <div class="uk-position-relative uk-visible-toggle uk-light" tabindex="-1" uk-slider>
                                    <?php
                                    echo ListView::widget([
                                        'summary' => false,
                                        'dataProvider' => $dataProvider,
                                        'itemView' => '@frontend/modules/uikit/views/eventlanding/items/_item-news',
                                        'options' => [
                                            'class' => 'uk-slider-items uk-grid',
                                        ]
                                    ]);
                                    ?>

                                    <div class="uk-flex uk-flex-middle uk-margin-top">
                                        <div class="uk-slidenav-container uk-margin-right">
                                            <a href="#" uk-slidenav-previous uk-slider-item="previous"></a>
                                            <a href="#" uk-slidenav-next uk-slider-item="next"></a>
                                        </div>
                                        <ul class="uk-slider-nav uk-dotnav uk-flex-center uk-margin"></ul>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>
