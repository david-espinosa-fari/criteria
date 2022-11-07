<?php


namespace GPM\Shared\SearchCriteria\Domain\ValueObjects;


use GPM\Shared\SearchCriteria\Domain\Exceptions\CriteriaError;

class Operator
{
    public const EQ  = '=';
    public const NEQ = '<>';
    public const LT  = '<';
    public const LTE = '<=';
    public const GT  = '>';
    public const GTE = '>=';
    public const CONTAINS = 'CONTAINS';

    private array $SUPPORTED_OPERATIONS = [self::EQ, self::NEQ, self::LT, self::LTE,
        self::GT, self::GTE, self::CONTAINS];
    /**
     * @var string
     */
    private $operator;

    public function __construct(string $operator)
    {
        if (!in_array($operator, $this->SUPPORTED_OPERATIONS, true)) {
            Throw new CriteriaError(
                sprintf('<%s> Bad request. Operator not supported. Supported are <%s>',
                    static::class,
                    implode(', ', $this->SUPPORTED_OPERATIONS)
                ),
                400
            );
        }

        $this->operator = $operator;
    }

    public function __toString()
    {
        return $this->operator;
    }

    public function getValue(): string
    {
        return (string)$this;
    }
}
