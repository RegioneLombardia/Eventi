<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\webmeeting
 * @category   WebMeeting View
 */

use open20\webmeeting\WebMeetingModule;
use yii\helpers\Html;

$appLink = Yii::$app->urlManager->createAbsoluteUrl(['/']);
$appLinkPrivacy = Yii::$app->urlManager->createAbsoluteUrl(['/admin/user-profile/privacy']);
$appName = Yii::$app->name;

$this->title = WebMeetingModule::_t('#webmeeting_invite', [
    'webexName' => $webexName
]);
$this->registerCssFile('http://fonts.googleapis.com/css?family=Roboto');

list($begin_date, $begin_hours) = explode (" ", $begin_date);
list($end_date, $end_hours) = explode (" ", $end_date);
?>

<table style="line-height: 18px;" width=" 600" border="0" cellpadding="0" cellspacing="0" align="center">
    <tr>
        <td>
            <div class="corpo"style="padding:10px;margin-bottom:10px;background-color:#ffffff;">
                <div class="sezione" style="overflow:hidden;color:#000000;">
                    <div class="testo">
                        <p style="margin-bottom: 20px;">
                            <span style="font-weight: bold;">
                                <?= WebMeetingModule::_tHtml('#webex_invitation_dear', [
                                    'display_name' => $invitee['display_name'],
                                ]); ?>
                            </span>
                        </p>

                        <p>
                            <?= WebMeetingModule::_tHtml('#webex_invitation_email', [
                                'title' => $title,
                                'web_link' => $web_link,
                                'begin_date' => $begin_date,
                                'begin_hours' => $begin_hours,
                                'end_date' => $end_date,
                                'end_hours' => $end_hours
                            ])?>
                        </p>
    
                        <?php if ($agenda) : ?>
                        <p>
                            <?= WebMeetingModule::_tHtml('#webex_agenda_email', [
                                'agenda' => $agenda,
                            ])?>
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </td>
    </tr>
</table>
