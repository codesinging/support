<?php
/**
 * Author: CodeSinging <codesinging@gmail.com>
 * Time: 2019/12/29 10:43
 */

namespace CodeSinging\Support\Tests;

use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{
    public function testLoadConfig()
    {
        $data = ['name' => 'helpers'];

        self::assertEquals($data, load_config(__DIR__ . '/../data/config.php'));
        self::assertEquals($data, load_config(__DIR__ . "/../data/config.json"));
        self::assertEquals($data, load_config(__DIR__ . "/../data/config.ini"));

        if (function_exists('yaml_parse_file')) {
            self::assertEquals($data, load_config(__DIR__ . "/../data/config.yaml"));
        }

    }
}