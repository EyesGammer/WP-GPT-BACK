<?php

namespace controllers;

use src\Route;
use src\RightsEngine;
use src\StripeAPI;
use src\TemplatesEngine;

class Dashboard {

    #[TemplatesEngine('index')]
    #[Route('/dashboard/(?P<testa>[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12})', 'GET')]
    #[RightsEngine(
        need_login: true,
        redirect_need_login: true,
        rights_check: array(
            'can_access' => true
        )
    )]
    public function clientDashboard( string $uuid_user ) : void {
        $user_access = $client = Access::getClientAccessByUserUUID( $uuid_user );
        $client = Client::getClientByUUID( $user_access[ 'access' ][ 'uuid_client' ] );
        add_hook( 'current-client', function() use ( $client ) {
            return $client;
        } );
        add_hook( 'page-style', function() {
            return array(
                get_url( '/assets/style.css' )
            );
        } );
        add_hook( 'dashboard-title', fn() => 'Dashboard' );
        add_hook( 'dashboard-content', function() {
            load_template( 'dashboard-home' );
        } );
        add_hook( 'dashboard-part', fn() => 'parts-navbar-dashboard' );
        add_hook( 'client-dashboard-uuid', fn() => $client[ 'uuid' ] );
        add_hook( 'client-dashboard-pseudo', fn() => $user_access[ 'user' ][ 'pseudo' ] );
        add_hook( 'page-content', fn() => load_template( 'general-dashboard' ) );
    }

    #[TemplatesEngine('index')]
    #[Route('/dashboard/billing/([0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12})', 'GET')]
    #[RightsEngine(
        need_login: true,
        redirect_need_login: true,
        rights_check: array(
            'can_access' => true
        )
    )]
    public function clientBilling( string $uuid_user ) : void {
        $user_access = $client = Access::getClientAccessByUserUUID( $uuid_user );
        $client = Client::getClientByUUID( $user_access[ 'access' ][ 'uuid_client' ] );
        add_hook( 'current-client', function() use ( $client ) {
            return $client;
        } );
        add_hook( 'page-style', function() {
            return array(
                get_url( '/assets/style.css' )
            );
        } );
        add_hook( 'dashboard-title', fn() => 'Facturation' );
        add_hook( 'dashboard-content', function() {
            load_template( 'dashboard-billing' );
        } );
        add_hook( 'dashboard-part', fn() => 'parts-navbar-dashboard' );
        add_hook( 'client-dashboard-uuid', fn() => $client[ 'uuid' ] );
        add_hook( 'client-dashboard-pseudo', fn() => $user_access[ 'user' ][ 'pseudo' ] );
        add_hook( 'page-content', fn() => load_template( 'general-dashboard' ) );
    }

    #[Route('/payment-ok/cs_(test|live)_([a-zA-Z0-9_]+)$', 'GET', 'Paiement')]
    public function test( string $scope, string $token ) {
        echo 'Merci pour votre achat';
        global $database, $security;
        $stripe = new StripeAPI( GPT_STRIPE );
        $session = $stripe->retrieveSession( 'cs_' . $scope . "_$token" );
        echo '<pre>';
        print_r( $session );
        echo '</pre>';
        if(
            empty( $session ) ||
            empty( $session[ 'amount' ] ) ||
            empty( $session[ 'intent' ] ) ||
            empty( $session[ 'email' ] )
        ) {
            echo 'Une erreur est survenue';
        } else {
            $uuid_stripe = create_uuid();
            $database->prepared_query(
                "INSERT INTO gpt_stripe(uuid,amount,intent,date) VALUES(?,?,?,NOW())",
                'sds',
                $uuid_stripe,
                $session[ 'amount' ],
                $security->encrypt( $session[ 'intent' ] )
            );
            $fetched_client = $database->prepared_query(
                "SELECT c.* FROM gpt_users u INNER JOIN gpt_access a ON a.uuid_user = u.uuid INNER JOIN gpt_clients c ON c.uuid = a.uuid_client WHERE u.email = ? LIMIT 1",
                's',
                $session[ 'email' ]
            );
            if( empty( $fetched_client ) ) {
                echo "Le client n'a pas pu être trouvé... Merci de contacter le support en lui communiquant ce code : $uuid_stripe";
            } else {
                $fetched_client = end( $fetched_client );
                $database->prepared_query(
                    "UPDATE gpt_clients SET credit = ? WHERE uuid = ?",
                    'ds',
                    floatval( $fetched_client[ 'credit' ] ) + $session[ 'amount' ],
                    $fetched_client[ 'uuid' ]
                );
                echo $token;
            }
        }
    }

}

// Silence is golden