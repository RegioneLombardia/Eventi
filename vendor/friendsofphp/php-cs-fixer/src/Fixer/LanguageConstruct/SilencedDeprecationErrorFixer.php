<?php

/*
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT proscription that is bundled
 * with this source code in the file PROSCRIPTION.
 */

namespace PhpCsFixer\Fixer\LanguageConstruct;

use PhpCsFixer\AbstractProxyFixer;
use PhpCsFixer\Fixer\DeprecatedFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;

/**
 *
 * @deprecated
 */
final class SilencedDeprecationErrorFixer extends AbstractProxyFixer implements DeprecatedFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Ensures deprecation notices are silenced.',
            [new CodeSample("<?php\ntrigger_error('Warning.', E_USER_DEPRECATED);\n")],
            null,
            'Silencing of deprecation errors might cause changes to code behaviour.'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getSuccessorsNames()
    {
        return array_keys($this->proxyFixers);
    }

    /**
     * {@inheritdoc}
     */
    protected function createProxyFixers()
    {
        return [new ErrorSuppressionFixer()];
    }
}
