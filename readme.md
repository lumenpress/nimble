# Eloquent ORM for WordPresse

- [Post/Page](#)
  - [Models](#)
  - [Buidlers](#)
    - [Types](#)
    - [Status](#)
    - [Slug](#)
    - [Url](#)
    - [Where & whereIn & orWhere & orWhereIn](#)
    - [Order By](#)
- [Menu](#)
    - [Location](#)
    - [Slug](#)
    - [Collection](#)
- [Term](#)
  - [Models](#)
  - [Buidlers](#)
    [Taxonomy](#)
    [Exists](#)
    [Where & whereIn & orWhere & orWhereIn](#)
- [Taxonomy/Category/Tag](#)
- [User](#)
- [Comment](#)

## Post/Page

### Models

- Inserts

```php
$post = new Post;
$post->title = 'title';
$post->content = 'content';
$post->save();
```

- Updates

```php
$post = Post::find(1);
$post->title = 'title';
$post->content = 'content';
$post->save();
```

### Buidlers

- Types

```php
// single type
Post::type('post');             
// equal
Post::where('post_type', 'post');

// multiple types
Post::type('page', 'post');
Post::type(['page', 'post']);
// equal
Post::whereIn('post_type', ['page', 'post']);
```

- Status

```php
// single status
Post::status('publish');
// equal
Post::where('post_status', 'publish');

// multiple status
Post::status('publish', 'draft');
Post::status(['publish', 'draft']);
// equal
Post::whereIn('post_status', ['publish', 'draft']);
```

- Slug

```php
Post::slug('post-name');
// equal
Post::where('post_name', 'post-name');
```

- Url

```php
Page::url('parent-name/post-name');
// equal
$parent = Page::slug('parent-name')->first();
Page::parent($parent->id)->slug('post-name')->first();
```

- Where & whereIn & orWhere & orWhereIn

```php
// query from post field
Page::where('field', 'value');

// query from post meta key
Page::where('meta.key', 'value');

// query from term taxonomy
Page::where('term.taxonomy', 'taxonomy');

// query from term name
Page::where('term.name', 'term name');

// query from term meta key
Page::where('term.meta.key', 'value');
```

- Order By

```php
// order by post field
Page::type('page')->orderBy('date', 'asc'); // asc & desc

// order by meta key value
Page::type('page')->orderBy('meta.key', 'desc');
```

## Menu

### Location

```php
Menu::location('main');
Menu::location('footer');
```

### Slug

```php
Menu::slug('main');
```

### Collection

```php
$menus = Menu::get();
$menus['main']; // location name
$menus[1]; // menu id
```

## Term

### Models

```php
$term = new Term;
$term->taxonomy = 'category';
$term->name = 'Category Name';
$term->save();
```

### Buidlers

Taxonomy

```php
Term::taxonomy('category');
```

Exists

```php
Term::exists($taxonomy, $name, $parent = 0);
```

Where & whereIn & orWhere & orWhereIn

```php
// query from term field
Term::where('field', 'value');

// query from term meta key
Term::where('meta.key', 'value');
```

## Taxonomy/Category/Tag

comming soon

## User

comming soon

## Comment

comming soon