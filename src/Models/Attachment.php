<?php 

namespace Lumenpress\Fluid\Models;

use Illuminate\Support\Str;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use Intervention\Image\ImageManager;

class Attachment extends AbstractPost
{
    protected static $sizes = [
        'thumbnail' => [150, 150],
        'medium' => [300, 300],
        'medium_large' => [768, 768],
        'large' => [1024, 1024],
    ];

    protected $postType = 'attachment';

    protected $file;

    protected $appends = [
        // 'filename',
        'width',
        'height',
        // 'sizes',
    ];

    protected $hidden = [
        'post_title',
        'post_excerpt',
        'post_content',
        'post_date',
        'post_date_gmt',
        'post_modified',
        'post_modified_gmt',
        'post_mime_type',
        'post_author',
        'post_content_filtered',
        'post_parent',
        'post_password',
        'menu_order',
        'guid',
        'comment_count',
        'post_type',
        'post_status',
        'comment_status',
        'ping_status', 
        'post_name',
        'pinged',
        'to_ping',
        // 'meta',
    ];

    protected $aliases = [
        'title'         => 'post_title',
        'name'          => 'post_name',
        'mime_type'     => 'post_mime_type',
        'url'           => 'link',
        'date'          => 'post_date',
        'modified'      => 'post_modified',
        'caption'       => 'post_excerpt',
        'description'   => 'post_content',
        'alt'           => 'meta._wp_attachment_image_alt',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->post_status = 'inherit';
        $this->ping_status = 'closed';
    }

    public function __toString()
    {
        return is_string($this->link) ? $this->link : '';
    }

    /**
     * Accessor for file attribute.
     *
     * @return returnType
     */
    public function getFileAttribute($value)
    {
        return $this->file;
    }

    /**
     * Mutator for file attribute.
     *
     * @return void
     */
    public function setFileAttribute($value)
    {
        $this->file = new File($value);

        if ($this->file->isImage()) {
            foreach (static::$sizes as $name => $args) {
                $this->file->addImageSize($name, $args);
            }
        }

        if (! $this->post_title) {
            $this->post_title = $this->file->name;
        }

        if (! $this->post_name) {
            $this->post_name = $this->file->name;
        }

        $this->post_mime_type = $this->file->mimeType;
    }

    /**
     * Accessor for width attribute.
     *
     * @return returnType
     */
    public function getWidthAttribute($value)
    {
        if ($this->file) {
            return $this->file->width;
        }

        return data_get($this, 'meta._wp_attachment_metadata.width');
    }

    /**
     * Accessor for height attribute.
     *
     * @return returnType
     */
    public function getHeightAttribute($value)
    {
        if ($this->file) {
            return $this->file->height;
        }

        return data_get($this, 'meta._wp_attachment_metadata.height');
    }

    /**
     * Accessor for sizes attribute.
     *
     * @return returnType
     */
    public function getSizesAttribute($value)
    {
        return array_map(function($item) {
            $item['link'] = dirname($this->link).'/'.$item['file'];
            return $item;
        }, data_get($this, 'meta._wp_attachment_metadata.sizes', []));
    }

    /**
     * Accessor for link attribute.
     *
     * @return returnType
     */
    public function getLinkAttribute($value)
    {
        return function_exists('wp_get_attachment_url') ? wp_get_attachment_url($this->ID) : null;
    }

    public function save(array $options = [])
    {
        if (! $this->file) {
            return false;
        }

        $this->file->save();

        $this->meta->_wp_attached_file = $this->file->uniquePath;

        if ($this->file->isImage()) {
            $this->meta->_wp_attachment_metadata = $this->file->metadata;
        }

        return parent::save($options);
    }
}

class File
{
    protected $path;

    protected $filesystem;

    protected $imageManager;

    protected $metadata = [];

    protected $attributes = [];

    protected $imageSizes = [];

    public function __construct($path, $filesystem = null)
    {
        $this->path = $path;
        $this->info = pathinfo($path);
        $this->imagesize = @getimagesize($path);

        if (! defined('WP_CONTENT_DIR')) {
            define('WP_CONTENT_DIR', __DIR__.'/../../tests/tmp');
        }

        $this->filesystem = $filesystem ?: new Filesystem(new Local(WP_CONTENT_DIR));
        $this->imageManager = new ImageManager(['driver' => 'gd']);
    }

    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    public function __get($key)
    {
        if (isset($this->attributes[$key])) {
            return $this->attributes[$key];
        }

        if (method_exists($this, "get".Str::studly($key))) {
            return $this->attributes[$key] = $this->{"get".Str::studly($key)}();
        }

        return;
    }

    /**
     * Accessor for isImage attribute.
     *
     * @return returnType
     */
    public function isImage()
    {
        return $this->imagesize !== false;
    }

    public function addImageSize($name, array $args)
    {
        $this->imageSizes[$name] = $args;
    }

    /**
     * Accessor for width attribute.
     *
     * @return returnType
     */
    public function getWidth()
    {
        return $this->isImage() ? $this->imagesize[0] : null;
    }

    /**
     * Accessor for height attribute.
     *
     * @return returnType
     */
    public function getHeight()
    {
        return $this->isImage() ? $this->imagesize[1] : null;
    }

    /**
     * Accessor for name attribute.
     *
     * @return returnType
     */
    public function getName()
    {
        return isset($this->info['filename']) ? $this->info['filename'] : null;
    }

    /**
     * Accessor for MimeType attribute.
     *
     * @return returnType
     */
    public function getMimeType()
    {
        return finfo_file(finfo_open(FILEINFO_MIME_TYPE), $this->path);
    }

    public function getExtension()
    {
        return pathinfo($this->path, PATHINFO_EXTENSION);
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getUniquePath($size = null)
    {
        $i = 1;
        $name = date('Y/m/') . $this->name;
        $size = $size ? '-'.$size : '';
        $rename = $name.$size;

        while ($this->filesystem->has($rename.'.'.$this->extension)) {
            $i++;
            $rename = $name.'-'.$i.$size;
        }

        return $rename.'.'.$this->extension;
    }

    public function getMetadata()
    {
        return array_merge([
            'width' => $this->width,
            'height' => $this->height,
            'file' => $this->uniquePath,
            'image_meta' => [
                'aperture' => 0,
                'credit' => '',
                'camera' => '',
                'caption' => '',
                'created_timestamp' => 0,
                'copyright' => '',
                'focal_length' => 0,
                'iso' => 0,
                'shutter_speed' => 0,
                'title' => '',
                'orientation' => 0,
                'keywords' => [],
            ],
        ], $this->metadata);
    }

    public function save($value='')
    {
        $this->filesystem->write($this->uniquePath, file_get_contents($this->path));
        if ($this->isImage()) {
            foreach ($this->imageSizes as $key => $args) {
                $img = $this->imageManager->make($this->path);
                $img->resize($args[0], $args[1], function ($constraint) {
                    $constraint->aspectRatio();
                });
                $this->filesystem->write(
                    $file = $this->getUniquePath($img->getWidth().'x'.$img->getHeight()), $img->encode());
                $this->metadata['sizes'][$key] = [
                    'file' => basename($file),
                    'width' => $img->getWidth(),
                    'height' => $img->getHeight(),
                    'mime-type' => $this->mimeType,
                ];
            }
        }
    }
}
