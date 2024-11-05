<?php
$client = do_hook( 'current-client' ) ?? null;
if( $client === null ) header( 'Location: ' . get_url( '/homepage' ) );

$current_page = do_hook( 'client-current-tab' );
?>
<div class="flex flex-col gap-4">
    <div class="flex items-center justify-between">
        <div class="bg-gray-200 rounded-md h-fit w-fit flex gap-2 py-1 px-1">
            <a href="<?= get_url( '/client/' . $client[ 'uuid' ] . '/single' ) ?>" class="<?= $current_page === 'single' ? 'bg-white ' : '' ?>w-fit h-11/12 py-1 px-2 text-xs transition duration-300 ease-in-out hover:bg-white">Client</a>
            <a href="<?= get_url( '/client/' . $client[ 'uuid' ] . '/access' ) ?>" class="<?= $current_page === 'access' ? 'bg-white ' : '' ?>w-fit h-11/12 py-1 px-2 text-xs transition duration-300 ease-in-out hover:bg-white">Accès</a>
            <a href="<?= get_url( '/client/' . $client[ 'uuid' ] . '/settings' ) ?>" class="<?= $current_page === 'settings' ? 'bg-white ' : '' ?>w-fit h-11/12 py-1 px-2 text-xs transition duration-300 ease-in-out hover:bg-white">Paramètres</a>
        </div>
        <div class="relative flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-2 w-4 h-4" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
            <input type="text" placeholder="Rechercher..." name="search-input" id="search-input" class="border border-gray-300 rounded-md outline-none pl-8 py-2 font-normal min-w-[300px]">
        </div>
    </div>
</div>