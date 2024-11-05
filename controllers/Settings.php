<?php

namespace controllers;

use src\Route;
use src\RightsEngine;
use src\TemplatesEngine;

class Settings {

    #[TemplatesEngine('index')]
    #[Route('/settings', 'GET', 'Paramètres')]
    #[RightsEngine(
        need_login: true,
        redirect_need_login: true,
        rights_check: array(
            'is_admin' => true
        )
    )]
    public function pageSettings() : void {
        add_hook( 'page-style', function() {
            return array(
                get_url( '/assets/style.css' )
            );
        } );
        add_hook( 'dashboard-title', fn() => 'Paramètres du compte général' );
        add_hook( 'dashboard-content', function() {
            load_template( 'account-settings' );
        } );
        add_hook( 'page-content', fn() => load_template( 'general-dashboard' ) );
    }

    #[Route('/settings/edit', 'POST')]
    #[RightsEngine(
        need_login: true,
        redirect_need_login: true,
        rights_check: array(
            'is_admin' => true
        )
    )]
    public function actionEditSettings() : void {
        $user = get_connected_user();
        if(
            ! $user ||
            ! validate_nonce( 'edit-account', $user[ 'uuid' ], $_SESSION[ '_auth_token' ], $_POST[ 'nonce' ] )
        ) set_error_message( "Erreur", "Impossible de faire cette action.", get_url( '/settings' ), true );
        foreach( $_POST as $key => $value ) {
            switch( $key ) {
                case 'account-api':
                    update_secured_option( 'general_api', $value );
                    break;
                case 'account-in':
                    Options::updateOption( 'general_in', $value );
                    break;
                case 'account-out':
                    Options::updateOption( 'general_out', $value );
                    break;
            }
        }
        header( 'Location: ' . get_url( '/settings' ) );
    }

}