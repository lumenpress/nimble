<?php

namespace Lumenpress\ORM\Models;

class Attachment extends AbstractPost
{
    protected $type = 'attachment';

    protected $appends = [
    ];

    protected $aliases = [
        'link' => 'guid',
    ];

    protected $hidden = [
        'guid',
    ];

    /**
     * Accessor for link attribute.
     *
     * @return returnType
     */
    public function getGuidAttribute($value)
    {
        return $value ?: wp_get_attachment_url($this->ID);
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
        if (!$force and $id = static::getAttachmentUniqid($src)) {
            return static::find($id);
        }

        // gives us access to the download_url() and wp_handle_sideload() functions
        if (!function_exists('media_handle_upload')) {
            require_once ABSPATH.'wp-admin'.'/includes/image.php';
            require_once ABSPATH.'wp-admin'.'/includes/file.php';
            require_once ABSPATH.'wp-admin'.'/includes/media.php';
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
        $file_array = [
            'name'     => basename($src),
            'tmp_name' => $tmp,
        ];

        /*
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
         * $post_id can be set to '0' to not attach it to any particular post.
         */
        $post_id = 0;

        $id = media_handle_sideload($file_array, $post_id);

        /**
         * We don't want to pass something to $id
         * if there were upload errors.
         * So this checks for errors.
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
