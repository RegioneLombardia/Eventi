<?php

use open20\amos\events\AmosEvents;
?>

<?php
if (!empty($dataProvider)) {
    if ($dataProvider->getTotalCount() == 1) {
        $model = \app\modules\cms\utility\CachedEvent::getModelEvent($parms);
        $landing =  \app\modules\cms\utility\CachedEvent::getModelLanding();
        if ($landing && !empty($landing->contact_info_organizator)) { ?>
            <?php
            $title =  \open20\amos\events\models\EventLanding::defaultLabelsTitleSections('title_request_info');
            if($landing && !empty($landing->title_request_info)){
                $title = $landing->title_request_info;
            }
            ?>
            <div class="uk-container uk-margin-large-top uk-margin-large-bottom uk-padding-top section-informazioni">
                <h2 class="uk-h1"><strong><?= $title ?></strong></h2>
                <p><?= $landing->contact_info_organizator ?></p>
            </div>
        <?php }
    }
}

?>

