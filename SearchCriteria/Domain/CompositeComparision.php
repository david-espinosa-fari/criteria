<?php


namespace GPM\Shared\SearchCriteria\Domain;


use GPM\Shared\SearchCriteria\Domain\Exceptions\CriteriaError;

class CompositeComparision implements IExpression
{
    public const TYPE_AND = 'AND';
    public const TYPE_OR  = 'OR';

    private array $SUPPORTED_OPERATIONS = [self::TYPE_AND, self::TYPE_OR];

    private string $type;

    /** @var IExpression[] */
    private array $expressions = [];

    /**
     * @param string $type
     * @param array  $expressions
     * @throws CriteriaError
     */
    public function __construct(string $type, array $expressions)
    {
        $this->setType($type);
        $this->setExpression($expressions);
    }

    /**
     * Returns the list of expressions nested in this composite.
     *
     * @return IExpression[]
     */
    public function getExpressionList(): array
    {
        return $this->expressions;
    }

    public function getType(): string
    {
        return $this->type;
    }

    private function setExpression(array $expressions): void
    {
        foreach ($expressions as $expr) {
            if (! ($expr instanceof IExpression)) {
                throw new CriteriaError('The array of expression should be instance of CompositeComparision or simple Comparision', 500);
            }

            $this->expressions[] = $expr;
        }
    }

    private function setType(string $type): void
    {
        if (! $this->isSupportedType($type)) {

            Throw new CriteriaError(
                sprintf('<%s> Bad request. Composite comparision not supported. Supported are <%s>',
                    static::class,
                    implode(', ', $this->SUPPORTED_OPERATIONS)
                ),
                400
            );
        }
        $this->type = $type;
    }

    private function isSupportedType(string $type): bool
    {
        return in_array($type, $this->SUPPORTED_OPERATIONS, true);
    }

}
