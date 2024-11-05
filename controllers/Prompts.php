<?php

namespace controllers;

use Random\RandomException;
use src\Route;
use src\Database;
use src\RightsEngine;
use src\TemplatesEngine;

class Prompts {

    private static string $single_security_key = ""; // Random security key
    private static string $single_security_salt = ""; // Random security salt

    #[TemplatesEngine('index')]
    #[Route('/prompts', 'GET', 'Prompts')]
    #[RightsEngine(
        need_login: true,
        redirect_need_login: true,
        rights_check: array(
            'is_admin' => true
        )
    )]
    public function pagePrompts() : void {
        add_hook( 'page-style', function() {
            return array(
                get_url( '/assets/style.css' )
            );
        } );
        add_hook( 'dashboard-title', fn() => 'Prompts disponibles' );
        add_hook( 'dashboard-content', function() {
            load_template( 'account-prompts' );
        } );
        add_hook( 'page-content', fn() => load_template( 'general-dashboard' ) );
    }

    #[Route('/prompts/([0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12})/edit', 'POST')]
    #[RightsEngine(
        need_login: true,
        redirect_need_login: true,
        rights_check: array(
            'is_admin' => true
        )
    )]
    public function actionEditPrompt( string $uuid_prompt ) : void {
        $user = get_connected_user();
        if(
            ! $user ||
            ! validate_nonce( 'edit-prompt', $user[ 'uuid' ], $_SESSION[ '_auth_token' ], $_POST[ 'nonce' ] )
        ) set_error_message( "Erreur", "Impossible de faire cette action.", get_url( '/prompts' ), true );
        global $database;
        foreach( $_POST as $key => $value ) {
            if( preg_match( '/^prompt-(\w+)$/i', $key, $matches ) ) {
                if( $matches[ 1 ] == 'nom' ) {
                    $database->prepared_query(
                        "UPDATE gpt_prompts SET " . $matches[ 1 ] . " = ?, slug = ? WHERE uuid = ?",
                        'sss',
                        $value,
                        get_slug( $value ),
                        $uuid_prompt
                    );
                } else {
                    $database->prepared_query(
                        "UPDATE gpt_prompts SET " . $matches[ 1 ] . " = ? WHERE uuid = ?",
                        'ss',
                        $value,
                        $uuid_prompt
                    );
                }
            }
        }
        header( 'Location: ' . get_url( '/prompts' ) );
    }

    /**
     * @throws RandomException
     */
    #[Route('/action/add/prompt', 'POST')]
    #[RightsEngine(
        need_login: true,
        redirect_need_login: true,
        rights_check: array(
            'is_admin' => true
        )
    )]
    public function actionAddPrompt() : void {
        $user = get_connected_user();
        if(
            ! $user ||
            ! validate_nonce( 'new-prompt', $user[ 'uuid' ], $_SESSION[ '_auth_token' ], $_POST[ 'nonce' ] )
        ) set_error_message( "Erreur", "Impossible de faire cette action.", $_POST[ 'referrer' ], true );
        if(
            empty( $_POST[ 'prompt-name' ] ) ||
            empty( $_POST[ 'prompt-body' ] )
        ) set_error_message( "Erreur", "Veuillez remplir les champs requis pour continuer.", $_POST[ 'referrer' ], true );
        global $database;
        $database->prepared_query(
            "INSERT INTO gpt_prompts (uuid,nom,slug,description,prompt) VALUES (?,?,?,?,?)",
            'sssss',
            create_uuid(),
            $_POST[ 'prompt-name' ],
            get_slug( $_POST[ 'prompt-name' ] ),
            $_POST[ 'prompt-description' ],
            $_POST[ 'prompt-body' ]
        );
        $args = array();
        foreach( $_POST as $key => $value ) {
            if( preg_match( '/arg-(\d+)-type/i', $key, $match ) ) {
                $current_index = intval( $match[ 1 ] );
                if( $_POST[ "arg-$current_index-type" ] === 'number' ) {
                    $args[ $_POST[ "arg-$current_index-name" ] ] = array(
                        'type' => $_POST[ "arg-$current_index-type" ],
                        'name' => 'kumo-gpt-' . create_uuid(),
                        'default' => intval( $_POST[ "arg-$current_index-number-def" ] ),
                        'max' => intval( $_POST[ "arg-$current_index-number-max" ] ),
                        'min' => intval( $_POST[ "arg-$current_index-number-min" ] ),
                        'display' => $_POST[ "arg-$current_index-desc" ]
                    );
                } else if( $_POST[ "arg-$current_index-type" ] === 'selector' ) {
                    $args[ $_POST[ "arg-$current_index-name" ] ] = array(
                        'type' => $_POST[ "arg-$current_index-type" ],
                        'name' => 'kumo-gpt-' . create_uuid(),
                        'display' => $_POST[ "arg-$current_index-desc" ],
                        'values' => $_POST[ "arg-$current_index-select-content" ]
                    );
                } else {
                    $args[ $_POST[ "arg-$current_index-name" ] ] = array(
                        'type' => $_POST[ "arg-$current_index-type" ],
                        'name' => 'kumo-gpt-' . create_uuid(),
                        'display' => $_POST[ "arg-$current_index-desc" ]
                    );
                }
            }
        }
        if( ! empty( $args ) ) {
            $prompt_id = $database->get_last_id();
            Options::addOption("prompt_config_$prompt_id", $args);
        }
        header( 'Location: ' . ( $_POST[ 'referrer' ] ?? get_url( '/homepage' ) ) );
    }

    #[Route('/action/delete/prompt/([0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12})', 'GET')]
    #[RightsEngine(
        need_login: true,
        redirect_need_login: true,
        rights_check: array(
            'is_admin' => true
        )
    )]
    public function actionDeletePrompt( string $uid_prompt ) : void {
        $user = get_connected_user();
        if(
            ! $user ||
            ! validate_nonce( 'delete-prompt', $user[ 'uuid' ], $_SESSION[ '_auth_token' ], $_GET[ 'n' ] )
        ) set_error_message( "Erreur", "Impossible de faire cette action.", get_url( '/prompts' ), true );
        global $database;
        $database->prepared_query(
            "DELETE FROM gpt_prompts WHERE uuid = ?",
            's',
            $uid_prompt
        );
        header( 'Location: ' . get_url( '/prompts' ) );
    }

    /**
     * Add new stat row to the DB
     *
     * @param string $uuid_prompt
     * @param array $client
     * @param int $tokens_in
     * @param int $tokens_out
     * @param float $time
     * @param array $response
     * @param Database|null $database_object
     * @return string
     * @throws RandomException
     */
    public static function addStat( string $uuid_prompt, array $client, int $tokens_in, int $tokens_out, float $time, array $response, ?Database $database_object=null ) : string {
        if( $database_object === null ) global $database;
        else $database = $database_object;
        list( $price_in, $price_out ) = Options::getGeneralPrices( $client[ 'uuid' ], $database_object );
        $price = $price_in * $tokens_in + $price_out * $tokens_out;
        global $security;
        $encrypted = $security
            ->set_key( self::$single_security_key )
            ->set_salt( self::$single_security_salt )
            ->encrypt( serialize( $response ) );
        $stat_uid = create_uuid();
        $database->prepared_query(
            'INSERT INTO gpt_stats (uuid,uuid_prompt,uuid_client,tokens_in,tokens_out,time,response,price) VALUES (?,?,?,?,?,?,?,?)',
            'sssiidsd',
            $stat_uid,
            $uuid_prompt,
            $client[ 'uuid' ],
            $tokens_in,
            $tokens_out,
            $time,
            $encrypted,
            $price
        );
        return $stat_uid;
    }

}