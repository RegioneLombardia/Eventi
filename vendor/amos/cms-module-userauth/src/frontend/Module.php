<?php

namespace amos\userauth\frontend;

use luya\base\Module as BaseModule;


class Module extends BaseModule
{
    const USERAUTH_CONFIG_REDIRECT_NAV_ID = 'userauth_redirect_nav_id';
    const USERAUTH_CONFIG_AFTER_LOGIN_NAV_ID = 'userauth_afterlogin_nav_id';
    const NOPERMISSION_CONFIG_REDIRECT_NAV_ID ='nopermission_redirect_nav_id';

    public $redirectLoginBackend = null;

    /**
     * @inheritdoc
     */
    public static function onLoad()
    {
        self::registerTranslation('userauth',
            static::staticBasePath().'/messages',
            [
            'userauth' => 'userauth.php',
        ]);
    }

    /**
     * @inheritdoc
     */
    public static function t($message, array $params = [])
    {
        return parent::baseT('userauth', $message, $params);
    }
}