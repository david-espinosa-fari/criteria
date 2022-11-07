<?php


namespace GPM\Shared\SearchCriteria\Domain\ValueObjects;


use GPM\Shared\Util\Util;

class Field
{

    private string $field;

    public function __construct(string $field)
    {
        $this->setField($field);
    }

    public function __toString()
    {
        return $this->field;
    }

    private function setField(string $field): void
    {
        $this->field = Util::scapeSpecialChars($field);
    }

    public function getValue(): string
    {
        return (string)$this;
    }

}
