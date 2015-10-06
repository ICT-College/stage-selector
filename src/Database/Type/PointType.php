<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         3.0.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Database\Type;

use App\Database\Point;
use Cake\Database\Driver;
use Cake\Database\Expression\FunctionExpression;
use Cake\Database\Expression\QueryExpression;
use Cake\Database\Query;
use Cake\Database\Type;
use Cake\Datasource\ConnectionManager;
use Cake\Error;

/**
 * Point type converter.
 *
 * Use to convert latitude/longitude data between PHP and the database types.
 */
class PointType extends Type
{

    /**
     * Convert point data into the database format.
     *
     * @param string|resource $value The value to convert.
     * @param Driver $driver The driver instance to convert with.
     * @return string|resource
     */
    public function toDatabase($value, Driver $driver)
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_string($value)) {
            $coordinates = explode(',', $value);
            $x = (float) $coordinates[0];
            $y = (float) $coordinates[1];

            $value = new Point($x, $y);
        }

        debug($value);

        if ($value instanceof Point) {
            return $value;
        }

        return new QueryExpression('GeomFromText(\'POINT(' . $value . ')\')');
    }

    /**
     * Convert point values to PHP string
     *
     * @param null|string|resource $value The value to convert.
     * @param Driver $driver The driver instance to convert with.
     * @return resource
     * @throws \Cake\Core\Exception\Exception
     */
    public function toPHP($value, Driver $driver)
    {
        if ($value === null) {
            return null;
        }

        $expression = new FunctionExpression('ASTEXT', [$value]);

        $result = ConnectionManager::get('default')->newQuery()->select($expression)->execute()->fetch()[0];

        return Point::fromText($result);
    }

//    public function toStatement($value, Driver $driver)
//    {
////        return \PDO::PARAM_LOB;
//    }
}