<?php
$client_uuid = do_hook( 'client-dashboard-uuid' );
$user_pseudo = do_hook( 'client-dashboard-pseudo' );

$user = get_connected_user();
$current_page = get_url( '+', true );
?>
<div class="w-2/12 h-full min-h-screen border-r border-gray-300 sticky top-0 px-6 py-10 flex flex-col gap-4">
    <a href="<?= get_url( '/dashboard/' . $user[ 'uuid' ] ) ?>" class="w-full h-fit flex gap-2 items-center select-none">
        <img src="<?= get_url( '/assets/img/logo-1024.png' ) ?>" alt="Logo Kumo GPT" class="w-1/3 h-auto aspect-square">
        <span class="text-3xl font-bold font-['Specify']" style="font-stretch: extra-expanded;">Kumo GPT</span>
    </a>
    <div class="flex flex-col gap-px mt-2">
        <span class="text-xl font-semibold px-4">Général</span>
        <a href="<?= get_url( '/dashboard/' . $user[ 'uuid' ] ) ?>" class="<?= $current_page === 'dashboard/' . $client_uuid ? 'bg-gray-300/30 ' : '' ?>w-full h-fit py-2 px-4 transition ease-in-out duration-300 hover:bg-gray-300/30 rounded-md flex items-center justify-start gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l-2 0l9 -9l9 9l-2 0" /><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" /><path d="M10 12h4v4h-4z" /></svg>
            Accueil
        </a>
        <a href="<?= get_url( '/dashboard/billing/' . $user[ 'uuid' ] ) ?>" class="<?= $current_page === 'dashboard/billing/' . $client_uuid ? 'bg-gray-300/30 ' : '' ?>w-full h-fit py-2 px-4 transition ease-in-out duration-300 hover:bg-gray-300/30 rounded-md flex items-center justify-start gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M14 11h-2.5a1.5 1.5 0 0 0 0 3h1a1.5 1.5 0 0 1 0 3h-2.5" /><path d="M12 17v1m0 -8v1" /></svg>
            Facturation
        </a>
        <a href="<?= get_url( '/statistics' ) ?>" class="<?= $current_page === 'statistics' ? 'bg-gray-300/30 ' : '' ?>w-full h-fit py-2 px-4 transition ease-in-out duration-300 hover:bg-gray-300/30 rounded-md flex items-center justify-start gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" /><path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" /><path d="M9 17v-5" /><path d="M12 17v-1" /><path d="M15 17v-3" /></svg>
            Statistiques
        </a>
    </div>
    <div class="flex flex-col gap-px mt-2">
        <span class="text-xl font-semibold px-4">Compte</span>
        <a href="<?= get_url( '/settings' ) ?>" class="<?= $current_page === 'settings' ? 'bg-gray-300/30 ' : '' ?>w-full h-fit py-2 px-4 transition ease-in-out duration-300 hover:bg-gray-300/30 rounded-md flex items-center justify-start gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" /><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /></svg>
            <?= $user_pseudo ?>
        </a>
        <a href="<?= get_url( '/dashboard/support/' . $user[ 'uuid' ] ) ?>" class="<?= $current_page === 'dashboard/support/' . $client_uuid ? 'bg-gray-300/30 ' : '' ?>w-full h-fit py-2 px-4 transition ease-in-out duration-300 hover:bg-gray-300/30 rounded-md flex items-center justify-start gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M20.136 11.136l-8.136 -8.136l-9 9h2v7a2 2 0 0 0 2 2h7" /><path d="M9 21v-6a2 2 0 0 1 2 -2h2c.467 0 .896 .16 1.236 .428" /><path d="M19 22v.01" /><path d="M19 19a2 2 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483" /></svg>
            Support
        </a>
        <a href="<?= get_url( '/dashboard/support/idea/' . $user[ 'uuid' ] ) ?>" class="<?= $current_page === 'dashboard/support/idea/' . $client_uuid ? 'bg-gray-300/30 ' : '' ?>text-blue-500 w-full h-fit py-2 px-4 transition ease-in-out duration-300 hover:bg-gray-300/30 rounded-md flex items-center justify-start gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12h1m8 -9v1m8 8h1m-15.4 -6.4l.7 .7m12.1 -.7l-.7 .7" /><path d="M9 16a5 5 0 1 1 6 0a3.5 3.5 0 0 0 -1 3a2 2 0 0 1 -4 0a3.5 3.5 0 0 0 -1 -3" /><path d="M9.7 17l4.6 0" /></svg>
            Proposer
        </a>
        <a href="<?= get_url( '/logout' ) ?>" class="text-red-500 w-full h-fit py-2 px-4 transition ease-in-out duration-300 hover:bg-gray-300/30 rounded-md flex items-center justify-start gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" /><path d="M9 12h12l-3 -3" /><path d="M18 15l3 -3" /></svg>
            Se déconnecter
        </a>
    </div>
</div>