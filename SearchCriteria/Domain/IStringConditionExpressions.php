<?php


namespace GPM\Shared\SearchCriteria\Domain;


interface IStringConditionExpressions
{
    public function createStringConditionExpression(Criteria $criteria): string;
}
