<?php


namespace Tests\GPM\Shared\SearchCriteria\Domain\Criteria;


use GPM\Shared\SearchCriteria\Domain\Comparision;
use GPM\Shared\SearchCriteria\Domain\CompositeComparision;
use GPM\Shared\SearchCriteria\Domain\Criteria;
use GPM\Shared\SearchCriteria\Domain\Exceptions\CriteriaError;
use GPM\Shared\SearchCriteria\Domain\IExpression;
use GPM\Shared\SearchCriteria\Domain\OrderBy;
use PHPUnit\Framework\TestCase;


class CriteriaTest extends TestCase
{

    /**
     * @group   PBI#69673
     * @group   PBI#68318
     * @group   Criteria
     * @group   UnitTest
     * @group SearchCriteria
     * @large
     */
    public function testSetup(): Criteria
    {
        $criteria = Criteria::create();
        $this->assertInstanceOf(Criteria::class, $criteria);

        return $criteria;
    }

    /**
     * @depends testSetup
     * @group   PBI#69673
     * @runTestsInSeparateProcesses
     * @group   PBI#68318
     * @group   Criteria
     * @group   UnitTest
     * @group   SearchCriteria
     * @param Criteria $criteria
     * @return Criteria
     */
    public function test_Create_should_return_instanceOf_criteria(Criteria $criteria): Criteria
    {
        $this->assertInstanceOf(Criteria::class, $criteria);

        return $criteria;
    }

    /**
     * @depends testSetup
     * @runTestsInSeparateProcesses
     * @group   PBI#69673
     * @group   PBI#68318
     * @group   Criteria
     * @group   UnitTest
     * @group   SearchCriteria
     * @param Criteria $criteria
     * @return void
     */
    public function test_is_emptyExpression_should_return_bool(Criteria $criteria): void
    {
        $this->assertIsBool($criteria->isEmptyExpression());
    }

    /**
     * @depends testSetup
     * @runTestsInSeparateProcesses
     * @group   PBI#69673
     * @group   PBI#68318
     * @group   Criteria
     * @group   UnitTest
     * @group   SearchCriteria
     * @param Criteria $criteria
     * @return void
     */
    public function test_where_should_return_void_when_set_an_expression(Criteria $criteria): void
    {
        $mock = $this->createMock(CompositeComparision::class);
        $mock1 = $this->createMock(Comparision::class);

        $this->assertNull($criteria->where($mock));
        $this->assertNull($criteria->where($mock1));

        $this->assertFalse($criteria->isEmptyExpression());
    }

    /**
     * @depends testSetup
     * @runTestsInSeparateProcesses
     * @group   PBI#69673
     * @group   PBI#68318
     * @group   Criteria
     * @group   UnitTest
     * @group   SearchCriteria
     * @param Criteria $criteria
     * @return void
     */
    public function test_andWhere_should_return_void_when_set_an_expression(Criteria $criteria): void
    {
        $mock = $this->createMock(CompositeComparision::class);
        $mock1 = $this->createMock(Comparision::class);

        $this->assertNull($criteria->andWhere($mock));
        $this->assertNull($criteria->andWhere($mock1));

        $this->assertFalse($criteria->isEmptyExpression());
    }

    /**
     * @runTestsInSeparateProcesses
     * @group   PBI#69673
     * @group   PBI#68318
     * @group   Criteria
     * @group   UnitTest
     * @group   SearchCriteria
     * @throws CriteriaError
     * @return void
     */
    public function test_andWhere_should_set_expression_with_instance_Comparision_when_no_other_expresion(): void
    {
        $mock = $this->createMock(Comparision::class);
        $criteria = Criteria::create();
        $criteria->andWhere($mock);

        $comparision = $criteria->getWhereExpression();
        $this->assertInstanceOf(Comparision::class, $comparision);
    }

    /**
     * @runTestsInSeparateProcesses
     * @group   PBI#69673
     * @group   PBI#68318
     * @group   Criteria
     * @group   UnitTest
     * @group   SearchCriteria
     * @throws CriteriaError
     * @return void
     */
    public function test_andWhere_should_set_expression_with_instance_CompositeComparision_when_other_expresion_already_exist(): void
    {
        $mock = $this->createMock(Comparision::class);
        $mock1 = $this->createMock(Comparision::class);
        $criteria = Criteria::create();

        $criteria->andWhere($mock);
        $criteria->andWhere($mock1);

        $comparision = $criteria->getWhereExpression();
        $this->assertInstanceOf(CompositeComparision::class, $comparision);
    }

    /**
     * @runTestsInSeparateProcesses
     * @group   PBI#69673
     * @group   PBI#68318
     * @group   Criteria
     * @group   UnitTest
     * @group   SearchCriteria
     * @throws CriteriaError
     * @return void
     */
    public function test_orWhere_should_set_expression_with_instance_Comparision_when_no_other_expresion(): void
    {
        $mock = $this->createMock(Comparision::class);
        $criteria = Criteria::create();
        $criteria->orWhere($mock);

        $comparision = $criteria->getWhereExpression();
        $this->assertInstanceOf(Comparision::class, $comparision);
    }

    /**
     * @runTestsInSeparateProcesses
     * @group   PBI#69673
     * @group   PBI#68318
     * @group   Criteria
     * @group   UnitTest
     * @group   SearchCriteria
     * @throws CriteriaError
     * @return void
     */
    public function test_orWhere_should_set_expression_with_instance_CompositeComparision_when_other_expresion_already_exist(): void
    {
        $mock = $this->createMock(Comparision::class);
        $mock1 = $this->createMock(Comparision::class);
        $criteria = Criteria::create();
        $criteria->orWhere($mock);
        $criteria->orWhere($mock1);

        $comparision = $criteria->getWhereExpression();
        $this->assertInstanceOf(CompositeComparision::class, $comparision);
    }

    /**
     * @runTestsInSeparateProcesses
     * @group   PBI#69673
     * @group   PBI#68318
     * @group   Criteria
     * @group   UnitTest
     * @group   SearchCriteria
     * GT: "greater than" GTE: "greather than or equal"
     * @return void
     */
    public function test_hasLimitAndOffset_should_return_bool(): void
    {
        $criteria = Criteria::create();
        $criteria->updateLimitAndOffset(1, 0);

        $this->assertTrue($criteria->hasLimitAndOffset());
        $this->assertIsBool($criteria->hasLimitAndOffset());
    }
    /**
     * @runTestsInSeparateProcesses
     * @group   PBI#69673
     * @group   PBI#68318
     * @group   Criteria
     * @group   UnitTest
     * @group   SearchCriteria
     * GT: "greater than" GTE: "greather than or equal"
     * @return void
     */
    public function test_updateLimitAndOffset_should_return_void_when_limit_GT_cero_and_offset_GTE_cero(): void
    {
        $goodLimit = rand(1,10);
        $goodOffset = rand(0,10);
        $criteria = Criteria::create();
        $this->assertNull($criteria->updateLimitAndOffset($goodLimit, $goodOffset));
        $this->assertTrue($criteria->hasLimitAndOffset());

        $this->assertSame($goodLimit, $criteria->getLimit());
        $this->assertSame($goodOffset, $criteria->getOffset());
    }

    /**
     * @runTestsInSeparateProcesses
     * @group   PBI#69673
     * @group   PBI#68318
     * @group   Criteria
     * @group   UnitTest
     * @group   SearchCriteria
     * LTE: "less than or equal"
     * @return void
     */
    public function test_updateLimitAndOffset_should_throw_CriteriaError_when_limit_LTE_cero_or_offset_LT_cero(): void
    {
        $badLimit = rand(-10, 0);
        $goodLimit = rand(1,10);

        $badOffset = rand(-10, -1);
        $goodOffset = rand(0,10);
        $criteria = Criteria::create();

        $this->expectException(CriteriaError::class);
        $criteria->updateLimitAndOffset($badLimit, $badOffset);

        $this->expectException(CriteriaError::class);
        $criteria->updateLimitAndOffset($goodLimit, $badOffset);

        $this->expectException(CriteriaError::class);
        $criteria->updateLimitAndOffset($badLimit, $goodOffset);
    }

    /**
     * @runTestsInSeparateProcesses
     * @group   PBI#69673
     * @group   PBI#68318
     * @group   Criteria
     * @group   UnitTest
     * @group   SearchCriteria
     * LTE: "less than or equal"
     * @return void
     */
    public function test_addOrderBy_should_return_void_when_set_orderBy(): void
    {
        $criteria = Criteria::create();
        $this->assertNull($criteria->addOrderBy('lerele', 'ASC'));
    }

    /**
     * @runTestsInSeparateProcesses
     * @group   PBI#69673
     * @group   PBI#68318
     * @group   Criteria
     * @group   UnitTest
     * @group   SearchCriteria
     * @return void
     */
    public function test_getOrderBy_should_return_instanceOf_OrderBy(): void
    {
        $criteria = Criteria::create();
        $criteria->addOrderBy('lerele', 'ASC');
        $this->assertInstanceOf(OrderBy::class, $criteria->getOrderBy());
    }

    /**
     * @runTestsInSeparateProcesses
     * @group   PBI#69673
     * @group   PBI#68318
     * @group   Criteria
     * @group   UnitTest
     * @group   SearchCriteria
     * @return void
     */
    public function test_hasOrderBy_should_return_bool(): void
    {
        $criteria = Criteria::create();
        $this->assertIsBool($criteria->hasOrderBy());
    }

    /**
     * @runTestsInSeparateProcesses
     * @group   PBI#69673
     * @group   PBI#68318
     * @group   Criteria
     * @group   UnitTest
     * @group   SearchCriteria
     * @throws CriteriaError
     * @return void
     */
    public function test_getComparisions_should_return_an_array(): void
    {
        $mock = $this->createMock(Comparision::class);
        $mock1 = $this->createMock(Comparision::class);
        $criteria = Criteria::create();
        $criteria->andWhere($mock);
        $criteria->orWhere($mock1);

        $compasisions = $criteria->getComparisions();
        $this->assertIsArray($compasisions);
    }

    /**
     * @runTestsInSeparateProcesses
     * @group   PBI#69673
     * @group   PBI#68318
     * @group   Criteria
     * @group   UnitTest
     * @group   SearchCriteria
     * @throws CriteriaError
     * @return void
     */
    public function test_getWhereExpression_should_throw_when_no_expresion_set(): void
    {
        $criteria = Criteria::create();

        $this->expectException(CriteriaError::class);
        $criteria->getWhereExpression();
    }

    /**
     * @runTestsInSeparateProcesses
     * @group   PBI#69673
     * @group   PBI#68318
     * @group   Criteria
     * @group   UnitTest
     * @group   SearchCriteria
     * @throws CriteriaError
     * @return void
     */
    public function test_getWhereExpression_should_return_instance_of_IExpresion(): void
    {
        $criteria = Criteria::create();
        $mock = $this->createMock(Comparision::class);
        $criteria->andWhere($mock);

        $comparision = $criteria->getWhereExpression();
        $this->assertInstanceOf(Comparision::class, $comparision);
        $this->assertInstanceOf(IExpression::class, $comparision);

        $mock1 = $this->createMock(Comparision::class);
        $criteria->andWhere($mock1);

        $compositeComparision = $criteria->getWhereExpression();
        $this->assertInstanceOf(CompositeComparision::class, $compositeComparision);
        $this->assertInstanceOf(IExpression::class, $compositeComparision);

    }
}
