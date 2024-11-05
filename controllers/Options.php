<?php

namespace controllers;

use src\Database;

class Options {

    /**
     * Get option from name
     *
     * @param string $option_name
     * @return array
     */
    public static function getOption( string $option_name ) : array {
        global $database;
        $option = $database->prepared_query(
            'SELECT * FROM gpt_options WHERE name = ? LIMIT 1',
            's',
            $option_name
        );
        if( empty( $option ) ) return array();
        return end( $option );
    }

    /**
     * Add option by triggering the updateOption
     *
     * @param string $option_name
     * @param string $option_value
     * @return void
     */
    public static function addOption( string $option_name, mixed $option_value ) : void {
        Options::updateOption( $option_name, $option_value );
    }

    /**
     * Update option from name (create option if not exists)
     *
     * @param string $option_name
     * @param mixed $option_value
     * @return void
     */
    public static function updateOption( string $option_name, mixed $option_value ) : void {
        $option = Options::getOption( $option_name );
        global $database;
        if( empty( $option ) ) {
            $database->prepared_query(
                'INSERT INTO gpt_options (name,value) VALUES (?, ?)',
                'ss',
                $option_name,
                serialize( $option_value )
            );
        } else {
            $database->prepared_query(
                'UPDATE gpt_options SET value = ? WHERE name = ? LIMIT 1',
                'ss',
                serialize( $option_value ),
                $option_name
            );
        }
    }

    /**
     * Delete option from database (if option exists)
     *
     * @param string $option_name
     * @return void
     */
    public static function deleteOption( string $option_name ) : void {
        $option = Options::getOption( $option_name );
        global $database;
        if( ! empty( $option ) ) {
            $database->prepared_query(
                'DELETE FROM gpt_options WHERE name = ? LIMIT 1',
                's',
                $option_name
            );
        }
    }

    /**
     * Get prices for API per client OR general prices
     *
     * @param string $uid_client
     * @param Database|null $database_object
     * @return array
     */
    public static function getGeneralPrices( string $uid_client, ?Database $database_object=null ) : array {
        if( $database_object === null ) global $database;
        else $database = $database_object;
        $options = $database->prepared_query(
            'SELECT gpt_options.*, ":", gpt_clients.* FROM gpt_clients, gpt_options WHERE uuid = ? AND ( gpt_options.name = CONCAT("price_in_", gpt_clients.id_client) OR gpt_options.name = CONCAT("price_out_", gpt_clients.id_client) )',
            's',
            $uid_client
        );
        $general_in = Options::getOption( 'general_in' );
        $general_out = Options::getOption( 'general_out' );
        $price_in = isset( $general_in[ 'value' ] ) ? floatval( unserialize( $general_in[ 'value' ] ) ) : 0.0005;
        $price_out = isset( $general_out[ 'value' ] ) ? floatval( unserialize( $general_out[ 'value' ] ) ) : 0.0005;
        if( ! empty( $options ) ) foreach( $options as $loop_option ) {
            if( $loop_option[ 'name' ] === 'price_in_' . $loop_option[ 'id_client' ] ) {
                $price_in = floatval( unserialize( $loop_option[ 'value' ] ) );
            } else if( $loop_option[ 'name' ] === 'price_out_' . $loop_option[ 'id_client' ] ) {
                $price_out = floatval( unserialize( $loop_option[ 'value' ] ) );
            }
        }
        return array( $price_in, $price_out );
    }

}