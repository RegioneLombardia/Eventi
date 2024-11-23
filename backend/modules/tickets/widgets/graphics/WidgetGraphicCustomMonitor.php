<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\basic\template
 * @category   CategoryName
 */

namespace backend\modules\tickets\widgets\graphics;

use Yii;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use yii\db\Expression;
use yii\db\Query;
use open20\amos\admin\models\UserProfile;
use kartik\grid\GridView;

class WidgetGraphicCustomMonitor extends \open20\amos\core\widget\WidgetGraphic {

  /**
   * @inheritdoc
   */
  public function init() {
    parent::init();
    
    $this->setCode('test');
    $this->setLabel(\Yii::t('amosapp', 'Monitoraggio (real-time)'));
    $this->setDescription(\Yii::t('amosapp', 'Riassume alcune informazioni di monitoraggio'));
  }

  /**
   * 
   * @return type
   */
  public function partecipantiPiattaforma() {
    $condizioneA = '';
    $condizioneB = '';

    $utentiTotali = UserProfile::find();
//    if (strlen($condizioneA) > 0) {
//      $utentiTotali->andWhere($condizioneA);
//    }
//    if (strlen($condizioneB) > 0) {
//      $utentiTotali->andWhere($condizioneB);
//    }
    
    $utentiSenzaDisattQ = UserProfile::find()->andWhere(['attivo' => 1]);
//    if (strlen($condizioneA) > 0) {
//      $utentiSenzaDisattQ->andWhere($condizioneA);
//    }
//    if (strlen($condizioneB) > 0) {
//      $utentiSenzaDisattQ->andWhere($condizioneB);
//    }
    
//    $utentiSenzaDisatt = $utentiSenzaDisattQ->count();
    
    $utentiAttivi = UserProfile::find()->andWhere(['validato_almeno_una_volta' => 1]);
//    if (strlen($condizioneA) > 0) {
//      $utentiAttivi->andWhere($condizioneA);
//    }
//    if (strlen($condizioneB) > 0) {
//      $utentiAttivi->andWhere($condizioneB);
//    }
    
    $utentiAttivi2 = clone $utentiAttivi;
    $utentiAttivi3 = clone $utentiAttivi;
    $utentiAttivi4 = clone $utentiAttivi;
    
    
//    $collaborazioni = UserProfile::find()->innerJoin('organizations', 'prevalent_partnership_id = organizations.id');
//    if (strlen($condizioneA) > 0) {
//      $collaborazioni->andWhere($condizioneA);
//    }
//    if (strlen($condizioneB) > 0) {
//      $collaborazioni->andWhere($condizioneB);
//    }
    
    $collaborazioni = UserProfile::find()->innerJoin('organizations', 'prevalent_partnership_id = organizations.id');
    
    $collaborazioni2 = clone $collaborazioni;
    $collaborazioni3 = clone $collaborazioni;
    $collaborazioni4 = clone $collaborazioni;
    
    $collaborazioni2->andWhere(['IN', 'enterprise', ['yes']]);
    $collaborazioni3->andWhere(['IN', 'research', ['yes']]);
    $collaborazioni4->andWhere(new Expression("pa IN ('yes') OR citizens IN ('yes')"));

    $utentiCountAttivi = $utentiAttivi->count();
    $utentiMaschiAttivi = $utentiAttivi2->andWhere(['sesso' => 'Maschio'])->count();
    $utentiFemmineAttivi = $utentiAttivi3->andWhere(['sesso' => 'Femmina'])->count();
    $utentiNdAttivi = $utentiAttivi4->andWhere(['sesso' => ''])->count();

    $organizzazioni = \openinnovation\organizations\models\Organizations::find();
    $news = \open20\amos\news\models\News::find();
    $discussioni = \open20\amos\discussioni\models\DiscussioniTopic::find();
    $community = \open20\amos\community\models\Community::find();

    $ritorno = [];

    $ritorno[] = [\Yii::t('monitors', 'Numero di partecipanti registrati'), floatval($utentiTotali->count())];
    $ritorno[] = [\Yii::t('monitors', 'Numero di partecipanti registrati al netto di successive disattivazioni'), floatval($utentiSenzaDisattQ->count())];
    
    /* $ritorno[] = [\Yii::t('monitors', 'Numero di partecipanti attivi (validati almeno una volta)'), floatval($utentiCountAttivi)];
      $ritorno[] = [\Yii::t('monitors', ' % maschi'), round(floatval((!$utentiCountAttivi ? 0 : (bcdiv(bcmul($utentiMaschiAttivi,
      100, 4), $utentiCountAttivi, 4)))), 2)];
      $ritorno[] = [\Yii::t('monitors', ' % femmine'), round(floatval((!$utentiCountAttivi ? 0 : (bcdiv(bcmul($utentiFemmineAttivi,
      100, 4), $utentiCountAttivi, 4)))), 2)];
      $ritorno[] = [\Yii::t('monitors', ' % non dichiarato'), round(floatval((!$utentiCountAttivi ? 0 : (bcdiv(bcmul($utentiNdAttivi,
      100, 4), $utentiCountAttivi, 4)))), 2)]; */
    
    $ritorno[] = [
      \Yii::t(
        'monitors',
        'Numero di partecipanti registrati che dichiarano con quale organizzazione collaborano in via prevalente'
      ),
      floatval($collaborazioni->count())
    ];
    
    $ritorno[] = [
      \Yii::t(
        'monitors',
        ' di cui con università, centri di ricerca e trasferimento tecnologico, laboratori altri soggetti con autonoma capacità di ricerca (alto)'
      ),
      floatval($collaborazioni3->count())];
    
    $ritorno[] = [\Yii::t('monitors', ' di cui con aziende (alto)'), floatval($collaborazioni2->count())];
    $ritorno[] = [\Yii::t(
      'monitors',
      ' di cui con altro (enti intermedi, associazioni, pubbliche amministrazioni locali etc... (alto)'), 
      floatval($collaborazioni4->count())
    ];
    
    $ritorno[] = [\Yii::t('monitors', 'Numero di organizzazioni registrate'), floatval($organizzazioni->count())];
    $ritorno[] = [\Yii::t('monitors', 'Numero community'), floatval($community->count())];
    $ritorno[] = [\Yii::t('monitors', 'Numero news pubblicate'), floatval($news->count())];
    $ritorno[] = [\Yii::t('monitors', 'Numero discussioni pubblicate'), floatval($discussioni->count())];
      
    $dati = [];
    $dati[] = [\Yii::t('monitors', 'Tipologia partecipanti attivi'), \Yii::t('monitors', 'N. partecipanti')];
    $dati[] = [\Yii::t('monitors', 'Maschi'), floatval($utentiMaschiAttivi)];
    $dati[] = [\Yii::t('monitors', 'Femmine'), floatval($utentiFemmineAttivi)];
    $dati[] = [\Yii::t('monitors', 'Non dichiarato'), floatval($utentiNdAttivi)];

    $dataProvider = new \yii\data\ArrayDataProvider([
      'allModels' => $ritorno,
      'pagination' => [
        'pageSize' => 10
      ]
    ]);

    return [$dati, $dataProvider];
  }

  /**
   * @inheritdoc
   */
  public function getHtml() {
    $dati = $this->partecipantiPiattaforma();
    $dataProvider = $dati[1];

    if (!is_null(Yii::$app->getModule('layout'))) {
      return $this->render(
        'monitoraggio_realtime',
        [
          'dataProvider' => $dataProvider,
        ]
      ); 
    }
    
    return '';
  }

}