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

use open20\amos\core\views\DataProviderView;
use open20\amos\core\views\grid\ActionColumn;
use open20\amos\core\utilities\ModalUtility;
use open20\amos\core\utilities\StringUtils;
use open20\amos\core\icons\AmosIcons;


$this->title = WebMeetingModule::_t('#invitees_list');
$this->params['breadcrumbs'][] = [
    'label' => WebMeetingModule::_t('#webmeeting_invitess'),
    'url' => ['join', 'id' => $model->id]
];
$this->params['breadcrumbs'][] = $this->title;

$js = <<<JS
$(".alphabet").click(function() {
    $("ul li.alphabet").removeClass("alphabet-active");
    $(this).addClass("alphabet-active");
});
JS;
$this->registerJs($js, \yii\web\View::POS_READY);

$this->beginBlock('tab_invitees');
$alphabet = [
    'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
    'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
];

?>
<div class="web-meeting-participants user-profile-index">
    <div class="m-t-20 m-b-20">
        <strong>
            <?= WebMeetingModule::_t('#community_users', [
                'community' => '',
                'numUser' => 0,
            ]);
            ?>
        </strong>
    </div>
    <div class="small m-t-20 m-b-20">
        <?= WebMeetingModule::_t('#search_user_hint') ?>
    </div>
    <!-- <div class="alphabet-filter"> -->
        <ul class="alphabet-filter">
            <?php foreach($alphabet as $letter) : ?>
            <li class="alphabet"><?= $letter ?></li>
            <?php endforeach; ?>
        </ul>
    <!-- </div> -->
    <div class="container-cta-partecipants m-t-20 m-b-20">
        <div class="fist-cta-partecipants m-r-10 m-t-5 m-b-5">        
            <span class="small m-r-10"><?= WebMeetingModule::_t('#matched_user_num', [
                'matched' => 0
            ]) ?>, 
            <a class="btn btn-xs btn-outline-tertiary" href="javascript:void(0)" title="><?= WebMeetingModule::_t('#select_all_users') ?>"><span><?= WebMeetingModule::_t('#select_all_users') ?></span></a>
        </div>
        <a class="btn btn-xs btn-primary m-t-5 m-b-5" href="javascript:void(0)" title="Visualizza tutti gli invitati"><span><?= WebMeetingModule::_t('#show_all_invitees', [
            'allInvitees' => 0
        ]) ?></span></a>
    </div>

    <?php
    $columns = [
        'class' => 'open20\amos\core\views\grid\ActionColumn',
        'template' => '{view} {update} {registra}',
        'buttons' => [
            'registra' => function ($url, $model) {
                /** @var \open20\amos\admin\models\UserProfile $model */
                $utente = Yii::$app->getUser();
                if ($utente->can('REGISTRAZIONE_ACCESSI')) {
                    return Html::a(AmosIcons::show(
                            'timer',
                            ['class' => 'btn btn-tool-secondary bk-btnPagine'])
                        . '<span class="sr-only">'
                        . AmosAdmin::t('amosadmin', 'Registra l\'accesso al servizio di facilitazione')
                        . '</span>', Yii::$app->urlManager->createUrl([
                        '/puntopei/pei-accessi-servizi-facilitazione/create',
                        'user_profile_id' => $model->id
                    ]),
                        [
                            'title' => AmosAdmin::t('amosadmin', 'Registra l\'accesso al servizio di facilitazione')
                        ]);
                }
            }            
        ],
    ];

    $dataProviderViewWidgetConf = [
        'dataProvider' => $userDataProvider,
        'currentView' => $currentView,
        'iconView' => [
            'itemView' => '_icon',
            'summary' => false,
        ],
        'gridView' => [
            'columns' => $columns
        ],
    ];

    echo DataProviderView::widget($dataProviderViewWidgetConf);
?>
</div>

<?php $this->endBlock(); ?>