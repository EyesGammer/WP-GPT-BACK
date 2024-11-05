<?php
@session_start();
$user = get_connected_user();

global $database;
$prompts = $database->prepared_query( 'SELECT * FROM gpt_prompts' );
if( empty( $prompts ) ) $prompts = array();
?>
<div class="flex flex-col gap-4">
    <div class="w-full rounded-md border border-gray-300 shadow-md flex flex-col gap-4 p-6">
        <h2 class="text-3xl mb-2">Liste des prompts</h2>
        <?php
        foreach( $prompts as $loop_prompt ) {
        ?>
        <div class="relative w-full flex flex-col pb-4 border-b border-gray-300">
            <div edit-container="<?= $loop_prompt[ 'uuid' ] ?>-nom" class="relative w-full flex justify-between items-start">
                <span edit-value="<?= $loop_prompt[ 'uuid' ] ?>-nom" class="font-semibold text-2xl">
                    <?= $loop_prompt[ 'nom' ] ?> <i class="text-xs text-gray-400"><?= $loop_prompt[ 'utilisations' ] ?> utilisations (slug: <u><?= $loop_prompt[ 'slug' ] ?></u>)</i>
                </span>
                <form action="<?= get_url( '/prompts/' . $loop_prompt[ 'uuid' ] . '/edit' ) ?>" method="POST" edit-form="<?= $loop_prompt[ 'uuid' ] ?>-nom" class="hidden gap-4 items-center justify-between">
                    <input type="hidden" name="nonce" value="<?= create_nonce( 'edit-prompt', $user[ 'uuid' ], $_SESSION[ '_auth_token' ] ) ?>">
                    <input type="text" name="prompt-nom" id="prompt-nom" class="h-full min-w-[300px] border border-gray-300 p-2 rounded-md outline-none" value="<?= $loop_prompt[ 'nom' ] ?>" placeholder="Nouveau nom">
                    <button class="border border-blue-500 bg-blue-500 text-white font-semibold transition duration-300 ease-in-out hover:bg-transparent hover:text-blue-500 px-4 py-1 rounded-md">Enregistrer</button>
                </form>
                <div class="flex gap-1 items-center">
                    <a href="<?= get_url( '/action/delete/prompt/' . $loop_prompt[ 'uuid' ] . '?n=' . create_nonce( 'delete-prompt', $_SESSION[ '_auth_user' ], $_SESSION[ '_auth_token' ] ) ) ?>" edit-delete="<?= $loop_prompt[ 'uuid' ] ?>-nom" class="text-red-500 w-fit h-fit px-4 py-2 rounded-md transition duration-300 ease-in-out hover:bg-gray-300/30">Supprimer</a>
                    <button edit-target="<?= $loop_prompt[ 'uuid' ] ?>-nom" class="text-blue-500 w-fit h-fit px-4 py-2 rounded-md transition duration-300 ease-in-out hover:bg-gray-300/30">Modifier</button>
                </div>
                <button edit-cancel="<?= $loop_prompt[ 'uuid' ] ?>-nom" class="hidden text-red-500 w-fit h-fit px-4 py-2 rounded-md transition duration-300 ease-in-out hover:bg-gray-300/30">Annuler</button>
            </div>
            <div edit-container="<?= $loop_prompt[ 'uuid' ] ?>-description" class="relative w-full flex justify-between items-start">
                <span edit-value="<?= $loop_prompt[ 'uuid' ] ?>-description">Description : <?= $loop_prompt[ 'description' ] ?></span>
                <form action="<?= get_url( '/prompts/' . $loop_prompt[ 'uuid' ] . '/edit' ) ?>" method="POST" edit-form="<?= $loop_prompt[ 'uuid' ] ?>-description" class="hidden gap-4 items-start justify-between">
                    <input type="hidden" name="nonce" value="<?= create_nonce( 'edit-prompt', $user[ 'uuid' ], $_SESSION[ '_auth_token' ] ) ?>">
                    <textarea name="prompt-description" id="prompt-description" cols="30" rows="5" class="h-full min-w-[300px] border border-gray-300 p-2 rounded-md outline-none"><?= $loop_prompt[ 'description' ] ?></textarea>
                    <button class="border border-blue-500 bg-blue-500 text-white font-semibold transition duration-300 ease-in-out hover:bg-transparent hover:text-blue-500 px-4 py-1 rounded-md">Enregistrer</button>
                </form>
                <button edit-target="<?= $loop_prompt[ 'uuid' ] ?>-description" class="text-blue-500 w-fit h-fit px-4 py-2 rounded-md transition duration-300 ease-in-out hover:bg-gray-300/30">Modifier</button>
                <button edit-cancel="<?= $loop_prompt[ 'uuid' ] ?>-description" class="hidden text-red-500 w-fit h-fit px-4 py-2 rounded-md transition duration-300 ease-in-out hover:bg-gray-300/30">Annuler</button>
            </div>
            <div edit-container="<?= $loop_prompt[ 'uuid' ] ?>-prompt" class="relative w-full flex justify-between items-start">
                <span edit-value="<?= $loop_prompt[ 'uuid' ] ?>-prompt">Prompt : <?= $loop_prompt[ 'prompt' ] ?></span>
                <form action="<?= get_url( '/prompts/' . $loop_prompt[ 'uuid' ] . '/edit' ) ?>" method="POST" edit-form="<?= $loop_prompt[ 'uuid' ] ?>-prompt" class="hidden gap-4 items-start justify-between">
                    <input type="hidden" name="nonce" value="<?= create_nonce( 'edit-prompt', $user[ 'uuid' ], $_SESSION[ '_auth_token' ] ) ?>">
                    <textarea name="prompt-prompt" id="prompt-prompt" cols="30" rows="5" class="h-full min-w-[300px] border border-gray-300 p-2 rounded-md outline-none"><?= $loop_prompt[ 'prompt' ] ?></textarea>
                    <button class="border border-blue-500 bg-blue-500 text-white font-semibold transition duration-300 ease-in-out hover:bg-transparent hover:text-blue-500 px-4 py-1 rounded-md">Enregistrer</button>
                </form>
                <button edit-target="<?= $loop_prompt[ 'uuid' ] ?>-prompt" class="text-blue-500 w-fit h-fit px-4 py-2 rounded-md transition duration-300 ease-in-out hover:bg-gray-300/30">Modifier</button>
                <button edit-cancel="<?= $loop_prompt[ 'uuid' ] ?>-prompt" class="hidden text-red-500 w-fit h-fit px-4 py-2 rounded-md transition duration-300 ease-in-out hover:bg-gray-300/30">Annuler</button>
            </div>
        </div>
        <?php
        }
        ?>
    </div>
    <form action="<?= get_url( '/action/add/prompt' ) ?>" method="POST" class="w-full rounded-md shadow-md border border-gray-300 p-6 grid grid-cols-2 gap-4">
        <h2 class="col-span-full text-3xl font-semibold">Ajouter un prompt</h2>
        <?php
        load_template( 'error' );
        ?>
        <div class="flex flex-col w-full gap-4 relative pr-4 border-r border-r-gray-300">
            <input type="hidden" name="referrer" value="<?= get_url( '/prompts' ) ?>">
            <input type="hidden" name="nonce" value="<?= create_nonce( 'new-prompt', $_SESSION[ '_auth_user' ], $_SESSION[ '_auth_token' ] ) ?>">
            <div class="relative h-fit flex flex-col mx-auto w-full">
                <label for="prommpt-name">Nom *</label>
                <input type="text" name="prompt-name" id="promp-name" class="outline-none px-2 border border-gray-400 rounded-md">
            </div>
            <div class="relative h-fit flex flex-col mx-auto w-full">
                <label for="prompt-description">Description</label>
                <textarea name="prompt-description" id="prompt-description" cols="30" rows="3" class="outline-none border border-gray-400 rounded-md"></textarea>
            </div>
            <div class="relative h-fit flex flex-col mx-auto w-full">
                <label for="prompt-body">Corps du prompt *</label>
                <textarea name="prompt-body" id="prompt-body" cols="30" rows="5" class="outline-none border border-gray-400 rounded-md"></textarea>
            </div>
            <button id="parseTemplate" data-target="prompt-body" data-hide="hide-parsed" class="select-none w-fit rounded-md border border-blue-500 bg-blue-500 px-4 py-2 relative text-white transition duration-300 ease-in-out hover:bg-transparent hover:text-blue-500 -right-full -translate-x-full">Récupérer les arguments</button>
        </div>
        <div class="flex flex-col w-full gap-4 relative">
            <h3 class="font-semibold text-xl">Arguments du prompt</h3>
            <span id="hide-parsed" class="italic text-sm">Appuyez sur le bouton "Récupérer les arguments" pour continuer...</span>
            <div id="content-parsed" class="w-full h-full grid grid-cols-2 gap-2"></div>
        </div>
        <div class="col-span-full h-fit flex flex-row-reverse justify-between items-end">
            <button id="create-prompt" class="disabled:bg-gray-500 disabled:border-gray-500 disabled:hover:text-white disabled:transition-none select-none w-fit rounded-md border border-black bg-black px-4 py-2 relative text-white transition duration-300 ease-in-out hover:bg-transparent hover:text-black" disabled>Créer le prompt</button>
            <span class="italic text-xs">* : Champs requis</span>
        </div>
    </form>
</div>
<script>
    const internal_api = '<?= get_url( '/internal/api/parse/template' ) ?>';
    const nonce = '<?= create_nonce( 'parse-template', $user[ 'uuid' ], $_SESSION[ '_auth_token' ] ) ?>';
</script>
<script src="<?= get_url( '/assets/js/prompts.js' ) ?>"></script>
