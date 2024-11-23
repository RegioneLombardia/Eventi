<?php
/**
 * @var $model \open20\amos\events\models\Event
 * @var $eventLanding \open20\amos\events\models\EventLanding
 * @var $configsCache Array
 * @var $enabledCache bool
 */

use open20\amos\events\models\Event;


$currentUrl = \Yii::$app->menu->current->alias;
$this->registerJsVar('configSections', Event::getConfigLandingSections());
$this->registerJsVar('event_id', $model->id);
$this->registerJsVar('language', \Yii::$app->language);
$this->registerJsVar('currentUrl', $currentUrl);
$this->registerJsVar('enableCache', $enabledCache);


$js = <<<JS
        $.each(configSections, function(section, config){
            var containerId = config.container_id;
            if(config.ajax){
                 $.ajax({
                          url: '/events/event-ajax/render-event-section-v2',
                          type: 'get',
                          data: {
                               event_id: event_id,
                               section: section,
                               language: language,
                               currentUrl: currentUrl,
                               enableCache: enableCache
                          },
                          success: function (data) {
                             $('#'+containerId).html(data);
                         }
                 });
            }
        });
      
JS;
$this->registerJs($js);

$modelSearch = new \open20\amos\events\models\search\EventSearch();
$params = ['conditionSearch' => "['event_id' => {$model->id}]"];


echo $this->render('eventlanding/sort-sections', [
    'model' => $model,
    'eventLanding' => $eventLanding
])
?>

<!-- CHANGE LANGUAGE-->
<?php
echo $this->render('eventlanding/change_language', [
    'model' => $model,
    'eventLanding' => $eventLanding
])
?>

<!-- HERO BANNER + STREAMING-->
<div id="<?= Event::getConfigsAttrSingleSection('hero-banner', 'container_id') ?>">
    <?php if (!Event::getConfigsAttrSingleSection('hero-banner', 'ajax')) { ?>
        <?php
        echo $this->render('eventlanding/hero-banner', [
            'model' => $model,
            'eventLanding' => $eventLanding
        ])
        ?>
    <?php } ?>
</div>


<!-- SECTION LOCATION MAP -->
<div id="<?= Event::getConfigsAttrSingleSection('location', 'container_id') ?>">
    <?php
    $dataProviderMap = $modelSearch->cmsMarkerMapSearch($params);
    echo $this->render('eventlanding/locationMap', [
        'model' => $model,
        'dataProvider' => $dataProviderMap
    ])
    ?>
</div>

<?php if ($this->beginCache('static_blocks2_' . $model->id, $configsCache)) { ?>
    <!-- SECTION PRESENTATION-->
    <div id="<?= Event::getConfigsAttrSingleSection('presentation', 'container_id') ?>">
        <?php
        echo $this->render('eventlanding/presentation', [
            'model' => $model,
        ])
        ?>
    </div>

    <!-- SECTION PROGRAMMA -->
    <div id="<?= Event::getConfigsAttrSingleSection('schedule', 'container_id') ?>">
        <?php
        echo $this->render('eventlanding/schedule', [
            'model' => $model,
        ])
        ?>
    </div>
    <?php
    $this->endCache();
}
?>

<!-- SECTION PROTAGONISTI -->
<div id="<?= Event::getConfigsAttrSingleSection('protagonists', 'container_id') ?>">
    <?php if (!Event::getConfigsAttrSingleSection('protagonists', 'ajax')) { ?>
        <?php
        /** @var  $dataProviderProtagonists \yii\data\ActiveDataProvider */
        $dataProviderProtagonists = $modelSearch->cmsSearchProtagonists($params);
        if ($dataProviderProtagonists->totalCount > 0) {
            echo $this->render('eventlanding/protagonists', [
                'model' => $model,
                'dataProvider' => $dataProviderProtagonists
            ]);
        }
        ?>
    <?php } ?>
</div>

<!-- SECTION NEWS -->
<div id="<?= Event::getConfigsAttrSingleSection('news', 'container_id') ?>">
    <?php if (!Event::getConfigsAttrSingleSection('news', 'ajax')) { ?>
        <?php
        $dataProviderNews = $modelSearch->cmsSearchNews($params);
        if ($dataProviderNews->totalCount > 0) {
            echo $this->render('eventlanding/news', [
                'model' => $model,
                'eventLanding' => $eventLanding,
                'dataProvider' => $dataProviderNews
            ]);
        }
        ?>
    <?php } ?>
</div>

<!-- SECTION DOCUMENTI -->
<div id="<?= Event::getConfigsAttrSingleSection('documents', 'container_id') ?>">
    <?php if (!Event::getConfigsAttrSingleSection('documents', 'ajax')) { ?>

        <?php
        $dataProviderDocuments = $modelSearch->cmsSearchDocuments($params);
        if ($dataProviderDocuments->totalCount > 0) {
            echo $this->render('eventlanding/documents', [
                'model' => $model,
                'dataProvider' => $dataProviderDocuments
            ]);
        }
        ?>
    <?php } ?>
</div>

<!-- SECTION IMAGE GALLERY -->
<div id="<?= Event::getConfigsAttrSingleSection('image-gallery', 'container_id') ?>">
    <?php if (!Event::getConfigsAttrSingleSection('image-gallery', 'ajax')) { ?>

        <?php
        $dataProviderGallery = $modelSearch->cmsSearchGallery($params);
        if ($dataProviderGallery->totalCount > 0) {
            echo $this->render('eventlanding/image-gallery', [
                'model' => $model,
                'eventLanding' => $eventLanding,
                'dataProvider' => $dataProviderGallery
            ]);
        }
        ?>
    <?php } ?>
</div>

<!-- SECTION VIDEO GALLERY -->
<div id="<?= Event::getConfigsAttrSingleSection('video-gallery', 'container_id') ?>">
    <?php if (!Event::getConfigsAttrSingleSection('video-gallery', 'ajax')) { ?>

        <?php
        $dataProviderVideo = $modelSearch->cmsSearchVideo($params);
        $totalCount = $dataProviderVideo->totalCount;
        if ($totalCount > 0) {
            echo $this->render('eventlanding/video-gallery', [
                'model' => $model,
                'dataProvider' => $dataProviderVideo,
                'eventLanding' => $eventLanding,
                'totalCount' => $totalCount

            ]);
        }
        ?>
    <?php } ?>
</div>


<!-- SECTION VIDEO INSTAGRAM -->
<div id="<?= Event::getConfigsAttrSingleSection('instagram', 'container_id') ?>">
    <?php if (!Event::getConfigsAttrSingleSection('instagram', 'ajax')) { ?>

        <?php
        $dataProviderInstgramVideo = $modelSearch->cmsSearchInstagramVideo($params);
        $totalCount = $dataProviderInstgramVideo->totalCount;
        if ($totalCount > 0) {
            echo $this->render('eventlanding/instagram', [
                'model' => $model,
                'dataProvider' => $dataProviderInstgramVideo,
                'eventLanding' => $eventLanding,
            ]);
        }
        ?>
    <?php } ?>
</div>

<!-- SECTION EVENTI CORRELATI  -->
<div id="<?= Event::getConfigsAttrSingleSection('related_events', 'container_id') ?>">
    <?php if (!Event::getConfigsAttrSingleSection('related_events', 'ajax')) { ?>

        <?php
        $dataProviderRelatedEvents = $modelSearch->cmsSearchRelatedEvents($params);
        $totalCount = $dataProviderRelatedEvents->totalCount;
        if ($totalCount > 0) {
            echo $this->render('eventlanding/related-events', [
                'model' => $model,
                'dataProvider' => $dataProviderRelatedEvents,
                'eventLanding' => $eventLanding,
            ]);
        }
        ?>
    <?php } ?>
</div>

<!-- SECTION RATING  -->
<div id="<?= Event::getConfigsAttrSingleSection('rating', 'container_id') ?>">
    <?php if (!Event::getConfigsAttrSingleSection('rating', 'ajax')) { ?>

        <?php
        echo $this->render('eventlanding/rating', [
            'model' => $model,
            'eventLanding' => $eventLanding,
        ]);
        ?>
    <?php } ?>
</div>

<!-- SECTION REQUEST INFO  -->
<div id="<?= Event::getConfigsAttrSingleSection('request_info', 'container_id') ?>">
    <?php if (!Event::getConfigsAttrSingleSection('request_info', 'ajax')) { ?>

        <?php
        echo $this->render('eventlanding/request-info', [
            'model' => $model,
            'eventLanding' => $eventLanding,
        ]);
        ?>
    <?php } ?>

</div>


<!-- SECTION SHARE  -->
<div id="<?= Event::getConfigsAttrSingleSection('share', 'container_id') ?>">
    <?php if (!Event::getConfigsAttrSingleSection('share', 'ajax')) { ?>

        <?php
        echo $this->render('eventlanding/share', [
            'model' => $model,
            'eventLanding' => $eventLanding,
        ]);
        ?>
    <?php } ?>

</div>

<!-- SECTION EVENTI FIGLI  -->
<div id="<?= Event::getConfigsAttrSingleSection('children_events', 'container_id') ?>">
    <?php if (!Event::getConfigsAttrSingleSection('children_events', 'ajax')) { ?>

        <?php
        $dataProviderChildrenEvents = $modelSearch->cmsChildrenSearch($params);
        $totalCount = $dataProviderChildrenEvents->totalCount;
        if ($totalCount > 0) {
            echo $this->render('eventlanding/children-events', [
                'model' => $model,
                'eventLanding' => $eventLanding,
                'dataProvider' => $dataProviderChildrenEvents
            ]);
        }
        ?>
    <?php } ?>

</div>


<!-- SECTION EVENTI FIGLI  -->
<div id="<?= Event::getConfigsAttrSingleSection('time-period', 'container_id') ?>">
    <?php if (!Event::getConfigsAttrSingleSection('time-period', 'ajax')) { ?>

        <?php
        $dataProviderTimePeriods = $modelSearch->cmsTimePeriodsSearch($params);
        $totalCount = $dataProviderTimePeriods->totalCount;
        if (!is_null($dataProviderTimePeriods) && $totalCount > 0) {
            echo $this->render('eventlanding/time-period', [
                'model' => $model,
                'eventLanding' => $eventLanding,
                'dataProvider' => $dataProviderTimePeriods
            ]);
        }
        ?>
    <?php } ?>
</div>







