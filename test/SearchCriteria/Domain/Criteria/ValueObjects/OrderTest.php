<?php


namespace Tests\GPM\Shared\SearchCriteria\Domain\Criteria\ValueObjects;


use GPM\Shared\SearchCriteria\Domain\Exceptions\CriteriaError;
use GPM\Shared\SearchCriteria\Domain\ValueObjects\Order;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{

    /**
     * @group   PBI#69673
     * @group   PBI#68318
     * @group   Operator
     * @group   UnitTest
     * @group   SearchCriteria
     */
    public function testSetup(): Order
    {
        $order = Order::create('ASC');
        $this->assertInstanceOf(Order::class, $order);

        return $order;
    }

    /**
     * @group   PBI#69673
     * @group   PBI#68318
     * @group   Operator
     * @group   UnitTest
     * @group   SearchCriteria
     */
    public function test_order_should_throw_on_un_existent_order_type(): void
    {
        $this->expectException(CriteriaError::class);
        new Order('un-existent-order');
    }

    /**
     * @group   PBI#69673
     * @group   PBI#68318
     * @group   Operator
     * @group   UnitTest
     * @group   SearchCriteria
     */
    public function test_create_should_return_default_order_if_not_set(): void
    {
        $order = Order::create('');
        $this->assertSame(Order::DESC, (string)$order);
    }

    /**
     * @group   PBI#69673
     * @group   PBI#68318
     * @group   Operator
     * @group   UnitTest
     * @group   SearchCriteria
     */
    public function test_create_should_return_default_order_type_if_value_is_wrong(): void
    {
        $order = Order::create('un-existent-order');
        $this->assertSame(Order::DESC, (string)$order);
    }

    /**
     * @group   PBI#69673
     * @group   PBI#68318
     * @group   Operator
     * @group   UnitTest
     * @group   SearchCriteria
     */
    public function test_getValue_should_return_current_order_type(): void
    {
        $order = Order::create('ASC');
        $this->assertSame(Order::ASC, $order->getValue());
    }


}
