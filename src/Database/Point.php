<?php

namespace App\Database;

use Cake\Database\Expression\FunctionExpression;
use Cake\Database\ExpressionInterface;
use Cake\Database\ValueBinder;

class Point implements \JsonSerializable, ExpressionInterface
{

    private $__x;
    private $__y;

    public function __construct($x, $y)
    {
        $this->__x = $x;
        $this->__y = $y;
    }

    public static function fromText($text)
    {
        list($x, $y) = sscanf($text, 'POINT(%f %f)');

        return new Point($x, $y);
    }

    public function x()
    {
        return $this->__x;
    }

    public function y()
    {
        return $this->__y;
    }

    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    function jsonSerialize()
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
