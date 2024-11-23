<?php

namespace open20\amos\events\models;


use open20\amos\events\AmosEvents;
use open20\amos\tag\models\Tag;
use yii\base\Model;
use yii\data\ArrayDataProvider;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\db\Query;
use yii\web\JsExpression;

class DataAnalyzerForm extends Model
{
    const MONTHLY_LIMIT = 3;
    /**
     * @var integer
     */
    public $event_type_id;

    /**
     * @var
     */
    public $event_group_referent_id;

    /**
     * @var string
     */
    public $date_interval;

    /**
     * @var string
     */
    public $start_date;

    /**
     * @var string
     */
    public $end_date;

    /**
     * @var
     */
    public $access_type;

    /**
     * @var
     */
    public $tagPreference;

    protected $isMonthly = false;
    protected $isDaily = false;
    protected $nMonth;


    const INTERVAL_CURRENT_MONTH = 1;
    const INTERVAL_LAST_MONTH = 2;
    const INTERVAL_3_MONTH_AGO = 3;
    const INTERVAL_NEXT_MONTH = 4;
    const INTERVAL_NEXT_3_MONTH = 5;
    const INTERVAL_PERSONALIZED = 6;
    const INTERVAL_6_MONTH_AGO = 7;
    //
    const INTERVAL_TODAY = 8;
    const INTERVAL_YERSTERDAY = 9;
    const INTERVAL_LAST_WEEK = 10;
    const INTERVAL_LAST_7_DAYS = 11;
    const INTERVAL_LAST_30_DAYS = 12;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['access_type', 'tagPreference', 'event_group_referent_id', 'start_date', 'end_date', 'date_interval', 'event_type_id'], 'safe']
        ];
    }

    public static function getIntervalsList($type = 'standard')
    {
        if ($type == 'standard') {
            return [
                self::INTERVAL_CURRENT_MONTH => AmosEvents::t('amosevents', "Mese corrente"),
                self::INTERVAL_3_MONTH_AGO => AmosEvents::t('amosevents', "3 mesi precedenti"),
                self::INTERVAL_LAST_MONTH => AmosEvents::t('amosevents', "Mese precedente"),
                self::INTERVAL_NEXT_MONTH => AmosEvents::t('amosevents', "Prossimo mese"),
                self::INTERVAL_NEXT_3_MONTH => AmosEvents::t('amosevents', "Prossimi 3 mesi"),
                self::INTERVAL_PERSONALIZED => AmosEvents::t('amosevents', "Personalizzato")
            ];
        } else if ($type == 'user_access') {
            return [
                self::INTERVAL_TODAY => AmosEvents::t('amosevents', "Oggi"),
                self::INTERVAL_YERSTERDAY => AmosEvents::t('amosevents', "Ieri"),
                self::INTERVAL_LAST_MONTH => AmosEvents::t('amosevents', "Mese precedente"),
                self::INTERVAL_LAST_7_DAYS => AmosEvents::t('amosevents', "Ultimi 7 giorni"),
                self::INTERVAL_LAST_30_DAYS => AmosEvents::t('amosevents', "Ultimi 30 giorni"),
                self::INTERVAL_PERSONALIZED => AmosEvents::t('amosevents', "Personalizzato")
            ];
        } else if ($type == 'past') {
            return [
                self::INTERVAL_CURRENT_MONTH => AmosEvents::t('amosevents', "Mese corrente"),
                self::INTERVAL_LAST_MONTH => AmosEvents::t('amosevents', "Mese precedente"),
                self::INTERVAL_3_MONTH_AGO => AmosEvents::t('amosevents', "3 mesi precedenti"),
                self::INTERVAL_6_MONTH_AGO => AmosEvents::t('amosevents', "6 mesi precedenti"),
                self::INTERVAL_PERSONALIZED => AmosEvents::t('amosevents', "Personalizzato")
            ];
        } else {
            return [
                self::INTERVAL_CURRENT_MONTH => AmosEvents::t('amosevents', "Mese corrente"),
                self::INTERVAL_3_MONTH_AGO => AmosEvents::t('amosevents', "3 mesi precedenti"),
                self::INTERVAL_LAST_MONTH => AmosEvents::t('amosevents', "Mese precedente"),
                self::INTERVAL_NEXT_MONTH => AmosEvents::t('amosevents', "Prossimo mese"),
                self::INTERVAL_NEXT_3_MONTH => AmosEvents::t('amosevents', "Prossimi 3 mesi"),
                self::INTERVAL_PERSONALIZED => AmosEvents::t('amosevents', "Personalizzato")
            ];
        }
    }

    private function getIntervalParams()
    {
        $startDate = null;
        $endDate = null;
        // Setting start date and end date based on Google Analytics requirement, depending on search interval
        switch ($this->date_interval) {
            case  self::INTERVAL_CURRENT_MONTH:
                $startDate = date('Y-m-d', strtotime('first day of this month'));
                $endDate = date('Y-m-d', strtotime('last day of this month'));
                break;
            case self::INTERVAL_3_MONTH_AGO:
                $startDate = date('Y-m-d', strtotime('first day of -3 month'));
                $endDate = date('Y-m-d', strtotime('last day of last month'));
                break;
            case self::INTERVAL_6_MONTH_AGO:
                $startDate = date('Y-m-d', strtotime('first day of -6 month'));
                $endDate = date('Y-m-d', strtotime('last day of last month'));
                break;
            case self::INTERVAL_LAST_MONTH:
                $startDate = date('Y-m-d', strtotime('first day of last month'));
                $endDate = date('Y-m-d', strtotime('last day of last month'));
                break;
            case self::INTERVAL_NEXT_MONTH:
                $startDate = date('Y-m-d', strtotime('first day of next month'));
                $endDate = date('Y-m-d', strtotime('last day of next month'));
                break;
            case self::INTERVAL_NEXT_3_MONTH:
                $startDate = date('Y-m-d', strtotime('first day of +3 month'));
                $endDate = date('Y-m-d', strtotime('last day of next month'));
                break;
            //
            case self::INTERVAL_YERSTERDAY:
                $startDate = date('Y-m-d', strtotime('yesterday'));
                $endDate = date('Y-m-d', strtotime('yesterday'));
                break;
            case self::INTERVAL_TODAY:
                $startDate = date('Y-m-d', strtotime('today'));
                $endDate = date('Y-m-d', strtotime('today'));
                break;
            case self::INTERVAL_LAST_7_DAYS:
                $startDate = date('Y-m-d', strtotime('today -7 days'));
                $endDate = date('Y-m-d', strtotime('today'));
                break;
            case self::INTERVAL_LAST_30_DAYS:
                $startDate = date('Y-m-d', strtotime('today -30 days'));
                $endDate = date('Y-m-d', strtotime('today'));
                break;
            case self::INTERVAL_PERSONALIZED:
            default:
                $startDate = $this->start_date;
                $endDate = $this->end_date;
                break;
        }

        if ($startDate == $endDate) {
            $startTime = new \DateTime($startDate);
            $endTime = new \DateTime($startDate);

            $startTime->setTime(0, 0);
            $endTime->setTime(23, 59);
            $startDate = $startTime->format('Y-m-d H:i:s');
            $endDate = $endTime->format('Y-m-d H:i:s');
        }

        return ['startDate' => $startDate, 'endDate' => $endDate];
    }


    /**
     * @param $stats
     * @return JsExpression
     */
    public function formatDateForChart($stats)
    {
        if ($this->isDaily) {
            $xpr = new JsExpression("new Date(" . $stats['year'] . "," . ($stats['month'] - 1) . "," . $stats['day'] . "," . $stats['hour'] . ")");
        } else {
            $xpr = new JsExpression("new Date(" . $stats['year'] . "," . ($stats['month'] - 1) . "," . $stats['day'] . ")");
        }
        return $xpr;
    }

    /**
     * @param $query Query
     * @return array
     */
    private function baseStatistic($query, $groupBy = null)
    {
        $intervalParams = $this->getIntervalParams();

        if (!empty($intervalParams['startDate']) && !empty($intervalParams['endDate'])) {
            $query->andWhere(['>=', 'begin_date_hour', $intervalParams['startDate']]);
            $query->andWhere(['<=', 'begin_date_hour', $intervalParams['endDate']]);
        }

        $queryTot = clone $query;
        if ($groupBy) {
            $query->groupBy($groupBy);
        }

        $result = $query->all();
        $count = $queryTot->count();

        return [
            'result' => $result,
            'tot' => $count
        ];
    }

    /**
     * @param $query Query
     * @param string $dateField
     * @param string $labelHAxis
     * @return array
     */
    public function basicChartStatistics($query, $dateField = 'begin_date_hour', $labelHAxis = 'N. eventi')
    {
        $intervalParams = $this->getIntervalParams();
        $maxDate = new \DateTime($intervalParams['endDate']);
        $minDate = new \DateTime($intervalParams['startDate']);

        $diffDate = $maxDate->diff($minDate);
        $nMonth = (12 * $diffDate->y) + $diffDate->m;
        if ($maxDate->format('Y-m-d') == $minDate->format('Y-m-d')) {
            $this->isDaily = true;
        }
        if ($nMonth > self::MONTHLY_LIMIT) {
            $this->nMonth = $nMonth;
            $this->isMonthly = true;
        }

        if (!empty($intervalParams['startDate']) && !empty($intervalParams['endDate'])) {
            $query->andWhere(['>=', $dateField, $intervalParams['startDate']]);
            $query->andWhere(['<=', $dateField, $intervalParams['endDate']]);
        }

        if ($this->isDaily) {
            $query->select(new Expression("count(*) as 'count', YEAR($dateField) as 'year', MONTH($dateField) as 'month', DAY($dateField) as 'day', HOUR($dateField) as 'hour', DATE_FORMAT($dateField, '%d/%m/%Y') as 'fulldate'"));
            $query->groupBy('year, month, day, hour');

        } else if ($this->isMonthly) {
            $query->select(new Expression("count(*) as 'count', YEAR($dateField) as 'year', MONTH($dateField) as 'month', 1 as 'day'"));
            $query->groupBy('year, month, day');

        } else {
            $query->select(new Expression("count(*) as 'count', YEAR($dateField) as 'year', MONTH($dateField) as 'month', DAY($dateField) as 'day', DATE_FORMAT($dateField, '%d/%m/%Y') as 'fulldate'"));
            $query->groupBy('year, month, day');

        }

        $statistics = [['Linea temporale', $labelHAxis]];

        //Punto di partenza  -> punto partenza e finale servono a google per calcolare l'intervallo
        $jsMinDate = $this->formatDateForChart(['year' => $minDate->format('Y'), 'month' => $minDate->format('m'), 'day' => $minDate->format('d'), 'hour' => $minDate->format('H')]);
        array_push($statistics, [$jsMinDate, 0]);

        $tmp = [];
//        pr($query->createCommand()->rawSql);
        foreach ($query->all() as $stats) {
            $count = $stats['count'];
            $tmp[$stats['day']] = $stats;
            array_push($statistics, [$this->formatDateForChart($stats), $count]);
        }
        //Punto finale
        $jsMaxDate = $this->formatDateForChart(['year' => $maxDate->format('Y'), 'month' => $maxDate->format('m'), 'day' => $maxDate->format('d'), 'hour' => $maxDate->format('H')]);
        array_push($statistics, [$jsMaxDate, 0]);

//        pr($statistics);
        return $statistics;

    }


    /**
     * @return array
     */
    public function eventDgStatistics()
    {
        $group = EventGroupReferent::findOne($this->event_group_referent_id);
        $query = new Query;
        $query->select("event_group_referent.denominazione, count(*) as 'count'")
            ->from('event_group_referent')
            ->leftJoin('event', 'event.event_group_referent_id = event_group_referent.id');

        if ($group && $this->event_group_referent_id != 'all') {
            $query->andWhere(['event_group_referent.id' => $this->event_group_referent_id]);
        }

        $results = $this->baseStatistic($query, 'event_group_referent_id');

        //aggiungo riga di totale
        $tmp = $results['result'];
        array_push($tmp, ['denominazione' => 'Totale', 'count' => $results['tot']]);
        $results['result'] = $tmp;

        return $results;
    }

    /**
     * @return array
     */
    public function eventDelegationsStatistics()
    {
        $group = EventGroupReferent::findOne($this->event_group_referent_id);
        $query = new Query;
        $query->select("event_group_referent.denominazione, count(*) as 'count'")
            ->from('event_group_referent')
            ->leftJoin('event', 'event.event_group_referent_id = event_group_referent.id');

        // Delegated events only
        $query->andWhere(['event.is_delegated_event' => true]);

        if ($group && $this->event_group_referent_id != 'all') {
            $query->andWhere(['event_group_referent.id' => $this->event_group_referent_id]);
        }

        $results = $this->baseStatistic($query, 'event_group_referent_id');

        //aggiungo riga di totale
        $tmp = $results['result'];
        array_push($tmp, ['denominazione' => 'Totale', 'count' => $results['tot']]);
        $results['result'] = $tmp;

        return $results;
    }


    /**
     * @return array
     */
    public function eventDgChartStatistics()
    {
        $group = EventGroupReferent::findOne($this->event_group_referent_id);

        $query = new Query;
        $query->from('event_group_referent')
            ->leftJoin('event', 'event.event_group_referent_id = event_group_referent.id')
            ->groupBy('event_group_referent.id')
            ->orderBy('begin_date_hour ASC');

        if ($group && $this->event_group_referent_id != 'all') {
            $query->andWhere(['event_group_referent_id' => $this->event_group_referent_id]);
        }

        $statistics = $this->basicChartStatistics($query);

        return $statistics;
    }

    /**
     * @return array
     */
    public function eventDelegationsChartStatistics()
    {
        $group = EventGroupReferent::findOne($this->event_group_referent_id);

        $query = new Query;
        $query->from('event_group_referent')
            ->leftJoin('event', 'event.event_group_referent_id = event_group_referent.id')
            ->groupBy('event_group_referent.id')
            ->orderBy('begin_date_hour ASC');

        // Delegated events only
        $query->andWhere(['event.is_delegated_event' => true]);

        if ($group && $this->event_group_referent_id != 'all') {
            $query->andWhere(['event_group_referent_id' => $this->event_group_referent_id]);
        }

        $statistics = $this->basicChartStatistics($query);

        return $statistics;
    }


    /**
     * @return array
     */
    public function eventTypeStatistics()
    {
        $type = EventType::findOne($this->event_type_id);

        $query = new Query;
        $query->select("event_type.title, count(*) as 'count'")
            ->from('event_type')
            ->leftJoin('event', 'event.event_type_id = event_type.id');

        if ($type && $this->event_type_id != 'all') {
            $query->andWhere(['event_type_id' => $this->event_type_id]);
        }

        $results = $this->baseStatistic($query, 'event_type.id');

        //aggiungo riga di totale
        $tmp = $results['result'];
        array_push($tmp, ['title' => 'Totale', 'count' => $results['tot']]);
        $results['result'] = $tmp;

        return $results;
    }

    /**
     * @return array
     */
    public function eventTypeChartStatistics()
    {
        $type = EventType::findOne($this->event_type_id);
        $query = new Query;

        $query
            ->from('event_type')
            ->leftJoin('event', 'event.event_type_id = event_type.id')
            ->groupBy('event_type.id')
            ->orderBy('begin_date_hour ASC');

        if ($type && $this->event_type_id != 'all') {
            $query->andWhere(['event_type_id' => $this->event_type_id]);
        }

        $statistics = $this->basicChartStatistics($query);
//        pr($statistics);
        return $statistics;

    }


    /**
     * @return array
     */
    public function eventPreferenceTagsStatistics()
    {
        $root = Tag::find()->andWhere(['codice' => Event::ROOT_TAG_PREFERENCE_CENTER])->one();


        $query = new Query;
        $query->select("tag.nome, count(*) as 'count'")
            ->from('tag')
            ->leftJoin('entitys_tags_mm as tag_preference', 'tag_preference.tag_id = tag.id')
            ->leftJoin("event", 'tag_preference.record_id = event.id')
            ->andWhere(['tag_preference.classname' => Event::className()])
            ->andWhere(['tag_preference.deleted_at' => null])
            ->andWhere(['tag_preference.root_id' => $root->id]);

        if ($this->tagPreference != 'all') {
            $query->andWhere(['tag_preference.tag_id' => $this->tagPreference]);
        }

        $results = $this->baseStatistic($query, 'tag.id');

        //aggiungo riga di totale
        $tmp = $results['result'];
        array_push($tmp, ['nome' => 'Totale', 'count' => $results['tot']]);
        $results['result'] = $tmp;

        return $results;
    }

    /**
     * @return array
     */
    public function eventPreferenceTagsChartStatistics()
    {
        $root = Tag::find()->andWhere(['codice' => Event::ROOT_TAG_PREFERENCE_CENTER])->one();

        $query = new Query;

        $query
            ->from('tag')
            ->leftJoin('entitys_tags_mm as tag_preference', 'tag_preference.tag_id = tag.id')
            ->leftJoin("event", 'tag_preference.record_id = event.id')
            ->andWhere(['tag_preference.classname' => Event::className()])
            ->andWhere(['tag_preference.deleted_at' => null])
            ->andWhere(['tag_preference.root_id' => $root->id])
            ->groupBy('tag.id')
            ->orderBy('begin_date_hour ASC');


        if ($this->tagPreference != 'all') {
            $query->andWhere(['tag_preference.tag_id' => $this->tagPreference]);
        }

        $statistics = $this->basicChartStatistics($query);
//        pr($statistics);
        return $statistics;

    }

    /**
     * @return array
     */
    public function eventParticipantstatistics()
    {
        $group = EventGroupReferent::findOne($this->event_group_referent_id);

        //REGISTRATI
        $query = new Query;
        $query->select(new Expression("event_type.title, count(*) as 'count_reg', 0 as 'count_inv', event_type.id as 'event_type_id'"))
            ->from('event_type')
            ->leftJoin('event', 'event.event_type_id = event_type.id')
            ->leftJoin('event_invitation', 'event_invitation.event_id = event.id')
            ->leftJoin('user_profile', 'event_invitation.user_id = user_profile.user_id')
            ->andWhere(['user_profile.attivo' => 1])
            ->andWhere(['event_invitation.deleted_at' => null])
            ->andWhere(['!=', 'event_invitation.user_id', new Expression('event.created_by')])
            ->andWhere(['!=', 'event.event_type_id', 100])
            ->andWhere(['<=', 'event.end_date_hour', new Expression('NOW()')]);

        if ($group && $this->event_group_referent_id != 'all') {
            $query->andWhere(['event.event_group_referent_id' => $this->event_group_referent_id]);
        }

        $intervalParams = $this->getIntervalParams();
        if (!empty($intervalParams['startDate']) && !empty($intervalParams['endDate'])) {
            $query->andWhere(['>=', 'begin_date_hour', $intervalParams['startDate']]);
            $query->andWhere(['<=', 'begin_date_hour', $intervalParams['endDate']]);
        }
        $query->groupBy('event_type.id');


        // INVITATI
        $query2 = new Query;
        $query2->select(new Expression("event_type.title, 0 as 'count_reg', count(*) as 'count_inv', event_type.id as 'event_type_id'"))
            ->from('event_type')
            ->innerJoin('event', 'event.event_type_id = event_type.id')
            ->innerJoin('event_internal_list', 'event_internal_list.event_id = event.id')
            ->innerJoin('event_invitation_sent', 'event_invitation_sent.event_internal_list_id = event_internal_list.id')
            ->andWhere(['event_internal_list.template' => ['registration_email', 'webmeeting_webex']])
            ->andWhere(['!=', 'event.event_type_id', 100])
            ->andWhere(['<=', 'event.end_date_hour', new Expression('NOW()')]);


        if ($group && $this->event_group_referent_id != 'all') {
            $query2->andWhere(['event.event_group_referent_id' => $this->event_group_referent_id]);
        }
        $intervalParams = $this->getIntervalParams();
        if (!empty($intervalParams['startDate']) && !empty($intervalParams['endDate'])) {
            $query2->andWhere(['>=', 'begin_date_hour', $intervalParams['startDate']]);
            $query2->andWhere(['<=', 'begin_date_hour', $intervalParams['endDate']]);
        }
        $query2->groupBy('event_type.id');

        // serve per tirar fuori sempre tutto l'elenco delle tipologie
        $query3 = new Query;
        $query3->select(new Expression("title, 0 as 'count_reg', 0 as 'count_inv', event_type.id as 'event_type_id'"))
            ->from('event_type');

        // UNION (REGISTRATI + INVITATI + ELENCO TIPOLOGIA (con count 0))
        $queryComplete = new Query;
        $queryComplete->select("title, SUM(count_reg) as 'tot_reg', SUM(count_inv) as 'tot_inv'")
            ->from($query->union($query2)->union($query3))
            ->groupBy('event_type_id');
        $result = $queryComplete->all();

        // calcolo totale
        $totReg = 0;
        $totInv = 0;
        foreach ($result as $elem) {
            $totReg += $elem['tot_reg'];
            $totInv += $elem['tot_inv'];
        }

        //aggiungo riga di totale
        array_push($result, ['title' => 'Totale', 'tot_reg' => $totReg, 'tot_inv' => $totInv]);

        return $result;
    }

    /**
     * @return array
     */
    public function eventParticipantsChartStatistics($results)
    {
        $statistics = [['Tipologia evento', 'Partecipanti', 'Invitati']];
        foreach ($results as $row) {
            array_push($statistics, [$row['title'], (integer)$row['tot_reg'], (integer)$row['tot_inv']]);
        }
//        pr($statistics);
        return $statistics;

    }

    /**
     * @return array
     */
    public function eventTypeParticipantsStatistics()
    {
        $group = EventGroupReferent::findOne($this->event_group_referent_id);

        $query = new Query;
        $query->select("event_type.title, count(*) as 'count'")
            ->from('event_type')
            ->leftJoin('event', 'event.event_type_id = event_type.id');

        if ($group && $this->event_group_referent_id != 'all') {
            $query->andWhere(['event.event_group_referent_id' => $this->event_group_referent_id]);
        }

        $results = $this->baseStatistic($query, 'event_type.id');


        return $results;
    }


    /**
     * @return array
     */
    public function userAccessStatistics()
    {
        $this->setDailyOrMonthlyForStatistics();
        $query = new Query;
        $query->from('user_access_log');

        if (\Yii::$app->controller->action->id == 'user-access-frontend') {
            $query->andWhere(['be_or_fe' => 'frontend']);
        } else {
            $query->andWhere(['OR', ['be_or_fe' => 'backend'], ['be_or_fe' => null]]);
        }


        if ($this->access_type != 'all') {
            if ($this->isDaily) {
                $query->select(new Expression("count(*) as 'count', YEAR(user_access_log.created_at) as 'year', MONTH(user_access_log.created_at) as 'month', DAY(user_access_log.created_at) as 'day', HOUR(user_access_log.created_at) as 'hour', DATE_FORMAT(user_access_log.created_at, '%d/%m/%Y') as 'fulldate'"));
                $query->groupBy('year, month, day, hour');
                $totRow = ['count' => 0, 'year' => '', 'month' => '', 'day' => '', 'hour' => '', 'fulldate' => 'tot'];

            } else if ($this->isMonthly) {
                $query->select(new Expression("count(*) as 'count', YEAR(user_access_log.created_at) as 'year', MONTH(user_access_log.created_at) as 'month', 1 as 'day', DATE_FORMAT(user_access_log.created_at, '1/%m/%Y') as 'fulldate'"));
                $query->groupBy('year, month, day');
                $totRow = ['count' => 0, 'year' => '', 'month' => '', 'day' => '', 'fulldate' => 'tot'];


            } else {
                $query->select(new Expression("count(*) as 'count', YEAR(user_access_log.created_at) as 'year', MONTH(user_access_log.created_at) as 'month', DAY(user_access_log.created_at) as 'day', DATE_FORMAT(user_access_log.created_at, '%d/%m/%Y') as 'fulldate'"));
                $query->groupBy('year, month, day');
                $totRow = ['count' => 0, 'year' => '', 'month' => '', 'day' => '', 'fulldate' => 'tot'];

            }
        } else {
            $query
                ->groupBy('user_access_log.access_method')
                ->select("user_access_log.access_type , user_access_log.access_method , count(*) as 'count'");
            $totRow = ['access_type' => '', 'access_method' => 'tot', 'count' => 0];

        }

        $intervalParams = $this->getIntervalParams();

        if (!empty($intervalParams['startDate']) && !empty($intervalParams['endDate'])) {
            $query->andWhere(['>=', 'user_access_log.created_at', $intervalParams['startDate']]);
            $query->andWhere(['<=', 'user_access_log.created_at', $intervalParams['endDate']]);
        }

        $queryTot = clone $query;
        if ($this->access_type != 'all') {
            $query->andWhere(['user_access_log.access_method' => $this->access_type]);
            $queryTot->andWhere(['user_access_log.access_method' => $this->access_type]);
            $queryTot->groupBy(null);
        } else {
            $queryTot->groupBy(null);
        }

        $result = $query->all();

        //aggiungo riga di totale
        $count = $queryTot->count();
        $totRow['count'] = $count;
        array_push($result, $totRow);

        return [
            'result' => $result,
            'tot' => $count
        ];
    }


    /**
     * @return array
     */
    public function userAccessChartStatistics()
    {
        $intervalParams = $this->getIntervalParams();
        $maxDate = new \DateTime($intervalParams['endDate']);
        $minDate = new \DateTime($intervalParams['startDate']);

        $diff = $maxDate->diff($minDate);
        if ($maxDate->format('Y-m-d') == $minDate->format('Y-m-d')) {
            $this->isDaily = true;
        }
        if ($diff->m > self::MONTHLY_LIMIT) {
            $this->isMonthly = true;
        }

        //Query base
        $query = new Query;
        $query->from('user_access_log')
            ->orderBy('created_at ASC');

        if (\Yii::$app->controller->action->id == 'user-access-frontend') {
            $query->andWhere(['be_or_fe' => 'frontend']);
        } else {
            $query->andWhere(['OR', ['be_or_fe' => 'backend'], ['be_or_fe' => null]]);
        }
        if ($this->access_type != 'all') {
            $query->andWhere(['user_access_log.access_method' => $this->access_type]);
        }

        $statistics = $this->basicChartStatistics($query, 'created_at', 'N. accessi');
        return $statistics;

    }

    /**
     * @return bool
     */
    public function getIsMonthly()
    {
        return $this->isMonthly;
    }

    /**
     * @return bool
     */
    public function getNMonth()
    {
        return $this->nMonth;
    }


    /**
     * @return ArrayDataProvider
     */
    public static function inactiveUsersSex()
    {
        //        $query->select(new Expression("SUM(IF(user_profile.sesso = 'Maschio', 1, 0)) as maschio,
//        SUM(IF(user_profile.sesso = 'Femmina', 1, 0)) as femmina,
//        SUM(IF(user_profile.sesso = 'None', 1, 0)) as nondefinito,
//        COUNT(*) as tot
//        "))
//            ->from('user')
//            ->innerJoin('user_profile', 'user.id = user_profile.user_id')
//            ->innerJoin('event_inactive_users_mailup', 'event_inactive_users_mailup.email = user.email');
        $query = new Query();
        $query->select(new Expression("sesso as label, COUNT(*) as n"))
            ->from('user')
            ->innerJoin('user_profile', 'user.id = user_profile.user_id')
            ->innerJoin('event_inactive_users_mailup', 'event_inactive_users_mailup.email = user.email')
            ->groupBy('sesso');

        $res = $query->all();

        //aggiungo riga totale
        $tot = 0;
        foreach ($res as $row) {
            $tot += $row['n'];
        }
        array_push($res, ['label' => 'Totale', 'n' => $tot]);

        $dataProvider = new ArrayDataProvider([
            'allModels' => $res
        ]);

        return $dataProvider;
    }


    /**
     * @return ArrayDataProvider
     */
    public static function inactiveUsersAge()
    {
        $query2 = new Query();
        $query2->select(new Expression("count(*) as n, user_profile_age_group.age_group as label"))
            ->from('user')
            ->innerJoin('user_profile', 'user.id = user_profile.user_id')
            ->leftJoin('user_profile_age_group', 'user_profile_age_group.id = user_profile.user_profile_age_group_id')
            ->innerJoin('event_inactive_users_mailup', 'event_inactive_users_mailup.email = user.email')
            ->groupBy('user_profile.user_profile_age_group_id');

        $res = $query2->all();

        //aggiungo riga totale
        $tot = 0;
        foreach ($res as $row) {
            $tot += $row['n'];
        }
        array_push($res, ['label' => 'Totale', 'n' => $tot]);

        $dataProvider = new ArrayDataProvider([
            'allModels' => $res
        ]);
        return $dataProvider;

    }

    /**
     * @return ArrayDataProvider
     */
    public static function inactiveUsersProvince()
    {
        $query3 = new Query();
        $query3->select(new Expression("count(*) as n, istat_province.nome as label"))
            ->from('user')
            ->innerJoin('user_profile', 'user.id = user_profile.user_id')
            ->leftJoin('istat_province', 'istat_province.id = user_profile.nascita_province_id')
            ->innerJoin('event_inactive_users_mailup', 'event_inactive_users_mailup.email = user.email')
            ->groupBy('user_profile.nascita_province_id');

        $res = $query3->all();

        //aggiungo riga totale
        $tot = 0;
        foreach ($res as $row) {
            $tot += $row['n'];
        }
        array_push($res, ['label' => 'Totale', 'n' => $tot]);
        $dataProvider = new ArrayDataProvider([
            'allModels' => $res
        ]);

        return $dataProvider;
    }

    /**
     * @return array
     */
    public function registrationUsersStatistics()
    {
        $this->setDailyOrMonthlyForStatistics();

        $query = new Query;
        $query->from('user_profile')
            ->leftJoin('event_group_referent', 'event_group_referent.id = user_profile.dg_of_registration');

        if ($this->event_group_referent_id != 'all') {
            if ($this->isDaily) {
                $query->select(new Expression("count(*) as 'count', YEAR(user_profile.created_at) as 'year', MONTH(user_profile.created_at) as 'month', DAY(user_profile.created_at) as 'day', HOUR(user_profile.created_at) as 'hour', DATE_FORMAT(user_profile.created_at, '%d/%m/%Y') as 'fulldate'"));
                $query->groupBy('year, month, day, hour');
                $totRow = ['count' => 0, 'year' => '', 'month' => '', 'day' => '', 'hour' => '', 'fulldate' => 'tot'];

            } else if ($this->isMonthly) {
                $query->select(new Expression("count(*) as 'count', YEAR(user_profile.created_at) as 'year', MONTH(user_profile.created_at) as 'month', 1 as 'day', DATE_FORMAT(user_profile.created_at, '1/%m/%Y') as 'fulldate'"));
                $query->groupBy('year, month, day');
                $totRow = ['count' => 0, 'year' => '', 'month' => '', 'day' => '', 'fulldate' => 'tot'];

            } else {
                $query->select(new Expression("count(*) as 'count', YEAR(user_profile.created_at) as 'year', MONTH(user_profile.created_at) as 'month', DAY(user_profile.created_at) as 'day', DATE_FORMAT(user_profile.created_at, '%d/%m/%Y') as 'fulldate'"));
                $query->groupBy('year, month, day');
                $totRow = ['count' => 0, 'year' => '', 'month' => '', 'day' => '', 'fulldate' => 'tot'];

            }
        } else {
            $query
                ->groupBy('user_profile.dg_of_registration')
                ->select(new Expression("user_profile.dg_of_registration, event_group_referent.denominazione as 'dg', count(*) as 'count'"));
            $totRow = ['dg' => 'Totale', 'n_registrations' => 'tot', 'count' => 0];

        }

        $intervalParams = $this->getIntervalParams();

        if (!empty($intervalParams['startDate']) && !empty($intervalParams['endDate'])) {
            $query->andWhere(['>=', 'user_profile.created_at', $intervalParams['startDate']]);
            $query->andWhere(['<=', 'user_profile.created_at', $intervalParams['endDate']]);
        }

        $queryTot = clone $query;
        if ($this->event_group_referent_id == 'none') {
            $query->andWhere(['user_import_task.dg_of_registration' => null]);
            $queryTot->andWhere(['user_import_task.dg_of_registration' => null]);
            $queryTot->groupBy(null);
        } else if($this->event_group_referent_id != 'all'){
            $query->andWhere(['user_profile.dg_of_registration' => $this->event_group_referent_id]);
            $queryTot->andWhere(['user_profile.dg_of_registration' => $this->event_group_referent_id]);
            $queryTot->groupBy(null);
        } else {
                $queryTot->groupBy(null);
            }

            $result = $query->all();

            //aggiungo riga di totale
            $count = $queryTot->count();
            $totRow['count'] = $count;
            array_push($result, $totRow);

            return [
                'result' => $result,
                'tot' => $count
            ];
        }

        /**
         * @return array
         */
        public function registrationUserChartStatistics()
        {
            $intervalParams = $this->getIntervalParams();
            $maxDate = new \DateTime($intervalParams['endDate']);
            $minDate = new \DateTime($intervalParams['startDate']);

            $diff = $maxDate->diff($minDate);
            if ($maxDate->format('Y-m-d') == $minDate->format('Y-m-d')) {
                $this->isDaily = true;
            }
            if ($diff->m > self::MONTHLY_LIMIT) {
                $this->isMonthly = true;
            }

            //Query base

            $query = new Query;
            $query->from('user_profile')
                ->leftJoin('event_group_referent', 'event_group_referent.id = user_profile.dg_of_registration')
                ->orderBy('user_profile.created_at ASC');
            if ($this->event_group_referent_id == 'none') {
                $query->andWhere(['user_profile.dg_of_registration' => null]);
            }else if ($this->event_group_referent_id != 'all') {
                $query->andWhere(['user_profile.dg_of_registration' => $this->event_group_referent_id]);
            }

            $statistics = $this->basicChartStatistics($query, 'user_profile.created_at', 'N. Utenti registrati');
            return $statistics;

        }


    /**
     * @return void
     * @throws \Exception
     */
        public function setDailyOrMonthlyForStatistics(){
            $intervalParams = $this->getIntervalParams();
            $maxDate = new \DateTime($intervalParams['endDate']);
            $minDate = new \DateTime($intervalParams['startDate']);

            $diffDate = $maxDate->diff($minDate);
            $nMonth = (12 * $diffDate->y) + $diffDate->m;
            if ($maxDate->format('Y-m-d') == $minDate->format('Y-m-d')) {
                $this->isDaily = true;
            }
            if ($nMonth > self::MONTHLY_LIMIT) {
                $this->nMonth = $nMonth;
                $this->isMonthly = true;
            }
        }

    }