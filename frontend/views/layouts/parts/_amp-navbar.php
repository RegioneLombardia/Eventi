<nav class="navbar-new-eventi">
  <div class="uk-navbar-right">
    <div class="uk-navbar-nav">
      <?php if (!\Yii::$app->user->isGuest) {
        $profile = \open20\amos\admin\models\UserProfile::find()->andWhere(['user_id' => \Yii::$app->user->id])->one();
        $nomeCognome = $profile->nomeCognome;
      ?>
        <amp-mega-menu height="50" layout="fixed-height">
          <nav>
            <ul>
              <li class="link-login">
                <span role="button"><span class="material-icons">
                    account_circle
                  </span>
                  <span class="username"><?= $nomeCognome ?></span>
                  <span class="material-icons">
                    expand_more
                  </span>
                </span>

                <div role="dialog">
                  <ul class="dropdownmenu">
                    <li>
                      <a href="<?= \Yii::$app->params['platform']['backendUrl'] . "/admin/user-profile/update?id=" . $profile->id ?>" title="<?= \Yii::t('app', "Il mio profilo") ?><"><?= \Yii::t('app', "Il mio profilo") ?></a>
                    </li>
                    <?php if (\Yii::$app->user->can('EVENT_DG_OPERATOR') || \Yii::$app->user->can('SUPER_USER_EVENT')) { ?>
                      <li>
                        <a href="<?= \Yii::$app->params['platform']['backendUrl'] . "/dashboard" ?>" title="<?= \Yii::t('app', "Accedi all'area riservata") ?><"><?= \Yii::t('app', "Accedi all'area riservata") ?></a>
                      </li>
                    <?php } ?>
                    <li>
                      <a href="<?= \Yii::$app->params['platform']['backendUrl'] . "/events/event-dashboard/my-events-home" ?>" title="<?= \Yii::t('app', "I miei eventi") ?><"><?= \Yii::t('app', "I miei eventi") ?></a>
                    </li>


                    <li>
                      <a href="<?= \Yii::$app->params['platform']['backendUrl'] . "/site/logout-frontend?redir=" . $redir ?>" title="<?= \Yii::t('app', "Logout") ?>"><?= \Yii::t('app', "Logout") ?></a>
                    </li>
                  </ul>
                </div>
              </li>
            </ul>
          </nav>
        </amp-mega-menu>


      <?php } else {
      ?>
        <div class="link-login">
          <!-- <a href="javascript:void(0)" class="uk-icon-button icon-apps" title="Menu di navigazione"><span class="mdi mdi-apps"></span></a> -->
          <a data-method="post" href="<?= \Yii::$app->params['platform']['backendUrl'] . "/admin/security/login-frontend?redirect_post_reg=" . $redir ?>"><span class="material-icons">
              account_circle
            </span> <span class="username"> <?= \Yii::t('app', "Accedi / Registrati") ?></span></a>

        </div>
      <?php } ?>
    </div>


  </div>
</nav>