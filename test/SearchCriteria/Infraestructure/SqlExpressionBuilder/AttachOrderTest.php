<?php


namespace Tests\GPM\Shared\SearchCriteria\Infraestructure\SqlExpressionBuilder;


use GPM\Shared\SearchCriteria\Domain\Criteria;
use GPM\Shared\SearchCriteria\Domain\ValueObjects\Order;
use GPM\Shared\SearchCriteria\Infraestructure\SqlExpressionBuilder;

class AttachOrderTest extends SqlExpressionBuilderTest
{

    private static SqlExpressionBuilder $sqlExpressionBuilder;

    /**
     * @group   PBI#69673
     * @group   SqlExpressionBuilder
     * @group   SearchCriteria
     * @group   UnitTest
     * @return SqlExpressionBuilder
     */
    public function testSetup(): SqlExpressionBuilder
    {
        return self::$sqlExpressionBuilder ?? $this->sqlExpressionBuilderSetup();
    }

    /**
     * @depends testSetup
     * @group   PBI#69673
     * @runTestsInSeparateProcesses
     * @group   SqlExpressionBuilder
     * @group   SearchCriteria
     * @group   UnitTest
     * @param SqlExpressionBuilder $builder
     * @return void
     */
    public function test_should_return_this_string_based_on_simple_expression(SqlExpressionBuilder $builder): void
    {
        $criteria = Criteria::create();
        $criteria->addOrderBy('serviceId', Order::ASC);
        $expresion = ' ORDER BY serviceId ASC';

        $formatedExpresion = self::getPrivateMethods($builder, 'attachOrder', [$criteria]);
        $this->assertEquals($expresion, $formatedExpresion);
    }

    /**
     * @depends testSetup
     * @group   PBI#69673
     * @runTestsInSeparateProcesses
     * @group   SqlExpressionBuilder
     * @group   SearchCriteria
     * @group   UnitTest
     * @param SqlExpressionBuilder $builder
     * @return void
     */
    public function test_should_return_empty_string_when_no_order_added_to_criteria(SqlExpressionBuilder $builder): void
    {
        $criteria = Criteria::create();
        $expresion = '';

        $formatedExpresion = self::getPrivateMethods($builder, 'attachOrder', [$criteria]);
        $this->assertEquals($expresion, $formatedExpresion);
    }


}
