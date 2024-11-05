<?php

namespace controllers;

use src\Database;
use src\RightsEngine;
use src\Route;

class Access {

    #[Route('/client/([0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12})/access/add', 'POST')]
    #[RightsEngine(
        need_login: true,
        redirect_need_login: true,
        rights_check: array(
            'is_admin' => true
        )
    )]
    public function actionAddAccess( string $uid_client ) : void {
        $user = get_connected_user();
        if(
            ! $user ||
            ! validate_nonce( 'client-access-add', $user[ 'uuid' ], $_SESSION[ '_auth_token' ], $_POST[ 'nonce' ] )
        ) set_error_message( "Erreur", "Impossible de faire cette action.", $_POST[ 'referrer' ] ?? get_url( '/client/' . $uid_client ), true );
        if( empty( $_POST[ 'client-add-access-username' ] ) || empty( 'client-add-access-password' ) ) set_error_message( "Erreur", "Veuillez remplir tous les champs pour continuer", $_POST[ 'referrer' ] ?? get_url( '/client/' . $uid_client ), true );
        global $database;
        $user_uuid = create_uuid();
        $access_uuid = create_uuid();
        $created_user = $database->prepared_query(
            'INSERT INTO gpt_users(uuid,pseudo,password,rights) VALUES (?,?,?,?)',
            'ssss',
            $user_uuid,
            $_POST[ 'client-add-access-username' ],
            password_hash( $_POST[ 'client-add-access-password' ], PASSWORD_DEFAULT ),
            serialize( array(
                'can_access' => true
            ) )
        );
        $created_access = $database->prepared_query(
            'INSERT INTO gpt_access(uuid,uuid_client,uuid_user,date) VALUES (?,?,?,NOW())',
            'sss',
            $access_uuid,
            $uid_client,
            $user_uuid
        );
        header( 'Location: ' . ( $_POST[ 'referrer' ] ?? get_url( '/client/' . $uid_client ) ) );
    }

    /**
     * Get client access by client uuid
     *
     * @param string $client_uuid
     * @param Database|null $database_object
     * @return array
     */
    public static function getClientAccessByUUID( string $client_uuid, ?Database $database_object=null ) : array {
        if( $database_object === null ) global $database;
        else $database = $database_object;
        $access = $database->prepared_query(
            'SELECT * FROM gpt_access WHERE uuid_client = ?',
            's',
            $client_uuid
        );
        if( empty( $access ) ) return array();
        $final_access = array();
        foreach( $access as $loop_access ) {
            $temp_user = $database->prepared_query(
                'SELECT * FROM gpt_users WHERE uuid = ? LIMIT 1',
                's',
                $loop_access[ 'uuid_user' ]
            );
            if( empty( $temp_user ) ) continue;
            $final_access[] = array(
                'access' => $loop_access,
                'user' => end( $temp_user )
            );
        }
        return $final_access;
    }

    /**
     * Get client access by user uuid
     *
     * @param string $client_uuid
     * @param Database|null $database_object
     * @return array
     */
    public static function getClientAccessByUserUUID( string $user_uuid, ?Database $database_object=null ) : array {
        if( $database_object === null ) global $database;
        else $database = $database_object;
        $access = $database->prepared_query(
            'SELECT * FROM gpt_access WHERE uuid_user = ? LIMIT 1',
            's',
            $user_uuid
        );
        if( empty( $access ) ) return array();
        $access = end( $access );
        $temp_user = $database->prepared_query(
            'SELECT * FROM gpt_users WHERE uuid = ? LIMIT 1',
            's',
            $access[ 'uuid_user' ]
        );
        if( empty( $temp_user ) ) return array();
        return array(
            'access' => $access,
            'user' => end( $temp_user )
        );
    }

}

// Silence is golden