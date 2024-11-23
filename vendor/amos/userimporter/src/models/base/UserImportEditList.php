<?php

namespace amos\userimporter\models\base;

use Yii;

/**
 * This is the base-model class for table "user_import_edit_list".
 *
 * @property integer $id
 * @property string $name
 * @property string $surname
 * @property string $fiscal_code
 * @property string $email
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 */
class  UserImportEditList extends \open20\amos\core\record\Record
{
    public $isSearch = false;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_import_edit_list';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email'], 'required'],
            [['email'], 'email'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['name', 'surname', 'fiscal_code', 'email'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('amosuserimporter', 'ID'),
            'name' => Yii::t('amosuserimporter', 'Name'),
            'surname' => Yii::t('amosuserimporter', 'Surname'),
            'fiscal_code' => Yii::t('amosuserimporter', 'Fiscal code'),
            'email' => Yii::t('amosuserimporter', 'Email'),
            'created_at' => Yii::t('amosuserimporter', 'Created at'),
            'updated_at' => Yii::t('amosuserimporter', 'Updated at'),
            'deleted_at' => Yii::t('amosuserimporter', 'Deleted at'),
            'created_by' => Yii::t('amosuserimporter', 'Created by'),
            'updated_by' => Yii::t('amosuserimporter', 'Updated at'),
            'deleted_by' => Yii::t('amosuserimporter', 'Deleted at'),
        ];
    }
}
