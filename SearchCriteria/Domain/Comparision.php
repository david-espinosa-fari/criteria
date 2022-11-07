<?php


namespace GPM\Shared\SearchCriteria\Domain;


use GPM\Shared\SearchCriteria\Domain\ValueObjects\Field;
use GPM\Shared\SearchCriteria\Domain\ValueObjects\Operator;
use GPM\Shared\SearchCriteria\Domain\ValueObjects\Value;

class Comparision implements IExpression
{

    private $field;
    private $op;
    private $value;

    /**
     * @param string $field
     * @param string $operator
     * @param mixed string | int $value
     * @throws Exceptions\CriteriaError
     */
    public function __construct(string $field, string $operator, $value)
    {
        $this->field = new Field($field);
        $this->op    = new Operator($operator);
        $this->value = new Value($value);
    }

    public function getField(): string
    {
        return $this->field->getValue();
    }

    public function getOperator(): string
    {
        return $this->op->getValue();
    }

    public function getValue()
    {
        return $this->value->getValue();
    }

}
