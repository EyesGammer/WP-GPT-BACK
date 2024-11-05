<?php

namespace src;

class HookEngine {

    private array $hooks = array();

    /**
     * Add hook to domain hooks list
     *
     * @param string $domain
     * @param callable|string $callback
     * @param mixed $args
     * @return void
     */
    public function addHook( string $domain, callable|string $callback, mixed $args=null ) : void {
        if( ! in_array( $domain, array_keys( $this->hooks ) ) ) {
            $this->hooks[ $domain ] = array(
                array(
                    'callback' => $callback,
                    'args' => $args
                )
            );
        } else {
            $this->hooks[ $domain ][] = array(
                'callback' => $callback,
                'args' => $args
            );
        }
    }

    /**
     * Start all hooks for the specified domain
     *
     * @param string $domain
     * @return mixed
     */
    public function doHook( string $domain ) : mixed {
        if( ! in_array( $domain, array_keys( $this->hooks ) ) ) return null;
        foreach( $this->hooks[ $domain ] as $hook ) {
            if(
                is_string( $hook['callback'] ) &&
                str_contains( $hook[ 'callback' ], '@' )
            ) {
                list( $scope, $method ) = explode( '@', $hook['callback'], 2 );
                if(
                    class_exists( $scope ) &&
                    method_exists( $scope, $method ) &&
                    ( new \ReflectionMethod( $scope, $method ) )->isStatic()
                ) {
                    return $scope::$method( $hook['args'] );
                }
            }
            if(
                (
                    is_string( $hook['callback'] ) &&
                    function_exists( $hook['callback'] )
                ) ||
                is_callable( $hook['callback'] )
            ) {
                return $hook['callback']( $hook[ 'args' ] );
            }
        }
        return null;
    }

}

// Silence is golden