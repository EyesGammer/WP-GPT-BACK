<?php

namespace src;

#[\Attribute( \Attribute::IS_REPEATABLE | \Attribute::TARGET_METHOD )]
class Route {

    public string|array $path = array();
    public ?string $method = 'GET';
    public ?string $name = '';
    public ?string $redirect_norights_url = '/homepage';

    /**
     * Set attributes for the Route.
     *
     * @param string|array $path The path to access the method (or array of paths to access the method)
     * @param string|null $method HTTP method to access the page
     * @param string|null $name Page name (displayed at the tab name)
     * @param bool|null $is_string
     * @param string|null $redirect_norights_url If rights are not ok, redirect to this url. Can be customised with db_content of RightsEngine (exemple: "/test/[uuid]", "[uuid]" will be replaced by the uuid of the current connected user)
     */
    public function __construct( string|array $path, ?string $method, ?string $name='', ?bool $is_string=false, ?string $redirect_norights_url=null ) {
        if( ! is_array( $path ) && ! $is_string ) {
            $this->path[] = $path;
        } else {
            $this->path = $path;
        }
        $this->method = $method;
        $this->name = $name;
        if( ! empty( $redirect_norights_url ) ) $this->redirect_norights_url = $redirect_norights_url;
    }

    /**
     * Set path as string to use in Router class
     *
     * @param string $path
     * @return Route
     */
    public function setAsString( string $path ) : Route {
        return new Route( path: $path, method: $this->method, name: $this->name, is_string: true, redirect_norights_url: $this->redirect_norights_url );
    }

}

// Silence is golden