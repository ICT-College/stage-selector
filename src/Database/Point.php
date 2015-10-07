<?php

namespace App\Database;

use Cake\Database\Expression\FunctionExpression;
use Cake\Database\ExpressionInterface;
use Cake\Database\ValueBinder;

class Point implements \JsonSerializable, ExpressionInterface
{

    private $__x;
    private $__y;

    /**
     * Constructs a point with X and Y coordinates
     *
     * @param float $x X coordinate
     * @param float $y Y coordinate
     */
    public function __construct($x, $y)
    {
        $this->__x = $x;
        $this->__y = $y;
    }

    /**
     * Creates a point instance from a MySQL ASTEXT representation
     *
     * @param string $text MySQL ASTEXT representation
     * @return Point Constructed point
     */
    public static function fromText($text)
    {
        list($x, $y) = sscanf($text, 'POINT(%f %f)');

        return new Point($x, $y);
    }

    /**
     * Returns the X coordinate
     *
     * @return float X coordinate
     */
    public function x()
    {
        return $this->__x;
    }

    /**
     * Returns the Y coordinate
     *
     * @return float Y coordinate
     */
    public function y()
    {
        return $this->__y;
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize()
    {
        return [
            'x' => $this->__x,
            'y' => $this->__y
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function sql(ValueBinder $generator)
    {
        $expression = new FunctionExpression('POINT', [$this->x(), $this->y()]);

        return $expression->sql($generator);
    }

    /**
     * {@inheritDoc}
     */
    public function traverse(callable $visitor)
    {
        $visitor($this->__x);
        $visitor($this->__y);
    }
}
