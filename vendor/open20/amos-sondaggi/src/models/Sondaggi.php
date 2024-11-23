<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\sondaggi\models
 * @category   CategoryName
 */

namespace open20\amos\sondaggi\models;

use open20\amos\attachments\behaviors\FileBehavior;
use open20\amos\attachments\models\File;
use open20\amos\community\utilities\CommunityUtil;
use open20\amos\core\helpers\Html;
use open20\amos\core\interfaces\NewsletterInterface;
use open20\amos\notificationmanager\behaviors\NotifyBehavior;
use open20\amos\sondaggi\AmosSondaggi;
use open20\amos\sondaggi\i18n\grammar\SondaggiGrammar;
use open20\amos\sondaggi\widgets\icons\WidgetIconSondaggi;
use open20\amos\workflow\behaviors\WorkflowLogFunctionsBehavior;
use raoul2000\workflow\base\SimpleWorkflowBehavior;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use open20\amos\sondaggi\models\SondaggiMap;

/**
 * Class Sondaggi
 * This is the model class for table "sondaggi".
 *
 * @method \cornernote\workflow\manager\components\WorkflowDbSource getWorkflowSource()
 * @method \yii\db\ActiveQuery hasOneFile($attribute = 'file', $sort = 'id')
 * @method \yii\db\ActiveQuery hasMultipleFiles($attribute = 'file', $sort = 'id')
 *
 * @package open20\amos\sondaggi\models
 */
class Sondaggi extends \open20\amos\sondaggi\models\base\Sondaggi implements NewsletterInterface
{
    // Workflow ID
    const WORKFLOW = 'SondaggiWorkflow';
    // Workflow statuses IDs
    const WORKFLOW_STATUS_BOZZA = 'SondaggiWorkflow/BOZZA';
    const WORKFLOW_STATUS_DAVALIDARE = 'SondaggiWorkflow/DAVALIDARE';
    const WORKFLOW_STATUS_VALIDATO = 'SondaggiWorkflow/VALIDATO';


    const SONDAGGI_LIVE_CHART_PIE = 1;
    const SONDAGGI_LIVE_CHART_COLUMN = 2;

    //public $regola_pubblicazione;
    //public $destinatari;
    //public $validatori;
    public $file;
    public $destinatari_pubblicazione;
    public $tipologie_entita;
    public $pubblico;
    public $attivita_formativa;
    public $punto_pei;
    public $mail_subject;
    public $mail_message;
    public $text_not_compilable;
    public $text_end;
    public $text_end_title;
    public $text_end_html;
    public $text_not_compilable_html;

    /**
     * @inheritdoc
     */
    public function representingColumn()
    {
        return [
            'titolo'
        ];
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if ($this->isNewRecord) {
            $this->status = $this->getWorkflowSource()->getWorkflow(self::WORKFLOW)->getInitialStatusId();
        }
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(),
            [
                //[['regola_pubblicazione', 'destinatari', 'validatori'], 'safe'],
                [['file'], 'file'],
                [['destinatari_pubblicazione'], 'safe'],
                //[['destinatari_pubblicazione', 'tipologie_entita'], 'required'],
                [['tipologie_entita'], 'safe']
            ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return
            ArrayHelper::merge(
                parent::attributeLabels(),
                [
//      'tagValues' => '',
//      'regola_pubblicazione' => 'Pubblicata per',
//      'destinatari' => 'Per i condominii',
                    'text_end_html' => AmosSondaggi::t('amossondaggi', 'Messaggio di fine sondaggio in HTML'),
                    'text_not_compilable_html' => AmosSondaggi::t('amossondaggi',
                        'Messaggio di sondaggio non compilabile in HTML'),
                    'mail_message' => AmosSondaggi::t('amossondaggi', 'Testo della e-mail di notifica'),
                    'mail_subject' => AmosSondaggi::t('amossondaggi', 'Oggetto della e-mail di notifica'),
                    'text_not_compilable' => AmosSondaggi::t('amossondaggi', 'Messaggio di sondaggio non compilabile'),
                    'text_end' => AmosSondaggi::t('amossondaggi', 'Messaggio di fine sondaggio'),
                    'text_end_title' => AmosSondaggi::t('amossondaggi', 'Titolo della pagina di fine sondaggio'),
                ]);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(),
            [
                'fileBehavior' => [
                    'class' => FileBehavior::className()
                ],
                'NotifyBehavior' => [
                    'class' => NotifyBehavior::className(),
                    'conditions' => []
                ],
                'workflow' => [
                    'class' => SimpleWorkflowBehavior::className(),
                    'defaultWorkflowId' => self::WORKFLOW,
                    'propagateErrorsToModel' => true
                ],
                'workflowLog' => [
                    'class' => WorkflowLogFunctionsBehavior::className()
                ]
            ]);
    }
    /*
      public static function find()
      {
      $SondaggiQuery = new SondaggiQuery(get_called_class());
      $SondaggiQuery->andWhere('sondaggi.deleted_at IS NULL');
      return $SondaggiQuery;
      }
     */

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        parent::afterFind();

        $this->file = $this->getFile()->one();
    }

    /**
     * Getter for $this->file;
     * @return \yii\db\ActiveQuery
     */
    public function getFile()
    {
        return $this->hasOneFile('file');
    }

    public function getAvatarUrl($dimension = 'original')
    {
        $url = '/img/img_default.jpg';
        if ($this->file) {
            $url = $this->file->getUrl($dimension);
        }
        return $url;
    }

    /**
     * Funzione che verifica se il sondaggio è pubblicabile o meno
     * @return boolean True | False - se il sondaggio è pubblicabile restituisce true, altrimenti false
     */
    public function verificaSondaggioPubblicabile()
    {
        $verifica = true;
        $pagine = $this->getSondaggiDomandePagines();
        $arrMapReq = [];
        $mapRequired = SondaggiMap::find()->andWhere(['obbligatorio' => 1])->select('id')->asArray()->all();
        if ($this->abilita_registrazione == 1) {
            foreach ($mapRequired as $v) {
                $arrMapReq[] = $v['id'];
            }
        }
        if ($pagine->count() > 0) {
            foreach ($pagine->all() as $Pagina) {
                $domande = SondaggiDomande::find()->andWhere(['sondaggi_domande_pagine_id' => $Pagina['id']]);
                if ($domande->count() > 0) {
                    foreach ($domande->all() as $Domanda) {
                        if (in_array($Domanda['sondaggi_domande_tipologie_id'], [1, 2, 3, 4, 7, 8])) {
                            $risposte = SondaggiRispostePredefinite::find()->andWhere(['sondaggi_domande_id' => $Domanda['id']]);
                            if ($risposte->count() == 0) {
                                $verifica = false;
                            } else {
                                if ($Domanda['min_int_multipla'] > 0) {
                                    $numRisp = $Domanda['min_int_multipla'];
                                    if ($numRisp >= $risposte->count()) {
                                        return false;
                                    }
                                }
                            }
                        }
                        if ($this->abilita_registrazione == 1) {
                            $map = $Domanda->getMapField();
                            if (!empty($arrMapReq) && !empty($map) && !empty($map->one())) {
                                if (in_array($map->one()->id, $arrMapReq)) {
                                    $key = array_search($map->one()->id, $arrMapReq);
                                    if ($key !== false) {
                                        unset($arrMapReq[$key]);
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $verifica = false;
                }
            }
            if (!empty($arrMapReq)) {
                $verifica = false;
            }
            $domande = $this->getSondaggiDomandes();
        } else {
            $verifica = false;
        }
        return $verifica;
    }

    /**
     * Restituisce il numero di partecipazioni al sondaggio, se non viene specificato un'utente
     * restituisce il numero totale
     * @param integer $personale 0 | 1 se viene inserito 0 restituirà il numero totale delle risposte al sondaggio, altrimenti il numero di partecipazioni personali
     * @return integer Numero di partecipazioni inclusive di quelle parziali
     */
    public function getNumeroPartecipazioni($personale = 0)
    {
        if ($personale) {
            $utente = \Yii::$app->getUser()->getId();
            $condition1 = new \yii\db\Expression("count(distinct(IF(sondaggi_risposte_sessioni.end_date IS NOT NULL OR sondaggi_risposte.id is not null and sondaggi_risposte_sessioni.end_date is null, sondaggi_risposte_sessioni.id, null))) partecipanti");

            $sessioni = SondaggiRisposteSessioni::find()
                ->innerJoin('sondaggi_risposte',
                    'sondaggi_risposte.sondaggi_risposte_sessioni_id = sondaggi_risposte_sessioni.id')
                ->andWhere(['sondaggi_risposte_sessioni.sondaggi_id' => $this->id])
                ->andWhere(['sondaggi_risposte_sessioni.user_profile_id' => $utente])
                ->select($condition1);
            return $sessioni->asArray()->one()['partecipanti'];
        } else {
            $condition1 = new \yii\db\Expression("count(distinct(IF(sondaggi_risposte_sessioni.end_date IS NOT NULL OR sondaggi_risposte.id is not null and sondaggi_risposte_sessioni.end_date is null, sondaggi_risposte_sessioni.id, null))) partecipanti");

            $sessioni = SondaggiRisposteSessioni::find()
                ->innerJoin('sondaggi_risposte',
                    'sondaggi_risposte.sondaggi_risposte_sessioni_id = sondaggi_risposte_sessioni.id')
                ->andWhere(['sondaggi_risposte_sessioni.sondaggi_id' => $this->id])
                ->select($condition1);
            //  pr($sessioni->createCommand()->rawSql);
            return $sessioni->asArray()->one()['partecipanti'];
        }
    }

    /**
     * @param array $post
     */
    public function getOtherAttributes($post = null)
    {
        $oldValues = $this->getSondaggiPubblicaziones()->one();
        if (!empty($post)) {
            if (empty($oldValues)) {
                $oldValues = new SondaggiPubblicazione();
                $oldValues->sondaggi_id = $this->id;
                $oldValues->ruolo = 'PUBBLICO';
            }
            if (isset($post['Sondaggi']['mail_subject'])) {

                $oldValues->mail_subject = $post['Sondaggi']['mail_subject'];
            }
            if (isset($post['Sondaggi']['mail_message'])) {
                $oldValues->mail_message = $post['Sondaggi']['mail_message'];
            }
            if (isset($post['Sondaggi']['text_end'])) {
                $oldValues->text_end = $post['Sondaggi']['text_end'];
            }
            if (isset($post['Sondaggi']['text_end_html'])) {
                $oldValues->text_end_html = $post['Sondaggi']['text_end_html'];
            }
            if (isset($post['Sondaggi']['text_end_title'])) {
                $oldValues->text_end_title = $post['Sondaggi']['text_end_title'];
            }
            if (isset($post['Sondaggi']['text_not_compilable'])) {
                $oldValues->text_not_compilable = $post['Sondaggi']['text_not_compilable'];
            }
            if (isset($post['Sondaggi']['text_not_compilable_html'])) {
                $oldValues->text_not_compilable_html = $post['Sondaggi']['text_not_compilable_html'];
            }
            $oldValues->save(false);
        } else {
            $this->mail_subject = $oldValues['mail_subject'];
            $this->mail_message = $oldValues['mail_message'];
            $this->text_end = $oldValues['text_end'];
            $this->text_end_html = $oldValues['text_end_html'];
            $this->text_end_title = $oldValues['text_end_title'];
            $this->text_not_compilable = $oldValues['text_not_compilable'];
            $this->text_not_compilable_html = $oldValues['text_not_compilable_html'];
        }
    }

    /**
     * @return bool
     * in base all'utente loggato, si recupera il numero di volte che è stato compilato il sondaggio
     * se questo è >= del valore inserito in 'compilazioni_disponibili' allora non sarà possibile compilare ulteriormente il sondaggio.
     * NB: se il valore in 'compilazioni_disponibili' è 0 (zero) si assume non ci sia limite
     */
    public function hasCompilazioniSuperate()
    {
        $utente_id = \Yii::$app->getUser()->getId();

        //se il numero di compilazioni è 0 (zero) => nessun limite di compilazione
        $compilazioni_disponibili = $this->compilazioni_disponibili;
        if ((!$compilazioni_disponibili || $compilazioni_disponibili === 0) && $this->abilita_criteri_valutazione == 0) {
            return false;
        }

        $q = $this->getTuttiPartecipanti($utente_id);

        $numero_compilazioni_x_utente = $q->count();

        $compilazioniSuperate = ($compilazioni_disponibili > 0 && $numero_compilazioni_x_utente >= $compilazioni_disponibili);
        $valutatoriSuperati = $this->valutazioniSuperate();

        return ($compilazioniSuperate || $valutatoriSuperati);
    }

    /**
     *
     * @return boolean
     */
    public function valutazioniSuperate()
    {
        $valutatori = 0;
        $limite_superato = false;
        if ($this->abilita_criteri_valutazione == 1) {
            $valutatori = $this->getNumeroValutatori();
            if ($this->abilita_criteri_valutazione == 1 && $valutatori >= $this->n_max_valutatori && $this->n_max_valutatori
                != 0) {
                $limite_superato = true;
            }
        }
        return $limite_superato;
    }

    /**
     *
     * @return type
     */
    public function getNumeroValutatori()
    {
        return SondaggiRisposteSessioni::find()->andWhere(['sondaggi_id' => $this->id])->count();
    }

    /**
     *
     * @param type $utente_id
     * @return type
     */
    public function getTuttiPartecipanti($utente_id = null)
    {
        $sondaggiRisposteTable = SondaggiRisposte::tableName();
        $sondaggiRisposteSessioniTable = SondaggiRisposteSessioni::tableName();
        /** @var ActiveQuery $q */
        $q = SondaggiRisposteSessioni::find();
        $q->select($sondaggiRisposteSessioniTable . '.id, ' . $sondaggiRisposteSessioniTable . '.user_id, ' . $sondaggiRisposteSessioniTable . '.sondaggi_id')
            ->innerJoin($sondaggiRisposteTable,
                $sondaggiRisposteTable . '.sondaggi_risposte_sessioni_id = ' . $sondaggiRisposteSessioniTable . '.id')
            ->andWhere([$sondaggiRisposteSessioniTable . '.sondaggi_id' => $this->id]);
        if (!empty($utente_id)) {
            $q->andWhere([$sondaggiRisposteSessioniTable . '.user_id' => $utente_id]);
        }
        $q->groupBy($sondaggiRisposteSessioniTable . '.id');
        return $q;
    }


    public function sondaggioLiveVoted()
    {
        $userId = \Yii::$app->user->id;
        $q = SondaggiRisposteSessioni::find()
            ->innerJoin('sondaggi_risposte', 'sondaggi_risposte.sondaggi_risposte_sessioni_id = sondaggi_risposte_sessioni.id')
            ->andWhere(['sondaggi_risposte_sessioni.sondaggi_id' => $this->id])
            ->andWhere(['sondaggi_risposte_sessioni.user_id' => $userId])
            ->count();
        return $q;
    }

    /**
     * @inheritdoc
     */
    public function getGridViewColumns()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getViewUrl()
    {
        return "sondaggi/pubblicazione/compila";
    }

    /**
     * @inheritdoc
     */
    public function getFullViewUrl()
    {
        return Url::toRoute(["/" . $this->getViewUrl(), "id" => $this->id]);
    }

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return $this->titolo;
    }

    /**
     * @inheritdoc
     */
    public function getShortDescription()
    {
        return $this->descrizione;
    }

    /**
     * @inheritdoc
     */
    public function getDescription($truncate)
    {
        $ret = $this->descrizione;
        if ($truncate) {
            $ret = $this->__shortText($this->descrizione, 200);
        }
        return $ret;
    }

    /**
     * @inheritdoc
     */
    public function getPluginWidgetClassname()
    {
        return WidgetIconSondaggi::className();
    }

    /**
     * @inheritdoc
     */
    public function getDraftStatus()
    {
        return self::WORKFLOW_STATUS_BOZZA;
    }

    /**
     * @inheritdoc
     */
    public function getToValidateStatus()
    {
        return self::WORKFLOW_STATUS_DAVALIDARE;
    }

    /**
     * @inheritdoc
     */
    public function getValidatedStatus()
    {
        return self::WORKFLOW_STATUS_VALIDATO;
    }

    /**
     * @inheritdoc
     */
    public function getValidatorRole()
    {
        return 'AMMINISTRAZIONE_SONDAGGI';
    }

    /**
     * @return SondaggiGrammar
     */
    public function getGrammar()
    {
        return new SondaggiGrammar();
    }

    /**
     * @inheritdoc
     */
    public function getCwhValidationStatuses()
    {
        return [self::WORKFLOW_STATUS_VALIDATO];
    }

    /**
     * @return array
     */
    public function getStatusToRenderToHide()
    {
        $statusToRender = [
            self::WORKFLOW_STATUS_BOZZA => AmosSondaggi::t('amossondaggi', 'Modifica in corso'),
        ];
        $isCommunityManager = false;
        if (\Yii::$app->getModule('community')) {
            $isCommunityManager = CommunityUtil::isLoggedCommunityManager();
        }
        // if you are a community manager a validator/facilitator or ADMIN you Can publish directly
        if (\Yii::$app->user->can('ADMIN') || $isCommunityManager) {
            $statusToRender = ArrayHelper::merge($statusToRender,
                [self::WORKFLOW_STATUS_VALIDATO => AmosSondaggi::t('amossondaggi', 'Pubblicata')]);
            $hideDraftStatus = [];
        } else {
            $statusToRender = ArrayHelper::merge($statusToRender,
                [
                    self::WORKFLOW_STATUS_DAVALIDARE => AmosSondaggi::t('amossondaggi', 'Richiedi pubblicazione'),
                ]);
            $hideDraftStatus[] = self::WORKFLOW_STATUS_VALIDATO;
        }

        return ['statusToRender' => $statusToRender, 'hideDraftStatus' => $hideDraftStatus];
    }

    /**
     * @inheritdoc
     */
    public function getWorkflowBaseStatusLabel()
    {
        return AmosSondaggi::t('amossondaggi', parent::getWorkflowBaseStatusLabel());
    }

    /**
     * @inheritdoc
     */
    public function getWorkflowStatusLabel()
    {
        return AmosSondaggi::t('amossondaggi', parent::getWorkflowStatusLabel());
    }

    /**
     * @inheritdoc
     */
    public function getModelImage()
    {
        return $this->getFile()->one();
    }

    /**
     * @inheritdoc
     */
    public function getModelImageUrl($size = 'original', $protected = true, $url = '/img/img_default.jpg', $absolute = false, $canCache = false)
    {
        /** @var File $surveyImage */
        $surveyImage = $this->getModelImage();
        if (!is_null($surveyImage)) {
            if ($protected) {
                $url = $surveyImage->getUrl($size, $absolute, $canCache);
            } else {
                $url = $surveyImage->getWebUrl($size, $absolute, $canCache);
            }
        }
        return $url;
    }

    /**
     * @return string
     */
    public function getModelImageUrlForSummaries()
    {
        return $this->getModelImageUrl('square_large', true, '/img/img_default.jpg', true, true);
    }

    /**
     * @return string
     */
    public function newsletterOrderByField()
    {
        return 'created_at';
    }

    /**
     * @return string
     */
    public function newsletterPublishedStatus()
    {
        return self::WORKFLOW_STATUS_VALIDATO;
    }

    /**
     * @param string $searchParam
     * @param ActiveQuery $query
     * @return ActiveQuery
     */
    public function newsletterSearchFilter($searchParam, $query)
    {
        if ($searchParam) {
            $query->andFilterWhere(['like', self::tableName() . '.titolo', $searchParam]);
        }
        return $query;
    }

    /**
     * @return string
     */
    public function newsletterContentTitle()
    {
        return $this->titolo;
    }

    /**
     * @return string
     */
    public function newsletterContentTitleField()
    {
        return 'titolo';
    }

    /**
     * @return string
     */
    public function newsletterContentStatusField()
    {
        return 'status';
    }

    /**
     * @return array
     */
    public function newsletterContentGridViewColumns()
    {
        return [
            [
                'label' => $this->getAttributeLabel('eventLogo'),
                'format' => 'html',
                'value' => function ($model) {
                    /** @var Sondaggi $model */
                    $url = '/img/img_default.jpg';
                    if ($model->file) {
                        $url = $model->file->getUrl('original');
                    }
                    $contentImage = Html::img($url, ['class' => 'gridview-image', 'alt' => AmosSondaggi::t('amossondaggi', 'Immagine del sondaggio')]);
                    return $contentImage;
                }
            ],
            'titolo:ntext',
            'status' => [
                'attribute' => 'status',
                'value' => function ($model) {
                    /** @var Sondaggi $model */
                    return $model->getWorkflowBaseStatusLabel();
                }
            ],
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
                'label' => AmosSondaggi::t('amossondaggi', '#published_on')
            ]
        ];
    }

    /**
     * @return array
     */
    public function newsletterSelectContentsGridViewColumns()
    {
        return [
            [
                'label' => $this->getAttributeLabel('eventLogo'),
                'format' => 'html',
                'value' => function ($model) {
                    /** @var Sondaggi $model */
                    $url = '/img/img_default.jpg';
                    if ($model->file) {
                        $url = $model->file->getUrl('original');
                    }
                    $contentImage = Html::img($url, ['class' => 'gridview-image', 'alt' => AmosSondaggi::t('amossondaggi', 'Immagine del sondaggio')]);
                    return $contentImage;
                }
            ],
            'titolo:ntext'
        ];
    }

    /**
     * @return bool
     */
    public function hasAlreadyPage()
    {
        return $this->getSondaggiDomandePagines()->count() > 0;
    }

    /**
     * @return bool
     */
    public function hasAlreadyDomande()
    {
        return $this->getSondaggiDomandes()->count() > 0;
    }

    /**
     * @return array
     */
    public static function sondaggiLiveTypeCharts()
    {
        return [
            self::SONDAGGI_LIVE_CHART_COLUMN => AmosSondaggi::t('amossondaggi', 'Grafico a barre'),
            self::SONDAGGI_LIVE_CHART_PIE => AmosSondaggi::t('amossondaggi', 'Grafico a torta')
        ];
    }

    /**
     * @param $id
     * @param $idPagina
     * @param bool $disableTitle
     * @return string
     */
    public static function renderSondaggiLive($id, $idPagina, $disableTitle = false){
        $risposte = new Risposte();

        $model    = Sondaggi::findOne(['id' => $id]);
        $pagineQuery    = $model->getSondaggiDomandePagines()
            ->joinWith('sondaggiDomandes')
            ->andWhere(['IN', 'sondaggi_domande.sondaggi_domande_tipologie_id', [1, 2, 3, 4]])
            ->groupBy('sondaggi_domande_pagine.id')
            ->orderBy('sondaggi_domande_pagine.id ASC');

        $pagine         = $pagineQuery->all();
        $prossimaPagina = null;
        $arrayPag       = [];
        foreach ($pagine as $Pag) {
            $arrayPag[] = $Pag['id'];
        }

        if (!empty($model)) {
            if ($idPagina > -2) {
                return \Yii::$app->controller->view->render('@vendor/open20/amos-sondaggi/src/views/sondaggi/risultati_live',
                    [
                        'model' => $model,
                        'idPagina' => $idPagina,
                        'risposte' => $risposte->getDati($id, $idPagina),
                        //  'report' => $risposte->getReport($id, $idPagina),
                        'domande' => $risposte->getDomandeStatistiche($id, $idPagina),
                        'tipo' => $risposte->getTipologia($id),
                        'filter' => $risposte,
                        'disableTitle' => $disableTitle,
                    ]);
            }
        }
        return '';
    }


}
