<?php
global $database;
?>
<div class="flex flex-col gap-4">
    <div class="flex items-center justify-between">
        <h2 class="text-3xl font-semibold">Clients</h2>
        <div class="relative flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-2 w-4 h-4" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
            <input type="text" placeholder="Rechercher..." name="search-input" id="search-input" class="border border-gray-300 rounded-md outline-none pl-8 py-2 font-normal min-w-[300px]">
        </div>
    </div>
    <div class="grid grid-cols-3 gap-2">
        <?php
        $clients = $database->prepared_query( "SELECT * FROM gpt_clients" );
        if( empty( $clients ) ) $clients = array( array(), array(), array() );
        list( $col_a_clients, $col_b_clients, $col_c_clients ) = count( $clients ) !== 0 && ( $temp = array_chunk( $clients, ceil( count( $clients ) / 3 ), true ) ) && count( $temp ) !== 3 ? array_pad( $temp, 3, array() ) : $clients;
        if( ! empty( $col_a_clients ) && count( $col_a_clients ) === count( $col_a_clients, COUNT_RECURSIVE ) ) $col_a_clients = array( $col_a_clients );
        if( ! empty( $col_b_clients ) && count( $col_b_clients ) === count( $col_b_clients, COUNT_RECURSIVE ) ) $col_b_clients = array( $col_b_clients );
        if( ! empty( $col_c_clients ) && count( $col_c_clients ) === count( $col_c_clients, COUNT_RECURSIVE ) ) $col_c_clients = array( $col_c_clients );
        ?>
        <div class="w-full flex flex-col gap-4">
            <?php
            foreach( $col_a_clients as $loop_client ) {
            ?>
            <a href="<?= get_url( '/client/' . $loop_client[ 'uuid' ] ) ?>" class="flex h-20 w-full rounded-md shadow-md border border-gray-300 gap-4 items-center transition ease-in-out duration-300 hover:shadow-xl">
                <div class="h-2/3 w-auto aspect-square ml-4 rounded-full">
                    <img src="<?= $loop_client[ 'image' ] ?>" class="w-auto h-full aspect-square rounded-full">
                </div>
                <div class="flex flex-col w-full h-full justify-start py-4">
                    <span class="font-bold text-xl"><?= $loop_client[ 'nom' ] ?></span>
                    <span class="text-gray-400 text-sm"><?= $loop_client[ 'email' ] ?></span>
                </div>
            </a>
            <?php
            }
            ?>
        </div>
        <div class="w-full flex flex-col gap-4">
            <?php
            foreach( $col_b_clients as $loop_client ) {
            ?>
            <a href="<?= get_url( '/client/' . $loop_client[ 'uuid' ] ) ?>" class="flex h-20 w-full rounded-md shadow-md border border-gray-300 gap-4 items-center transition ease-in-out duration-300 hover:shadow-xl">
                <div class="h-2/3 w-auto aspect-square ml-4 rounded-full">
                    <img src="<?= $loop_client[ 'image' ] ?>" class="w-auto h-full aspect-square rounded-full">
                </div>
                <div class="flex flex-col w-full h-full justify-start py-4">
                    <span class="font-bold text-xl"><?= $loop_client[ 'nom' ] ?></span>
                    <span class="text-gray-400 text-sm"><?= $loop_client[ 'email' ] ?></span>
                </div>
            </a>
            <?php
            }
            ?>
        </div>
        <div class="w-full flex flex-col gap-4">
            <?php
            foreach( $col_c_clients as $loop_client ) {
            ?>
            <a href="<?= get_url( '/client/' . $loop_client[ 'uuid' ] ) ?>" class="flex h-20 w-full rounded-md shadow-md border border-gray-300 gap-4 items-center transition ease-in-out duration-300 hover:shadow-xl">
                <div class="h-2/3 w-auto aspect-square ml-4 rounded-full">
                    <img src="<?= $loop_client[ 'image' ] ?>" class="w-auto h-full aspect-square rounded-full">
                </div>
                <div class="flex flex-col w-full h-full justify-start py-4">
                    <span class="font-bold text-xl"><?= $loop_client[ 'nom' ] ?></span>
                    <span class="text-gray-400 text-sm"><?= $loop_client[ 'email' ] ?></span>
                </div>
            </a>
            <?php
            }
            ?>
        </div>
    </div>
</div>