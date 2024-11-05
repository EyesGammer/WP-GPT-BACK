<?php
$client = do_hook( 'current-client' ) ?? null;
if( $client === null ) header( 'Location: ' . get_url( '/homepage' ) );

$client_access = \controllers\Access::getClientAccessByUUID( $client[ 'uuid' ] );

@session_start();
$user = get_connected_user();
?>
<div class="flex flex-col gap-4">
    <div class="w-full h-fit grid grid-cols-2 gap-4">
        <div class="w-full rounded-md border border-gray-300 shadow-md flex flex-col gap-4 p-6">
            <h2 class="text-3xl mb-2">Liste des accès</h2>
            <?php
            if( empty( $client_access ) ) {
                ?>
                <span class="italic text-gray-900">Aucun accès...</span>
                <?php
            } else foreach( $client_access as $index => $loop_access ) {
                ?>
                <div class="relative w-full flex flex-wrap justify-between items-center">
                    <span edit-value="<?= $index ?>"><?= $loop_access[ 'user' ][ 'pseudo' ] ?></span>
                    <form action="<?= get_url( '/client/' . $client[ 'uuid' ] . '/access/edit' ) ?>" method="POST" edit-form="<?= $index ?>" class="hidden gap-4 items-center justify-between">
                        <input type="hidden" name="nonce" value="<?= create_nonce( 'edit-client-access', $user[ 'uuid' ], $_SESSION[ '_auth_token' ] ) ?>">
                        <input type="text" name="access-username" id="access-username" class="h-full min-w-[300px] border border-gray-300 p-2 rounded-md outline-none" value="<?= $client[ 'nom' ] ?>" placeholder="Nouvel identifiant...">
                        <button class="border border-blue-500 bg-blue-500 text-white font-semibold transition duration-300 ease-in-out hover:bg-transparent hover:text-blue-500 px-4 py-1 rounded-md">Enregistrer</button>
                    </form>
                    <button edit-target="<?= $index ?>" class="text-blue-500 w-fit h-fit px-4 py-2 rounded-md transition duration-300 ease-in-out hover:bg-gray-300/30">Modifier</button>
                    <button edit-cancel="<?= $index ?>" class="hidden text-red-500 w-fit h-fit px-4 py-2 rounded-md transition duration-300 ease-in-out hover:bg-gray-300/30">Annuler</button>
                </div>
                <?php
            }
            ?>
        </div>
        <form action="<?= get_url( '/client/' . $client[ 'uuid' ] . '/access/add' ) ?>" method="POST" class="w-full h-fit rounded-md border border-gray-300 shadow-md flex flex-col gap-4 p-6">
            <input type="hidden" name="nonce" value="<?= create_nonce( 'client-access-add', $user[ 'uuid' ], $_SESSION[ '_auth_token' ] ) ?>">
            <input type="hidden" name="referrer" value="<?= get_url( '+' ) ?>">
            <h2 class="text-3xl mb-2">Ajouter un accès</h2>
            <div class="relative w-full h-fit">
                <label for="client-add-access-username">Identifiant</label>
                <input type="text" name="client-add-access-username" id="client-add-access-username" placeholder="Identifiant de connexion..." class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            </div>
            <div class="relative w-full h-fit">
                <label for="client-add-access-password">Mot de passe</label>
                <div class="w-full flex items-center justify-center">
                    <input type="password" name="client-add-access-password" id="client-add-access-password" placeholder="Mot de passe..." class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    <div data-target="client-add-access-password" class="change-password-type h-full w-auto aspect-square cursor-pointer ml-2 transition duration-300 ease-in-out group">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 hidden group-hover:block" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 4c4.29 0 7.863 2.429 10.665 7.154l.22 .379l.045 .1l.03 .083l.014 .055l.014 .082l.011 .1v.11l-.014 .111a.992 .992 0 0 1 -.026 .11l-.039 .108l-.036 .075l-.016 .03c-2.764 4.836 -6.3 7.38 -10.555 7.499l-.313 .004c-4.396 0 -8.037 -2.549 -10.868 -7.504a1 1 0 0 1 0 -.992c2.831 -4.955 6.472 -7.504 10.868 -7.504zm0 5a3 3 0 1 0 0 6a3 3 0 0 0 0 -6z" stroke-width="0" fill="currentColor" /></svg>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 block group-hover:hidden" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M21 9c-2.4 2.667 -5.4 4 -9 4c-3.6 0 -6.6 -1.333 -9 -4" /><path d="M3 15l2.5 -3.8" /><path d="M21 14.976l-2.492 -3.776" /><path d="M9 17l.5 -4" /><path d="M15 17l-.5 -4" /></svg>
                    </div>
                </div>
            </div>
            <button class="select-none w-fit rounded-md border border-black bg-black px-4 py-2 relative text-white transition duration-300 ease-in-out hover:bg-transparent hover:text-black">Créer l'accès</button>
        </form>
    </div>
</div>
<script src="<?= get_url( '/assets/js/password.js' ) ?>"></script>
<script src="<?= get_url( '/assets/js/edit-form.js' ) ?>"></script>
