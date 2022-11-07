<?php


namespace Tests\GPM\Shared\SearchCriteria\Infraestructure\SqlExpressionBuilder;


use GPM\Shared\SearchCriteria\Domain\Comparision;
use GPM\Shared\SearchCriteria\Domain\CompositeComparision;
use GPM\Shared\SearchCriteria\Domain\Exceptions\CriteriaError;
use GPM\Shared\SearchCriteria\Domain\ValueObjects\Operator;
use GPM\Shared\SearchCriteria\Infraestructure\SqlExpressionBuilder;
use GPM\Shared\Util\Uuid;

class WalkCompositeExpressionTest extends SqlExpressionBuilderTest
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
     * @group   SqlExpressionBuilder
     * @group   SearchCriteria
     * @group   UnitTest
     * @param SqlExpressionBuilder $builder
     * @throws CriteriaError
     * @throws CriteriaError
     * @return void
     */
    public function test_should_return_string_ignoring_and_given_only_one_comparision_and_first_expression(SqlExpressionBuilder $builder): void
    {
        $ran = Uuid::random();
        $expectedExpresion = '('.$ran.' = :'.$ran.')';
        $comparision = new Comparision($ran,Operator::EQ,1);

        $compositeComparision = new CompositeComparision(CompositeComparision::TYPE_AND, [$comparision]);

        $formatedExpresion = self::getPrivateMethods($builder, 'walkCompositeExpression', [$compositeComparision]);
        $this->assertEquals($expectedExpresion, $formatedExpresion);
    }

    /**
     * @depends testSetup
     * @group   PBI#69673
     * @group   SqlExpressionBuilder
     * @group   SearchCriteria
     * @group   UnitTest
     * @param SqlExpressionBuilder $builder
     * @throws CriteriaError
     * @throws CriteriaError
     * @return void
     */
    public function test_should_concat_string_with_OR_given_CompositeComparision(SqlExpressionBuilder $builder): void
    {
        $expectedExpresion = '((field = :field) OR field2 != :field2)';
        $comparision = new Comparision('field',Operator::EQ,1);
        $comparision2 = new Comparision('field2',Operator::NEQ,1);

        $compositeComparision1 = new CompositeComparision(CompositeComparision::TYPE_AND, [$comparision]);

        $compositeComparision = new CompositeComparision(CompositeComparision::TYPE_OR, [$compositeComparision1,$comparision2]);

        $formatedExpresion = self::getPrivateMethods($builder, 'walkCompositeExpression', [$compositeComparision]);
        $this->assertEquals($expectedExpresion, $formatedExpresion);
    }

    /**
     * @depends testSetup
     * @group   PBI#69673
     * @group   SqlExpressionBuilder
     * @group   SearchCriteria
     * @group   UnitTest
     * @param SqlExpressionBuilder $builder
     * @throws CriteriaError
     * @throws CriteriaError
     * @return void
     */
    public function test_should_concat_string_with_AND_given_CompositeComparision(SqlExpressionBuilder $builder): void
    {
        $expectedExpresion = '((field = :field) AND field2 != :field2)';
        $comparision = new Comparision('field',Operator::EQ,1);
        $comparision2 = new Comparision('field2',Operator::NEQ,1);

        $compositeComparision1 = new CompositeComparision(CompositeComparision::TYPE_OR, [$comparision]);

        $compositeComparision = new CompositeComparision(CompositeComparision::TYPE_AND, [$compositeComparision1,$comparision2]);

        $formatedExpresion = self::getPrivateMethods($builder, 'walkCompositeExpression', [$compositeComparision]);
        $this->assertEquals($expectedExpresion, $formatedExpresion);
    }
    /**
     * @depends testSetup
     * @group   PBI#69673
     * @group   SqlExpressionBuilder
     * @group   SearchCriteria
     * @group   UnitTest
     * @param SqlExpressionBuilder $builder
     * @throws CriteriaError
     * @throws CriteriaError
     * @return void
     */
    public function test_should_throw_if_TYPE_of_CompositeComparision_unknow(SqlExpressionBuilder $builder): void
    {
        $mock = $this->createMock(CompositeComparision::class);
        $mock->method('getType')->willReturn('non-existent-Type');

        $compositeComparision = new CompositeComparision(CompositeComparision::TYPE_OR, [$mock]);

        $this->expectException(CriteriaError::class);
        $formatedExpresion = self::getPrivateMethods($builder, 'walkCompositeExpression', [$compositeComparision]);
    }


}
