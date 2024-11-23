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

/**
 * Class SondaggiRisposteSessioni
 * This is the model class for table "sondaggi_risposte_sessioni".
 * @package open20\amos\sondaggi\models
 */
class SondaggiRisposteSessioni extends \open20\amos\sondaggi\models\base\SondaggiRisposteSessioni
{
    public $byBassRuleCwh = true;
    /**
     * @inheritdoc
     */
    public function representingColumn()
    {
        return [
            //inserire il campo o i campi rappresentativi del modulo
        ];
    }
}
