<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\invitations\widgets
 * @category   CategoryName
 */

namespace open20\amos\invitations\widgets;

use open20\amos\core\helpers\Html;
use open20\amos\invitations\models\Invitation;
use open20\amos\invitations\models\InvitationForm;
use open20\amos\invitations\models\InvitationUser;
use open20\amos\invitations\Module;
use yii\helpers\ArrayHelper;

/**
 * Class InviteUserWidget
 * @package open20\amos\invitations\widgets
 */
class InviteUserWidget extends \yii\base\Widget
{

    public $btnOptions = [];

    public $btnLabel;

    public $layout = '{invitationBtn}{invitationModalForm}';

    public function run()
    {
        $content = preg_replace_callback("/{\\w+}/", function ($matches) {
            $content = $this->renderSection($matches[0]);

            return $content === false ? $matches[0] : $content;
        }, $this->layout);

        return $content;
    }

    /**
     * @inheritdoc
     */
    public function renderSection($name)
    {
        switch ($name) {
            case '{invitationBtn}':
                return $this->renderInvitationBtn();
            case '{invitationModalForm}':
                return $this->renderInvitationModalForm();
            default:
                return false;
        }
    }

    public function renderInvitationBtn()
    {
        if(is_null($this->btnLabel)){
            $this->btnLabel = Module::t('amosinvitations', '#new_invitation_btn');
        }
        $btnOptions = ArrayHelper::merge( [
            'class' => 'btn btn-administration-primary',
            'data-target' => '#invite-new-user-modal',
            'data-toggle' => 'modal'
        ], $this->btnOptions);

        $btn = Html::a($this->btnLabel,
            null,
            $btnOptions
        ) ;

        return $btn;

    }

    public function renderInvitationModalForm()
    {
         return $this->render('invite-user', ['invitation' => new Invitation(), 'invitationUser' => new InvitationUser()]);
    }


}