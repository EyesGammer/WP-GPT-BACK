( edit_buttons => {
    edit_buttons.forEach( button => {
        let edit_id = button.getAttribute( 'edit-target' );
        let parent_node = document.querySelector( `[edit-container="${ edit_id }"]` );
        let span_value = parent_node.querySelector( `[edit-value="${ edit_id }"]` );
        let form = parent_node.querySelector( `[edit-form="${ edit_id }"]` );
        let button_cancel = parent_node.querySelector( `[edit-cancel="${ edit_id }"]` );
        if( ! form || ! span_value || ! button_cancel ) return;
        let delete_button = parent_node.querySelector( `[edit-delete="${ edit_id }"]` );
        button.addEventListener( 'click', () => {
            button.classList.add( 'hidden' );
            span_value.classList.add( 'hidden' );
            form.classList.toggle( 'hidden' )
            form.classList.toggle( 'flex' );
            button_cancel.classList.toggle( 'hidden' );
            delete_button.classList.toggle( 'hidden' );
        } );
        button_cancel.addEventListener( 'click', () => {
            button.classList.remove( 'hidden' );
            span_value.classList.remove( 'hidden' );
            form.classList.toggle( 'hidden' )
            form.classList.toggle( 'flex' );
            button_cancel.classList.toggle( 'hidden' );
            delete_button.classList.toggle( 'hidden' );
        } );
    } );
} )( [...document.querySelectorAll( '[edit-target]' )] );

const create_selector = ( id, selector_target ) => {
    const svg_text = '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>';
    const container = document.createElement( 'div' );
    container.className = "to-show-selector relative border border-gray-300 rounded-md flex flex-col w-full h-fit !hidden";
    container.id = selector_target;
    const input_container = document.createElement( 'div' );
    input_container.className = "flex gap-px w-full h-10";
    const input_new_value = document.createElement( 'input' );
    input_new_value.type = "text";
    input_new_value.name = `arg-${ id }-temp-option`;
    input_new_value.id = `arg-${ id }-temp-option`;
    input_new_value.placeholder = "Ajoutez une option...";
    input_new_value.className = "text-sm p-2.5 text-gray-900 bg-gray-50 focus:border-blue-500 focus:ring-blue-500 block w-full rounded-t-md";
    const input_add_button = document.createElement( 'button' );
    input_add_button.className = "text-white bg-black border border-black transition ease-in-out duration-300 hover:bg-transparent hover:text-black aspect-square w-auto flex items-center justify-center rounded-tr-md";
    input_add_button.dataset.value = `arg-${ id }-temp-option`;
    input_add_button.dataset.target = `arg-${ id }-select-content[]`;
    let parser = new DOMParser();
    input_add_button.appendChild( parser.parseFromString( svg_text, "image/svg+xml" ).documentElement );
    input_container.appendChild( input_new_value );
    input_container.appendChild( input_add_button );
    input_new_value.addEventListener( 'keydown', event => {
        if( event.key === 'Enter' ) {
            event.preventDefault();
            input_add_button.click();
        }
    } );
    container.appendChild( input_container );
    const option_selector = document.createElement( 'select' );
    option_selector.multiple = true;
    option_selector.className = "text-sm p-2.5 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 block w-full rounded-md";
    option_selector.name = `arg-${ id }-select-content[]`;
    option_selector.id = `arg-${ id }-select-content[]`;
    const default_option = document.createElement( 'option' )
    default_option.value = 'none';
    default_option.className = "to-delete-temp-option";
    default_option.innerText = "Ajoutez une option...";
    option_selector.appendChild( default_option )
    container.appendChild( option_selector );
    return container;
};
const create_select = ( type_id, number_target, selector_target ) => {
    const select = document.createElement( 'select' );
    select.className = "type-selector bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-md focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5";
    select.id = type_id;
    select.name = type_id;
    const text_option = document.createElement( 'option' );
    text_option.value = "text";
    text_option.innerText = "Texte court";
    const textarea_option = document.createElement( 'option' );
    textarea_option.value = "textarea";
    textarea_option.innerText = "Texte long";
    const number_option = document.createElement( 'option' );
    number_option.value = "number";
    number_option.innerText = "Nombre";
    number_option.dataset.target = number_target;
    const selector_option = document.createElement( 'option' );
    selector_option.value = "selector";
    selector_option.innerText = "Sélecteur";
    selector_option.dataset.target = selector_target;
    select.appendChild( text_option );
    select.appendChild( textarea_option );
    select.appendChild( number_option );
    select.appendChild( selector_option );
    return select;
};
const create_number_container = ( id, number_id ) => {
    const container = document.createElement( 'div' );
    container.className = "grid grid-cols-3 gap-2 w-full h-fit !hidden";
    container.id = number_id;
    const div_min = document.createElement( 'div' );
    div_min.className = "relative";
    const label_min = document.createElement( 'label' );
    label_min.innerText = "Minimum";
    label_min.for = `arg-${ id }-number-min`;
    label_min.className = "block mb-2 text-sm font-medium text-gray-900";
    const input_min = document.createElement( 'input' );
    input_min.type = "number";
    input_min.name = `arg-${ id }-number-min`;
    input_min.id = `arg-${ id }-number-min`;
    input_min.className = "bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5";
    input_min.value = 0;
    div_min.appendChild( label_min );
    div_min.appendChild( input_min );
    const div_max = document.createElement( 'div' );
    div_max.className = "relative";
    const label_max = document.createElement( 'label' );
    label_max.innerText = "Maximum";
    label_min.for = `arg-${ id }-number-max`;
    label_max.className = "block mb-2 text-sm font-medium text-gray-900";
    const input_max = document.createElement( 'input' );
    input_max.type = "number";
    input_max.name = `arg-${ id }-number-max`;
    input_max.id = `arg-${ id }-number-max`;
    input_max.className = "bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5";
    input_max.value = 3;
    div_max.appendChild( label_max );
    div_max.appendChild( input_max );
    const div_def = document.createElement( 'div' );
    div_def.className = "relative";
    const label_def = document.createElement( 'label' );
    label_def.innerText = "Défaut";
    label_def.for = `arg-${ id }-number-def`;
    label_def.className = "block mb-2 text-sm font-medium text-gray-900";
    const input_def = document.createElement( 'input' );
    input_def.type = "number";
    input_def.name = `arg-${ id }-number-def`;
    input_def.id = `arg-${ id }-number-def`;
    input_def.className = "bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5";
    input_def.value = 3;
    div_def.appendChild( label_def );
    div_def.appendChild( input_def );
    container.appendChild( div_min );
    container.appendChild( div_max );
    container.appendChild( div_def );
    return container;
};
const create_argument = ( id, text_title ) => {
    const type_id = `arg-${ id }-type`;
    const number_id = `arg-${ id }-number-container`;
    const selector_target = `arg-${ id }-select-container`;
    const container = document.createElement( 'div' );
    container.className = "arg-container relative flex flex-col gap-2";
    const title = document.createElement( 'h4' );
    title.className = "font-semibold text-lg";
    title.innerText = text_title.charAt( 0 ).toUpperCase() + text_title.slice( 1 );
    const input_hidden = document.createElement( 'input' );
    input_hidden.value = text_title;
    input_hidden.type = 'hidden';
    input_hidden.name = `arg-${ id }-name`;
    input_hidden.id = `arg-${ id }-name`;
    const label_desc = document.createElement( 'label' );
    label_desc.innerText = "Description courte";
    label_desc.className = "block text-sm font-medium text-gray-900";
    label_desc.for = `arg-${ id }-desc`;
    const input_desc = document.createElement( 'input' );
    input_desc.placeholder = "Description courte";
    input_desc.type = 'text';
    input_desc.name = `arg-${ id }-desc`;
    input_desc.id = `arg-${ id }-desc`;
    input_desc.className = "bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5";
    const label = document.createElement( 'label' );
    label.innerText = "Type de l'argument";
    label.className = "block text-sm font-medium text-gray-900";
    label.for = type_id;
    const select = create_select( type_id, number_id, selector_target );
    const number_container = create_number_container( id, number_id );
    const selector = create_selector( id, selector_target );
    container.appendChild( title );
    container.appendChild( input_hidden );
    container.appendChild( label_desc );
    container.appendChild( input_desc );
    container.appendChild( label );
    container.appendChild( select );
    container.appendChild( number_container );
    container.appendChild( selector );
    return container;
};
( button_parse => {
    const target = document.querySelector( `#${ button_parse.dataset.target }` );
    const to_hide = document.querySelector( `#${ button_parse.dataset.hide }` );
    const content_parsed_container = document.querySelector( '#content-parsed' );
    const button_create_prompt = document.querySelector( '#create-prompt' );
    button_create_prompt.addEventListener( 'click', () => {
        ( selector_arg => {
            selector_arg.forEach( selector => [...selector.querySelectorAll( 'option' )].forEach( item => item.selected = true ) );
        } )( [...document.querySelectorAll( '[id$="-select-content[]"]' )] );
    } );
    if( ! target ) return;
    button_parse.addEventListener( 'click', async ( event ) => {
        event.preventDefault();
        if( target.value === '' ) return;
        let result = await fetch( internal_api, {
            method: 'POST',
            body: JSON.stringify( {
                prompt: target.value,
                nonce: nonce
            } )
        } ).then( res => res.json() ).catch( err => {
            to_hide.classList.add( 'hidden' );
            console.error( err );
        } );
        to_hide.classList.add( 'hidden' );
        if(
            result.hasOwnProperty( 'code' ) &&
            result.code === 0 &&
            result.hasOwnProperty( 'message' )
        ) {
            [...content_parsed_container.querySelectorAll( 'div.arg-container' )].forEach( item => item.remove() );
            result.message.forEach( ( item, index ) => {
                content_parsed_container.appendChild( create_argument( index, item ) );
            } );
            ( type_selects => {
                type_selects.forEach( item => {
                    item.addEventListener( 'change', () => {
                        let childs = [...item.querySelectorAll( 'option' )];
                        let selected = childs[ item.selectedIndex ];
                        if( selected.dataset.hasOwnProperty( 'target' ) ) {
                            let target = document.querySelector( `#${ selected.dataset.target }` );
                            if( ! target ) return;
                            target.classList.remove( '!hidden' );
                        } else {
                            childs.filter( sub_item => sub_item.dataset.hasOwnProperty( 'target' ) )
                                .forEach( sub_item => document.querySelector( `#${ sub_item.dataset.target }` )?.classList?.add( '!hidden' ) );
                        }
                    } );
                } );
            } )( [...document.querySelectorAll( '.type-selector' )] );
            ( input_temp_options_button => {
                const new_option = ( value, text ) => {
                    const option = document.createElement( 'option' );
                    option.value = text;
                    option.name = value;
                    option.innerText = text;
                    return option;
                };
                const slugify = text => text
                    .replace( /^\s+|\s+$/g, '' )
                    .toLowerCase()
                    .replace( /[^a-z0-9 -]/g, '' )
                    .replace( /\s+/g, '-' )
                    .replace( /-+/g, '-' );
                input_temp_options_button.forEach( temp_button => {
                    const select_target = document.querySelector( `[id="${ temp_button.dataset.target }"]` );
                    temp_button.addEventListener( 'click', event => {
                        event.preventDefault();
                        [...select_target.children].filter( item => [...item.classList].includes( 'to-delete-temp-option' ) ).forEach( item => item.remove() );
                        const select_new_value = document.querySelector( `[id="${ temp_button.dataset.value }"]` );
                        if( ! select_new_value ) return;
                        select_target.appendChild( new_option( slugify( select_new_value.value ), select_new_value.value ) );
                        select_new_value.value = '';
                    } );
                } );
            } )( [...document.querySelectorAll( '[data-value*="arg"]' )] );
            button_create_prompt.disabled = false;
        }
    } );
} )( document.querySelector( "#parseTemplate" ) );