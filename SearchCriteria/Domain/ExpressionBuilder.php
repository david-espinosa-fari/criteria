<?php


namespace GPM\Shared\SearchCriteria\Domain;


use GPM\Shared\SearchCriteria\Domain\Exceptions\CriteriaError;

abstract class ExpressionBuilder
{

    /**
     * Converts a comparison expression into the target query language output.
     * @param Comparision $comparison
     * @return mixed
     */
    abstract protected function walkComparison(Comparision $comparison);

    /**
     * Converts a composite expression into the target query language output.
     * @param CompositeComparision $expr
     * @return mixed
     */
    abstract protected function walkCompositeExpression(CompositeComparision $expr);

    /**
     * Dispatches walking an expression to the appropriate handler.
     * @param IExpression $expr
     * @throws CriteriaError
     * @return mixed
     */
    protected function dispatch(IExpression $expr)
    {
        switch (true) {
            case $expr instanceof Comparision:
                return $this->walkComparison($expr);
            case $expr instanceof CompositeComparision:
                return $this->walkCompositeExpression($expr);
            default:
                throw new CriteriaError('Unknown Expression ' . get_class($expr));
        }
    }
}
