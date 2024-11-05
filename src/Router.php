<?php

namespace src;

use ReflectionClass;
use ReflectionMethod;
use src\Route;

class Router {

    private array $routes = array();

    /**
     * Register new route to router
     *
     * @param string $namespace
     * @return void
     * @throws \ReflectionException
     */
    public function register( string $namespace ) : void {
        $reflection = new ReflectionClass( $namespace );
        $methods = $reflection->getMethods( ReflectionMethod::IS_PUBLIC );
        foreach( $methods as $method ) {
            $annotations = $method->getAttributes( Route::class );
            if( $annotations ) {
                $template_instance = null;
                $render_template = $method->getAttributes( TemplatesEngine::class )[ 0 ] ?? null;
                $template_instance = $render_template?->newInstance();
                $rights_instance = null;
                $rights_engine = $method->getAttributes( RightsEngine::class )[ 0 ] ?? null;
                $rights_instance = $rights_engine?->newInstance();
                $security_instance = null;
                $security_engine = $method->getAttributes( Security::class )[ 0 ] ?? null;
                $security_instance = $security_engine?->newInstance();
                foreach( $annotations as $annotation ) {
                    $route = $annotation->newInstance();
                    foreach( $route->path as $loop_path ) {
                        $this->routes[ $loop_path ] = array(
                            'route' => $route->setAsString( $loop_path ),
                            'method' => $route->method,
                            'callback' => $method
                        ) + ( (
                            (
                                is_array( $route->path ) &&
                                ! in_array( $route->redirect_norights_url, $route->path )
                            ) &&
                            $route->redirect_norights_url !== $route->path
                        ) ? array(
                            'redirect_norights' => get_url( $route->redirect_norights_url )
                        ) : array() ) + ( $render_template && $template_instance !== null ? array(
                            'render' => $template_instance->toRender
                        ) : array() ) + ( ! empty( $route->name ) ? array(
                            'name' => $route->name
                        ) : array() ) + ( $rights_engine && $rights_instance !== null ? array(
                            'rights_callback' => $rights_instance
                        ) : array() ) + ( $security_engine && $security_instance !== null ? array(
                            'security_token' => $security_instance->bearer_token,
                            'security_required' => $security_instance->token_required
                        ) : array() );
                    }
                }
            }
        }
    }

    /**
     * Page when no routes implemented yet
     *
     * @return void
     */
    private function noRoutes() : void {
        echo "No routes implemented yet. Please create some to continue.";
    }

    /**
     * Page when rights not accepted
     *
     * @param string|null $route
     * @return void
     */
    private function noRights( ?string $route=null ) : void {
        if( ! empty( $route ) ) {
            header( 'HTTP/1.1 401 Unauthorized', true, 401 );
            header( 'Location: ' . $route );
            exit;
        } else {
            echo "No routes implemented yet. Please create some to continue.";
        }
    }

    /**
     * Replace arguments into a string using the value at the index of the named argument
     *
     * @param string $input
     * @param array|null $user_content
     * @return string
     */
    private function replaceArgs( string $input, ?array $user_content=null ) : string {
        if( ! empty( $user_content ) && preg_match_all( '/\[(\w+)\]/im', $input, $matches ) ) {
            foreach( $matches[ 1 ] as $current_find ) {
                if( isset( $user_content[ $current_find ] ) ) {
                    $input = str_replace( "[$current_find]", $user_content[ $current_find ], $input );
                }
            }
        }
        return $input;
    }

    /**
     * Match route path with route
     *
     * @param string $path
     * @param string $method
     * @return array|null
     */
    public function match( string $path, string $method ) : ?array {
        if(
            $method === 'POST' &&
            isset( $_POST ) &&
            empty( $_POST )
        ) {
            $_POST = json_decode( file_get_contents( 'php://input' ), true );
        }
        if( in_array( $path, array_keys( $this->routes ) ) ) {
            $user_content = get_connected_user();
            $this->routes[ $path ][ 'redirect_norights' ] = $this->replaceArgs( $this->routes[ $path ][ 'redirect_norights' ], $user_content );
            if( $this->routes[ $path ][ 'method' ] !== $method ) {
                $this->noRoutes();
                return array(
                    'route' => '/noRoutes',
                    'method' => 'GET',
                    'callback' => 'Router::noRoutes'
                );
            }
            do_hook( 'before-loading-route' );
            if( isset( $this->routes[ $path ][ 'rights_callback' ] ) ) {
                if( ! $this->routes[ $path ][ 'rights_callback' ]->check_rights( $user_content ) ) {
                    if( ! empty( $this->routes[ $path ] ) ) {
                        $this->routes[ $path ][ 'redirect_norights' ] = $this->replaceArgs( $this->routes[ $path ][ 'redirect_norights' ], $user_content );
                        $this->noRights( $this->routes[ $path ][ 'redirect_norights' ] );
                    } else $this->routes[ $path ][ 'rights_callback' ]->noRights();
                    return null;
                }
            }
            $http_method = $this->routes[ $path ][ 'callback' ]->name;
            if(
                isset( $this->routes[ $path ][ 'security_token' ] ) &&
                isset( $this->routes[ $path ][ 'security_required' ] ) &&
                empty( $this->routes[ $path ][ 'security_token' ] ) &&
                $this->routes[ $path ][ 'security_required' ]
            ) {
                $this->noRoutes();
                return array(
                    'route' => '/noRoutes',
                    'method' => 'GET',
                    'callback' => 'Router::noRoutes'
                );
            } else if( isset( $this->routes[ $path ][ 'security_token' ] ) ) {
                ( new $this->routes[ $path ][ 'callback' ]->class )->$http_method( $this->routes[ $path ][ 'security_token' ] );
            } else {
                ( new $this->routes[ $path ][ 'callback' ]->class )->$http_method();
            }
            //( new $this->routes[ $path ][ 'callback' ]->class )->$method();
            $this->setPageParameters( $path );
            return $this->routes[ $path ] ?? null;
        } else {
            list( $route, $match ) = $this->match_route( $path );
            if( $route === null || $match === null ) {
                $this->noRoutes();
                return null;
            }
            if( $route[ 'method' ] !== $method ) {
                $this->noRoutes();
                return array(
                    'route' => '/noRoutes',
                    'method' => 'GET',
                    'callback' => 'Router::noRoutes'
                );
            }
            $http_method = $route[ 'callback' ]->name;
            if(
                isset( $route[ 'security_token' ] ) &&
                isset( $route[ 'security_required' ] ) &&
                empty( $route[ 'security_token' ] ) &&
                $route[ 'security_required' ]
            ) {
                $this->noRoutes();
                return array(
                    'route' => '/noRoutes',
                    'method' => 'GET',
                    'callback' => 'Router::noRoutes'
                );
            } else if( isset( $route[ 'security_token' ] ) ) {
                ( new $route[ 'callback' ]->class )->$http_method( $route[ 'security_token' ], ...array_slice( $match, 1 ) );
            } else {
                ( new $route[ 'callback' ]->class )->$http_method( ...array_slice( $match, 1 ) );
            }
            $this->setPageParameters( $path );
            return $route ?? null;
        }
    }

    /**
     * Match route (and regex routes) using the path
     *
     * @param string $path
     * @return array|null
     */
    private function match_route( string $path ) : ?array {
        foreach( $this->routes as $route ) {
            if( preg_match( '@^' . $route[ 'route' ]->path . '$@', $path , $matches ) ) {
                do_hook( 'before-loading-route' );
                $match = array_filter( $matches, fn( $x ) => is_integer( $x ), ARRAY_FILTER_USE_KEY );
                $named = array_filter( $matches, fn( $x ) => ! is_integer( $x ), ARRAY_FILTER_USE_KEY );
                foreach( $named as $key => $value ) {
                    global ${ $key };
                    ${ $key } = $value;
                }
                if( isset( $this->routes[ $route[ 'route' ]->path ][ 'rights_callback' ] ) ) {
                    if( ! $this->routes[ $route[ 'route' ]->path ][ 'rights_callback' ]->check_rights( get_connected_user() ) ) {
                        $this->routes[ $route[ 'route' ]->path ][ 'rights_callback' ]->noRights();
                        return null;
                    }
                }
                return array(
                    $this->routes[ $route[ 'route' ]->path ],
                    $match
                );
            }
        }
        return null;
    }

    /**
     * Start the routing logic
     *
     * @return void
     */
    public function start() : void {
        if( empty( $this->routes ) ) {
            $this->noRoutes();
        }
        $method = $_SERVER[ 'REQUEST_METHOD' ];
        $all_query = parse_url( preg_replace('/([^:])(\/{2,})/', '$1/', $_SERVER[ 'REQUEST_URI' ] ) );
        $uri = '/' . implode( '/', array_diff( preg_split( '@/@', $all_query[ 'path' ], -1, PREG_SPLIT_NO_EMPTY ), preg_split( '@/@', '/', -1, PREG_SPLIT_NO_EMPTY ) ) );
        $uri = substr_replace(
            $uri,
            '',
            0,
            strlen( GPT_ALT )
        );
        if( @$uri[ 0 ] !== '/' ) $uri = "/$uri";
        $this->match( $uri, $method );
    }

    /**
     * Set page parameters
     *
     * @param string $path
     * @return void
     */
    private function setPageParameters( string $path ) : void {
        list( $route, $_ ) = $this->match_route( $path );
        if( isset( $route[ 'name' ] ) ) {
            $name = $route[ 'name' ];
            add_hook( 'page-title', function() use ( $name ) {
                return $name;
            } );
        }
        if( isset( $route[ 'render' ] ) ) {
            load_template( $route[ 'render' ] );
        }
    }

}

// Silence is golden