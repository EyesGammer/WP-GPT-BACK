<?php

namespace src;

#[\Attribute]
class Security {

    private string $key;
    private string $salt;
    private string $old_key;
    private string $old_salt;
    private string $method = 'AES-256-CBC';
    public string $bearer_token = '';
    public bool $token_required = true;
    public bool $access_all = false;

    public function __construct( ?string $bearer_token=null, ?string $method=null, ?bool $token_required=null, ?bool $access_all=null ) {
        if( $bearer_token !== null ) $this->bearer_token = $bearer_token;
        if( $method !== null ) $this->method = $method;
        if( $token_required !== null ) $this->token_required = $token_required;
        if( $access_all !== null ) $this->access_all = $access_all;
        $this->key = $this->get_default_key();
        $this->salt = $this->get_default_salt();
        $this->bearer_token = $this->getBearer();
        if( $this->access_all ) header( 'Access-Control-Allow-Origin: *' );
    }

    /**
     * Get authorization headers
     *
     * @return null|string
     */
    private function getHeaders() : ?string {
        $headers = null;
        if( isset( $_SERVER[ 'Authorization' ] ) ) {
            $headers = trim( $_SERVER[ 'Authorization' ] );
        } else if( isset( $_SERVER[ 'HTTP_AUTHORIZATION' ] ) ) {
            $headers = trim( $_SERVER[ 'HTTP_AUTHORIZATION' ] );
        } else if( function_exists( 'apache_request_headers' ) ) {
            $request_headers = apache_request_headers();
            $request_headers = array_combine( array_map( 'ucwords', array_keys( $request_headers ) ), array_values( $request_headers ) );
            if( isset( $request_headers[ 'Authorization' ] ) ) {
                $headers = trim( $request_headers[ 'Authorization' ] );
            }
        }
        return $headers;
    }

    /**
     * Get Bearer token from headers
     *
     * @return string
     */
    private function getBearer() : string {
        $headers = $this->getHeaders();
        if( ! empty( $headers ) ) {
            if( preg_match( '/Bearer\s(\S+)/', $headers, $matches ) ) {
                return $matches[ 1 ];
            }
        }
        return '';
    }

    /**
     * Encrypt data
     *
     * @param string $value
     * @return string
     */
    public function encrypt( string $value ) : string {
        if( ! extension_loaded( 'openssl' ) ) return $value;
        $ivlen = openssl_cipher_iv_length( $this->method );
        $iv = openssl_random_pseudo_bytes( $ivlen );
        $raw_value = openssl_encrypt( $value . $this->salt, $this->method, $this->key, 0, $iv );
        if( ! $raw_value ) return $value;
        $this->key = $this->old_key ?? $this->key;
        $this->salt = $this->old_salt ?? $this->salt;
        return base64_encode( $iv . $raw_value );
    }

    /**
     * Decrypt data
     *
     * @param string $value
     * @return string
     */
    public function decrypt( string $value ) : string {
        if( ! extension_loaded( 'openssl' ) ) return $value;
        $raw_value = base64_decode( $value, true );
        $ivlen = openssl_cipher_iv_length( $this->method );
        $iv = substr( $raw_value, 0, $ivlen );
        $raw_value = substr( $raw_value, $ivlen );
        $value = openssl_decrypt( $raw_value, $this->method, $this->key, 0, $iv );
        if( ! $value || ! str_ends_with( $value, $this->salt ) ) return $raw_value;
        $return = substr( $value, 0, - strlen( $this->salt ) );
        $this->key = $this->old_key ?? $this->key;
        $this->salt = $this->old_salt ?? $this->salt;
        return $return;
    }

    /**
     * Get default key if GPT_KEY not defined
     *
     * @return string
     */
    public function get_default_key() : string {
        if( defined( 'GPT_KEY' ) && GPT_KEY !== '' ) return GPT_KEY;
        return 'hlaqLiEPYkK5Aye2RNeCucBD3YXPWYGIgJzWgXkOO7UqxnurKr';
    }

    /**
     * Set secure key
     *
     * @param string $key
     * @return Security
     */
    public function set_key( string $key ) : Security {
        $this->old_key = $this->key;
        $this->key = $key;
        return $this;
    }

    /**
     * Get default salt if GPT_SALT not defined
     *
     * @return string
     */
    public function get_default_salt() : string {
        if( defined( 'GPT_SALT' ) && GPT_SALT !== '' ) return GPT_SALT;
        return 's1ms4XTDIY25RRew90VmyYlM5UHzEzrr8Nla6DzviRbytDTQ6v';
    }

    /**
     * Set secure salt
     *
     * @param string $salt
     * @return Security
     */
    public function set_salt( string $salt ) : Security {
        $this->old_salt = $this->salt;
        $this->salt = $salt;
        return $this;
    }

}

// Silence is golden