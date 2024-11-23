<?php
/**
 * @var $model \open20\amos\events\models\Event
 * @var $eventLanding \open20\amos\events\models\EventLanding
 * @var $dataProvider \yii\data\ActiveDataProvider
 */


?>

<?php
if ($eventLanding && !empty($eventLanding->contact_info_organizator)) { ?>
    <div id="contact_info_organizator-container">
        <meta name="csrf-param" content="_csrf">


        <?php
        $title = \open20\amos\events\models\EventLanding::defaultLabelsTitleSections('title_request_info');
        if ($eventLanding && !empty($eventLanding->title_request_info)) {
            $title = $eventLanding->title_request_info;
        }
        ?>
        <div class="uk-container uk-margin-large-top uk-margin-large-bottom uk-padding-top section-informazioni">
            <h2 class="uk-h1"><strong><?= $title ?></strong></h2>
            <p><?= $eventLanding->contact_info_organizator ?></p>
        </div>

    </div>
<?php } ?>
