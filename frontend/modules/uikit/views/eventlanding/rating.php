<?php
/**
 * @var $model \open20\amos\events\models\Event
 * @var $eventLanding \open20\amos\events\models\EventLanding
 * @var $dataProvider \yii\data\ActiveDataProvider
 */

use open20\amos\events\models\EventLandingRating;
use backend\modules\eventsadmin\utility\EventsAdminUtility;

$eventId = $eventLanding->event_id;
$enableRating = $eventLanding->enable_rating;

?>
<?php
if ($enableRating) {

    ?>
    <div id="layoutsection-fd5" class="uk-section-default uk-visible@xl uk-section">

        <div class="uk-container">

            <?php


            $ratingModel = EventLandingRating::instance();
            $ratingTitle = $eventLanding->rating_title;
            $ratingDescription = $eventLanding->rating_description;
            $platformUrl = Yii::$app->params['platform']['frontendUrl'];
            $userId = null;
            if (!Yii::$app->user->isGuest) {
                $userId = Yii::$app->user->id;
            }
            $userIP = \Yii::$app->request->userIP;

            $js = <<<JS

        $('#rating-event').on('rating:change', function() {
            var val = $(this).val();
            var platformUrl = '{$platformUrl}';
            var eventId = '{$eventId}';
            var userId = '{$userId}';
            var userIP = '{$userIP}';
            $.ajax({
               url: platformUrl+'/events/event/rate-event',
               type: 'GET',
               crossOrigin: true,
               // dataType: 'json',
               data: {
                   id: eventId,
                   rating: val,
                   user_id: userId,
                   user_ip: userIP
               },
               success: function (data) { 
                   $('.vote-success').fadeIn();
               }
            })
        });

JS;

            $this->registerJs($js); ?>
            <?php $form = \open20\amos\core\forms\ActiveForm::begin() ?>

            <div class="rating-container">
                <h2><?= $ratingTitle ?></h2>
                <p class="lead"><?= $ratingDescription ?></p>
                <?php
                $labelStars = \Yii::t('site', 'stelle');
                $labelStar = \Yii::t('site', 'stella');
                if (!Yii::$app->user->isGuest) {
                    $userVote = EventLandingRating::findOne(['event_id' => $eventId, 'user_id' => Yii::$app->user->id]);
                    echo $form->field($ratingModel, 'rating')->widget(\kartik\widgets\StarRating::className(), [
                        //'pluginEvents' => [],
                        'bsVersion' => '4',
                        'disabled' => !empty($userVote),
                        'pluginOptions' => [
                            'theme' => 'krajee-uni',
                            'filledStar' => '&#x2605;',
                            'emptyStar' => '&#x2606;',
                            'step' => 1,
                            //'showCaption' => false,
                            'showClear' => false,
                            'starCaptions' => [
                                1 => '1 ' . $labelStar,
                                2 => '2 ' . $labelStars,
                                3 => '3 ' . $labelStars,
                                4 => '4 ' . $labelStars,
                                5 => '5 ' . $labelStars
                            ],
                            'starTitles' => [
                                1 => '1 ' . $labelStar,
                                2 => '2 ' . $labelStars,
                                3 => '3 ' . $labelStars,
                                4 => '4 ' . $labelStars,
                                5 => '5 ' . $labelStars
                            ],
                            'starCaptionClasses' => []
                        ],
                        'options' => [
                            'id' => 'rating-event',
                            'value' => !empty($userVote) ? $userVote->rating : null
                        ]
                    ])->label(false);
                } else {
                    $guestVote = null;
                    if (\open20\amos\events\models\Event::hasVoted($eventId)) {
                        $cookies = \Yii::$app->request->cookies;
                        $cookieValue = $cookies->getValue('review_event');
                        $arrayCookieValues = json_decode($cookieValue);
                        $guestVote = EventLandingRating::findOne($arrayCookieValues->$eventId)->rating;
                    }

                    echo $form->field($ratingModel, 'rating')->widget(\kartik\widgets\StarRating::className(), [
                        //'pluginEvents' => [],
                        'bsVersion' => '4',
                        'disabled' => !is_null($guestVote),
                        'pluginOptions' => [
                            'theme' => 'krajee-uni',
                            'filledStar' => '&#x2605;',
                            'emptyStar' => '&#x2606;',
                            'step' => 1,
                            //'showCaption' => false,
                            'showClear' => false,
                            'starCaptions' => [
                                1 => '1 ' . $labelStar,
                                2 => '2 ' . $labelStars,
                                3 => '3 ' . $labelStars,
                                4 => '4 ' . $labelStars,
                                5 => '5 ' . $labelStars
                            ],
                            'starTitles' => [
                                1 => '1 ' . $labelStar,
                                2 => '2 ' . $labelStars,
                                3 => '3 ' . $labelStars,
                                4 => '4 ' . $labelStars,
                                5 => '5 ' . $labelStars
                            ],
                            'starCaptionClasses' => []
                        ],
                        'options' => [
                            'id' => 'rating-event',
                            'value' => $guestVote
                        ]
                    ])->label(false);
                } ?>

            </div>

            <?php $form = \open20\amos\core\forms\ActiveForm::end() ?>

            <div class="vote-success" style="display: none">
                <?= \Yii::t('site', 'Ti ringraziamo per avere espresso la tua opinione') ?>
            </div>

            <?php if ($userVote || $guestVote) { ?>
                <div class="already-voted">
                    <?= \Yii::t('site', 'La tua opinione risulta già espressa in precedenza') ?>
                </div>
            <?php }
            ?>

        </div>
    </div>
    <?php
}
?>