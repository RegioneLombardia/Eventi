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
use yii\helpers\ArrayHelper;

/**
 * Class SondaggiDomandePagine
 * This is the model class for table "sondaggi_domande_pagine".
 * @package open20\amos\sondaggi\models
 */
class SondaggiDomandePagine extends \open20\amos\sondaggi\models\base\SondaggiDomandePagine
{
    public $byBassRuleCwh = true;
    public $file;

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
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['file'], 'file']
        ]);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'fileBehavior' => [
                'class' => FileBehavior::className()
            ],
        ]);
    }

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
}
