<?php

namespace wn\libraries\helpers;

/**
 * Class ArrayHelper helper для работы с массивами
 *
 * @package wn\libraries\helpers
 */
class ArrayHelper
{
    /**
     * Найти первый элемент в массиве, используя функцию обратного вызова
     *
     * @param array $array
     * @param callable $func
     * @param mixed $data
     * @return mixed
     */
    public static function find(array $array, callable $func, $data = null)
    {
        foreach ($array as $value) {
            if (call_user_func($func, $value, $data) === true)
                return $value;
        }

        return null;
    }
}