<?php


namespace Tests\GPM\Shared\SearchCriteria\Infraestructure\SqlExpressionBuilder;


use GPM\Shared\SearchCriteria\Domain\Comparision;
use GPM\Shared\SearchCriteria\Domain\Criteria;
use GPM\Shared\SearchCriteria\Domain\Exceptions\CriteriaError;
use GPM\Shared\SearchCriteria\Domain\ValueObjects\Operator;
use GPM\Shared\SearchCriteria\Domain\ValueObjects\Order;
use GPM\Shared\SearchCriteria\Infraestructure\SqlExpressionBuilder;

class CreateStringConditionExpressionTest extends SqlExpressionBuilderTest
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
     * @throws CriteriaError
     * @return void
     */
    public function test_should_return_this_string_based_on_simple_expression(SqlExpressionBuilder $builder): void
    {
        $expresion = 'WHERE serviceId = :serviceId';
        $criteria = Criteria::create();
        $criteria->where(new Comparision('serviceId',Operator::EQ,1));

        $formatedExpresion = $builder->createStringConditionExpression($criteria);
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
     * @throws CriteriaError
     * @return void
     */
    public function test_should_return_this_string_based_on_CompositeComparision(SqlExpressionBuilder $builder): void
    {
        $expresion = 'WHERE ((((((serviceId = :serviceId AND field2 > :field2) OR field3 != :field3) OR field4 >= :field4) OR field4 < :field4) AND field5 <= :field5) OR field6 LIKE :field6)';
        $criteria = Criteria::create();
        $criteria->where(new Comparision('serviceId',Operator::EQ,1));
        $criteria->andWhere(new Comparision('field2',Operator::GT,3));
        $criteria->orWhere(new Comparision('field3',Operator::NEQ,1));
        $criteria->orWhere(new Comparision('field4',Operator::GTE,1));
        $criteria->orWhere(new Comparision('field4',Operator::LT,1));
        $criteria->andWhere(new Comparision('field5',Operator::LTE,1));
        $criteria->orWhere(new Comparision('field6',Operator::CONTAINS,1));

        $formatedExpresion = $builder->createStringConditionExpression($criteria);
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
     * @throws CriteriaError
     * @return void
     */
    public function test_should_return_string_based_on_complex_CompositeComparision(SqlExpressionBuilder $builder): void
    {
        $expresion = 'WHERE ((serviceId = :serviceId AND field2 > :field2) OR field6 LIKE :field6) ORDER BY serviceId ASC LIMIT 3 OFFSET 4';
        $criteria = Criteria::create();
        $criteria->where(new Comparision('serviceId',Operator::EQ,1));
        $criteria->andWhere(new Comparision('field2',Operator::GT,3));
        $criteria->orWhere(new Comparision('field6',Operator::CONTAINS,1));
        $criteria->addOrderBy('serviceId', Order::ASC);
        $criteria->updateLimitAndOffset(3, 4);

        $formatedExpresion = $builder->createStringConditionExpression($criteria);
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
     * @throws CriteriaError
     * @return void
     */
    public function test_should_concat_LIMIT_and_offset_if_criteria_have_it(SqlExpressionBuilder $builder): void
    {
        $expresion = 'WHERE serviceId = :serviceId LIMIT 100 OFFSET 2';
        $criteria = Criteria::create();
        $criteria->where(new Comparision('serviceId',Operator::EQ,1));
        $criteria->updateLimitAndOffset(100, 2);

        $formatedExpresion = $builder->createStringConditionExpression($criteria);
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
     * @throws CriteriaError
     * @return void
     */
    public function test_should_concat_order_ASC_if_criteria_have_it(SqlExpressionBuilder $builder): void
    {
        $expresion = 'WHERE serviceId = :serviceId ORDER BY serviceId ASC';
        $criteria = Criteria::create();
        $criteria->where(new Comparision('serviceId',Operator::EQ,1));
        $criteria->addOrderBy('serviceId', Order::ASC);

        $formatedExpresion = $builder->createStringConditionExpression($criteria);
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
     * @throws CriteriaError
     * @return void
     */
    public function test_should_concat_order_DESC_by_default_if_criteria_have_order_without_direction(SqlExpressionBuilder $builder): void
    {
        $expresion = 'WHERE serviceId = :serviceId ORDER BY serviceId DESC';
        $criteria = Criteria::create();
        $criteria->where(new Comparision('serviceId',Operator::EQ,1));
        $criteria->addOrderBy('serviceId');

        $formatedExpresion = $builder->createStringConditionExpression($criteria);
        $this->assertEquals($expresion, $formatedExpresion);
    }
}
