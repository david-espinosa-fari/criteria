<?php


namespace Tests\GPM\Shared\SearchCriteria\Domain\Criteria;


use GPM\Shared\SearchCriteria\Domain\Comparision;
use GPM\Shared\SearchCriteria\Domain\CompositeComparision;
use GPM\Shared\SearchCriteria\Domain\Exceptions\CriteriaError;
use GPM\Shared\SearchCriteria\Domain\IExpression;
use GPM\Shared\SearchCriteria\Domain\ValueObjects\Operator;
use PHPUnit\Framework\TestCase;

class CompositeComparisionTest extends TestCase
{

    /**
     * @group   PBI#69673
     * @group   PBI#68318
     * @group   Comparision
     * @group   UnitTest
     * @group SearchCriteria
     */
    public function testSetup(): CompositeComparision
    {
        $compositeComparision = new CompositeComparision('AND', [
            new Comparision('field1', Operator::NEQ, 'value1'),
            new CompositeComparision('OR', [new Comparision('field2', Operator::NEQ, 'value2')])
        ]);
        $this->assertInstanceOf(CompositeComparision::class, $compositeComparision);

        return $compositeComparision;
    }

    /**
     * @depends testSetup
     * @group   PBI#69673
     * @runTestsInSeparateProcesses
     * @group   PBI#68318
     * @group   Comparision
     * @group   UnitTest
     * @group   SearchCriteria
     * @param CompositeComparision $compositeComparision
     */
    public function test_CompositeComparision_should_implement_iExpresion(CompositeComparision $compositeComparision): void
    {
        $this->assertInstanceOf(IExpression::class, $compositeComparision);
    }

    /**
     * @depends testSetup
     * @group   PBI#69673
     * @runTestsInSeparateProcesses
     * @group   PBI#68318
     * @group   Comparision
     * @group   UnitTest
     * @group   SearchCriteria
     * @param CompositeComparision $compositeComparision
     */
    public function test_getExpressionList_should_return_an_array(CompositeComparision $compositeComparision): void
    {
        $this->assertIsArray($compositeComparision->getExpressionList());
    }

    /**
     * @depends testSetup
     * @group   PBI#69673
     * @runTestsInSeparateProcesses
     * @group   PBI#68318
     * @group   Comparision
     * @group   UnitTest
     * @group   SearchCriteria
     * @param CompositeComparision $compositeComparision
     */
    public function test_getType_should_return_a_string(CompositeComparision $compositeComparision): void
    {
        $this->assertIsString($compositeComparision->getType());
    }

    /**
     * @group   PBI#69673
     * @runTestsInSeparateProcesses
     * @group   PBI#68318
     * @group   Comparision
     * @group   UnitTest
     * @group   SearchCriteria
     * @throws CriteriaError
     */
    public function test_CompositeComparision_should_throw_CriteriaError_if_type_not_supported(): void
    {
        $this->expectException(CriteriaError::class);
        $compositeComparision = new CompositeComparision('UN-EXISTENT-TYPE', [
            new Comparision('field1', Operator::NEQ, 'value1')
        ]);
    }

    /**
     * @group   PBI#69673
     * @runTestsInSeparateProcesses
     * @group   PBI#68318
     * @group   Comparision
     * @group   UnitTest
     * @group   SearchCriteria
     * @throws CriteriaError
     */
    public function test_CompositeComparision_should_throw_CriteriaError_if_expression_not_instanceOf_IExpression(): void
    {
        $this->expectException(CriteriaError::class);
        $compositeComparision = new CompositeComparision('AND', [
            new Comparision('field1', Operator::NEQ, 'value1'),
            new CompositeComparision('OR', [new Comparision('field2', Operator::NEQ, 'value2')]),
            'something_distint_of IExpresion',
        ]);
    }

}
