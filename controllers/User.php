<?php

namespace controllers;

use src\Route;
use src\RightsEngine;
use src\TemplatesEngine;

class User {

    #[TemplatesEngine('index')]
    #[Route('/login', 'GET', 'Se connecter', redirect_norights_url: '/dashboard/[uuid]')]
    #[RightsEngine(
        only_not_logged: true,
        only_not_logged_redirect_url: '/homepage',
        redirect_only_not_logged: true
    )]
    public function login() : void {
        add_hook( 'page-style', function() {
            return array(
                get_url( '/assets/style.css' )
            );
        } );
        add_hook( 'page-content', fn() => load_template( 'user-login' ) );
    }

    #[Route('/login/connect', 'POST')]
    public function _connect() : void {
        global $database;
        $content = $database->prepared_query(
            "SELECT * FROM gpt_users WHERE pseudo = ?",
            's',
            $_POST[ 'user-name' ]
        );
        if( $content === null ) set_error_message( "Erreur", "L'utilisateur n'existe pas.", $_POST[ 'referrer' ], true );
        else $content = end( $content );
        if( password_verify( $_POST[ 'user-pass' ], $content[ 'password' ] ) ) {
            login( $content, noad_redirect_url: get_url( '/dashboard/' . $content[ 'uuid' ] ) );
        } else set_error_message( "Erreur", "Mot de passe incorrect.", $_POST[ 'referrer' ], true );
    }

    #[Route('/logout', 'GET')]
    public function _logout() : void {
        logout();
    }

    /*public static function autre() : void {
        echo '<br>je suis ici<br>';
    }

    #[Route('/test', 'GET', 'autre')]
    public function action() : void {
        echo 'Coucou.<br>';
        add_hook( 'domain-test', 'controllers\Autre@autre' );
        do_hook( 'domain-test' );
    }*/

}

// Silence is golden