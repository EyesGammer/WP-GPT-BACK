<?php
$limit = 25;
if( isset( $_GET[ 'limit' ] ) ) $limit = max( intval( $_GET[ 'limit' ] ), 1 );
$current_page = 1;
if( isset( $_GET[ 'page' ] ) && is_numeric( $_GET[ 'page' ] ) ) $current_page = intval( $_GET[ 'page' ] );
$offset = ( $current_page * $limit ) - $limit;

$stats = \controllers\Stats::getAllStatsWithClients( limit: $limit, offset: $offset );

$general_in = \controllers\Options::getOption( 'general_in' );
$general_out = \controllers\Options::getOption( 'general_out' );
if( empty( $general_in ) ) $general_in = 0.0005;
else $general_in = floatval( unserialize( $general_in[ 'value' ] ) );
if( empty( $general_out ) ) $general_out = 0.0005;
else $general_out = floatval( unserialize( $general_out[ 'value' ] ) );

$stats_count = \controllers\Stats::getStatsCount();
$pages_count = ceil( $stats_count / $limit );
?>
<div class="w-full h-fit rounded-md border border-gray-300 shadow-md p-6 flex flex-col">
    <form action="<?= get_url( '/statistics/general' ) ?>" class="w-full h-fit mb-4 flex gap-2 items-end">
        <div class="relative w-fit">
            <label for="limit">Limite d'affichage</label>
            <input type="number" value="<?= $limit ?>" min="1" max="200" name="limit" id="limit" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
        </div>
        <button class="h-fit w-fit border border-black rounded-md bg-black px-4 py-2 text-white duration-300 transition ease-in-out hover:text-black hover:bg-transparent">Envoyer</button>
    </form>
    <table class="flex-1">
        <thead>
            <tr class="[&>*]:font-semibold [&>*]:text-gray-400 [&>*]:py-2 border-b border-gray-300">
                <th class="text-left">Client</th>
                <th class="text-right">Date</th>
                <th class="text-right">Tokens In</th>
                <th class="text-right">Tokens Out</th>
                <th class="text-right">Prix Client</th>
                <th class="text-right">Prix Achat</th>
                <th class="text-right">Temps de réponse</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach( $stats as $loop_stat ) {
            ?>
            <tr class="[&>:not(:first-child)]:text-right [&>*]:py-2 border-b border-gray-300" data-uuid="<?= $loop_stat[ 'stats' ][ 'uuid' ] ?>">
                <td class="text-left">
                    <a href="<?= get_url( '/client/' . $loop_stat[ 'stats' ][ 'uuid_client' ] ) ?>" class="underline">
                        <?= $loop_stat[ 'client' ][ 'nom' ] ?>
                    </a>
                </td>
                <td><?= date( 'd/m/Y H:i:s', strtotime( $loop_stat[ 'stats' ][ 'date' ] ) ) ?></td>
                <td><?= $loop_stat[ 'stats' ][ 'tokens_in' ] ?></td>
                <td><?= $loop_stat[ 'stats' ][ 'tokens_out' ] ?></td>
                <td><?= number_format( floatval( $loop_stat[ 'stats' ][ 'price' ] ), 8, ',', ' ' ) ?></td>
                <td><?= number_format( floatval(
                        floatval( $loop_stat[ 'stats' ][ 'tokens_in' ] ) * $general_in + floatval( $loop_stat[ 'stats' ][ 'tokens_out' ] ) * $general_out
                    ), 8, ',', ' ' ) ?></td>
                <td><?= str_replace( '.', ',', $loop_stat[ 'stats' ][ 'time' ] ) ?>s</td>
            </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
    <div class="w-full h-fit leading-6 mt-4 flex flex-wrap gap-2 items-end [&>a]:text-xs [&>a]:border [&>a]:border-black [&>a]:rounded-md [&>a]:px-4 [&>a]:py-2 [&>a]:flex [&>a]:items-center [&>a]:justify-center">
        <?php
        if( $current_page != 1 ) {
            ?>
            <a href="<?= get_url( "/statistics/general?limit=$limit&page=1" ) ?>" class="bg-white text-black transition duration-300 hover:bg-black hover:text-white">
                <<
            </a>
            <a href="<?= get_url( "/statistics/general?limit=$limit&page=" . $current_page - 1 ) ?>" class="bg-white text-black transition duration-300 hover:bg-black hover:text-white">
                <
            </a>
            <?php
        }
        for( $i = 1; $i < $pages_count + 1; $i++ ) {
        ?>
        <a href="<?= get_url( "/statistics/general?limit=$limit&page=$i" ) ?>"<?= $current_page === $i ? ' class="bg-black text-white"' : 'bg-white text-black transition duration-300 hover:bg-black hover:text-white' ?>>
            <?= $i ?>
        </a>
        <?php
        }
        if( $current_page != $pages_count ) {
            ?>
            <a href="<?= get_url( "/statistics/general?limit=$limit&page=$pages_count" ) ?>" class="bg-white text-black transition duration-300 hover:bg-black hover:text-white">
                >>
            </a>
            <a href="<?= get_url( "/statistics/general?limit=$limit&page=" . $current_page + 1 ) ?>" class="bg-white text-black transition duration-300 hover:bg-black hover:text-white">
                >
            </a>
            <?php
        }
        ?>
    </div>
</div>