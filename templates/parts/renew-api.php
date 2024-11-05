<?php
$user = do_hook( 'renew-api-user' );
$client = do_hook( 'renew-api-client' );

@session_start();
?>
<form id="api-key" action="<?= get_url( '/client/' . $client[ 'uuid' ] . '/new/key' ) ?>" method="POST" class="w-full rounded-md border border-gray-300 shadow-md flex flex-col gap-4 p-6">
    <h2 class="text-3xl mb-2"><?= do_hook( 'renew-api-title' ) ?? 'Clé API' ?></h2>
    <input type="hidden" name="referrer" value="<?= get_url( '+' ) ?>#api-key">
    <input type="hidden" name="nonce" value="<?= create_nonce( 'client-new-api', $user[ 'uuid' ], $_SESSION[ '_auth_token' ] ) ?>">
    <button class="w-fit text-white rounded-md border border-blue-500 bg-blue-500 px-4 py-2 transition duration-300 ease-in-out hover:bg-transparent hover:text-blue-500">Générer une clé API</button>
    <?php
    if( isset( $_SESSION[ 'new_token' ] ) && ! empty( $_SESSION[ 'new_token' ] ) ) {
        ?>
        <p>
            <span>Nouvelle clé <i>(cliquez pour copier)</i> :</span><br>
            <b class="can-copy select-none cursor-pointer break-all"><?= $_SESSION[ 'new_token' ] ?></b>
        </p>
        <?php
        unset( $_SESSION[ 'new_token' ] );
    }
    ?>
    <hr class="border-t-gray-300 w-full">
    <p class="text-xs text-justify">
        Une fois généré, la clé API ne peut pas être récupérée.<br>
        Veuillez la garder dans un endroit sécurisé. Elle peut être re-généré à tout moment.<br>
        Une fois re-générée, l'ancienne clé devient alors inutilisable.
    </p>
</form>