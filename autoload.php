<?php

spl_autoload_register( function( $class ) {
    $file = join( DIRECTORY_SEPARATOR, [ __DIR__, str_replace( '\\', '/', $class ) . '.php' ] );
    if( file_exists( $file ) ) {
        return require_once $file;
    }
    return false;
} );

// Silence is golden