<div class="w-full h-full min-h-screen flex">
    <?php
    load_template( do_hook( 'dashboard-part' ) ?? 'parts-navbar' );
    ?>
    <div class="w-10/12 h-full flex flex-col">
        <div class="h-16 w-full border-b border-gray-300 px-20 flex gap-2 items-center">
            <div class="h-full w-fit flex items-center">
                <h1 class="text-xl font-bold font-['Nexa'] mr-10"><?= do_hook( 'dashboard-title' ) ?></h1>
            </div>
        </div>
        <div class="px-10 py-14 flex flex-col gap-8">
            <?= do_hook( 'dashboard-content' ) ?>
        </div>
    </div>
</div>