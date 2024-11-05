<?php

use controllers\Stats;

$client = do_hook( 'current-client' ) ?? null;
if( $client === null ) header( 'Location: ' . get_url( '/homepage' ) );

$url_exploded = explode( '/', get_url( '+', true ) );
$current_page = 'single';
if( in_array( 'settings', $url_exploded ) ) $current_page = 'settings';
else if( in_array( 'access', $url_exploded ) ) $current_page = 'access';

add_hook( 'client-current-tab', fn() => $current_page );

$stats = Stats::getStatsWithClientsByClientUUID( $client[ 'uuid' ] );
add_hook( 'client-stats-content', fn() => $stats );

load_template( 'client-navbar' );
load_template( 'client-card' );

if( $current_page === 'single' ) {
    add_hook('stats-title', fn() => "Statistiques d'utilisation");
    add_hook('stats-sub-title', fn() => "30 dernières utilisation");
    add_hook( 'stats-content', fn() => $stats );
    load_template('general-stats');
} else if( $current_page === 'settings' ) {
    load_template( 'client-settings' );
} else if( $current_page === 'access' ) {
    load_template( 'client-access' );
}

load_template('error');
?>