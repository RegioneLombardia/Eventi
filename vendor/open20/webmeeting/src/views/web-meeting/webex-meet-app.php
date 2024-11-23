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

if (!empty($model) && (!empty($model->web_link))) :
    $form = ActiveForm::begin([
        'options' => [
            'id' => 'call-controls',
        ]
    ]);
?>
    <script crossorigin src="https://unpkg.com/webex@^1/umd/webex.min.js"></script>

    <div class="col-xs-12 webex-meet-form">
        <div id="meet-status-remote" class="meet-status-remote">
            <div id="loader" class="loader hide"></div>
            <video id="remote-view-video" class="remote-view-video" autoplay playsinline></video>
            <audio id="remote-view-audio" autoplay playsinline></audio>
            <video id="remote-screen" style="position:absolute;" class="remote-screen" muted autoplay playsinline></video>
            <!--modal partecipanti-->
            <div id="partecipants-container" class="partecipants-container not-active">
                <h5><?= WebMeetingModule::_t('#tab_invitees') ?></h5>
                <div id="online-participants"></div>
            </div>
        </div>

        <div id="call-tools" class="down-toolbar">
            <div class="button-container">
                <button id="dial" title="Partecipa" class="btn btn-primary" type="button" onclick="setOnlineStatus()">Partecipa</button>
                <div class="videocall-option">
                    <button id="btn-audio-manager" class="btn disabled" title="Disattiva l'audio" type="button"><span id="mute-audio" class="am am-mic-outline"></span></button>
                    <button id="btn-video-manager" class="btn disabled" title="Disattiva la videocamera" type="button"><span id="mute-video" class="am am-videocam"></span></span></button>
                    <button id="btn-share-screen"class="btn disabled" title="Condividi il tuo schermo" type="button"><span id="share-screen" class="am am-devices-off"></span></button>
                    <button id="stop-screen-share" class="btn btn-outline-primary hide" title="Interrompi la condivisione del tuo schermo" type="button"><span id="stop-screen-share" class="am am-devices"></span></button>
                </div>
            </div>

            <div id="meet-status-local" class="meet-status-local" >
                <video id="self-view" class="video-self-view" muted autoplay playsinline></video>
                <video id="self-screen" class="video-self-screen" muted autoplay playsinline></video>         
            </div>

            <div class="partecipants-button">
                <button id="partecipants" class="btn disabled" type="button" title="Vedi i partecipanti" onclick="getOnlineUsers()">
                    <span class="dash dash-users"></span>
                </button>
            </div>
        </div>

        <input id="access-token" name="accessToken" type="hidden" value="<?= $access_token['access_token'] ?>">
        <input id="sip-address" name="sip-address" size="255" type="hidden" value="<?= $model->sip_address ?>">
    </div>

    <?php ActiveForm::end(); ?>

<?php else: ?>
    <div class="webex-meeting">
        <p><?= WebMeetingModule::_t('#no_webex_meeting_found'); ?></p>
    </div>
<?php endif; ?>

<script language="javascript" type="text/javascript">
    function setOnlineStatus() {
        var status = 1;
        
        if (activeMeeting) {
            status = 0;
        }
        
        $.ajax({
            url: '<?= WebMeetingModule::getSetOnlineLink() ?>',
            type: 'post',
            data: {
                id: '<?= $model->id ?>',
                uid: '<?= $user_id ?>',
                status: status,
            },
            success: function (data) {}
        });
    }
    
    function getOnlineUsers() {
        if (activeMeeting) {
            $.ajax({
            url: '<?= WebMeetingModule::getOnlineUsersLink() ?>',
            type: 'post',
            data: {
                id: '<?= $model->meeting_id ?>',
            },
            success: function (html) {
                $('#online-participants').html(html);
            }
        });
        }
    }
</script>
