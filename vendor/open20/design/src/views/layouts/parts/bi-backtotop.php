<?php

use open20\design\Module;
?>
<div class="d-flex align-items-center">
  <a href="javascript::void(0)" aria-hidden="true" title="Torna su" data-attribute="back-to-top" class="back-to-top back-to-top-small shadow z-index-1031" data-toggle="tooltip" data-placement="left" data-original-title="<?= Module::t('amosdesign', 'Torna su') ?>" >
    <svg class="icon icon-light">
      <use xlink:href="<?= $currentAsset->baseUrl ?>/sprite/material-sprite.svg#arrow-up"></use>
    </svg>
      <span class="sr-only"><?= Module::t('amosdesign', 'Torna in cima alla pagina') ?></span>
  </a>
</div>

