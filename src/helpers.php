<?php 

if (!function_exists('lumenpress_is_url')) {
    function lumenpress_is_url($value)
    {
        return preg_match('@^//@', $value) or filter_var($value, FILTER_VALIDATE_URL) !== false;
    }
}

if (!function_exists('lumenpress_url')) {
    function lumenpress_url($value = '')
    {
        if (lumenpress_is_url($value)) {
            return $value;
        }
        return function_exists('home_url') ? home_url($value) : url($value);
    }
}

if (!function_exists('lumenpress_asset_url')) {
    function lumenpress_asset_url($value = '')
    {
        if (lumenpress_is_url($value)) {
            return $value;
        }
        return config('wordpress.assets.base_url').$value;
    }
}

if (!function_exists('lumenpress_get_current_user_id')) {
    function lumenpress_get_current_user_id()
    {
        return function_exists('get_current_user_id') ? get_current_user_id() : 0;
    }
}

if (!function_exists('lumenpress_get_term_link')) {
    function lumenpress_get_term_link($term, $taxonomy = '')
    {
        return function_exists('get_term_link') ? get_term_link($term, $taxonomy) : '';
    }
}

if (!function_exists('lumenpress_get_permalink')) {
    function lumenpress_get_permalink($post)
    {
        if (function_exists('get_permalink')) {
            return get_permalink($post->ID);
        }
        if (getenv('APP_ENV') === 'testing') {
            $url = getenv('APP_SITEURL');
        } else {
            $url = url('');
        }
        $part = $post->post_type == 'page' ? '' : $post->post_type;
        return "$url/$part/$post->post_name";
    }
}

if (!function_exists('luemnpress_get_the_content')) {
    function luemnpress_get_the_content($value)
    {
        if (function_exists('apply_filters')) {
            return apply_filters('the_content', $value);
        }
        return $value;
    }
}

if (!function_exists('lumenpress_is_serialized')) {
    function lumenpress_is_serialized( $data, $strict = true )
    {
        if (function_exists('is_serialized')) {
            return is_serialized($data, $strict);
        }
        // if it isn't a string, it isn't serialized.
        if ( ! is_string( $data ) ) {
            return false;
        }
        $data = trim( $data );
        if ( 'N;' == $data ) {
            return true;
        }
        if ( strlen( $data ) < 4 ) {
            return false;
        }
        if ( ':' !== $data[1] ) {
            return false;
        }
        if ( $strict ) {
            $lastc = substr( $data, -1 );
            if ( ';' !== $lastc && '}' !== $lastc ) {
                return false;
            }
        } else {
            $semicolon = strpos( $data, ';' );
            $brace     = strpos( $data, '}' );
            // Either ; or } must exist.
            if ( false === $semicolon && false === $brace )
                return false;
            // But neither must be in the first X characters.
            if ( false !== $semicolon && $semicolon < 3 )
                return false;
            if ( false !== $brace && $brace < 4 )
                return false;
        }
        $token = $data[0];
        switch ( $token ) {
            case 's' :
                if ( $strict ) {
                    if ( '"' !== substr( $data, -2, 1 ) ) {
                        return false;
                    }
                } elseif ( false === strpos( $data, '"' ) ) {
                    return false;
                }
                // or else fall through
            case 'a' :
            case 'O' :
                return (bool) preg_match( "/^{$token}:[0-9]+:/s", $data );
            case 'b' :
            case 'i' :
            case 'd' :
                $end = $strict ? '$' : '';
                return (bool) preg_match( "/^{$token}:[0-9.E-]+;$end/", $data );
        }
        return false;
    }
}
