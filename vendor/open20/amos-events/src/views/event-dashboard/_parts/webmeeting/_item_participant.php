<?php

use yii\helpers\Html;

?>
<div class="item-participant">
    <?php if (!$readOnly) { ?>
        <?php if ($participants) { ?>
            <?= \yii\helpers\Html::a(\open20\amos\core\icons\AmosIcons::show('close rounded-icon rounded-danger text-white'), 'javascript:void(0)', ['class' => 'delete-users btn btn-xs btn-icon', 'data-key' => $model->user_id]) ?>
        <?php } else { ?>
            <?php
            $value = false;
            if (in_array($model->user_id, $participants_user_ids)) {
                $value = true;
            } ?>
            <?= \yii\helpers\Html::checkbox('selected-users', $value, ['value' => $model->user_id, 'class' => 'checkbox-user']) ?>
        <?php } ?>
    <?php } ?>
    <div class="avatar-wrapper avatar-extra-text" data-key="<?= $model->user_id ?>">
        <div class="avatar size-xl">
            <?php echo \open20\amos\admin\widgets\UserCardWidget::widget(['model' => $model, 'avatarDimension' => 'table_small', 'avatarXS' => true]); ?>
        </div>
        <div class="extra-text">
            <h4><?= Html::a($model->cognome . ' ' . $model->nome, ['/admin/user-profile/view', 'id' => $model->id], ['title' => 'Visualizza profilo di ' . $model->cognome . ' ' . $model->nome, 'target' => '_blank']) ?></h4>
        </div>
    </div>
</div>