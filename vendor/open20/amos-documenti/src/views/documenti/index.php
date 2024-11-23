<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\documenti\views\documenti
 * @category   CategoryName
 */

use open20\amos\core\helpers\Html;
use open20\amos\core\icons\AmosIcons;
use open20\amos\core\utilities\ModalUtility;
use open20\amos\core\views\DataProviderView;
use open20\amos\documenti\AmosDocumenti;
use open20\amos\documenti\models\Documenti;
use open20\amos\documenti\models\search\DocumentiSearch;
use open20\amos\documenti\utility\DocumentsUtility;
use open20\amos\documenti\widgets\DocumentsOwlCarouselWidget;

if ($currentView['name'] == 'expl') {
    echo $this->render('_explorer', []);
    return null;
}

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var open20\amos\documenti\models\search\DocumentiSearch $model
 * @var \open20\amos\dashboard\models\AmosUserDashboards $currentDashboard
 */

/** @var \open20\amos\documenti\controllers\DocumentiController $controller */
$controller = Yii::$app->controller;
$actionId = $controller->action->id;
$actionColumnsTemplate = $controller->getGridViewActionColumnsTemplate($actionId);

$foldersEnabled = $controller->documentsModule->enableFolders;
$enableCategories = $controller->documentsModule->enableCategories;
$hidePubblicationDate = $controller->documentsModule->hidePubblicationDate;
$showCountDocumentRecursive = $controller->documentsModule->showCountDocumentRecursive;

$parentId = \Yii::$app->request->get('parentId');
//$this->params['subTitleSection'] = 'ciao';
if (!empty($parentId)) {
    $folder = Documenti::findOne($parentId);
    if ($folder) {
        $this->params['subTitleSection'] = '<span class="icon icon-folder icon-sm mdi mdi-folder "></span>'.ucfirst($folder->getTitle());
        $this->params['subTitleAdditionalClass'] = 'lead m-t-10';

    }
}

$columns = [];
if ($foldersEnabled) {
    $columns['type'] = [
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

            if(\Yii::$app->params['befe']){
                return DocumentsUtility::getDocumentIcon($model);
            }else {
                $icon = DocumentsUtility::getDocumentIcon($model, true);
                if ($model->drive_file_id) {
                    return AmosIcons::show($icon, ['title' => $title], 'dash') . AmosIcons::show('google-drive', ['class' => 'google-sync'], 'am');
                } else {
                    return AmosIcons::show($icon, ['title' => $title], 'dash');
                }
            }
        }
    ];
    $columns['titolo'] = [
        'attribute' => 'titolo',
        'headerOptions' => [
            'id' => $model->getAttributeLabel('titolo')
        ],
        'contentOptions' => [
            'headers' => $model->getAttributeLabel('titolo')
        ],
        'format' => 'raw',
        'value' => function ($model) use ($actionId) {
        $stringa = \open20\amos\documenti\models\DocumentiCartellePath::getPath($model);
           if ($model->is_folder) { 
                $options =   [
                'title' => AmosDocumenti::t(
                        'amosdocumenti',
                        'Apri la cartella '
                     ) . '"'.$stringa. $model->titolo . '"'
                ];
           }else{
                $options =   [
                'title' => AmosDocumenti::t(
                        'amosdocumenti',
                        'Scarica il documento '
                     ) . '"'.$stringa. $model->titolo . '"'
                ];
           }
            /** @var Documenti $model */
            $title = $model->titolo;
            $append = '';
            if ($model->is_folder) {
                $url = [$actionId, 'parentId' => $model->id];
            } else {
                $url = '';
                $document = $model->getDocumentMainFile();
                if ($document) {
                    $append = ' ('.$document->formattedSize.')';
                    $url = $document->getUrl();
                } else {
                    $url = $model->link_document;
                    $options['target'] = '_blank';
                }
            }

            return Html::a(
                $title,
                $url,
                $options
            );
        }
    ];

    $columns['downloads'] = [
        'label' => AmosDocumenti::t('amosdocumenti', '#num_downloads'),
        'value' => function ($model) {
            /** @var Documenti $model */
            if ($model->is_folder) {
                return '';
            } else {
                $numDown = 0;
                $file = $model->getDocumentMainFile();
                if (!is_null($file)) {
                    $numDown = $file->getNumDownloads();
                }
                return $numDown;
            }
        },
    ];

    if (!isset(\Yii::$app->params['hideListsContentCreatorName']) || (\Yii::$app->params['hideListsContentCreatorName'] === false)) {
        $columns['updated_by'] = [
            'attribute' => 'updatedUserProfile',
            'label' => AmosDocumenti::t('amosdocumenti', '#updated_by'),
            'value' => function ($model) {
                /** @var Documenti $model */
                $profile = \open20\amos\admin\models\UserProfile::find()->andWhere(['user_id' => $model->updated_by])->one();
                if (empty($profile)) {
                    return '';
                }

                if (isset(\Yii::$app->params['disableLinkContentCreator']) && (\Yii::$app->params['disableLinkContentCreator'] === true)) {
                    return $profile->nomeCognome;
                }

                return Html::a(
                    $profile->nomeCognome,
                    $profile->getFullViewUrl(),
                    [
                        'title' => AmosDocumenti::t('amosdocumenti', 'Apri il profilo di {nome_profilo}', ['nome_profilo' => $profile->nomeCognome])
                    ]
                );
            },
            'format' => 'html'
        ];
    }

    $columns[] = [
        'label' => AmosDocumenti::t('amosdocumenti', 'Documents'),
        'value' => function ($model) use ($showCountDocumentRecursive) {
            /** @var Documenti $model */
            if ($model->is_folder) {
                if ($showCountDocumentRecursive) {
                    return count($model->allDocumentChildrens);
                } else {
                    return count($model->documentChildrens);
                }
            } else {
                return '';
            }
        },
    ];
} else {
    $columns['titolo'] = [
        'attribute' => 'titolo',
        'headerOptions' => [
            'id' => $model->getAttributeLabel('titolo')
        ]
    ];

    if (!isset(\Yii::$app->params['hideListsContentCreatorName']) || (\Yii::$app->params['hideListsContentCreatorName'] === false)) {
        $columns['created_by'] = [
            'attribute' => 'createdUserProfile',
            'label' => AmosDocumenti::t('amosdocumenti', 'Pubblicato Da'),
        ];
    }
}

$columns['status'] = [
    'label' => AmosDocumenti::t('amosdocumenti', 'Stato'),
    'value' => function ($model) {
        /** @var Documenti $model */
        return AmosDocumenti::t('amosdocumenti', $model->status);
    },
    'attribute' => 'status'
];

$columns['data_pubblicazione'] = [
    'label' => AmosDocumenti::t('amosdocumenti', '#uploaded_at'),
    'attribute' => 'data_pubblicazione',
    'value' => function ($model) {
        /** @var Documenti $model */
        return (is_null($model->data_pubblicazione)) ? AmosDocumenti::t('amosdocumenti', 'Subito') : Yii::$app->formatter->asDate($model->data_pubblicazione);
    }
];

if (!$foldersEnabled) {
    $columns['data_rimozione'] = [
        'attribute' => 'data_rimozione',
        'value' => function ($model) {
            /** @var Documenti $model */
            return (is_null($model->data_rimozione)) ? AmosDocumenti::t('amosdocumenti', 'Mai') : Yii::$app->formatter->asDate($model->data_rimozione);
        }
    ];

    $columns['status'] = [
        'attribute' => 'status',
        'value' => function ($model) {
            /** @var Documenti $model */
            return ($model->hasWorkflowStatus() ? AmosDocumenti::t('amosdocumenti', $model->getWorkflowStatus()->getLabel()) : '--');
        }
    ];
}

if ($enableCategories) {
    $columns['documenti_categorie_id'] = [
        'attribute' => 'documentiCategorie.titolo',
        'label' => AmosDocumenti::t('amosdocumenti', 'Categoria'),
    ];
}

if ($controller->documentsModule->enableDocumentVersioning) {
    $columns['version'] = [
        'attribute' => 'version',
        'value' => function ($model) {
            /** @var Documenti $model */
            return (!$model->is_folder && $model->version ? $model->version : '');
        },
    ];
}

//the columns for export have to be before the special columns (ExpandRowColumn, Action column)
$exportColumns = $columns;
$exportColumns['type'] = [
    'value' => function ($model) {
        /** @var Documenti $model */
        if ($model->is_folder) {
            $return = AmosDocumenti::t('amosdocumenti', '#folder');
        } else {
            $return = AmosDocumenti::t('amosdocumenti', '#document');
        }
        return $return;
    }
];

if ($controller->documentsModule->enableDocumentVersioning && !$model->is_folder) {
    $columns['expandAllTitle'] = [
        'class' => 'kartik\grid\ExpandRowColumn',
        'expandAllTitle' => AmosDocumenti::t('amosdocumenti', 'Version'),
        'allowBatchToggle' => false,
        'header' => AmosDocumenti::t('amosdocumenti', 'Expand / Collapse'),
        'headerOptions' => [
            'style' => 'white-space: nowrap;',
        ],
        'contentOptions' => [
            'class' => 'text-center',
        ],
        'value' => function ($model, $key, $index, $column) use ($controller) {
            /** @var Documenti $model */
            $queryParams = \Yii::$app->request->getQueryParams();
            $queryParams['parent_id'] = $model->id;

            /** @var DocumentiSearch $modelSearch */
            $modelSearch = $controller->documentsModule->createModel('DocumentiSearch');
            /** @var  $dataProvider \yii\data\ActiveDataProvider */
            $dataProvider = $modelSearch->searchVersions($queryParams);

            if (!$model->is_folder && $dataProvider->count > 0) {
                return \kartik\grid\GridView::ROW_COLLAPSED;
            } else
                return '';
        },
        'expandIcon' => AmosIcons::show(
            'caret-down',
            ['title' => AmosDocumenti::t('amosdocumenti', '#expand_title')]
        ),
        'collapseIcon' => AmosIcons::show(
            'caret-up',
            ['title' => AmosDocumenti::t('amosdocumenti', '#collapse_title')]
        ),
        'expandTitle' => AmosDocumenti::t('amosdocumenti', ''),
        'collapseTitle' => AmosDocumenti::t('amosdocumenti', ''),
        'detailUrl' => \yii\helpers\Url::to(['/documenti/documenti/list-only'])
    ];
}

$deleteOptions = DocumentsUtility::getGridActionColumnsButtonsOptions('delete');
$deleteOptions['data-confirm'] = function ($model) {
    /** @var Documenti $model */
    if ($model->is_folder) {
        $trans = AmosDocumenti::t('amosdocumenti', '#confirm_delete_folder');
    } else {
        $trans = AmosDocumenti::t('amoscore', 'Sei sicuro di voler cancellare questo elemento?');
    }
    return $trans;
};

$actionColumns = [
    'class' => 'open20\amos\core\views\grid\ActionColumn',
    'template' => $actionColumnsTemplate,
    'deleteOptions' => $deleteOptions,
    'buttons' => [
        'duplicateBtn' => function ($url, $model) use ($controller) {
            /** @var Documenti $model */
            return $this->render('_duplicate_btn', [
                'model' => $model,
                'isInIndex' => true
            ]);
        },
        'view' => function ($url, $model) {
            /** @var Documenti $model */
            $btn = '';
            if (!$model->is_folder && Yii::$app->getUser()->can('DOCUMENTI_READ', ['model' => $model])) {
                $btn = Html::a(
                    AmosIcons::show('file'),
                    ['view', 'id' => $model->id],
                    [
                        'class' => 'btn btn-tools-secondary',
                        'title' => AmosDocumenti::t('amosdocumenti', 'Open the card')
                    ]
                );
            }
            return $btn;
        },
        'newDocVersion' => function ($url, $model) use ($controller) {
            /** @var Documenti $model */
            $btn = '';
            if ($model->status == Documenti::DOCUMENTI_WORKFLOW_STATUS_VALIDATO && Yii::$app->getUser()->can(
                    'DOCUMENTI_UPDATE',
                    [
                        'model' => $model,
                        'newVersion' => $controller->documentsModule->enableDocumentVersioning
                    ]
                )) {
                if ($controller->documentsModule->enableDocumentVersioning && !$model->is_folder && (is_null($model->version_parent_id))) {
                    $btn = ModalUtility::addConfirmRejectWithModal([
                        'modalId' => 'new-document-version-modal-id-' . $model->id,
                        'modalDescriptionText' => AmosDocumenti::t('amosdocumenti', '#NEW_DOCUMENT_VERSION_MODAL_TEXT'),
                        'btnText' => AmosIcons::show('plus', ['class' => '']),
                        'btnLink' => \yii\helpers\Url::to([
                            '/documenti/documenti/new-document-version',
                            'id' => $model['id']
                        ]),
                        'btnOptions' => [
                            'title' => AmosDocumenti::t(
                                'amosdocumenti',
                                'New document version'
                            ), 'class' => 'btn btn-tools-secondary'
                        ]
                    ]);
                }
            }
            return $btn;
        },
        'validate' => function ($url, $model) {
            /** @var Documenti $model */
            $btn = '';
            if (Yii::$app->getUser()->can('DocumentValidate', ['model' => $model])) {
                $btn = ModalUtility::addConfirmRejectWithModal([
                    'modalId' => 'validate-document-modal-id-' . $model->id,
                    'modalDescriptionText' => AmosDocumenti::t('amosdocumenti', '#VALIDATE_DOCUMENT_MODAL_TEXT'),
                    'btnText' => AmosIcons::show('check-circle', ['class' => '']),
                    'btnLink' => Yii::$app->urlManager->createUrl([
                        '/documenti/documenti/validate-document',
                        'id' => $model->id
                    ]),
                    'btnOptions' => [
                        'title' => AmosDocumenti::t(
                            'amosdocumenti',
                            'Publish'
                        ), 'class' => 'btn btn-tools-secondary'
                    ]
                ]);
            }
            return $btn;
        },
        'reject' => function ($url, $model) {
            /** @var Documenti $model */
            $btn = '';
            if (Yii::$app->getUser()->can('DocumentValidate', ['model' => $model])) {
                $btn = ModalUtility::addConfirmRejectWithModal([
                    'modalId' => 'reject-document-modal-id-' . $model->id,
                    'modalDescriptionText' => AmosDocumenti::t('amosdocumenti', '#REJECT_DOCUMENT_MODAL_TEXT'),
                    'btnText' => AmosIcons::show('minus-circle', ['class' => '']),
                    'btnLink' => Yii::$app->urlManager->createUrl([
                        '/documenti/documenti/reject-document',
                        'id' => $model->id
                    ]),
                    'btnOptions' => ['title' => AmosDocumenti::t('amosdocumenti', 'Reject'), 'class' => 'btn btn-tools-secondary']
                ]);
            }
            return $btn;
        },
        'update' => function ($url, $model) use ($enableVersioning) {
            /** @var Documenti $model */
            $btn = '';
            if (Yii::$app->user->can('DOCUMENTI_UPDATE', ['model' => $model])) {
                $action = '/documenti/documenti/update?id=' . $model->id;
                $options = ModalUtility::getBackToEditPopup(
                    $model,
                    'DocumentValidate',
                    $action,
                    [
                        'class' => 'btn btn-tools-secondary',
                        'title' => Yii::t('amoscore', 'Modifica'),
                        'data-pjax' => '0'
                    ]
                );
                $btn = Html::a(\open20\amos\core\icons\AmosIcons::show('edit'), $action, $options);
            }
            return $btn;
        }
    ]
];
$columns[] = $actionColumns;
?>
<div class="documents-index">
    <?php
    echo $this->render('_search', ['model' => $model, 'originAction' => Yii::$app->controller->action->id]);

    echo DocumentsOwlCarouselWidget::widget([
        'owlCarouselId' => 'documentOwlCarousel',
        'owlCarouselClass' => 'document-owl-carousel',
        'singleItemView' => '@vendor/open20/amos-documenti/src/views/documenti/amos_owl_carousel_widget_item',
        'owlCarouselJSOptions' => "{
        margin: 10,
        nav: true,
        loop: false,
        autoplay: false,
        autoplayTimeout: 3000,
        responsive: {
            320: {
                items: 1,
                stagePadding: 30
            },
            420: {
                items: 1,
                stagePadding: 30
            },
            620: {
                items: 1,
                stagePadding: 30
            },
            768: {
                items: 2,
                stagePadding: 30
            },
            992: {
                items: 2,
                stagePadding: 30
            },
            1199: {
                items: 3,
                stagePadding: 30
            }
        }
    }"
    ]);

    echo DataProviderView::widget([
        'dataProvider' => $dataProvider,
        'currentView' => $currentView,
        'gridView' => [
            'rowOptions' => function ($model) {
                return ['class' => 'kv-disable-click'];
            },
            'columns' => $columns,
            'enableExport' => true
        ],
        'listView' => [
            'itemView' => '_item',
            'showItemToolbar' => false,
        ],
        'exportConfig' => [
            'exportEnabled' => true,
            'exportColumns' => $exportColumns
        ]
    ]);
    ?>
</div>
