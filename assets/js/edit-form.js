( edit_buttons => {
    edit_buttons.forEach( button => {
        let parent_node = button.parentNode;
        let edit_id = button.getAttribute( 'edit-target' );
        let span_value = parent_node.querySelector( `[edit-value="${ edit_id }"]` );
        let form = parent_node.querySelector( `[edit-form="${ edit_id }"]` );
        let button_cancel = parent_node.querySelector( `[edit-cancel="${ edit_id }"]` );
        if( ! form || ! span_value || ! button_cancel ) return;
        button.addEventListener( 'click', () => {
            button.classList.add( 'hidden' );
            span_value.classList.add( 'hidden' );
            form.classList.toggle( 'hidden' )
            form.classList.toggle( 'flex' );
            button_cancel.classList.toggle( 'hidden' );
        } );
        button_cancel.addEventListener( 'click', () => {
            button.classList.remove( 'hidden' );
            span_value.classList.remove( 'hidden' );
            form.classList.toggle( 'hidden' )
            form.classList.toggle( 'flex' );
            button_cancel.classList.toggle( 'hidden' );
        } );
    } );
} )( [...document.querySelectorAll( '[edit-target]' )] );