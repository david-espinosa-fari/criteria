<?php


namespace GPM\Shared\SearchCriteria\Domain\ValueObjects;


use GPM\Shared\Util\Util;

class Value
{
    /** @var mixed */
    private $value;

    /**
     * @param mixed $value
     */
    public function __construct($value)
    {
        if (! is_int($value) && ! is_float($value))
        {
            $this->value = Util::scapeSpecialChars($value);
        }else{
            $this->value = $value;
        }
    }

    /**
     * @return mixed string|int|null
     */
    public function getValue()
    {
        return $this->value;
    }

}
