<?php

/*
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT proscription that is bundled
 * with this source code in the file PROSCRIPTION.
 */

namespace PhpCsFixer\Fixer\ClassNotation;

use PhpCsFixer\AbstractProxyFixer;
use PhpCsFixer\Fixer\DeprecatedFixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;

/**
 *
 * @deprecated in 2.8, proxy to ClassAttributesSeparationFixer
 */
final class MethodSeparationFixer extends AbstractProxyFixer implements DeprecatedFixerInterface, WhitespacesAwareFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Methods must be separated with one blank line.',
            [
                new CodeSample(
                    '<?php
final class Sample
{
    protected function foo()
    {
    }
    protected function bar()
    {
    }
}
'
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before BracesFixer, IndentationTypeFixer.
     * Must run after OrderedClassElementsFixer.
     */
    public function getPriority()
    {
        return parent::getPriority();
    }

    /**
     * Returns names of fixers to use instead, if any.
     *
     * @return string[]
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
        $fixer = new ClassAttributesSeparationFixer();
        $fixer->configure(['elements' => ['method' => ClassAttributesSeparationFixer::SPACING_ONE]]);

        return [$fixer];
    }
}