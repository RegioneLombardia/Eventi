<?php

/*
 *
 * (c) Bulat Shakirzyanov <mallluhuct@gmail.com>
 *
 */

namespace Imagine\Image;

use Imagine\Exception\InvalidArgumentException;

/**
 * The point class.
 */
final class Point implements PointInterface
{
    /**
     * @var int
     */
    private $x;

    /**
     * @var int
     */
    private $y;

    /**
     * Constructs a point of coordinates.
     *
     * @param int $x
     * @param int $y
     *
     * @throws \Imagine\Exception\InvalidArgumentException
     */
    public function __construct($x, $y)
    {
        if (!is_numeric($x) || !is_numeric($y)) {
            throw new InvalidArgumentException('x or y must be numeric');
        }

        $x = (int) round((float) $x);
        $y = (int) round((float) $y);

        if ($x < 0 || $y < 0) {
            throw new InvalidArgumentException(sprintf('A coordinate cannot be positioned outside of a bounding box (x: %s, y: %s given)', $x, $y));
        }

        $this->x = $x;
        $this->y = $y;
    }

    /**
     * {@inheritdoc}
     *
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * {@inheritdoc}
     *
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * {@inheritdoc}
     *
     */
    public function in(BoxInterface $box)
    {
        return $this->x < $box->getWidth() && $this->y < $box->getHeight();
    }

    /**
     * {@inheritdoc}
     *
     */
    public function move($amount)
    {
        return new self($this->x + $amount, $this->y + $amount);
    }

    /**
     * {@inheritdoc}
     *
     */
    public function __toString()
    {
        return sprintf('(%d, %d)', $this->x, $this->y);
    }
}
