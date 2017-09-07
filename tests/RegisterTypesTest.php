<?php

namespace Lumenpress\Fluid\Tests;

use Faker\Factory;
use Lumenpress\Fluid\Models\Post;
use Lumenpress\Fluid\Models\Taxonomy;

class RegisterTypesTest extends TestCase
{
    /**
     * @group register
     */
    public function testRegisterPostTypes()
    {
        $typeClasses = [
            'post' => CustomPost::class,
            'page' => CustomPage::class,
        ];

        $model = new Post();

        foreach ($typeClasses as $type => $class) {
            Post::register($type, $class);
            $this->assertSame(get_class($model->newInstance(['post_type' => $type])), $class, $type);
            $this->assertSame(Post::getClassNameByType($type), $class, $type);
        }

        $model->title = 'Title1';
        $model->save();

        $model = Post::find($model->id);

        $this->assertSame(get_class($model), $typeClasses['post'], 'message');

        $model = new Post();
        $model->title = 'Title1';
        $model->type = 'page';
        $model->save();

        $model = Post::find($model->id);

        $this->assertSame(get_class($model), $typeClasses['page'], 'message');
    }

    /**
     * @group register
     */
    public function testRegisterTaxonomyTypes()
    {
        $typeClasses = [
            'category' => CustomCategory::class,
            'post_tag' => CustomTag::class,
        ];

        $model = new Taxonomy();

        foreach ($typeClasses as $type => $class) {
            Taxonomy::register($type, $class);
            $this->assertSame(get_class($model->newInstance(['taxonomy' => $type])), $class, $type);
            $this->assertSame(Taxonomy::getClassNameByType($type), $class, $type);
        }

        $model = new Taxonomy();
        $model->name = 'Foo';
        $model->taxonomy = 'category';
        $model->save();

        $model = Taxonomy::find($model->id);

        $this->assertSame(get_class($model), $typeClasses['category'], 'message');
    }

    /**
     * @group register
     */
    public function testPostQueryBuilder()
    {
        $faker = Factory::create();

        $types = [
            'post' => CustomPost::class,
            'page' => CustomPage::class,
        ];

        foreach ($types as $key => $value) {
            Post::register($key, $value);
        }

        for ($i = 0; $i < 10; $i++) {
            $model = new Post();
            $model->title = $faker->name;
            $model->type = $faker->randomElement(array_keys($types));
            $model->save();
        }

        $models = Post::get();

        foreach ($models as $model) {
            $this->assertEquals($types[$model->type], get_class($model), $model->type);
        }
    }

    /**
     * @group register
     */
    public function testTaxonomyQueryBuilder()
    {
        $faker = Factory::create();

        $types = [
            'category' => CustomCategory::class,
            'post_tag' => CustomTag::class,
        ];

        foreach ($types as $key => $value) {
            Taxonomy::register($key, $value);
        }

        for ($i = 0; $i < 10; $i++) {
            $model = new Taxonomy();
            $model->name = $faker->name;
            $model->taxonomy = $faker->randomElement(array_keys($types));
            $model->save();
        }

        $models = Taxonomy::get();

        foreach ($models as $model) {
            $this->assertEquals($types[$model->type], get_class($model), $model->type);
        }
    }

    /**
     * @group register
     */
    public function testTaxonomyPost()
    {
        $faker = Factory::create();

        $postTypes = [
            'post' => CustomPost::class,
            'page' => CustomPage::class,
        ];

        $taxonomyTypes = [
            'category' => CustomCategory::class,
            'post_tag' => CustomTag::class,
        ];

        foreach ($taxonomyTypes as $taxonomyType => $taxonomyClass) {
            Taxonomy::register($taxonomyType, $taxonomyClass);
            $taxonomy = new Taxonomy();
            $taxonomy->name = $faker->name;
            $taxonomy->taxonomy = $taxonomyType;
            $taxonomy->save();
            foreach ($postTypes as $postType => $postClass) {
                Post::register($postType, $postClass);
                $post = new Post();
                $post->title = $faker->name;
                $post->type = $postType;
                $post->tax->{$taxonomy->type} = $taxonomy->name;
                $post->save();
            }
            foreach ($taxonomy->posts as $post) {
                $this->assertEquals(get_class($post), $postTypes[$post->type], $post->type);
            }
        }
    }

    /**
     * @group register
     */
    public function testPostTaxonomy()
    {
        $faker = Factory::create();

        $postTypes = [
            'post' => CustomPost::class,
            'page' => CustomPage::class,
        ];

        $taxonomyTypes = [
            'category' => CustomCategory::class,
            'post_tag' => CustomTag::class,
        ];

        foreach ($postTypes as $postType => $postClass) {
            Post::register($postType, $postClass);

            $post = new Post();
            $post->title = $faker->name;
            $post->type = $postType;

            foreach ($taxonomyTypes as $taxonomyType => $taxonomyClass) {
                Taxonomy::register($taxonomyType, $taxonomyClass);
                $taxonomy = new Taxonomy();
                $taxonomy->name = $faker->name;
                $taxonomy->taxonomy = $taxonomyType;
                $taxonomy->save();
                $post->tax->{$taxonomy->type} = $taxonomy->name;
            }

            $post->save();

            foreach ($post->tax as $taxonomy) {
                $this->assertEquals(get_class($taxonomy), $taxonomyTypes[$taxonomy->type], $taxonomy->type);
            }
        }
    }
}

class CustomPost extends Post
{
}

class CustomPage extends Post
{
    protected $modelType = 'page';
}

class CustomCategory extends Taxonomy
{
    protected $taxonomy = 'category';
}

class CustomTag extends Taxonomy
{
    protected $taxonomy = 'post_tag';
}
