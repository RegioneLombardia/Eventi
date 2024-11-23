<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\invitations
 * @category   CategoryName
 */

use open20\amos\invitations\models\Invitation;
use open20\amos\invitations\models\InvitationUser;
use yii\web\View;

/**
 * @var View $this
 * @var Invitation $invitation
 * @var InvitationUser $invitationUser
 */

$this->title = Yii::t('amosinvitations', '#send-invitation-titile');
?>
<div class="invitation-create">
<?= $this->render(
    '_form', 
    [
        'invitation' => $invitation,
        'invitationUser' => $invitationUser,
    ]) 
?>
</div>
