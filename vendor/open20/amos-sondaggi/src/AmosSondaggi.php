<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\sondaggi
 * @category   CategoryName
 */

namespace open20\amos\sondaggi;

use open20\amos\core\interfaces\CmsModuleInterface;
use open20\amos\core\module\AmosModule;
use open20\amos\core\module\ModuleInterface;
use open20\amos\sondaggi\widgets\icons\WidgetIconAmministraSondaggi;
use open20\amos\sondaggi\widgets\icons\WidgetIconCompilaSondaggi;
use open20\amos\sondaggi\widgets\icons\WidgetIconPubblicaSondaggi;
use open20\amos\sondaggi\widgets\icons\WidgetIconSondaggi;
use Yii;
use yii\db\Connection;
use yii\helpers\ArrayHelper;
use open20\amos\core\interfaces\BreadcrumbInterface;


/**
 * Class AmosSondaggi
 * @package open20\amos\sondaggi
 */
class AmosSondaggi extends AmosModule implements ModuleInterface, CmsModuleInterface, BreadcrumbInterface
{
    public static $CONFIG_FOLDER = 'config';
    public $controllerNamespace = 'open20\amos\sondaggi\controllers';
    public $newFileMode = 0666;
    public $newDirMode = 0777;

    /**
     * In the case of a private poll for role, it is possible to send the notification to the users who can fill out the survey.
     * @var boolean
     */
    public $enableNotificationEmailByRoles = false;

    /**
     * Default email for the sender
     * @var string
     */
    public $defaultEmailSender;

    /**
     * It allows to show in the first page of the results the geoChart based on the province of domicile.
     * @var boolean
     */
    public $enableGeoChart = false;

    /**
     * It allows to show in the first page of the results a partecipant report if available.
     * @var boolean
     */
    public $enablePartecipantsReport = false;

    /**
     * The fields that will be displayed in the participant's //TO-DO
     * @var array
     */
    public $fieldsByPartecipants = [];

    /**
     * @var string|boolean the layout that should be applied for views within this module. This refers to a view name
     * relative to [[layoutPath]]. If this is not set, it means the layout value of the [[module|parent module]]
     * will be taken. If this is false, layout will be disabled within this module.
     */
    public $layout = 'main';
    public $name = 'Sondaggi';

    /**
     * @var Connection|array|string the DB connection object or the application component ID of the DB connection.
     */
    public $db = 'db';
    
    /**
     * Hide the Option wheel in the graphic widget
     * @var bool|false $hideWidgetGraphicsActions
     */
    public $hideWidgetGraphicsActions = false;
    
    /**
     * @var array $viewPathEmailSummary
     */
    public $viewPathEmailSummary = [
        'open20\amos\sondaggi\models\Sondaggi' => '@vendor/open20/amos-sondaggi/src/views/email/notify_summary'
    ];
    
    /**
     * @var array $viewPathEmailSummaryNetwork
     */
    public $viewPathEmailSummaryNetwork = [
        'open20\amos\sondaggi\models\Sondaggi' => '@vendor/open20/amos-sondaggi/src/views/email/notify_summary_network'
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->db = Yii::$app->db;

        \Yii::setAlias('@open20/amos/' . static::getModuleName() . '/controllers/', __DIR__ . '/controllers/');
        // initialize the module with the configuration loaded from config.php
        $config = require(__DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php');
        Yii::configure($this, ArrayHelper::merge($config, $this));
    }
    

    /**
     * @inheritdoc
     */
    public static function getModuleName()
    {
        return "sondaggi";
    }

    /**
     * @inheritdoc
     */
    public function getWidgetIcons()
    {
        return [
            WidgetIconSondaggi::className(),
            WidgetIconCompilaSondaggi::className(),
            WidgetIconPubblicaSondaggi::className(),
            WidgetIconAmministraSondaggi::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getWidgetGraphics()
    {
        return [
        ];
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultModels()
    {
        return [
            'Sondaggi' => __NAMESPACE__ . '\\' . 'models\Sondaggi',
            'SondaggiSearch' => __NAMESPACE__ . '\\' . 'models\search\SondaggiSearch',
        ];
    }


    /**
     *
     * @return string
     */
    public static function getModelClassName()
    {
        return AmosSondaggi::instance()->model('Sondaggi');
    }

    /**
     *
     * @return string
     */
    public static function getModelSearchClassName()
    {
        return AmosSondaggi::instance()->model('SondaggiSearch');
    }

    /**
     *
     * @return string
     */
    public function getFrontEndMenu($dept = 1)
    {
        $menu = "";
        $app  = \Yii::$app;
        if ((is_null($app->user) || $app->user->id == $app->params['platformConfigurations']['guestUserId'])) {
            //$menu .= $this->addFrontEndMenu(AmosSondaggi::t('amossondaggi','Gestione sondaggi'), AmosSondaggi::toUrlModule('/sondaggi'));
        }else{
            $menu .= $this->addFrontEndMenu(AmosSondaggi::t('amossondaggi','#menu_front_sondaggi'), AmosSondaggi::toUrlModule('/sondaggi'));
        }
        return $menu;
    }

    /**
     * @return array
     */
    public function getIndexActions(){
        return [
            'sondaggi/index',
            'pubblicazione/all',
            'pubblicazione/own',
            'pubblicazione/own-interest',
            'pubblicazione/all-admin',

        ];
    }

    /**
     * @return array
     */
    public function defaultControllerIndexRoute()
    {
        return [
            'sondaggi' => '/sondaggi/pubblicazione/own-interest',
            'sondaggi-domande' => '/sondaggi/pubblicazione/own-interest',
            'sondaggi-domande-pagine' => '/sondaggi/pubblicazione/own-interest',
            'sondaggi-domande-tipologie' => '/sondaggi/pubblicazione/own-interest',
            'sondaggi-risposte' => '/sondaggi/pubblicazione/own-interest',
            'sondaggi-risposte-predefinite' => '/sondaggi/pubblicazione/own-interest',
            'pubblicazione' => '/sondaggi/pubblicazione/own-interest',
        ];
    }

    /**
     * @return array
     */
    public function defaultControllerIndexRouteSlogged()
    {
        return [
            'sondaggi' => '/sondaggi/pubblicazione/all',
            'sondaggi-domande' => '/sondaggi/pubblicazione/all',
            'sondaggi-domande-pagine' => '/sondaggi/pubblicazione/all',
            'sondaggi-domande-tipologie' => '/sondaggi/pubblicazione/all',
            'sondaggi-risposte' => '/sondaggi/pubblicazione/all',
            'sondaggi-risposte-predefinite' => '/sondaggi/pubblicazione/all',
            'pubblicazione' => '/sondaggi/pubblicazione/all',
        ];
    }


    /**
     * @return array
     */
    public function getControllerNames(){
        $names =  [
            'sondaggi' => self::t('amosdocumenti', "Sondaggi"),
            'pubblicazione'  => self::t('amosdocumenti', "Sondaggi"),
            'sondaggi-domande'  => self::t('amosdocumenti', "Sondaggi"),
            'sondaggi-domande-pagine'  => self::t('amosdocumenti', "Sondaggi"),
            'sondaggi-domande-tipologie'  => self::t('amosdocumenti', "Sondaggi"),
            'sondaggi-risposte'  => self::t('amosdocumenti', "Sondaggi"),
            'sondaggi-risposte-predefinite'  => self::t('amosdocumenti', "Sondaggi"),

        ];

        return $names;
    }
}
