<?php

namespace Tests\GPM\Shared\SearchCriteria\Infraestructure\SqlExpressionBuilder;


use GPM\Shared\SearchCriteria\Infraestructure\SqlExpressionBuilder;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Shared\ReflectClassUtility;

abstract class SqlExpressionBuilderTest extends KernelTestCase
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface|null
     */
    protected static $container;

    /**
     * @group   PBI#69673
     * @group   SqlExpressionBuilder
     * @group   SearchCriteria
     * @group   Integration
     * @group   UnitTest
     * @return SqlExpressionBuilder
     */
    public function sqlExpressionBuilderSetup(): SqlExpressionBuilder
    {
        self::bootKernel();
        self::$container = self::$kernel->getContainer();
        $sqlExpressionBuilder = self::$container->get('gpm.sql_expression_builder');

        $this->assertInstanceOf(SqlExpressionBuilder::class, $sqlExpressionBuilder);

        return $sqlExpressionBuilder;
    }

    protected static function getPrivateMethods(SqlExpressionBuilder $obj, string $privateMethodName, array $args)
    {
        return ReflectClassUtility::callPrivateMethod($obj, $privateMethodName, $args);
    }

}
