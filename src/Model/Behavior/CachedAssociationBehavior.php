<?php

namespace App\Model\Behavior;

use Cake\Database\ExpressionInterface;
use Cake\Database\Expression\Comparison;
use Cake\ORM\Behavior;
use Cake\ORM\Query;

class CachedAssociationBehavior extends Behavior
{

    /**
     * Find a assosiation and cache
     *
     * @param Query $query Query to cache
     * @param array $options Options
     * @return Query
     */
    public function findCachedAssociation(Query $query, array $options)
    {
        $query->cache(function (Query $query) {
            $comparedFields = [];
            $query->clause('where')->traverse(function (ExpressionInterface $expression) use (&$comparedFields) {
                if (!$expression instanceof Comparison) {
                    return null;
                }

                $comparedFields[$expression->getField() . ' ' . $expression->getOperator()] = $expression->getValue();
            });

            return 'association_' . $this->_table->alias() . '_' . md5(json_encode($comparedFields));
        });

        return $query;
    }
}
