<?php


namespace Tests\GPM\Shared\SearchCriteria\Infraestructure\SqlExpressionBuilder;


use GPM\Shared\SearchCriteria\Domain\Criteria;
use GPM\Shared\SearchCriteria\Infraestructure\SqlExpressionBuilder;

class AttachLimitOffsetTest extends SqlExpressionBuilderTest
{

    private static SqlExpressionBuilder $sqlExpressionBuilder;

    /**
     * @group   PBI#69673
     * @group   SqlExpressionBuilder
     * @group   SearchCriteria
     * @group   UnitTest
     * @return SqlExpressionBuilder
     * @large
     */
    public function testSetup(): SqlExpressionBuilder
    {
        self::$sqlExpressionBuilder = self::$sqlExpressionBuilder ?? $this->sqlExpressionBuilderSetup();
        $this->assertInstanceOf(SqlExpressionBuilder::class, self::$sqlExpressionBuilder);

        return self::$sqlExpressionBuilder;
    }

    /**
     * @depends testSetup
     * @runTestsInSeparateProcesses
     * @group   PBI#69673
     * @group   SqlExpressionBuilder
     * @group   SearchCriteria
     * @group   UnitTest
     * @param SqlExpressionBuilder $builder
     * @return void
     */
    public function test_should_return_this_string_when_add_limit_and_offset(SqlExpressionBuilder $builder): void
    {
        $criteria = Criteria::create();
        $criteria->updateLimitAndOffset(100, 2);
        $expresion = ' LIMIT 100 OFFSET 2';

        $formatedExpresion = self::getPrivateMethods($builder, 'attachLimitOffset', [$criteria]);
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
    public function test_should_return_empty_string_when_no_limit_and_offset(SqlExpressionBuilder $builder): void
    {
        $criteria = Criteria::create();
        $expresion = '';

        $formatedExpresion = self::getPrivateMethods($builder, 'attachLimitOffset', [$criteria]);
        $this->assertEquals($expresion, $formatedExpresion);
    }

}
