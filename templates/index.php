<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="prefetch" href="<?= get_url( '/assets/img/logo-1024.jpg' ) ?>">
    <link rel="prefetch" href="<?= get_url( '/assets/img/logo-1024.png' ) ?>">
    <link rel="icon" href="<?= get_url( '/assets/img/logo-1024.jpg' ) ?>">
    <title><?= do_hook( 'page-title' ) ?></title>
    <link rel="stylesheet" href="<?= get_url( '/assets/fonts.css' ) ?>">
    <?php
    $styles = do_hook( 'page-style' );
    if( $styles ) foreach( $styles as $style ) {
    ?>
        <link rel="stylesheet" href="<?= $style ?>">
    <?php
    }
    ?>
</head>
<body>

<script>
    const gpt_internal_api = {
        api_url: '<?= get_url( '/local/api' ) ?>',
        setAPI: function( url ) {
            this.api_url = url;
            return this;
        },
        post: async function( endpoint, config, text=false ) {
            return await fetch( `${ this.api_url }${ endpoint }`, config ?? {
                method: 'POST'
            } )
                .then( response => text ? response.text() : response.json() )
                .catch( error => console.error( error ) );
        }
    };
</script>

<?= do_hook( 'page-content' ) ?>

<script>
    ( can_copy => {
        can_copy.forEach( item => {
            item.addEventListener( 'click', event => {
                event.preventDefault();
                if( navigator && navigator.clipboard && navigator.clipboard.write ) {
                    navigator.clipboard.writeText( item.textContent );
                }
            } );
        } );
    } )( [...document.querySelectorAll( '.can-copy' )] );
</script>

</body>
</html>