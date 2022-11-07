<?php

namespace GPM\Shared\SearchCriteria\Infraestructure;


use GPM\Shared\SearchCriteria\Domain;
use GPM\Shared\SearchCriteria\Domain\Comparision;
use GPM\Shared\SearchCriteria\Domain\CompositeComparision;
use GPM\Shared\SearchCriteria\Domain\Criteria;
use GPM\Shared\SearchCriteria\Domain\Exceptions\CriteriaError;
use GPM\Shared\SearchCriteria\Domain\IExpression;
use GPM\Shared\SearchCriteria\Domain\IStringConditionExpressions;
use GPM\Shared\SearchCriteria\Domain\ValueObjects\Operator;
use GPM\Shared\SearchCriteria\Domain\ExpressionBuilder;

class SqlExpressionBuilder extends ExpressionBuilder implements IStringConditionExpressions
{

    public static array $comparisonMap = [
        Operator::EQ => '= %s',
        Operator::NEQ => '!= %s',
        Operator::GT => '> %s',
        Operator::GTE => '>= %s',
        Operator::LT => '< %s',
        Operator::LTE => '<= %s',
        Operator::CONTAINS => 'LIKE %s',
    ];

    /**
     * This will return the string after where in a query ex. '(((serviceName = :serviceName AND serviceHost = :serviceHost) AND serviceType = :serviceType) AND serviceVersion =
     * :serviceVersion)'
     * @param Criteria $criteria
     * @return string
     */
    public function createStringConditionExpression(Criteria $criteria): string
    {
            try{
                $whereCondition = 'WHERE '.$this->dispatch($criteria->getWhereExpression());
            }catch (CriteriaError $e)
            {
                //there is no expresion
                $whereCondition = '';
            }

         return $whereCondition.$this->attachOrder($criteria).$this->attachLimitOffset($criteria);
    }

    private function attachOrder(Criteria $criteria): string
    {
        $orderBy = '';
        if($criteria->hasOrderBy())
        {
            $orderBy .= ' ORDER BY '.$criteria->getOrderBy()->field().' '.$criteria->getOrderBy()->order();
        }
        return $orderBy;
    }

    private function attachLimitOffset(Criteria $criteria): string
    {
        $limitOffset = '';
        if($criteria->hasLimitAndOffset())
        {
            $limitOffset .= ' LIMIT '.$criteria->getLimit().' OFFSET '.$criteria->getOffset();
        }
        return $limitOffset;
    }

    /**
     * Converts a comparison expression into the target query language output.
     * @param $comparison
     * @return mixed
     */
    protected function walkComparison(Comparision $comparison): string
    {
        if ( 'null' === $comparison->getValue() && ($comparison->getOperator() === Operator::EQ)) {
            return $comparison->getField() . ' IS NULL';
        }

        if ( 'null' === $comparison->getValue()  && $comparison->getOperator() === Operator::NEQ) {
            return $comparison->getField() . ' IS NOT NULL';
        }

        return $comparison->getField() . ' ' . sprintf(
            self::$comparisonMap[$comparison->getOperator()],
                ':'.$comparison->getField()
            );
    }

    /**
     * Converts a composite expression into the target query language output.
     * @param CompositeComparision $expr
     * @throws Domain\Exceptions\CriteriaError
     * @return mixed
     */
    protected function walkCompositeExpression(CompositeComparision $expr): string
    {
        $expressionList = [];

        foreach ($expr->getExpressionList() as $child) {
            $expressionList[] = $this->dispatch($child);
        }

        switch ($expr->getType()) {
            case CompositeComparision::TYPE_AND:
                return '('.implode(' AND ', $expressionList).')';
            case CompositeComparision::TYPE_OR:
                return '('.implode(' OR ', $expressionList).')';
            default:
                throw new CriteriaError("Unknown composite ".$expr->getType(), 400);
        }
    }

    protected function dispatch(IExpression $expr): string
    {
       return parent::dispatch($expr);
    }

}
