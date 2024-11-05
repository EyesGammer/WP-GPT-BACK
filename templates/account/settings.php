<?php
@session_start();
$user = get_connected_user();

$api_key = get_secured_option( 'general_api' );
if( empty( $api_key ) ) $api_key = '';
else $api_key = $api_key[ 'value' ];

$general_in = \controllers\Options::getOption( 'general_in' );
$general_out = \controllers\Options::getOption( 'general_out' );
?>
<div class="flex flex-col gap-4">
    <div class="w-full rounded-md border border-gray-300 shadow-md flex flex-col gap-4 p-6">
        <h2 class="text-3xl mb-2">Paramètres</h2>
        <div class="relative w-full flex justify-between items-center">
            <span edit-value="1">Clé API : <i class="font-semibold"><?= $api_key ?></i></span>
            <form action="<?= get_url( '+/edit' ) ?>" method="POST" edit-form="1" class="hidden gap-4 items-center justify-between">
                <input type="hidden" name="nonce" value="<?= create_nonce( 'edit-account', $user[ 'uuid' ], $_SESSION[ '_auth_token' ] ) ?>">
                <input type="text" name="account-api" id="account-api" class="h-full min-w-[300px] border border-gray-300 p-2 rounded-md outline-none" value="<?= $api_key ?>" placeholder="Nouvelle clé API">
                <button class="border border-blue-500 bg-blue-500 text-white font-semibold transition duration-300 ease-in-out hover:bg-transparent hover:text-blue-500 px-4 py-1 rounded-md">Enregistrer</button>
            </form>
            <button edit-target="1" class="text-blue-500 w-fit h-fit px-4 py-2 rounded-md transition duration-300 ease-in-out hover:bg-gray-300/30">Modifier</button>
            <button edit-cancel="1" class="hidden text-red-500 w-fit h-fit px-4 py-2 rounded-md transition duration-300 ease-in-out hover:bg-gray-300/30">Annuler</button>
        </div>
        <div class="relative w-full flex justify-between items-center">
            <span edit-value="2">Prix IN (pour 1000 tokens) : <i class="font-semibold"><?= isset( $general_in[ 'value' ] ) ? unserialize( $general_in[ 'value' ] ) : '' ?></i></span>
            <form action="<?= get_url( '+/edit' ) ?>" method="POST" edit-form="2" class="hidden gap-4 items-center justify-between">
                <input type="hidden" name="nonce" value="<?= create_nonce( 'edit-account', $user[ 'uuid' ], $_SESSION[ '_auth_token' ] ) ?>">
                <input type="text" name="account-in" id="account-in" class="h-full min-w-[300px] border border-gray-300 p-2 rounded-md outline-none" value="<?= isset( $general_in[ 'value' ] ) ? unserialize( $general_in[ 'value' ] ) : '' ?>" placeholder="Nouveau prix pour 1000 tokens">
                <button class="border border-blue-500 bg-blue-500 text-white font-semibold transition duration-300 ease-in-out hover:bg-transparent hover:text-blue-500 px-4 py-1 rounded-md">Enregistrer</button>
            </form>
            <button edit-target="2" class="text-blue-500 w-fit h-fit px-4 py-2 rounded-md transition duration-300 ease-in-out hover:bg-gray-300/30">Modifier</button>
            <button edit-cancel="2" class="hidden text-red-500 w-fit h-fit px-4 py-2 rounded-md transition duration-300 ease-in-out hover:bg-gray-300/30">Annuler</button>
        </div>
        <div class="relative w-full flex justify-between items-center">
            <span edit-value="3">Prix OUT (pour 1000 tokens) : <i class="font-semibold"><?= isset( $general_out[ 'value' ] ) ? unserialize( $general_out[ 'value' ] ) : '' ?></i></span>
            <form action="<?= get_url( '+/edit' ) ?>" method="POST" edit-form="3" class="hidden gap-4 items-center justify-between">
                <input type="hidden" name="nonce" value="<?= create_nonce( 'edit-account', $user[ 'uuid' ], $_SESSION[ '_auth_token' ] ) ?>">
                <input type="text" name="account-out" id="account-out" class="h-full min-w-[300px] border border-gray-300 p-2 rounded-md outline-none" value="<?= isset( $general_out[ 'value' ] ) ? unserialize( $general_out[ 'value' ] ) : '' ?>" placeholder="Nouveau prix pour 1000 tokens">
                <button class="border border-blue-500 bg-blue-500 text-white font-semibold transition duration-300 ease-in-out hover:bg-transparent hover:text-blue-500 px-4 py-1 rounded-md">Enregistrer</button>
            </form>
            <button edit-target="3" class="text-blue-500 w-fit h-fit px-4 py-2 rounded-md transition duration-300 ease-in-out hover:bg-gray-300/30">Modifier</button>
            <button edit-cancel="3" class="hidden text-red-500 w-fit h-fit px-4 py-2 rounded-md transition duration-300 ease-in-out hover:bg-gray-300/30">Annuler</button>
        </div>
    </div>
</div>
<script src="<?= get_url( '/assets/js/edit-form.js' ) ?>"></script>