<?php

use open20\design\assets\BootstrapItaliaDesignAsset;
use open20\design\Module;

//$currentAsset = BootstrapItaliaDesignAsset::register($this);

?>

<div class="item-results">
  <a href="<?= $url ?>" title="<?= Module::t('amosdesign', 'Visualizza la pagina') . ' ' . $title ?>">
    <div class="it-right-zone">
      <span class="text">
        <?php if (!empty($beginDateHour)) { ?>
          <div>
            <em>
              <?= Module::t('amosdesign', "Dal") . ' ' . $beginDateHour . ' ' . Module::t('amosdesign', "al") . ' ' . $beginDateHour ?>
              <?= ' - ' . $location ?>
            </em>
          </div>
        <?php } ?>
        <?= $title ?>
        <em>
          <?= $description ?>
        </em>
      </span>
      <span class="it-multiple">
          <span class="mdi mdi-arrow-right"></span>
      </span>
    </div>
  </a>
</div>