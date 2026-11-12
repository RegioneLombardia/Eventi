<?php

/*
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Fixer;

use PhpCsFixer\WhitespacesFixerConfig;

/**
 */
interface WhitespacesAwareFixerInterface extends FixerInterface
{
    public function setWhitespacesConfig(WhitespacesFixerConfig $config);
}
