<?php

namespace tracker\enum;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */

interface IEnum
{
    /**
     * Must be returned an array, when:
     *   indexes - keys of value enum,
     *   values - string description enum value.
     *
     * @return array
     */
    public static function getList();

    /**
     * Must be returned sting desctiption from getList() by index.
     *
     * @param $value integer|string one of indexes from getList
     *
     * @throw yii\base\InvalidParamException if $value is wrong
     * @return string
     */
    public static function getLabel($value);
}
