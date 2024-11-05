<?php

namespace controllers;

use src\Route;
use src\Database;
use src\RightsEngine;
use src\TemplatesEngine;

class Client {

    private static string $single_security_key = "ZN-O'9tr+cVwfi+%Iw7nZ]SK5Z2;,K@]8=9;]rD#FMMKY6CK!%";
    private static string $single_security_salt = "#o]t%BepFxtXW%J{Bi^c;r%lLRFm4Ko&YRq,xq$9gOUWR_am!H";

    private static array $months_fr_abrev = array(
        'janvier' => 'janv',
        'fevrier' => 'fev',
        'mars' => 'mars',
        'avril' => 'avr',
        'mai' => 'mai',
        'juin' => 'juin',
        'juillet' => 'juil',
        'aout' => 'aout',
        'septembre' => 'sept',
        'octobre' => 'oct',
        'novembre' => 'nov',
        'decembre' => 'dec'
    );

    /**
     * Get french months to use in charts
     *
     * @param string $searched
     * @return string
     */
    public static function getMonthFRAbrevByName( string $searched ) : string {
        return self::$months_fr_abrev[ iconv( 'UTF-8', 'ASCII//TRANSLIT', $searched ) ];
    }

    /**
     * Get french months to use in charts by month number
     *
     * @param int $searched
     * @return string
     */
    public static function getMonthFRAbrevByPos( int $searched ) : string {
        return self::$months_fr_abrev[ array_keys( self::$months_fr_abrev )[ $searched ] ];
    }

    #[TemplatesEngine('index')]
    #[Route([
        '/client/([0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12})$',
        '/client/([0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12})/(single|settings|access)$'
    ], 'GET', 'Client')]
    #[RightsEngine(
        need_login: true,
        redirect_need_login: true,
        rights_check: array(
            'is_admin' => true
        )
    )]
    public function pageClient( string $uid_client ) : void {
        global $database;
        $client = $database->prepared_query(
            "SELECT * FROM gpt_clients WHERE uuid = ?",
            's',
            $uid_client
        );
        if( ! empty( $client ) ) $client = end( $client );
        add_hook( 'current-client', fn() => $client );
        add_hook( 'page-style', function() {
            return array(
                get_url( '/assets/style.css' )
            );
        } );
        add_hook( 'dashboard-title', fn() => 'Client : ' . $client[ 'nom' ] );
        add_hook( 'dashboard-content', function() {
            load_template( 'client-index' );
        } );
        add_hook( 'page-content', fn() => load_template( 'general-dashboard' ) );
    }

    #[Route('/client/([0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12})/edit$', 'POST')]
    #[RightsEngine(
        need_login: true,
        redirect_need_login: true,
        rights_check: array(
            'is_admin' => true
        )
    )]
    public function editClient( string $uid_client ) : void {
        $user = get_connected_user();
        if(
            ! $user ||
            ! validate_nonce( 'edit-client', $user[ 'uuid' ], $_SESSION[ '_auth_token' ], $_POST[ 'nonce' ] )
        ) set_error_message( "Erreur", "Impossible de faire cette action.", get_url( '/client/' . $uid_client . '/settings' ), true );
        global $database;
        $client = $database->prepared_query(
            'SELECT * FROM gpt_clients WHERE uuid = ?',
            's',
            $uid_client
        );
        if( empty( $client ) ) set_error_message( "Erreur", "Le client n'existe pas.", get_url( '/client/' . $uid_client . '/settings' ), true );
        else $client = end( $client );
        foreach( $_POST as $key => $value ) {
            if( preg_match( '/client-(\w+)/i', $key, $matches ) ) {
                if( in_array( $matches[ 1 ], array( 'nom', 'email' ) ) ) {
                    $database->prepared_query(
                        'UPDATE gpt_clients SET ' . $matches[ 1 ] . ' = ? WHERE uuid = ?',
                        'ss',
                        $value,
                        $uid_client
                    );
                } else if( $matches[ 1 ] === 'credit' ) {
                    $database->prepared_query(
                        'UPDATE gpt_clients SET ' . $matches[ 1 ] . ' = ? WHERE uuid = ?',
                        'ds',
                        floatval( $value ),
                        $uid_client
                    );
                } else if( $matches[ 1 ] === 'model' ) {
                    $model = 'gpt-3.5-turbo';
                    if( $value === 'gpt-4' ) $model = 'gpt-4';
                    $database->prepared_query(
                        'UPDATE gpt_clients SET ' . $matches[ 1 ] . ' = ? WHERE uuid = ?',
                        'ss',
                        $model,
                        $uid_client
                    );
                } else {
                    switch( $matches[ 1 ] ) {
                        case 'in':
                            Options::updateOption( 'price_in_' . $client[ 'id_client' ], $value );
                            break;
                        case 'out':
                            Options::updateOption( 'price_out_' . $client[ 'id_client' ], $value );
                            break;
                    }
                }
            }
        }
        header( 'Location: ' . get_url( '/client/' . $uid_client . '/settings' ) );
    }

    #[Route('/client/([0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12})/new/key', 'POST')]
    #[RightsEngine(
        need_login: true,
        redirect_need_login: true
    )]
    public function getApiKey( string $uid_client ) : void {
        $user = get_connected_user();
        if(
            ! $user ||
            ! validate_nonce( 'client-new-api', $user[ 'uuid' ], $_SESSION[ '_auth_token' ], $_POST[ 'nonce' ] )
        ) set_error_message( "Erreur", "Impossible de faire cette action.", get_url( '/add/client' ), true );
        $content = array(
            'user' => $user[ 'uuid' ],
            'client' => $uid_client,
            'generated' => time()
        );
        global $security, $database;
        $encrypted = $security->encrypt( serialize( $content ) );
        $_SESSION[ 'new_token' ] = $encrypted;
        $database->prepared_query(
            'UPDATE gpt_clients SET token = ? WHERE uuid = ?',
            'ss',
            $security
                ->set_key( $this::$single_security_key )
                ->set_salt( $this::$single_security_salt )
                ->encrypt( $encrypted ),
            $uid_client
        );
        header( 'Location: ' . ( $_POST[ 'referrer' ] ?? get_url( '/homepage' ) ) );
    }

    /**
     * Get client with decrypted token by uuid
     *
     * @param string $uid_client
     * @return array
     */
    public static function getClientByUUID( string $uid_client ) : array {
        global $security, $database;
        $client = $database->prepared_query(
            'SELECT * FROM gpt_clients WHERE uuid = ?',
            's',
            $uid_client
        );
        if( empty( $client ) ) return array();
        $client = end( $client );
        $client[ 'token' ] = $security
            ->set_key( Client::$single_security_key )
            ->set_salt( Client::$single_security_salt )
            ->decrypt( $client[ 'token' ] );
        return $client;
    }

    /**
     * Update client's credit with token in and out, with price in/out
     *
     * @param string $uid_client
     * @param int $tokens_in
     * @param int $tokens_out
     * @param Database|null $database_object
     * @return void
     */
    public static function changeCredit( string $uid_client, int $tokens_in, int $tokens_out, ?Database $database_object=null ) : void {
        if( $database_object === null ) global $database;
        else $database = $database_object;
        list( $price_in, $price_out ) = Options::getGeneralPrices( $uid_client, $database_object );
        $client = Client::getClientByUUID( $uid_client );
        if( empty( $client ) ) return;
        $price = $price_in * $tokens_in + $price_out * $tokens_out;
        $database->prepared_query(
            'UPDATE gpt_clients SET credit = ? WHERE uuid = ?',
            'ds',
            floatval( $client[ 'credit' ] ) - floatval( $price ),
            $uid_client
        );
    }

}

// Silence is golden