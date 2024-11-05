<?php

use controllers\Options;
use Random\RandomException;

define( 'NONCE_SALT', 'a088959d5ab6b84c6f85d3eeb58edb8b965395ba9a87536c78fbe085cd2fdec1' );

/**
 * Add hook to the HookEngine
 *
 * @param string $domain
 * @param callable|string $callback
 * @param mixed $args
 * @return void
 */
function add_hook( string $domain, callable|string $callback, mixed $args=null ) : void {
    global $hookEngine;
    $hookEngine->addHook( $domain, $callback, $args );
}

/**
 * Run all hooks of the specified domain
 *
 * @param string $domain
 * @return mixed
 */
function do_hook( string $domain ) : mixed {
    global $hookEngine;
    return $hookEngine->doHook( $domain );
}

/**
 * Load template from path using the TemplateEngine
 *
 * @param string $path
 * @return void
 */
function load_template( string $path ) : void {
    global $templatesEngine;
    $templatesEngine->loadTemplate( $path );
}

/**
 * Get URL
 *
 * @param string|null $path
 * @param bool $return_part
 * @param bool $without_get
 * @return string
 */
function get_url( ?string $path, bool $return_part=false, bool $without_get=false ) : string {
    if( $path[ 0 ] == '+' ) $path = isset( $_SERVER[ 'REQUEST_URI' ] ) ? $_SERVER[ 'REQUEST_URI' ] . substr( $path, 1 ) : substr( $path, 1 );
    $temp = explode( GPT_ALT, $path, 2 );
    $url = ( ( @$_SERVER[ 'HTTPS' ] ? 'https://' : 'http://' ) . ( $_SERVER[ 'HTTP_HOST' ] . GPT_ALT ?? '' ) );
    $temp_path = end( $temp );
    if( $temp_path !== null ) {
        $url = preg_replace( '/([^:])(\/{2,})/', '$1/', $url . "/$temp_path" );
    }
    if( $return_part ) $url = trim( end( $temp ), '/' );
    if( $without_get ) $url = strtok( $url, '?' );
    return $url;
}

/**
 * Create a nonce (thanks WordPress)
 *
 * @param string $action
 * @param string $id_user
 * @param string $token
 * @return string
 */
function create_nonce( string $action, string $id_user, string $token ) : string {
    $tick = ceil( time() / ( 86400 / 2 ) );
    return substr(
        hash_hmac(
            'md5',
            $tick . '|' . $action . '|' . $id_user . '|' . $token,
            NONCE_SALT
        ),
        -12,
        10
    );
}

/**
 * Validate the nonce (thanks WordPress)
 *
 * @param string $action
 * @param string $id_user
 * @param string $token
 * @param string $nonce
 * @return bool
 */
function validate_nonce( string $action, string $id_user, string $token, string $nonce ) : bool {
    $tick = ceil( time() / ( 86400 / 2 ) );
    $excepted = substr(
        hash_hmac(
            'md5',
            $tick . '|' . $action . '|' . $id_user . '|' . $token,
            NONCE_SALT
        ),
        -12,
        10
    );
    if( hash_equals( $excepted, $nonce ) ) return true;
    return false;
}

/**
 * Set error message and redirect to referrer page
 *
 * @param string $title
 * @param string $message
 * @param string $referrer
 * @param bool $state
 * @return void
 */
function set_error_message( string $title, string $message, string $referrer, bool $state=true ) : void {
    @session_start();
    $_SESSION[ '_error' ] = array(
        'title' => $title,
        'message' => $message,
        'state' => $state
    );
    header( 'Location: ' . $referrer );
    exit;
}

/**
 * Get error message
 *
 * @return array|null
 */
function get_error_message() : ?array {
    @session_start();
    if(
        ! isset( $_SESSION[ '_error' ] ) ||
        ! isset( $_SESSION[ '_error' ][ 'title' ] ) ||
        ! isset( $_SESSION[ '_error' ][ 'message' ] ) ||
        ! isset( $_SESSION[ '_error' ][ 'state' ] )
    ) return null;
    $temp = $_SESSION[ '_error' ];
    unset( $_SESSION[ '_error' ] );
    return $temp;
}

/**
 * Create UUID (length 32 chars)
 *
 * @return string
 * @throws RandomException
 */
function create_uuid() : string {
    $data = random_bytes( 16 );
    $data[ 6 ] = chr( ord( $data[ 6 ] ) & 0x0f | 0x40 );
    $data[ 8 ] = chr( ord( $data[ 8 ] ) & 0x3f | 0x80 );
    return vsprintf( '%s%s-%s-%s-%s-%s%s%s', str_split( bin2hex( $data ), 4 ) );
}

/**
 * Connect user and set session
 *
 * @param array $user_content
 * @param string|null $redirect_url
 * @param string|null $noad_redirect_url
 * @return void
 */
function login( array $user_content, string $redirect_url=null, string $noad_redirect_url=null ) : void {
    @session_start();
    $_SESSION[ '_auth_user' ] = $user_content[ 'uuid' ];
    $_SESSION[ '_auth_token' ] = md5( $user_content[ 'uuid' ] . '|' . session_id() );
    if( empty( $user_content[ 'rights' ] ) ) {
        header( 'Location: ' . ( $noad_redirect_url === null ? get_url( '/homepage' ) : $noad_redirect_url ) );
        return;
    } else {
        $unserialized_content = unserialize( $user_content[ 'rights' ] );
        if(
            ! isset( $unserialized_content[ 'is_admin' ] ) ||
            (
                ! $unserialized_content[ 'is_admin' ] &&
                ! isset( $unserialized_content[ 'can_access' ] )
            ) ||
            (
                isset( $unserialized_content[ 'can_access' ] ) &&
                ! $unserialized_content[ 'can_access' ]
            )
        ) {
            header( 'Location: ' . ( $noad_redirect_url === null ? get_url( '/homepage' ) : $noad_redirect_url ) );
            return;
        }
    }
    header( 'Location: ' . ( $redirect_url === null ? get_url( '/homepage' ) : $redirect_url ) );
}

/**
 * Logout the user
 *
 * @param string|null $redirect_url
 * @return void
 */
function logout( string $redirect_url=null ) : void {
    @session_start();
    $_SESSION[ '_auth_user' ] = null;
    $_SESSION[ '_auth_token' ] = null;
    session_destroy();
    header( 'Location: ' . ( $redirect_url === null ? get_url( '/login' ) : $redirect_url ) );
}

/**
 * Get connected user
 *
 * @return array|null
 */
function get_connected_user() : ?array {
    @session_start();
    if( isset( $_SESSION[ '_auth_user' ] ) ) {
        global $database;
        $user = $database->prepared_query(
            "SELECT * FROM gpt_users WHERE uuid = ?",
            's',
            $_SESSION[ '_auth_user' ]
        );
        if( empty( $user ) ) return null;
        else $user = end( $user );
        return $user;
    }
    return null;
}

/**
 * Get slug from string
 *
 * @param string $input
 * @param string $divider
 * @return string
 */
function get_slug( string $input, string $divider='-' ) : string {
    $input = preg_replace( '~[^\pL\d]+~u', $divider, $input );
    $input = iconv( 'utf-8', 'us-ascii//TRANSLIT', $input );
    $input = preg_replace( '~[^-\w]+~', '', $input );
    $input = trim( $input, $divider );
    $input = preg_replace( '~-+~', $divider, $input );
    $input = strtolower( $input );
    if( empty( $input ) ) return 'n-a';
    return $input;
}

/**
 * Get secured option
 *
 * @param string $name
 * @param string|null $secured_key
 * @param string|null $secured_salt
 * @return array
 */
function get_secured_option( string $name, ?string $secured_key=null, ?string $secured_salt=null ) : array {
    $option = \controllers\Options::getOption( $name );
    if( ! empty( $option ) ) {
        global $security;
        if(
            $secured_key !== null &&
            $secured_salt !== null
        ) {
            $option[ 'value' ] = $security
                ->set_key( $secured_key )
                ->set_salt( $secured_salt )
                ->decrypt( $option[ 'value' ] );
        } else {
            $option[ 'value' ] = $security->decrypt( unserialize( $option[ 'value' ] ) );
        }
        return $option;
    }
    return array();
}

/**
 * Update secured option
 *
 * @param string $name
 * @param string $value
 * @param string|null $secured_key
 * @param string|null $secured_salt
 * @return void
 */
function update_secured_option( string $name, mixed $value, ?string $secured_key=null, ?string $secured_salt=null ) : void {
    global $security;
    if(
        $secured_key !== null &&
        $secured_salt !== null
    ) {
        $value = $security
            ->set_key( $secured_key )
            ->set_salt( $secured_salt )
            ->encrypt( $value );
    } else {
        $value = $security->encrypt( $value );
    }
    Options::updateOption( $name, $value );
}

// Silence is golden