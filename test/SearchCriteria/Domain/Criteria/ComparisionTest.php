<?php


namespace Tests\GPM\Shared\SearchCriteria\Domain\Criteria;


use GPM\Shared\SearchCriteria\Domain\Comparision;
use GPM\Shared\SearchCriteria\Domain\IExpression;
use GPM\Shared\SearchCriteria\Domain\ValueObjects\Operator;
use PHPUnit\Framework\TestCase;

class ComparisionTest extends TestCase
{

    /**
     * @group   PBI#69673
     * @group   PBI#68318
     * @group   Comparision
     * @group   UnitTest
     * @group SearchCriteria
     */
    public function testSetup(): Comparision
    {
        $comparision = new Comparision('field1', Operator::GT, '2');
        $this->assertInstanceOf(Comparision::class, $comparision);

        return $comparision;
    }

    /**
     * @depends testSetup
     * @group   PBI#69673
     * @runTestsInSeparateProcesses
     * @group   PBI#68318
     * @group   Comparision
     * @group   UnitTest
     * @group SearchCriteria
     */
    public function test_Comparision_should_implement_iExpresion(Comparision $comparision): void
    {
        $this->assertInstanceOf(IExpression::class, $comparision);
    }
}
