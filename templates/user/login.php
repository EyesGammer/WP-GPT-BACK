<?php
load_template( 'error' );
?>
<div class="background-circuit w-full h-full absolute bg-white"></div>
<style>
    .background-circuit {
        z-index: -1;
        background-image: url("http://localhost:90/assets/circuit-board.svg");
        clip-path: polygon( 0% 54.908%,1.856% 54.21%,1.856% 54.21%,2.463% 53.983%,3.171% 53.713%,3.969% 53.399%,4.845% 53.039%,5.788% 52.633%,6.786% 52.18%,7.829% 51.68%,8.905% 51.131%,10.003% 50.532%,11.111% 49.884%,11.111% 49.884%,12.221% 49.189%,13.332% 48.474%,14.444% 47.768%,15.555% 47.102%,16.667% 46.506%,17.778% 46.009%,18.89% 45.643%,20.001% 45.436%,21.112% 45.42%,22.222% 45.624%,22.222% 45.624%,23.333% 46.067%,24.443% 46.718%,25.555% 47.535%,26.666% 48.476%,27.778% 49.499%,28.889% 50.561%,30.001% 51.62%,31.112% 52.635%,32.223% 53.562%,33.333% 54.359%,33.333% 54.359%,34.444% 54.99%,35.554% 55.466%,36.666% 55.807%,37.777% 56.031%,38.889% 56.156%,40.001% 56.202%,41.112% 56.186%,42.223% 56.128%,43.334% 56.045%,44.444% 55.957%,44.444% 55.957%,45.555% 55.882%,46.666% 55.81%,47.777% 55.735%,48.888% 55.647%,50% 55.539%,51.112% 55.402%,52.223% 55.229%,53.334% 55.011%,54.445% 54.741%,55.556% 54.409%,55.556% 54.409%,56.666% 54.011%,57.777% 53.549%,58.888% 53.032%,59.999% 52.467%,61.111% 51.861%,62.223% 51.224%,63.334% 50.561%,64.446% 49.882%,65.556% 49.193%,66.667% 48.502%,66.667% 48.502%,67.777% 47.817%,68.888% 47.144%,69.999% 46.493%,71.111% 45.872%,72.222% 45.289%,73.334% 44.754%,74.445% 44.275%,75.557% 43.861%,76.667% 43.52%,77.778% 43.261%,77.778% 43.261%,78.888% 43.09%,79.999% 42.998%,81.11% 42.979%,82.222% 43.022%,83.333% 43.12%,84.445% 43.264%,85.556% 43.445%,86.668% 43.655%,87.779% 43.885%,88.889% 44.126%,88.889% 44.126%,89.997% 44.367%,91.095% 44.606%,92.171% 44.842%,93.214% 45.072%,94.213% 45.293%,95.155% 45.503%,96.031% 45.698%,96.829% 45.877%,97.537% 46.036%,98.144% 46.173%,100% 46.589%,100% 100%,98.144% 100%,98.144% 100%,97.537% 100%,96.829% 100%,96.031% 100%,95.155% 100%,94.213% 100%,93.214% 100%,92.171% 100%,91.095% 100%,89.997% 100%,88.889% 100%,88.889% 100%,87.779% 100%,86.668% 100%,85.556% 100%,84.445% 100%,83.333% 100%,82.222% 100%,81.11% 100%,79.999% 100%,78.888% 100%,77.778% 100%,77.778% 100%,76.667% 100%,75.557% 100%,74.445% 100%,73.334% 100%,72.222% 100%,71.111% 100%,69.999% 100%,68.888% 100%,67.777% 100%,66.667% 100%,66.667% 100%,65.556% 100%,64.446% 100%,63.334% 100%,62.223% 100%,61.111% 100%,59.999% 100%,58.888% 100%,57.777% 100%,56.666% 100%,55.556% 100%,55.556% 100%,54.445% 100%,53.334% 100%,52.223% 100%,51.112% 100%,50% 100%,48.888% 100%,47.777% 100%,46.666% 100%,45.555% 100%,44.444% 100%,44.444% 100%,43.334% 100%,42.223% 100%,41.112% 100%,40.001% 100%,38.889% 100%,37.777% 100%,36.666% 100%,35.554% 100%,34.444% 100%,33.333% 100%,33.333% 100%,32.223% 100%,31.112% 100%,30.001% 100%,28.889% 100%,27.778% 100%,26.666% 100%,25.555% 100%,24.443% 100%,23.333% 100%,22.222% 100%,22.222% 100%,21.112% 100%,20.001% 100%,18.89% 100%,17.778% 100%,16.667% 100%,15.555% 100%,14.444% 100%,13.332% 100%,12.221% 100%,11.111% 100%,11.111% 100%,10.003% 100%,8.905% 100%,7.829% 100%,6.786% 100%,5.788% 100%,4.845% 100%,3.969% 100%,3.171% 100%,2.463% 100%,1.856% 100%,0% 100% );
    }
</style>
<a href="<?= get_url( '/homepage' ) ?>" class="absolute top-4 left-4 w-fit h-fit flex justify-between items-center gap-2">
    <img src="<?= get_url( '/assets/img/logo-1024.png' ) ?>" alt="Logo Kumo GPT" class="w-44 h-auto aspect-square">
    <span class="text-4xl font-['Specify'] font-bold" style="font-stretch: extra-expanded;">Kumo GPT</span>
</a>
<div class="w-4/12 mx-auto h-screen flex flex-col gap-4 justify-center">
    <span class="text-2xl font-semibold -mb-4">Se connecter</span>
    <form action="<?= get_url( '/login/connect' ) ?>" method="POST" class="rounded-md border border-gray-400 shadow-xl w-full flex flex-col mx-auto gap-4 p-10 relative bg-white">
        <input type="hidden" name="referrer" value="<?= get_url( '+' ) ?>">
        <div class="relative h-fit flex flex-col mx-auto w-full">
            <label for="user-name">Identifiant ou adresse mail</label>
            <input type="text" name="user-name" id="user-name" class="outline-none px-2 border border-gray-400 rounded-md" autofocus>
        </div>
        <div class="relative h-fit flex flex-col mx-auto w-full">
            <label for="user-pass">Mot de passe</label>
            <div class="w-full flex items-center justify-center">
                <input type="password" name="user-pass" id="user-pass" class="outline-none px-2 border border-gray-400 rounded-md w-full">
                <div data-target="user-pass" class="change-password-type h-full w-auto aspect-square cursor-pointer ml-2 transition duration-300 ease-in-out group">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 hidden group-hover:block" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 4c4.29 0 7.863 2.429 10.665 7.154l.22 .379l.045 .1l.03 .083l.014 .055l.014 .082l.011 .1v.11l-.014 .111a.992 .992 0 0 1 -.026 .11l-.039 .108l-.036 .075l-.016 .03c-2.764 4.836 -6.3 7.38 -10.555 7.499l-.313 .004c-4.396 0 -8.037 -2.549 -10.868 -7.504a1 1 0 0 1 0 -.992c2.831 -4.955 6.472 -7.504 10.868 -7.504zm0 5a3 3 0 1 0 0 6a3 3 0 0 0 0 -6z" stroke-width="0" fill="currentColor" /></svg>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 block group-hover:hidden" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M21 9c-2.4 2.667 -5.4 4 -9 4c-3.6 0 -6.6 -1.333 -9 -4" /><path d="M3 15l2.5 -3.8" /><path d="M21 14.976l-2.492 -3.776" /><path d="M9 17l.5 -4" /><path d="M15 17l-.5 -4" /></svg>
                </div>
            </div>
        </div>
        <button class="select-none w-fit rounded-md border border-black bg-black px-4 py-2 relative text-white transition duration-300 ease-in-out hover:bg-transparent hover:text-black -right-full -translate-x-full">Se connecter</button>
        <a href="<?= get_url( 'forgot' ) ?>" class="italic underline">Mot de passe oublié ?</a>
    </form>
    <script src="<?= get_url( '/assets/js/password.js' ) ?>"></script>
</div>