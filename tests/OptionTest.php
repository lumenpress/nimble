<?php

namespace LumenPress\Nimble\Tests;

use LumenPress\Nimble\Models\Option;

class OptionTest extends TestCase
{
    /**
     * @group option
     */
    public function testOption()
    {
        require_once realpath(dirname(PHPUNIT_COMPOSER_INSTALL).'/lumenpress/testing').'/tests/wp-tests-load.php';

        $options = Option::getInstance();

        foreach ($options as $key => $option) {
            $this->assertEquals($option->value, get_option($option->key), $option->key);
        }
    }
}
