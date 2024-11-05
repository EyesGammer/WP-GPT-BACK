<?php

namespace controllers;

use src\Route;
use src\TemplatesEngine;

class Account {

    #[TemplatesEngine('homepage')]
    #[Route('/', 'GET', 'Accueil')]
    public function pageHome() : void {
        add_hook( 'page-style', function() {
            return array(
                get_url( '/assets/style.css' )
            );
        } );
        add_hook( 'home-content', function() {
            load_template( 'index-title-component' );
            load_template( 'index-cards-component' );
        } );
        add_hook( 'page-content', fn() => load_template( 'account-home' ) );
    }

}