<?php
@session_start();
?>
<div class="flex flex-col gap-4">
    <div class="flex items-center justify-between">
        <h2 class="text-3xl font-semibold">Ajouter un client</h2>
    </div>
    <div class="flex flex-col">
        <?php
        load_template( 'error' );
        ?>
        <form action="<?= get_url( '/action/add/client' ) ?>" method="POST" class="rounded-md border border-gray-400 shadow-xl flex flex-col w-fit gap-4 p-10 relative">
            <input type="hidden" name="referrer" value="<?= get_url( '/homepage' ) ?>">
            <input type="hidden" name="nonce" value="<?= create_nonce( 'new-client', $_SESSION[ '_auth_user' ], $_SESSION[ '_auth_token' ] ) ?>">
            <div class="relative h-fit flex flex-col mx-auto w-full">
                <label for="client-name">Nom</label>
                <input type="text" name="client-name" id="client-name" class="outline-none px-2 border border-gray-400 rounded-md">
            </div>
            <div class="relative h-fit flex flex-col mx-auto w-full">
                <label for="client-email">Adresse mail</label>
                <div class="w-full flex items-center justify-center">
                    <input type="email" name="client-email" id="client-email" class="outline-none px-2 border border-gray-400 rounded-md w-full">
                </div>
            </div>
            <div class="relative h-fit flex flex-col mx-auto w-full">
                <label for="client-email">Crédit de base (euros)</label>
                <div class="w-full flex items-center justify-center">
                    <input type="credit" name="client-credit" id="client-credit" min="5" class="outline-none px-2 border border-gray-400 rounded-md w-full" value="5">
                </div>
            </div>
            <button class="select-none w-fit rounded-md border border-black bg-black px-4 py-2 relative text-white transition duration-300 ease-in-out hover:bg-transparent hover:text-black -right-full -translate-x-full">Créer le client</button>
        </form>
    </div>
</div>