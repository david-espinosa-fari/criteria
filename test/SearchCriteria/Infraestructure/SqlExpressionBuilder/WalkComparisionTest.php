<?php


namespace Tests\GPM\Shared\SearchCriteria\Infraestructure\SqlExpressionBuilder;


use GPM\Shared\SearchCriteria\Domain\Comparision;
use GPM\Shared\SearchCriteria\Domain\Exceptions\CriteriaError;
use GPM\Shared\SearchCriteria\Domain\ValueObjects\Operator;
use GPM\Shared\SearchCriteria\Infraestructure\SqlExpressionBuilder;
use GPM\Shared\Util\Uuid;

class WalkComparisionTest extends SqlExpressionBuilderTest
{

    private static SqlExpressionBuilder $sqlExpressionBuilder;
    private static string $equal;

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
     * @return void
     */
    public function test_should_return_string_concated_with_two_points_comparision(SqlExpressionBuilder $builder): void
    {
        $ran = Uuid::random();
        self::$equal =

        $expresion = $ran.' '. sprintf(SqlExpressionBuilder::$comparisonMap[Operator::EQ], ':'.$ran);
        $comparision = new Comparision($ran,Operator::EQ,1);

        $formatedExpresion = self::getPrivateMethods($builder, 'walkComparison', [$comparision]);
        $this->assertEquals($expresion, $formatedExpresion);
    }

    /**
     * @depends testSetup
     * @group   PBI#69673
     * @group   SqlExpressionBuilder
     * @group   SearchCriteria
     * @group   UnitTest
     * @param SqlExpressionBuilder $builder
     * @throws CriteriaError
     * @return void
     */
    public function test_should_return_string_with_EQUAL_operator_set(SqlExpressionBuilder $builder): void
    {
        $ran = Uuid::random();

        $expresion = $ran.' '. sprintf(SqlExpressionBuilder::$comparisonMap[Operator::EQ], ':'.$ran);
        $comparision = new Comparision($ran,Operator::EQ,1);

        $formatedExpresion = self::getPrivateMethods($builder, 'walkComparison', [$comparision]);
        $this->assertEquals($expresion, $formatedExpresion);
    }

    /**
     * @depends testSetup
     * @group   PBI#69673
     * @group   SqlExpressionBuilder
     * @group   SearchCriteria
     * @group   UnitTest
     * @param SqlExpressionBuilder $builder
     * @throws CriteriaError
     * @return void
     */
    public function test_should_return_string_with_like_given_contain_operator(SqlExpressionBuilder $builder): void
    {
        $randomString = Uuid::random();
        $expresion = $randomString.' '. sprintf(SqlExpressionBuilder::$comparisonMap[Operator::CONTAINS], ':'.$randomString);
        $comparision = new Comparision($randomString,Operator::CONTAINS,1);

        $formatedExpresion = self::getPrivateMethods($builder, 'walkComparison', [$comparision]);
        $this->assertEquals($expresion, $formatedExpresion);
    }

    /**
     * @depends testSetup
     * @group   PBI#69673
     * @group   SqlExpressionBuilder
     * @group   SearchCriteria
     * @group   UnitTest
     * @param SqlExpressionBuilder $builder
     * @throws CriteriaError
     * @return void
     */
    public function test_should_return_string_with_ISnuLL_given_Comparision_operator_Equal_and_value_NUL(SqlExpressionBuilder $builder): void
    {
        $randomString = Uuid::random();
        $expresion = $randomString.' IS NULL';
        $comparision = new Comparision($randomString,Operator::EQ,'null');

        $formatedExpresion = self::getPrivateMethods($builder, 'walkComparison', [$comparision]);
        $this->assertEquals($expresion, $formatedExpresion);
    }

    /**
     * @depends testSetup
     * @group   PBI#69673
     * @group   SqlExpressionBuilder
     * @group   SearchCriteria
     * @group   UnitTest
     * @param SqlExpressionBuilder $builder
     * @throws CriteriaError
     * @return void
     */
    public function test_should_return_string_with_IS_NOT_NULL_given_Comparision_operator_not_equal_and_value_NUL(SqlExpressionBuilder $builder): void
    {
        $randomString = Uuid::random();
        $expresion = $randomString.' IS NOT NULL';
        $comparision = new Comparision($randomString,Operator::NEQ,'null');

        $formatedExpresion = self::getPrivateMethods($builder, 'walkComparison', [$comparision]);
        $this->assertEquals($expresion, $formatedExpresion);
    }

}
