<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\agid\service\models\base
 * @category   CategoryName
 */

namespace open20\agid\service\models\base;

use open20\amos\core\record\Record;
use open20\agid\service\Module;
use open20\amos\admin\AmosAdmin;


/**
 * Class AgidServiceTypeRoles
 *
 * This is the base-model class for table "service_agid_type_roles".
 *
 * @property integer $id
 * @property integer $service_agid_type_id
 * @property integer $user_id
 * @property string $role
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 *
 *
 * @package open20\agid\service\models\base
 */
class AgidServiceTypeRoles extends Record
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'service_agid_type_roles';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['service_agid_type_id', 'user_id','role'], 'required'],
            [['user_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['role'], 'string', 'max' => 255],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Module::t('agid-service', 'ID'),
            'service_agid_type_id' => Module::t('agid-service', 'Service agid type ID'),
            'role' => Module::t('agid-service', 'ruolo'),
            'user_id' => Module::t('agid-service', 'User Id'),
            'created_at' => Module::t('agid-service', 'Created at'),
            'updated_at' => Module::t('agid-service', 'Updated at'),
            'deleted_at' => Module::t('agid-service', 'Deleted at'),
            'created_by' => Module::t('agid-service', 'Created by'),
            'updated_by' => Module::t('agid-service', 'Updated by'),
            'deleted_by' => Module::t('agid-service', 'Deleted by'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceAgidType()
    {
        return $this->hasOne(Module::instance()->model('ServiceAgidType'), ['id' => 'service_agid_type_id']);
    }

    /**
     * @inheritdoc
     */
    public function getUser()
    {
        return $this->hasOne(AmosAdmin::instance()->createModel('User')->className(), ['id' => 'user_id']);
    }
}
