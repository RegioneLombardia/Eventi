<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\events\controllers
 */

namespace open20\amos\events\controllers;

use open20\amos\events\models\base\EventRelated;
use open20\amos\events\models\EventHighlights;
use open20\amos\events\models\EventLanding;
use open20\amos\events\utility\EventsUtility;
use open20\amos\events\utility\OrderElementsUtility;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * Class EventRelatedController
 * This is the class for controller "EventRelatedController".
 * @package open20\amos\events\controllers
 */
class EventRelatedController extends \open20\amos\events\controllers\base\EventRelatedController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'order-related-events',
                        ],
                        'roles' => ['@']
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post', 'get'],
                ]
            ]
        ]);
    }

    /**
     * @param $id
     * @param $slider_id
     * @param $direction
     * @return \yii\web\Response
     */
    public function actionOrderRelatedEvents($id, $direction, $event_id)
    {

        $landing = EventLanding::find()->andWhere(['event_id' => $event_id])->one();
        if ($landing) {
            EventsUtility::updateLuyaPageTimestampForCache($landing);
        }
        $elements = EventRelated::find()->andWhere(['event_id' => $event_id])->orderBy('n_order ASC')->all();

        $orderList = [];
        foreach ($elements as $element) {
            $orderList [] = $element->id;
        }

        //find the element  in the ids array and move it up or down
        $indexElemToMove = array_search($id, $orderList);

        if ($direction == 'up') {
            $orderList = OrderElementsUtility::up($orderList, $indexElemToMove);
        } else {
            $orderList = OrderElementsUtility::down($orderList, $indexElemToMove);
        }
        //save the element with the new order
        $this->resetNumberOrder($orderList);

        return $this->redirect(['/events/event-dashboard/landing-manage-contents', 'id' => $event_id, '#' => 'container-related-events']);
    }


    /**
     * @param $orderList
     */
    public function resetNumberOrder($orderList)
    {
        $i = 1;
        foreach ($orderList as $id) {
            $containerElemMm = EventRelated::findOne($id);
            $containerElemMm->n_order = $i;
            $containerElemMm->save(false);
            $i++;
        }
    }


}
