<?php


namespace GPM\Shared\SearchCriteria\Domain;


class ExtractComparisionsFromExpressions extends ExpressionBuilder
{

    /**
     * @var Comparision[]
     */
    private array $comparisions;

    public function extractComparisionsFromExpressions(IExpression $expression)
    {
        $this->dispatch($expression);
        return $this->comparisions;
    }

    /**
     *
     * @param Comparision $comparison
     * @return mixed
     */
    protected function walkComparison(Comparision $comparison): void
    {
        $this->comparisions[] = $comparison;
    }

    /**
     * Converts a composite expression into the target query language output.
     * @param CompositeComparision $expr
     * @throws Exceptions\CriteriaError
     * @return mixed
     */
    protected function walkCompositeExpression(CompositeComparision $expr): void
    {
        //while this be an compositeComparision, iterate to find Comparisions
        foreach ($expr->getExpressionList() as $child)
        {
            $this->dispatch($child);
        }
    }
}
