<?php
$client = do_hook( 'current-client' ) ?? null;
if( $client === null ) header( 'Location: ' . get_url( '/homepage' ) );

$stats = do_hook( 'client-stats-content' );
$total_price = array_reduce( $stats, function( $sum, $item ) {
    return $sum += floatval( $item[ 'stats' ][ 'price' ] );
}, 0 );
?>
<div class="w-full rounded-md border border-gray-300 p-6 shadow-md flex flex-col gap-6">
    <div class="flex gap-4 items-center justify-between">
        <div class="flex gap-4">
            <div class="h-16 w-auto aspect-square rounded-full">
                <img src="<?= $client[ 'image' ] ?>" class="w-auto h-full aspect-square rounded-full">
            </div>
            <div class="flex flex-col">
                <span class="text-2xl"><?= $client[ 'nom' ] ?></span>
                <span class="text-gray-400"><?= $client[ 'email' ] ?></span>
            </div>
        </div>
        <div class="flex items-center justify-between gap-2">
            <div class="h-full w-fit p-4 rounded-md border border-gray-300 flex flex-col gap-2">
                <span class="text-gray-400">Utilisations</span>
                <span class="font-bold text-4xl"><?= count( $stats ) ?></span>
            </div>
            <div class="h-full w-fit p-4 rounded-md border border-gray-300 flex flex-col gap-2">
                <span class="text-gray-400">Crédit</span>
                <span class="font-bold text-4xl"><?= number_format( floatval( $client[ 'credit' ] ), 3, ',', ' ' ) ?>€</span>
            </div>
            <div class="h-full w-fit p-4 rounded-md border border-gray-300 flex flex-col gap-2">
                <span class="text-gray-400">Montant</span>
                <span class="font-bold text-4xl"><?= $total_price ?>€</span>
            </div>
        </div>
    </div>
</div>