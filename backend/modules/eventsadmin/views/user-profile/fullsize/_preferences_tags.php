<?php

use yii\helpers\ArrayHelper;
use open20\amos\events\utility\EventsUtility;

?>


<?php
$preferenceCenterTags = EventsUtility::getPreferenceCenterTags();
$preferencesTags = \backend\modules\eventsadmin\controllers\UserProfileController::loadTagPreferences($model);
?>
<h3><?= \Yii::t('app', "Tag di interesse informativo") ?></h3>
<?= \yii\helpers\Html::checkboxList('preferencesTags', $preferencesTags, EventsUtility::getLabelPreference(), ['encode' => false]); ?>
<span class="tooltip-error-field">
    <span id="error_preference_tag" class="help-block help-block-error"></span>
</span>