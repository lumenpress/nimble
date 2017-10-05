<?php

namespace LumenPress\Nimble\Tests;

use Laravel\Lumen\Application;
use LumenPress\Nimble\ServiceProvider;
use LumenPress\Testing\WordPressTestCase;

class ServiceProviderTest extends TestCase
{
    use WordPressTestCase;

    protected function createApplication($config)
    {
        require_once realpath(dirname(PHPUNIT_COMPOSER_INSTALL).'/lumenpress/testing').'/tests/wp-tests-load.php';

        $app = new Application;
        $app->withFacades();
        $app->withEloquent();

        $app->make('config')->set('nimble', $config);

        // $app->register(ServiceProvider::class);
        $provider = new ServiceProvider($app);

        $provider->registerPostTemplates();
        $provider->registerObjects();
        $provider->registerModels();

        return $app;
    }

    /**
     * @group provider
     */
    public function testPostTemplates()
    {
        $app = $this->createApplication([
            'post_templates' => [
                // string
                'about' => 'About Us',

                // array
                'home' => [
                    'name'      => 'Home Page',
                    'post_type' => 'post',  // string|array
                ],

                'slider' => [
                    'name'      => 'Slider',
                    'post_type' => ['page', 'post'],  // string|array
                ],
            ],
        ]);

        foreach (['page', 'post'] as $type) {
            $templates[$type] = apply_filters("theme_{$type}_templates", []);
        }

        foreach (config('nimble.post_templates') as $key => $args) {
            if (is_array($args) && array_key_exists('post_type', $args)) {
                foreach ((array) $args['post_type'] as $postType) {
                    $this->assertTrue(array_key_exists($key, $templates[$postType]), $key);
                }
            } else {
                $this->assertTrue(array_key_exists($key, $templates['page']), $key);
            }
        }
    }

    /**
     * @group provider
     */
    public function testPostTypes()
    {
        $app = $this->createApplication([
            'post_types' => [
                'news' => [
                    'label' => 'News',
                    'public' => true,
                ],
            ],
        ]);

        global $wp_post_types;

        foreach (config('nimble.post_types') as $type => $args) {
            $this->assertTrue(array_key_exists($type, $wp_post_types), $type);
        }
    }

    /**
     * @group provider
     */
    public function testTaxonomies()
    {
        $app = $this->createApplication([
            'taxonomies' => [
                'genre' => [
                    'label' => 'Genre',
                    'public' => true,
                    'object_type' => 'post',
                ],
            ],
        ]);

        global $wp_taxonomies;

        foreach (config('nimble.taxonomies') as $taxonomy => $args) {
            $this->assertTrue(array_key_exists($taxonomy, $wp_taxonomies), $taxonomy);
        }
    }
}
