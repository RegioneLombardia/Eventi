<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\invitations
 * @category   CategoryName
 */

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;

/**
* @var yii\web\View $this
* @var open20\amos\invitations\models\Invitation $model
*/

$this->title =  Yii::t('amosinvitations', 'Invitation addressed to {nameSurname}', ['nameSurname' => $model->getNameSurname()]);
?>
<div class="invitation-view col-xs-12">

    <?php
    try {
        echo DetailView::widget([
            'model' => $model,
            'attributes' => [
                'name',
                'surname',
                'message:html',
                'send_time:datetime',
                'send:boolean',
                'invitationUser.email',
                'invitationUser.numberNotificationSended',
                'invitationUser.numberNotificationSendedByMe',
                'created_at:datetime',
            ],
        ]);
    } catch (Exception $e) {
        // pr($e->getMessage());
    } ?>

    <div class="btnViewContainer pull-right">
        <?= Html::a(Yii::t('amosinvitations', 'Chiudi'), Url::previous(), ['class' => 'btn btn-secondary']); ?>
    </div>

</div>
