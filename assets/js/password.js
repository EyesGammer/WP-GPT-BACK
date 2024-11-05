( change_password_switch => {
    change_password_switch.forEach( item => {
        if( ! item.dataset.hasOwnProperty( 'target' ) ) return;
        let svg = [...item.querySelectorAll( 'svg' )];
        item.addEventListener( 'click', () => {
            let temp_input = document.querySelector( `#${ item.dataset.target }` );
            if( temp_input.type === 'text' ) temp_input.type = 'password';
            else if( temp_input.type === 'password' ) temp_input.type = 'text';
        } );
    } );
} )( document.querySelectorAll( '.change-password-type' ) );