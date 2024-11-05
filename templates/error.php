<?php
$info_message = get_error_message();
if( isset( $info_message ) ) {
?>
    <div class="w-fit absolute top-4 right-4 bg-orange-400/30 p-4 rounded-md shadow-md">
        <span class="text-xl font-semibold"><?= $info_message[ 'title' ] ?></span>
        <p class="text-sm text-justify"><?= $info_message[ 'message' ] ?></p>
    </div>
<?php
}
?>