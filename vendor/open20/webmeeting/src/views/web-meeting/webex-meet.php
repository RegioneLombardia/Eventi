<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\webmeeting
 * @category   WebMeeting View
 */
use open20\webmeeting\assets\WebMeetingAssets;
use open20\webmeeting\WebMeetingModule;
use open20\amos\core\forms\ActiveForm;
use yii\helpers\Html;

WebMeetingAssets::register($this);

$this->title = strip_tags($model->title);
$this->params['breadcrumbs'][] = ['label' => '', 'url' => ['/web-meeting']];
$this->params['breadcrumbs'][] = ['label' => 'WebMeeting', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf8">
        <title>Space Widget Demo</title>
        <script src="https://code.s4d.io/widget-space/production/bundle.js"></script>
        <link rel="stylesheet" href="https://code.s4d.io/widget-space/production/main.css">
    </head>
    <body>
        <div id="my-webexteams-widget" style="width: 100%; height: 500px;"/>
        <script>
            var widgetEl = document.getElementById('my-webexteams-widget');
            // Init a new widget
            webex.widget(widgetEl).spaceWidget({
                accessToken: '<?= $access_token['access_token'] ?>',
                destinationId: '<?= $model->sip_address ?>',
                destinationType: 'sip'
            });
        </script>
    </body>
</html>