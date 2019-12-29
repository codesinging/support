<?php
/**
 * Author: CodeSinging <codesinging@gmail.com>
 * Time: 2019/12/29 10:41
 */

if (!function_exists('load_config')) {
    /**
     * Load configuration from a file.
     *
     * @param string $file
     *
     * @return array
     */
    function load_config(string $file)
    {
        $type = pathinfo($file, PATHINFO_EXTENSION);
        $config = [];

        switch ($type) {
            case 'php':
                $config = include $file;
                break;
            case 'yml':
            case 'yaml':
                if (function_exists('yaml_parse_file')) {
                    $config = yaml_parse_file($file);
                }
                break;
            case 'ini':
                $config = parse_ini_file($file, true, INI_SCANNER_TYPED);
                break;
            case 'json':
                $config = json_decode(file_get_contents($file), true);
                break;
        }

        return is_array($config) ? $config : [];
    }
}