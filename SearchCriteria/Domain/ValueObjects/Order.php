<?php


namespace GPM\Shared\SearchCriteria\Domain\ValueObjects;


use GPM\Shared\SearchCriteria\Domain\Exceptions\CriteriaError;

class Order
{
    public const ASC = 'ASC';
    public const DESC = 'DESC';

    private array $SUPPORTED_ORDERS = [self::ASC, self::DESC,];
    /**
     * @var string
     */
    private string $order;

    public function __construct(string $order)
    {
        if (!in_array($order, $this->SUPPORTED_ORDERS, true)) {
            Throw new CriteriaError(
                sprintf('<%s> Bad request. Operator not supported. Supported are <%s>',
                    static::class,
                    implode(', ', $this->SUPPORTED_ORDERS)
                ),
                400
            );
        }

        $this->order = $order;
    }

    public static function create($order): self
    {
        return new self(strtoupper($order) === self::ASC ? self::ASC : self::DESC);
    }

    public function __toString()
    {
        return $this->order;
    }

    public function getValue(): string
    {
        return (string)$this;
    }

}
