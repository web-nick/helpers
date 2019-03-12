<?php

namespace webnick\helpers;

/**
 * Class FileHelper helper для работы с файловой системой
 *
 * @package wn\libraries\helpers
 */
class FileHelper
{
    /**
     * Мультибайтовый аналог PHP-функции pathinfo()
     *
     * @param string $path
     * @param int $options
     * @return array|string
     */
    public static function mb_pathinfo(string $path, int $options = null)
    {
        $ret = ['dirname' => '', 'basename' => '', 'extension' => '', 'filename' => ''];

        $pathinfo = [];

        if (preg_match('%^(.*?)[\\\\/]*(([^/\\\\]*?)(\.([^\.\\\\/]+?)|))[\\\\/\.]*$%im', $path, $pathinfo)) {
            if (array_key_exists(1, $pathinfo))
                $ret['dirname'] = $pathinfo[1];

            if (array_key_exists(2, $pathinfo))
                $ret['basename'] = $pathinfo[2];

            if (array_key_exists(5, $pathinfo))
                $ret['extension'] = $pathinfo[5];

            if (array_key_exists(3, $pathinfo))
                $ret['filename'] = $pathinfo[3];
        }


        switch ($options) {
            case PATHINFO_DIRNAME:
            case 'dirname':
                return $ret['dirname'];

            case PATHINFO_BASENAME:
            case 'basename':
                return $ret['basename'];

            case PATHINFO_EXTENSION:
            case 'extension':
                return $ret['extension'];

            case PATHINFO_FILENAME:
            case 'filename':
                return $ret['filename'];

            default:
                return $ret;
        }
    }
}