<?php


namespace GPM\Shared\SearchCriteria\Domain;


use GPM\Shared\SearchCriteria\Domain\ValueObjects\Field;
use GPM\Shared\SearchCriteria\Domain\ValueObjects\Order;

/**
 * Class OrderBy
 * @package GPM\Shared\SearchCriteria\Domain
 *          //The ORDER BY is used to sort the result-set in ascending or descending order.
 */
class OrderBy
{
    /**
     * @var Field
     */
    private Field $field;

    /**
     * @var Order
     */
    private Order $order;

    public function __construct(Field $field, Order $order)
    {
        $this->field = $field;
        $this->order = $order;
    }

    public static function build($field, $order): self
    {
        return new self(new Field($field), Order::create($order));
    }

    public function field(): string
    {
        return $this->field->getValue();
    }

    public function order(): string
    {
        return $this->order->getValue();
    }

}
