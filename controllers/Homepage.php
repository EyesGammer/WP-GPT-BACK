<?php

namespace controllers;

use src\Route;
use src\RightsEngine;
use src\TemplatesEngine;

class Homepage {

    #[TemplatesEngine('index')]
    #[Route('/homepage', 'GET', 'Accueil', redirect_norights_url: '/dashboard/[uuid]')]
    #[RightsEngine(
        need_login: true,
        redirect_need_login: true,
        rights_check: array(
            'is_admin' => true
        )
    )]
    public function homepage() : void {
        add_hook( 'page-style', function() {
            return array(
                get_url( '/assets/style.css' )
            );
        } );
        add_hook( 'dashboard-title', fn() => 'Gestion du service GPT' );
        add_hook( 'dashboard-content', function() {
            load_template( 'general-clients' );

            add_hook( 'stats-content', fn() => Stats::getAllStatsWithClients() );
            add_hook( 'stats-chart-title', fn() => "Tokens utilisés (y) par jours (x)" );
            load_template( 'general-stats' );
        } );
        add_hook( 'page-content', fn() => load_template( 'general-dashboard' ) );
    }

    #[Route('/test','POST')]
    public function test() : void {
        $header = $_SERVER[ 'HTTP_STRIPE_SIGNATURE' ];
        ob_start();
        echo '<pre>\n';
        print_r( $_GET );
        echo '</pre>\n';
        $data = ob_get_clean();
        file_put_contents( './test.txt', $data );
        http_response_code( 200 );
    }

    #[Route('/action/add/client', 'POST')]
    #[RightsEngine(
        need_login: true,
        redirect_need_login: true,
        rights_check: array(
            'is_admin' => true
        )
    )]
    public function action_addClient() : void {
        $user = get_connected_user();
        if(
            ! $user ||
            ! validate_nonce( 'new-client', $user[ 'uuid' ], $_SESSION[ '_auth_token' ], $_POST[ 'nonce' ] )
        ) set_error_message( "Erreur", "Impossible de faire cette action.", get_url( '/add/client' ), true );
        if(
            empty( $_POST[ 'client-name' ] ) ||
            empty( $_POST[ 'client-email' ] ) ||
            empty( $_POST[ 'client-credit' ] )
        ) set_error_message( "Erreur", "Veuillez remplir tous les champs pour continuer.", get_url( '/add/client' ), true );
        global $database;
        $database->prepared_query(
            "INSERT INTO gpt_clients (uuid,nom,email,credit) VALUES (?,?,?,?)",
            'sssd',
            create_uuid(),
            $_POST[ 'client-name' ],
            $_POST[ 'client-email' ],
            floatval( $_POST[ 'client-credit' ] )
        );
        header( 'Location: ' . ( $_POST[ 'referrer' ] ?? get_url( '/homepage' ) ) );
    }

    #[TemplatesEngine('index')]
    #[Route('/add/client', 'GET', 'Accueil')]
    #[RightsEngine(
        need_login: true,
        redirect_need_login: true,
        rights_check: array(
            'is_admin' => true
        )
    )]
    public function addClients() : void {
        add_hook( 'page-style', function() {
            return array(
                get_url( '/assets/style.css' )
            );
        } );
        add_hook( 'dashboard-title', fn() => 'Ajouter un client' );
        add_hook( 'dashboard-content', function() {
            load_template( 'general-add-client' );
        } );
        add_hook( 'page-content', fn() => load_template( 'general-dashboard' ) );
    }

}

// Silence is golden