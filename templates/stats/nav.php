<?php
$current = do_hook( 'page-stats-current' );
?>
<div class="w-full h-fit">
    <div class="bg-gray-200 rounded-md h-fit w-fit flex gap-2 py-1 px-1">
        <a href="<?= get_url( '/statistics/general' ) ?>" class="<?= $current === 'statistics/general' ? 'bg-white ' : '' ?>w-fit h-11/12 py-1 px-2 text-xs transition duration-300 ease-in-out hover:bg-white">Général</a>
        <a href="<?= get_url( '/statistics/chart' ) ?>" class="<?= $current === 'statistics/chart' ? 'bg-white ' : '' ?>w-fit h-11/12 py-1 px-2 text-xs transition duration-300 ease-in-out hover:bg-white">Graphique</a>
    </div>
</div>