<?php

namespace wn\libraries\helpers;

/**
 * Class SecurityHelper helper для защиты принимаемых параметров
 *
 * @package wn\libraries\helpers
 */
class SecurityHelper
{
    /**
     * @var array $defaultOptions возможные значения:
     *  - 'tags' => 'strip', 'mnemonic', 'none';
     *  - 'quotes' => 'delete', 'slash', 'mnemonic', 'none'
     */
    public static $defaultOptions = [
        'tags' => 'strip',
        'quotes' => 'delete'
    ];

    /**
     * Обработать глобальные массивы, по параметрам безопасности
     *
     * @param array $options
     * @throws \Exception
     */
    public static function handleGlobalArrays(array $options = null): void
    {
        self::handleOptions($options);

        $_REQUEST = self::handleParam($_REQUEST, $options);

        $_GET = self::handleParam($_GET, $options);

        $_POST = self::handleParam($_POST, $options);

        $_COOKIE = self::handleParam($_COOKIE, $options);
    }

    /**
     * Обработать параметр по переданным опциям безопасности
     *
     * @param string|array $param обработать строку или массив, в котором будут обработаны все строковые значения
     * @param array $options
     * @return string|array
     * @throws \Exception
     */
    public static function handleParam($param, array $options = null)
    {
        self::handleOptions($options);

        if (is_array($param)) {
            foreach ($param as &$value)
                $value = self::handleParam($value, $options);

            return $param;
        }

        if (!is_string($param))
            return $param;

        $param = self::handleMnemonics($param, $options);

        $param = self::handleTags($param, $options);

        $param = self::handleQuotes($param, $options);

        return trim($param);
    }

    /**
     * Обработать мнемоники в строке
     *
     * @param string $str
     * @param array $options
     * @return string
     */
    protected static function handleMnemonics(string $str, array &$options): string
    {
        if ($options['quotes'] == 'mnemonic')
            return htmlspecialchars($str, ENT_QUOTES);
        elseif ($options['tags'] == 'mnemonic')
            return htmlspecialchars($str, ENT_NOQUOTES);

        return $str;
    }

    /**
     * Обработать теги в строке
     *
     * @param string $str
     * @param array $options
     * @return string
     */
    protected static function handleTags(string $str, array &$options): string
    {
        if ($options['tags'] == 'strip')
            return strip_tags($str);

        return $str;
    }

    /**
     * Обработать кавычки в строке
     *
     * @param string $str
     * @param array $options
     * @return string
     */
    protected static function handleQuotes(string $str, array &$options): string
    {
        if ($options['quotes'] == 'slash')
            return addslashes($str);
        elseif ($options['quotes'] == 'delete')
            return preg_replace('~[\'"]~', '', $str);

        return $str;
    }

    /**
     * Обработать опции класса
     *
     * @param array|null $options
     * @throws \Exception
     */
    protected static function handleOptions(array &$options = null): void
    {
        $options or $options = self::$defaultOptions;

        self::checkOptions($options);
    }

    /**
     * Проверка опций обработки на корректность
     *
     * @param array|null $options
     * @throws \Exception
     */
    protected static function checkOptions(array &$options): void
    {
        $correct_array = [
            'tags' => ['strip', 'mnemonic', 'none'],
            'quotes' => ['delete', 'slash', 'mnemonic', 'none']
        ];

        if (!$int_arr = array_intersect_key($correct_array, $options) or count($int_arr) != count($correct_array))
            throw new \Exception('Incorrect options array');

        foreach ($options as $key => $value) {
            if (!array_intersect($options, $correct_array[$key]))
                throw new \Exception("Incorrect value '$value' in key '$key' of options array");
        }
    }
}