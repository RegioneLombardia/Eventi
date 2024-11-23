<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\events\widgets
 * @category   CategoryName
 */

namespace open20\amos\events\widgets;

use open20\amos\comuni\models\IstatComuni;
use open20\amos\comuni\models\IstatProvince;
use open20\amos\core\forms\ShowUserTagsWidget;
use open20\amos\events\models\search\EventSearch;
use yii\base\Widget;
use yii\helpers\ArrayHelper;

/**
 * Class EventsShowUserTagsWidget
 * @package open20\amos\events\widgets
 */
class ComingNextEventWidget extends Widget
{
    public $onlyListView = false;
    public $tagPreference;

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function run()
    {
        $modelSearch = new EventSearch();
//        $defaultComune = $this->getDefaultComune();
//        $defaultComune = $this->getDefaultProvincia();
//        if(!empty($defaultComune)){
//            $modelSearch->currentComune = $defaultComune->id;
//        }
//        $dataComuni = $this->getComuni();

        \open20\amos\favorites\assets\FavoritesAsset::register($this->view);
        $dataProvider = $modelSearch->cmsPublishedSearch(\yii::$app->request->get());
        $filtersEnabled = $this->getFiltersSearchEnabled($modelSearch);


        if ($this->onlyListView) {
            return $this->render('coming_next_event_widget_list', [
                'dataProvider' => $dataProvider,
                'filtersEnabled' => $filtersEnabled,
                'tagPreference' => $this->tagPreference
            ]);
        } else {
            return $this->render('coming_next_event_widget', [
                'model' => $modelSearch,
                'dataProvider' => $dataProvider,
//                'dataComuni' => $dataComuni,
                'filtersEnabled' => $filtersEnabled,
                'tagPreference' => $this->tagPreference

            ]);
        }
    }

    /**
     * @return mixed|null
     * @throws \yii\base\InvalidConfigException
     */
    public function getDefaultComune()
    {
        $moduleEvents = \Yii::$app->getModule('events');
        $comuni = null;
        if (!empty($moduleEvents->showOnlyRegionInWizard)) {
            $comuni = IstatComuni::find()->andWhere(['nome' => 'Milano'])->one();
        }
        return $comuni;
    }

    /**
     * @param $modelSearch
     * @return array
     */
    public function getFiltersSearchEnabled($modelSearch)
    {
        $filtersEnabled = [];
        $cloneModelSearch = clone $modelSearch;
        $cloneModelSearch->enableCount = true;
        $arrayCounts = $cloneModelSearch->cmsPublishedSearch(\yii::$app->request->get());
        foreach ($arrayCounts as $elem) {
            $filtersEnabled [] = $elem['id'];
        }

        return $filtersEnabled;
    }

    /**
     * @return mixed|null
     * @throws \yii\base\InvalidConfigException
     */
    public function getDefaultProvincia()
    {
        $moduleEvents = \Yii::$app->getModule('events');
        $comuni = null;
        if (!empty($moduleEvents->showOnlyRegionInWizard)) {
            $comuni = IstatProvince::find()->andWhere(['nome' => 'Milano'])->one();
        }
        return $comuni;
    }

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function getComuni()
    {
        $moduleEvents = \Yii::$app->getModule('events');

        $comuni = IstatComuni::find();
        if (!empty($moduleEvents->showOnlyRegionInWizard)) {
            $comuni->andWhere(['istat_regioni_id' => $moduleEvents->showOnlyRegionInWizard])
                ->orderBy('istat_comuni.nome ASC');
        }
        $dataComuni = ArrayHelper::map($comuni->all(), 'id', 'nome');
        return $dataComuni;
    }

}
