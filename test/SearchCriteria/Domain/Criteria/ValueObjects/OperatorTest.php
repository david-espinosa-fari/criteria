<?php


namespace Tests\GPM\Shared\SearchCriteria\Domain\Criteria\ValueObjects;


use GPM\Shared\SearchCriteria\Domain\Exceptions\CriteriaError;
use GPM\Shared\SearchCriteria\Domain\ValueObjects\Operator;
use PHPUnit\Framework\TestCase;

class OperatorTest extends TestCase
{

    /**
     * @group   PBI#69673
     * @group   PBI#68318
     * @group   Operator
     * @group   UnitTest
     * @group   SearchCriteria
     * @throws CriteriaError
     */
    public function testSetup(): Operator
    {
        $comparision = new Operator(Operator::LTE);
        $this->assertInstanceOf(Operator::class, $comparision);

        return $comparision;
    }

    /**
     * @group   PBI#69673
     * @runTestsInSeparateProcesses
     * @group   PBI#68318
     * @group   Operator
     * @group   UnitTest
     * @group   SearchCriteria
     * @throws CriteriaError
     */
    public function test_should_trow_CriteriaError_when_not_allowed_operator_arrive(): void
    {
        $this->expectException(CriteriaError::class);
        $comparision = new Operator('un-existent-operator');
    }
    /**
     * @depends testSetup
     * @group   PBI#69673
     * @runTestsInSeparateProcesses
     * @group   PBI#68318
     * @group   Operator
     * @group   UnitTest
     * @group   SearchCriteria
     */
    public function test_should_return_operator_when_call_class_as_string(Operator $operator): void
    {
        $stringOperator = (string)$operator;
        $this->assertIsString($stringOperator);
        $this->assertSame(Operator::LTE, $stringOperator);
    }


}
