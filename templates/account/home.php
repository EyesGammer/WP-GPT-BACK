<div class="h-16 w-full bg-[#3E267A] shadow-md">
    <div class="mx-auto w-4/5 h-full flex items-center justify-between">
        <div class="flex items-center justify-start gap-4 h-full">
            <img src="<?= get_url('/assets/img/logo-1024-violet.png') ?>" alt="Logo Kumo GPT" class="h-full w-auto aspect-square">
            <h1 class="uppercase font-['Inter'] font-black text-[#9F88D9] text-2xl">Kumo GPT</h1>
        </div>
        <div class="h-2/3 flex items-stretch gap-2">
            <button class="bg-[#9F88D9] border border-[#9F88D9] px-4 font-light outline-none text-white rounded-full transition ease-in-out duration-300 hover:bg-transparent">Se connecter</button>
            <button class="border border-[#9F88D9] px-4 font-light outline-none text-white rounded-full transition ease-in-out duration-300 hover:text-white hover:bg-[#9F88D9]">Créer un compte</button>
        </div>
    </div>
</div>
<div class="w-full px-20 py-10">
    <?= do_hook( 'home-content' ) ?>
</div>