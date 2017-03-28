<?php

namespace tracker\enum;

use yii\base\InvalidParamException;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class BaseEnum implements IEnum
{
    public static function getList()
    {
        return [];
    }

    public static function getLabel($value)
    {
        $list = static::getList();

        if (!isset($list[$value])) {
            throw new InvalidParamException();
        }

        return $list[$value];
    }
}
