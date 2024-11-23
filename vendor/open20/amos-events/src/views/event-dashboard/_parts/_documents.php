<?php

use open20\amos\events\AmosEvents;
use open20\amos\documenti\AmosDocumenti;
use open20\amos\core\icons\AmosIcons;
use open20\amos\documenti\utility\DocumentsUtility;
use open20\amos\layout\assets\BootstrapItaliaCustomSpriteAsset;
use yii\helpers\Html;

$eventModel = $model;
echo Html::a(\Yii::t('amosevents', 'Aggiungi Documenti'),
    ['/documenti/documenti/create', 'urlRedirect' => '/events/event-dashboard/landing-manage-contents?id=' . $model->id,
        'offunusedfields' => true,
        'scope_id' => $model->community_id],
    ['class' => 'btn btn-sm btn-secondary mb-3 save-all-data',
//            'data-confirm' => \Yii::t('amosevents','Stai per lasciare la pagina, assicurarsi di aver salvato dati. Proseguire?')
    ]);
?>
<?php

echo \open20\amos\core\views\AmosGridView::widget([
    'dataProvider' => $dataProviderDocs,
    'columns' => [
        'type' => [
            'label' => AmosDocumenti::t('amosdocumenti', '#type'),
            'format' => 'html',
            'value' => function ($model) {
                /** @var Documenti $model */
                $title = AmosDocumenti::t('amosdocumenti', 'Documenti');
                if ($model->is_folder) {
                    $title = AmosDocumenti::t('amosdocumenti', '#folder');
                } else {
                    $documentFile = $model->getDocumentMainFile();
                    if ($documentFile) {
                        $title = $documentFile->type;
                    }
                }

                $icon = DocumentsUtility::getDocumentIcon($model, true);
                if ($model->drive_file_id) {
                    return AmosIcons::show($icon, ['title' => $title], 'dash') . AmosIcons::show('google-drive', ['class' => 'google-sync'], 'am');
                } else {
                    return AmosIcons::show($icon, ['title' => $title], 'dash');
                }
            }
        ],
        'titolo' => [
            'attribute' => 'titolo',
            'headerOptions' => [
                'id' => $model->getAttributeLabel('titolo')
            ],
            'contentOptions' => [
                'headers' => $model->getAttributeLabel('titolo')
            ],
            'format' => 'html',
            'value' => function ($model) use ($actionId) {
                /** @var Documenti $model */
                $title = $model->titolo;
                if ($model->is_folder) {
                    $url = [$actionId, 'parentId' => $model->id];
                } else {
                    $url = '';
                    $document = $model->getDocumentMainFile();
                    if ($document) {
                        $url = $document->getUrl();
                    }
                }
                return Html::a(
                    $title,
                    $url,
                    [
                        'title' => AmosDocumenti::t(
                                'amosdocumenti',
                                'Scarica il documento'
                            ) . '"' . $model->titolo . '"'
                    ]
                );
            }
        ],
        /*  'created_by' => [
              'attribute' => 'createdUserProfile',
              'label' => AmosEvents::t('amosnews', 'Pubblicato Da'),
              'value' => function ($model) {
                  return Html::a($model->createdUserProfile->nomeCognome, ['/admin/user-profile/view', 'id' => $model->createdUserProfile->id], [
                              'title' => AmosEvents::t('amosnews', 'Apri il profilo di {nome_profilo}', ['nome_profilo' => $model->createdUserProfile->nomeCognome])
                  ]);
              },
              'format' => 'html'
          ],*/
//            'data_pubblicazione' => [
//                'label' => $hidePubblicationDate ? AmosNews::t('amosnews', 'Pubblicato il') : AmosNews::t('amosnews', 'Pubblica dal'),
//                'attribute' => 'data_pubblicazione',
//                'value' => function ($model) {
//                    $model = $model->news;
//                    /** @var News $model */
//                    return (is_null($model->data_pubblicazione)) ? AmosNews::t('amosnews', 'Subito') : Yii::$app->formatter->asDate($model->data_pubblicazione);
//                }
//            ],
//            'data_rimozione' => [
//                'visible' => (!$hidePubblicationDate && !$hideDataRimozioneView),
//                'attribute' => 'data_rimozione',
//                'value' => function ($model) {
//                    $model = $model->news;
//                    /** @var News $model */
//                    return (is_null($model->data_rimozione)) ? AmosNews::t('amosnews', 'Mai') : Yii::$app->formatter->asDate($model->data_rimozione);
//                }
//            ],
        /* 'status' => [
             'attribute' => 'status',
             'value' => function ($model) {
                 return $model->hasWorkflowStatus() ? AmosEvents::t('amosnews', $model->getWorkflowStatus()->getLabel()) : '--';
             }
         ],*/
        [
            'class' => \open20\amos\core\views\grid\ActionColumn::className(),
            'buttons' => [
                // 'update' => function($url, $model)use ($eventModel){
                //     return Html::a(\open20\amos\core\icons\AmosIcons::show('edit'), [
                //         '/news/news/update','id' => $model->id, 'urlRedirect' => '/events/event-dashboard/landing-manage-contents?id='.$eventModel->id
                //     ], ['class' => 'btn btn-primary']);
                // },
                // 'view' => function($url, $model) use ($eventModel){
                //     return Html::a(\open20\amos\core\icons\AmosIcons::show('file'), [
                //         '/news/news/view','id' => $model->id, 'urlRedirect' => '/events/event-dashboard/landing-manage-contents?id='.$eventModel->id
                //     ], ['class' => 'btn btn-primary']);
                // },
                // 'delete' => function($url, $model)use ($eventModel){
                //     return Html::a(\open20\amos\core\icons\AmosIcons::show('delete'), [
                //         '/news/news/delete','id' => $model->id, 'urlRedirect' => '/events/event-dashboard/landing-manage-contents?id='.$eventModel->id
                //     ], ['class' => 'btn btn-primary']);
                // }

                'view' => function($url, $model) use ($eventModel) {
                    return '';
                },
                'update' => function ($url, $model) use ($eventModel) {
                    $url = ['/documenti/documenti/update', 'id' => $model->id, 'urlRedirect' => '/events/event-dashboard/landing-manage-contents?id=' . $eventModel->id, 'offunusedfields' => true, 'scope_id' => $eventModel->community_id];
                    $spriteAsset = BootstrapItaliaCustomSpriteAsset::register($this);
                    $iconModifica = "<use xlink:href=" . $spriteAsset->baseUrl . "/material-sprite.svg#ic_edit></use>";
                    $svgIconModifica = Html::tag('svg', $iconModifica, ['class' => 'icon icon-white']);
                    $spanSvgIconModifica = Html::tag('span', $svgIconModifica, ['class' => 'rounded-icon rounded-secondary p-1']) . Html::tag('span', AmosEvents::t('amosevents', 'Modifica'), ['class' => 'sr-only']);
                    return Html::a($spanSvgIconModifica, $url, ['class' => 'btn btn-xs btn-icon', 'data-toggle' => 'tooltip', 'title' => 'Modifica'], true);
                },
                /*'view' => function ($url, $model) use ($eventModel) {
                    $url = ['/documenti/documenti/view', 'id' => $model->id, 'urlRedirect' => '/events/event-dashboard/landing-manage-contents?id=' . $eventModel->id];
                    $spriteAsset = BootstrapItaliaCustomSpriteAsset::register($this);
                    $iconView = "<use xlink:href=" . $spriteAsset->baseUrl . "/material-sprite.svg#ic_visibility></use>";
                    $svgiconView = Html::tag('svg', $iconView, ['class' => 'icon icon-white']);
                    $spanSvgiconView = Html::tag('span', $svgiconView, ['class' => 'rounded-icon rounded-secondary p-1']) . Html::tag('span', AmosEvents::t('amosevents', 'Visualizza'), ['class' => 'sr-only']);
                    return Html::a($spanSvgiconView, $url, ['class' => 'btn btn-xs btn-icon', 'data-toggle' => 'tooltip', 'title' => 'Visualizza'], true);
                },*/
                'delete' => function ($url, $model) use ($landing, $eventModel) {
                    $url = ['/documenti/documenti/delete', 'id' => $model->id, 'urlRedirect' => '/events/event-dashboard/landing-manage-contents?id=' . $eventModel->id];
                    $spriteAsset = BootstrapItaliaCustomSpriteAsset::register($this);
                    $iconDelete = "<use xlink:href=" . $spriteAsset->baseUrl . "/material-sprite.svg#ic_close></use>";
                    $svgIconDelete = Html::tag('svg', $iconDelete, ['class' => 'icon icon-white']);
                    $spanSvgIconDelete = Html::tag('span', $svgIconDelete, ['class' => 'rounded-icon rounded-danger p-1']) . Html::tag('span', AmosEvents::t('amosevents', 'Elimina'), ['class' => 'sr-only']);
                    return Html::a($spanSvgIconDelete, $url, ['class' => 'btn btn-xs btn-icon', 'data-toggle' => 'tooltip', 'title' => 'Elimina'], true);
                }
            ]
        ]
    ]
]);
?>