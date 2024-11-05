<?php

use controllers\Options;

$client = do_hook( 'current-client' ) ?? null;
if( $client === null ) header( 'Location: ' . get_url( '/homepage' ) );

$price_in_option = Options::getOption( 'price_in_' . $client[ 'id_client' ] );
if( empty( $price_in_option ) ) {
    $general_price_in = Options::getOption( 'general_in' );
    if( ! empty( $general_price_in ) ) $price_in_option = unserialize( $general_price_in[ 'value' ] );
}
$price_out_option = Options::getOption( 'price_out_' . $client[ 'id_client' ] );
if( empty( $price_out_option ) ) {
    $general_price_out = Options::getOption( 'general_out' );
    if( ! empty( $general_price_out ) ) $price_out_option = unserialize( $general_price_out[ 'value' ] );
}

@session_start();
$user = get_connected_user();
?>
<div class="flex flex-col gap-4">
    <div class="w-full rounded-md border border-gray-300 shadow-md flex flex-col gap-4 p-6">
        <h2 class="text-3xl mb-2">Paramètres</h2>
        <div class="relative w-full flex justify-between items-center">
            <span edit-value="1"><?= $client[ 'nom' ] ?></span>
            <form action="<?= get_url( '/client/' . $client[ 'uuid' ] . '/edit' ) ?>" method="POST" edit-form="1" class="hidden gap-4 items-center justify-between">
                <input type="hidden" name="nonce" value="<?= create_nonce( 'edit-client', $user[ 'uuid' ], $_SESSION[ '_auth_token' ] ) ?>">
                <input type="text" name="client-nom" id="client-nom" class="h-full min-w-[300px] border border-gray-300 p-2 rounded-md outline-none" value="<?= $client[ 'nom' ] ?>" placeholder="Nouveau nom">
                <button class="border border-blue-500 bg-blue-500 text-white font-semibold transition duration-300 ease-in-out hover:bg-transparent hover:text-blue-500 px-4 py-1 rounded-md">Enregistrer</button>
            </form>
            <button edit-target="1" class="text-blue-500 w-fit h-fit px-4 py-2 rounded-md transition duration-300 ease-in-out hover:bg-gray-300/30">Modifier</button>
            <button edit-cancel="1" class="hidden text-red-500 w-fit h-fit px-4 py-2 rounded-md transition duration-300 ease-in-out hover:bg-gray-300/30">Annuler</button>
        </div>
        <div class="relative w-full flex justify-between items-center">
            <span edit-value="2"><?= $client[ 'email' ] ?></span>
            <form action="<?= get_url( '/client/' . $client[ 'uuid' ] . '/edit' ) ?>" method="POST" edit-form="2" class="hidden gap-4 items-center justify-between">
                <input type="hidden" name="nonce" value="<?= create_nonce( 'edit-client', $user[ 'uuid' ], $_SESSION[ '_auth_token' ] ) ?>">
                <input type="email" name="client-email" id="client-email" class="h-full min-w-[300px] border border-gray-300 p-2 rounded-md outline-none" value="<?= $client[ 'email' ] ?>" placeholder="Nouvel email">
                <button class="border border-blue-500 bg-blue-500 text-white font-semibold transition duration-300 ease-in-out hover:bg-transparent hover:text-blue-500 px-4 py-1 rounded-md">Enregistrer</button>
            </form>
            <button edit-target="2" class="text-blue-500 w-fit h-fit px-4 py-2 rounded-md transition duration-300 ease-in-out hover:bg-gray-300/30">Modifier</button>
            <button edit-cancel="2" class="hidden text-red-500 w-fit h-fit px-4 py-2 rounded-md transition duration-300 ease-in-out hover:bg-gray-300/30">Annuler</button>
        </div>
        <hr class="border-t-gray-300 w-full">
        <span class="text-xl font-semibold">Prix appliqués</span>
        <div class="relative w-full flex justify-between items-center">
            <span edit-value="3">Prix IN (pour 1000 tokens) : <?= isset( $price_in_option[ 'value' ] ) ? unserialize( $price_in_option[ 'value' ] ) : 0.0005 ?> euros</span>
            <form action="<?= get_url( '/client/' . $client[ 'uuid' ] . '/edit' ) ?>" method="POST" edit-form="3" class="hidden gap-4 items-center justify-between">
                <input type="hidden" name="nonce" value="<?= create_nonce( 'edit-client', $user[ 'uuid' ], $_SESSION[ '_auth_token' ] ) ?>">
                <input type="text" name="client-in" id="client-in" class="h-full min-w-[300px] border border-gray-300 p-2 rounded-md outline-none" value="<?= isset( $price_in_option[ 'value' ] ) ? unserialize( $price_in_option[ 'value' ] ) : 0.0005 ?>" placeholder="Nouveau prix pour 1000 tokens">
                <button class="border border-blue-500 bg-blue-500 text-white font-semibold transition duration-300 ease-in-out hover:bg-transparent hover:text-blue-500 px-4 py-1 rounded-md">Enregistrer</button>
            </form>
            <button edit-target="3" class="text-blue-500 w-fit h-fit px-4 py-2 rounded-md transition duration-300 ease-in-out hover:bg-gray-300/30">Modifier</button>
            <button edit-cancel="3" class="hidden text-red-500 w-fit h-fit px-4 py-2 rounded-md transition duration-300 ease-in-out hover:bg-gray-300/30">Annuler</button>
        </div>
        <div class="relative w-full flex justify-between items-center">
            <span edit-value="4">Prix OUT (pour 1000 tokens) : <?= isset( $price_out_option[ 'value' ] ) ? unserialize( $price_out_option[ 'value' ] ) : 0.0005 ?> euros</span>
            <form action="<?= get_url( '/client/' . $client[ 'uuid' ] . '/edit' ) ?>" method="POST" edit-form="4" class="hidden gap-4 items-center justify-between">
                <input type="hidden" name="nonce" value="<?= create_nonce( 'edit-client', $user[ 'uuid' ], $_SESSION[ '_auth_token' ] ) ?>">
                <input type="text" name="client-out" id="client-out" class="h-full min-w-[300px] border border-gray-300 p-2 rounded-md outline-none" value="<?= isset( $price_out_option[ 'value' ] ) ? unserialize( $price_out_option[ 'value' ] ) : 0.0005 ?>" placeholder="Nouveau prix pour 1000 tokens">
                <button class="border border-blue-500 bg-blue-500 text-white font-semibold transition duration-300 ease-in-out hover:bg-transparent hover:text-blue-500 px-4 py-1 rounded-md">Enregistrer</button>
            </form>
            <button edit-target="4" class="text-blue-500 w-fit h-fit px-4 py-2 rounded-md transition duration-300 ease-in-out hover:bg-gray-300/30">Modifier</button>
            <button edit-cancel="4" class="hidden text-red-500 w-fit h-fit px-4 py-2 rounded-md transition duration-300 ease-in-out hover:bg-gray-300/30">Annuler</button>
        </div>
        <div class="relative w-full flex justify-between items-center">
            <span edit-value="5">Crédit restant : <?= floatval( $client[ 'credit' ] ) ?> euros</span>
            <form action="<?= get_url( '/client/' . $client[ 'uuid' ] . '/edit' ) ?>" method="POST" edit-form="5" class="hidden gap-4 items-center justify-between">
                <input type="hidden" name="nonce" value="<?= create_nonce( 'edit-client', $user[ 'uuid' ], $_SESSION[ '_auth_token' ] ) ?>">
                <input type="number" step="any" name="client-credit" id="client-credit" class="h-full min-w-[300px] border border-gray-300 p-2 rounded-md outline-none" value="<?= floatval( $client[ 'credit' ] ) ?>" placeholder="Nouveau crédit restant">
                <button class="border border-blue-500 bg-blue-500 text-white font-semibold transition duration-300 ease-in-out hover:bg-transparent hover:text-blue-500 px-4 py-1 rounded-md">Enregistrer</button>
            </form>
            <button edit-target="5" class="text-blue-500 w-fit h-fit px-4 py-2 rounded-md transition duration-300 ease-in-out hover:bg-gray-300/30">Modifier</button>
            <button edit-cancel="5" class="hidden text-red-500 w-fit h-fit px-4 py-2 rounded-md transition duration-300 ease-in-out hover:bg-gray-300/30">Annuler</button>
        </div>
        <div class="relative w-full flex justify-between items-center">
            <span edit-value="6">Modèle utilisé : <?= $client[ 'model' ] ?></span>
            <form action="<?= get_url( '/client/' . $client[ 'uuid' ] . '/edit' ) ?>" method="POST" edit-form="6" class="hidden gap-4 items-center justify-between">
                <input type="hidden" name="nonce" value="<?= create_nonce( 'edit-client', $user[ 'uuid' ], $_SESSION[ '_auth_token' ] ) ?>">
                <select name="client-model" id="client-model" class="h-full min-w-[300px] border border-gray-300 p-2 rounded-md outline-none">
                    <option value="gpt-3.5-turbo">GPT 3.5 Turbo</option>
                    <option value="gpt-4">GPT 4</option>
                </select>
                <button class="border border-blue-500 bg-blue-500 text-white font-semibold transition duration-300 ease-in-out hover:bg-transparent hover:text-blue-500 px-4 py-1 rounded-md">Enregistrer</button>
            </form>
            <button edit-target="6" class="text-blue-500 w-fit h-fit px-4 py-2 rounded-md transition duration-300 ease-in-out hover:bg-gray-300/30">Modifier</button>
            <button edit-cancel="6" class="hidden text-red-500 w-fit h-fit px-4 py-2 rounded-md transition duration-300 ease-in-out hover:bg-gray-300/30">Annuler</button>
        </div>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <?php
        add_hook( 'renew-api-client', fn() => $client );
        add_hook( 'renew-api-user', fn() => $user );
        load_template( 'parts-renew-api' );
        ?>
    </div>
</div>
<script src="<?= get_url( '/assets/js/edit-form.js' ) ?>"></script>