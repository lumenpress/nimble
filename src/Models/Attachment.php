<?php 

namespace Lumenpress\Fluid\Models;

use Illuminate\Filesystem\Filesystem;

class Attachment extends AbstractPost
{
    protected $postType = 'attachment';

    protected $file;

    protected $filesystem;

    protected $metadata = [];

    protected $appends = [
        'filename',
        'width',
        'height',
        'sizes',
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
        'meta',
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

        $this->filesystem = new Filesystem;
        $this->post_status = 'inherit';
        $this->ping_status = 'closed';
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
        $this->post_title = $this->filesystem->basename($value);
        $this->post_mime_type = $this->filesystem->mimeType($value);
    }

    /**
     * Accessor for Caption attribute.
     *
     * @return returnType
     */
    public function getCaptionAttribute($value)
    {
        return $this->post_excerpt;
    }

    /**
     * Accessor for filename attribute.
     *
     * @return returnType
     */
    public function getFilenameAttribute($value)
    {
        return basename($this->meta->_wp_attached_file);
    }

    /**
     * Accessor for width attribute.
     *
     * @return returnType
     */
    public function getWidthAttribute($value)
    {
        return data_get($this, 'meta._wp_attachment_metadata.width');
    }

    /**
     * Accessor for height attribute.
     *
     * @return returnType
     */
    public function getHeightAttribute($value)
    {
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
        return wp_get_attachment_url($this->ID);
    }

    public function storage($file, $size = null)
    {
    }

    public function save(array $options = [])
    {
        if (! $this->file) {
            return false;
        }
        // $this->storage();
        $this->meta->_wp_attached_file = '';
        $this->meta->_wp_attachment_metadata = $this->metadata;
    }

    public function __toString()
    {
        return is_string($this->link) ? $this->link : '';
    }

    private static function getAttachmentUniqid($value)
    {
        $meta = Meta::table('postmeta')
            ->where('meta_key', '_lumenpress_asset_uniqid')
            ->where('meta_value', $value)
            ->first();
        return $meta ? $meta->post_id : 0;
    }

    public static function upload($src, $force = false)
    {
        if (!$force and $id = self::getAttachmentUniqid($src)) {
            return static::find($id);
        }

        // gives us access to the download_url() and wp_handle_sideload() functions
        if (!function_exists('media_handle_upload')) {
            require_once(ABSPATH . "wp-admin" . '/includes/image.php');
            require_once(ABSPATH . "wp-admin" . '/includes/file.php');
            require_once(ABSPATH . "wp-admin" . '/includes/media.php');
        }

        // URL to the WordPress logo
        //$url = get_template_directory_uri() . '/' . $src;
        //$tmp = download_url( $url );
        $tmp = null;
        if (stripos($src, lumenpress_asset_url()) === 0) {
            $url = str_replace(lumenpress_asset_url(), '', $src);
            $url = file_exists($url) ? $url : lumenpress_asset_path($url);
            $tmp = wp_tempnam($url);
            @copy($url, $tmp);
        } elseif (lumenpress_is_url($src)) {
            $tmp = download_url($src, 5000);
        } else {
            $url = file_exists($src) ? $src : lumenpress_asset_path($src);
            $tmp = wp_tempnam($url);
            @copy($url, $tmp);
        }
        // clearing the stat cache
        @clearstatcache(true, $tmp);
        $file_array = array(
            'name' => basename($src),
            'tmp_name' => $tmp,
        );

        /**
         * Check for download errors
         * if there are error unlink the temp file name
         */
        if (is_wp_error($tmp)) {
            @unlink($file_array['tmp_name']);
            throw new \Exception($tmp->get_error_message(), 1);
            return $tmp;
        }

        /**
         * now we can actually use media_handle_sideload
         * we pass it the file array of the file to handle
         * and the post id of the post to attach it to
         * $post_id can be set to '0' to not attach it to any particular post
         */
        $post_id = 0;

        $id = media_handle_sideload($file_array, $post_id);

        /**
         * We don't want to pass something to $id
         * if there were upload errors.
         * So this checks for errors
         */
        if (is_wp_error($id)) {
            @unlink($file_array['tmp_name']);
            throw new \Exception($id->get_error_message(), 1);
            return $id;
        }

        add_post_meta($id, '_lumenpress_asset_uniqid', $src, true);

        @unlink($file_array['tmp_name']);

        return static::find($id);
    }
}
