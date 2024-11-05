<?php
$client = do_hook( 'current-client' ) ?? null;
if( $client === null ) header( 'Location: ' . get_url( '/homepage' ) );

$stats = \controllers\Stats::getStatsWithClientsByClientUUID( $client[ 'uuid' ] );
$total_price = array_reduce( $stats, function( $sum, $item ) {
    return $sum += floatval( $item[ 'stats' ][ 'price' ] );
}, 0 );

@session_start();
$user = get_connected_user();
?>
<a href="https://buy.stripe.com/test_cN25lw5tG0TOddC7ss" class="w-fit bg-black border border-black rounded-md px-4 py-2 text-white transition duration-300 ease-in-out hover:text-black hover:bg-transparent">Test</a>
