<?php


namespace GPM\Shared\SearchCriteria\Domain;

use GPM\Shared\SearchCriteria\Domain\Exceptions\CriteriaError;

class Criteria
{
    /**
     * @var IExpression $expression
     */
    private $expression;
    /**
     * since limit and offset could be null, cannot add the type of the variable since could be null
     * @var int
     */
    private $limit;
    /**
     * since limit and offset could be null, cannot add the type of the variable
     * @var int
     */
    private $offset;
    /**
     * @var OrderBy
     */
    private $orderBy;

    public static function create(): Criteria
    {
        return new self();
    }

    public function andWhere(IExpression $expression): void
    {
        if ($this->isEmptyExpression()) {
            $this->where($expression);
        } else {
            $this->setExpression(
                new CompositeComparision(
                    CompositeComparision::TYPE_AND,
                    [$this->expression, $expression]
                )
            );
        }
    }

    public function where(IExpression $expression): void
    {
        $this->setExpression($expression);
    }

    public function orWhere(IExpression $expression): void
    {
        if ($this->isEmptyExpression()) {
            $this->where($expression);
        } else {
            $this->setExpression(
                new CompositeComparision(
                    CompositeComparision::TYPE_OR,
                    [$this->expression, $expression]
                )
            );
        }
    }

    public function updateLimitAndOffset(int $limit=1000, int $offset=0): void
    {
        $this->meetsLimitAndOffset($limit, $offset);
        $this->limit = $limit;
        $this->offset = $offset;
    }

    public function addOrderBy(string $field, $order='DESC'): void
    {
        $this->orderBy = OrderBy::build($field, $order);
    }

    public function getOrderBy(): OrderBy
    {
        return $this->orderBy;
    }

    /**
     * return an array of Comparision objects
     * @throws CriteriaError
     * @return array
     */
    public function getComparisions(): array
    {
        return (new ExtractComparisionsFromExpressions())->extractComparisionsFromExpressions($this->getWhereExpression());
    }

    public function getWhereExpression(): IExpression
    {
        if ($this->isEmptyExpression())
        {
            throw new CriteriaError( sprintf('<%s> Message: There is no expression. Handle it, because it is possible that there are no expressions, but there are limit, offset and order.', static::class), 400);
        }
        return $this->expression;
    }

    private function setExpression(IExpression $expression): void
    {
        $this->expression = $expression;
    }

    public function isEmptyExpression(): bool
    {
        return null === $this->expression;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getOffset(): int
    {
        return  $this->offset;
    }

    public function hasLimitAndOffset(): bool
    {
        try{
            $this->meetsLimitAndOffset($this->limit, $this->offset);
            return true;
        }catch(CriteriaError $e)
        {
            //limit and offset could be null since you could fetch all
            return false;
        }
    }

    public function hasOrderBy(): bool
    {
        return isset($this->orderBy);
    }

    private function meetsLimitAndOffset($limit, $offset): void
    {
        if (!($limit > 0 && $offset >=0))
        {
            Throw new CriteriaError(
                sprintf('<%s>  Values for limits and offset not supported',
                    static::class
                ),
                400
            );
        }
    }

}
