<?php

namespace controllers;

use src\Route;
use src\RightsEngine;
use src\TemplatesEngine;

class Stats {

    /**
     * Get all stats from database
     *
     * @return array
     */
    public static function getAllStats() : array {
        global $database;
        return $database->prepared_query( 'SELECT * FROM gpt_stats ORDER BY date DESC' );
    }

    /**
     * Get all stats and associated clients from database
     *
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public static function getAllStatsWithClients( int $limit=-1, int $offset=-1 ) : array {
        global $database;
        $statement = 'SELECT stats.*, "|", client.* FROM gpt_stats stats INNER JOIN gpt_clients client ON client.uuid = stats.uuid_client ORDER BY stats.date DESC';
        if( $limit === -1 && $offset === -1 ) {
            $content = $database->prepared_query( $statement );
        } else {
            $content = $database->prepared_query( $statement . " LIMIT $offset, $limit" );
        }
        if( empty( $content ) ) return array();
        return array_map( function( $current ) {
            $client = array_splice( $current, array_search( '|', array_values( $current ) ) + 1 );
            $stats = array_diff_key( $current, $client );
            unset( $stats[ '|' ] );
            return array(
                'stats' => $stats,
                'client' => $client
            );
        }, $content );
    }

    /**
     * Get all stats and associated clients by client uuid from database
     *
     * @param string $uuid
     * @return array
     */
    public static function getStatsWithClientsByClientUUID( string $uuid ) : array {
        global $database;
        $content = $database->prepared_query(
            'SELECT stats.*, "|", client.* FROM gpt_stats stats INNER JOIN gpt_clients client ON client.uuid = stats.uuid_client WHERE client.uuid = ? ORDER BY stats.date DESC',
            's',
            $uuid
        );
        if( empty( $content ) ) return array();
        return array_map( function( $current ) {
            $client = array_splice( $current, array_search( '|', array_values( $current ) ) + 1 );
            $stats = array_diff_key( $current, $client );
            unset( $stats[ '|' ] );
            return array(
                'stats' => $stats,
                'client' => $client
            );
        }, $content );
    }

    public static function getStatsCount() : int {
        global $database;
        $count = $database->prepared_query( 'SELECT COUNT(*) FROM gpt_stats' );
        if( empty( $count ) ) return 0;
        return intval( end( $count[ 0 ] ) );
    }

    #[TemplatesEngine('index')]
    #[Route(
        array(
            '/statistics',
            '/statistics/(general|chart)'
        ),
        'GET',
        'Statistiques'
    )]
    #[RightsEngine(
        need_login: true,
        redirect_need_login: true,
        rights_check: array(
            'is_admin' => true
        )
    )]
    public function statsPage() {
        add_hook( 'page-style', function() {
            return array(
                get_url( '/assets/style.css' )
            );
        } );
        add_hook( 'dashboard-title', fn() => "Statistiques d'utilisation" );
        add_hook( 'dashboard-content', function() {
            $current = get_url( '+', true, true );
            if( $current === 'statistics' ) $current = 'statistics/general';
            add_hook( 'page-stats-current', fn() => $current );
            load_template( 'stats-nav' );
            switch( $current ) {
                case 'statistics':
                case 'statistics/general':
                    load_template( 'stats-general' );
                    break;
            }
        } );
        add_hook( 'page-content', fn() => load_template( 'general-dashboard' ) );
    }

}