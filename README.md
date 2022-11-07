# criteria
find by criteria in any table
This module is responsible for building complex conditionals that will later be translated to perform a database query, at the moment there is only support for sql but translations for mongodb, elastick, etc. could be included.
Advantages: 

low learning curve

Reduces the number of functions like findByName, findByLastname, findByAgeWhitName, etc.

Knowledge of the specific language to be translated into is not required to obtain records. For example, you do not need to know SQL to perform a complex query, such as (((A = X and B >= X) OR C < X) AND D like %X% )

It is agnostic to the technology to which it will be translated.

Has the ability to sort by field and fetch by limit and offset.

Could use with any tabla with any tecnology(for the moment only have support for sql).

Disadvantages:

I can't think of any right now

Constrains
You can only get records from one table at a time, forget about joins, relationships between tables and how they behave, they are created in the application without depending on the persistence system.

General flow

Create a criteria instance.

    $criteria = Criteria::create();

Added comparisions trough public methods where, andWhere,  orWhere

    $criteria->where(new Comparision('fieldNameFromDataBase', Operator::EQ, $backupfileId));
    $criteria->andWhere(new Comparision('serviceId', Operator::EQ,$serviceId));
    $criteria->orWhere(new Comparision('otherFieldName', Operator::GT,'someValue'));
    $criteria->updateLimitAndOffset(100, 2);
    $criteria->addOrderBy('serviceId', Order::ASC);
On infrastructure inject the expresion builder. Something that implement IStringConditionExpressions $expresionBuilder,  like SqlExpressionBuilder

    function __construct(IStringConditionExpressions $expresionBuilder)

create a method that accept the criteria as argument and invoke them. Example.
    
    public function findByCriteria(Criteria $criteria): List
        {
            $sql = 'SELECT * FROM yourSuperTable '.$this->expresionBuilder->createStringConditionExpression($criteria);

Supported comparisions operators

    \Shared\SearchCriteria\Domain\ValueObjects\Operator
    public const EQ  = '=';
    public const NEQ = '<>'; not equal
    public const LT  = '<';
    public const LTE = '<=';
    public const GT  = '>';
    public const GTE = '>=';
    public const CONTAINS = 'CONTAINS';
    
Supported orderings

    \Shared\SearchCriteria\Domain\ValueObjects\Order
    public const ASC = 'ASC';
    public const DESC = 'DESC';
    
How to use it


    $criteria = Criteria::create();
    $criteria->where(new Comparision('fieldNameFromDataBase', Operator::LT, $backupfileId));
    $criteria->andWhere(new Comparision('otherField1', Operator::EQ, $serviceId));
    $criteria->orWhere(new Comparision('serviceId', Operator::GT, 'someValue'));
    $criteria->andWhere(new Comparision('otherField2', Operator::NEQ, $serviceId));
    $criteria->updateLimitAndOffset(100, 2);
    $criteria->addOrderBy('serviceId', Order::ASC); 
    findByCriteria(Criteria $criteria)

Test

    phpunit --group SearchCriteria
    phpunit test/Shared/SearchCriteria/

Currently Out of scope
Support for other tecnologyes like Mongodb, ElasticSearch, etc.

Contributions
If you want to support other tecnologys, extend from Shared\SearchCriteria\Domain\ExpressionBuilder it will give you some advantages.

Related complementary documentations
tests/Shared/SearchCriteria
