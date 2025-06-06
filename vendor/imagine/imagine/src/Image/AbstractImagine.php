<?php

/*
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 */

namespace Imagine\Image;

use Imagine\Exception\InvalidArgumentException;
use Imagine\Factory\ClassFactory;
use Imagine\Factory\ClassFactoryInterface;
use Imagine\Image\Metadata\MetadataReaderInterface;

abstract class AbstractImagine implements ImagineInterface
{
    /**
     * @var \Imagine\Image\Metadata\MetadataReaderInterface|null
     */
    private $metadataReader;

    /**
     * @var \Imagine\Factory\ClassFactoryInterface
     */
    private $classFactory;

    /**
     * {@inheritdoc}
     *
     */
    public function setMetadataReader(MetadataReaderInterface $metadataReader)
    {
        $this->metadataReader = $metadataReader;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     */
    public function getMetadataReader()
    {
        if ($this->metadataReader === null) {
            $this->metadataReader = $this->getClassFactory()->createMetadataReader();
        }

        return $this->metadataReader;
    }

    /**
     * {@inheritdoc}
     *
     */
    public function setClassFactory(ClassFactoryInterface $classFactory)
    {
        $this->classFactory = $classFactory;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     */
    public function getClassFactory()
    {
        if ($this->classFactory === null) {
            $this->classFactory = new ClassFactory();
        }

        return $this->classFactory;
    }

    /**
     * Checks a path that could be used with ImagineInterface::open and returns
     * a proper string.
     *
     * @param string|object $path
     *
     * @throws \Imagine\Exception\InvalidArgumentException in case the given path is invalid
     *
     * @return string
     */
    protected function checkPath($path)
    {
        // provide compatibility with objects such as \SplFileInfo
        if (is_object($path) && method_exists($path, '__toString')) {
            $path = (string) $path;
        }

        $handle = @fopen($path, 'r');

        if ($handle === false) {
            throw new InvalidArgumentException(sprintf('File %s does not exist.', $path));
        }

        fclose($handle);

        return $path;
    }
}
