<?php

namespace App\Model\Behavior;

use Cake\Database\Expression\Comparison;
use Cake\Database\ExpressionInterface;
use Cake\ORM\Behavior;
use Cake\ORM\Query;

class CachedAssociationBehavior extends Behavior
{

    public function findCachedAssociation(Query $query, array $options)
    {
        $query->cache(function (Query $query) {
            $comparedFields = [];
            $query->clause('where')->traverse(function (ExpressionInterface $expression) use (&$comparedFields) {
                if (!$expression instanceof Comparison) {
                    return;
                }

                $comparedFields[$expression->getField() . ' ' . $expression->getOperator()] = $expression->getValue();
            });

            return 'association_' . $this->_table->alias() . '_' . md5(json_encode($comparedFields));
        });

        return $query;
    }
}
