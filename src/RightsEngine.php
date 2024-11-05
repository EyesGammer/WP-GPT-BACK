<?php

namespace src;

#[\Attribute( \Attribute::IS_REPEATABLE | \Attribute::TARGET_METHOD )]
class RightsEngine {

    private array $strict_check = array();
    private array $rights_check = array();
    private bool $need_login = false;
    private string $need_login_redirect_url = '';
    private bool $redirect_need_login = false;
    private bool $only_not_logged = false;
    private string $only_not_logged_redirect_url = '';
    private bool $redirect_only_not_logged = false;

    public function __construct( ?bool $need_login=null, ?string $need_login_redirect_url=null, ?bool $redirect_need_login=null, ?bool $only_not_logged=null, ?string $only_not_logged_redirect_url=null, ?bool $redirect_only_not_logged=null, ?array $strict_check=null, ?array $rights_check=null ) {
        if( $need_login ) $this->need_login = $need_login;
        if( $redirect_need_login ) $this->redirect_need_login = $redirect_need_login;
        if( $only_not_logged ) $this->only_not_logged = $only_not_logged;
        if( $redirect_only_not_logged ) $this->redirect_only_not_logged = $redirect_only_not_logged;
        if( $strict_check ) $this->strict_check = $strict_check;
        if( $rights_check ) $this->rights_check = $rights_check;
        if( $need_login_redirect_url ) $this->need_login_redirect_url = get_url( $need_login_redirect_url );
        else $this->need_login_redirect_url = get_url( '/login' );
        if( $only_not_logged_redirect_url ) $this->only_not_logged_redirect_url = get_url( $only_not_logged_redirect_url );
        else $this->only_not_logged_redirect_url = get_url( '/logout' );
    }

    /**
     * Check rights based on parameters
     *
     * @param array|null $db_content
     * @return bool
     */
    public function check_rights( ?array $db_content=null ) : bool {
        $user = get_connected_user();
        if( $this->only_not_logged && $user !== null ) {
            if( $this->redirect_only_not_logged ) {
                header( 'Location: ' . $this->only_not_logged_redirect_url );
                return true;
            } else return false;
        }
        if( $this->need_login ) {
            if( ! $user ) {
                if( $this->redirect_need_login ) {
                    header( 'Location: ' . $this->need_login_redirect_url );
                    return true;
                } else return false;
            }
        }
        if( ! empty( $this->strict_check ) ) {
            foreach( $this->strict_check as $loop_key => $loop_check ) {
                if( in_array( $loop_key, array_keys( $db_content ) ) ) {
                    if( $loop_check != $db_content[ $loop_key ] ) {
                        return false;
                    }
                }
            }
        }
        if( ! empty( $this->rights_check ) ) {
            if( empty( $db_content[ 'rights' ] ) ) return false;
            $unserialized_rights = unserialize( $db_content[ 'rights' ] );
            if( ! empty( array_diff_key( $this->rights_check, $unserialized_rights ) ) ) return false;
            foreach( $this->rights_check as $loop_key => $loop_check ) {
                if( in_array( $loop_key, array_keys( $db_content ) ) ) {
                    if( $loop_check != $db_content[ $loop_key ] ) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    /**
     * Page when rights are not respected
     */
    public function noRights() : void {
        echo "You don't have required rights to access this page.";
    }
}