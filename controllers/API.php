<?php

namespace controllers;

use Random\RandomException;
use src\Route;
use src\RightsEngine;
use src\Security;

class API {

    private string $api_url = 'https://api.openai.com/v1/chat/completions';

    private array $base_request = array(
        'model' => null,
        'messages' => array(
            array(
                'role' => 'user',
                'content' => null
            )
        ),
        'temperature' => 0.5,
        'max_tokens' => 4096,
        'top_p' => 1,
        'frequency_penalty' => 0,
        'presence_penalty' => 0
    );

    /**
     * Get client from token
     *
     * @param string $token
     * @return array
     */
    private function getClient( string $token ) : array {
        global $security;
        $decrypted_token = $security->decrypt( $token );
        if( ( $temp = @unserialize( $decrypted_token ) ) === false ) {
            header( 'HTTP/1.1 401 Unauthorized', true, 401 );
            header( 'Content-Type: application/json' );
            echo json_encode( array(
                'message' => 'Unauthorized. Please provide correct token.',
                'code' => 1
            ) );
            exit;
        }
        $decrypted = $temp;
        $client = Client::getClientByUUID( $decrypted[ 'client' ] );
        if(
            ! isset( $client[ 'token' ] ) ||
            $client[ 'token' ] !== $token
        ) {
            header( 'HTTP/1.1 401 Unauthorized', true, 401 );
            header( 'Content-Type: application/json' );
            echo json_encode( array(
                'message' => 'Unauthorized. Please provide valid token.',
                'code' => 1
            ) );
            exit;
        }
        return array( $client, $decrypted );
    }

    /**
     * Remplace template content with passed arguments
     *
     * @param string $template
     * @param array $args
     * @return string
     */
    public static function fillTemplate( string $template, array $args ) : string {
        foreach( $args as $loop_args => $loop_content ) {
            $template = str_replace( "[$loop_args]", $loop_content, $template );
        }
        return $template;
    }

    /**
     * Parse template to get arguments list
     *
     * @param string $template
     * @param mixed $args
     * @return void
     */
    public static function parseTemplate( string $template, mixed &$args ) : void {
        if( preg_match_all( '|\[.*?]|i', $template, $matches ) ) {
            $args = array_map( fn( $x ) => trim( $x, '[]' ), $matches[ 0 ] );
        } else $args = array();
    }

    #[Route('/internal/api/parse/template', 'POST')]
    #[RightsEngine(
        need_login: true,
        redirect_need_login: true,
        rights_check: array(
            'is_admin' => true
        )
    )]
    public function parseTemplateAPI() : void {
        $user = get_connected_user();
        if(
            ! $user ||
            ! validate_nonce( 'parse-template', $user[ 'uuid' ], $_SESSION[ '_auth_token' ], $_POST[ 'nonce' ] )
        ) {
            header( 'HTTP/1.1 401 Unauthorized', true, 401 );
            header( 'Content-Type: application/json' );
            echo json_encode( array(
                'message' => 'Unauthorized. Please provide valid token.',
                'code' => 1
            ) );
            return;
        }
        $args = array();
        self::parseTemplate( $_POST[ 'prompt' ], $args );
        echo json_encode( array(
            'code' => 0,
            'message' => $args
        ) );
    }

    /**
     * @throws RandomException
     */
    #[Route('/api/prompt', 'POST')]
    #[Security(access_all: true)]
    public function startAction( string $token ) : void {
        list( $client, $decrypted_content ) = $this->getClient( $token );
        if( empty( $_POST[ 'message' ] ) ) {
            header( 'HTTP/1.1 400 Bad Request', true, 400 );
            header( 'Content-Type: application/json' );
            echo json_encode( array(
                'message' => 'Please provide message to continue.',
                'code' => 1
            ) );
            exit;
        }
        if( empty( $_POST[ 'slug' ] ) ) {
            header( 'HTTP/1.1 400 Bad Request', true, 400 );
            header( 'Content-Type: application/json' );
            echo json_encode( array(
                'message' => 'Please provide prompt slug to continue.',
                'code' => 1
            ) );
            exit;
        }
        global $database;
        $prompt = $database->prepared_query(
            'SELECT * FROM gpt_prompts WHERE slug = ?',
            's',
            $_POST[ 'slug' ]
        );
        if( empty( $prompt ) ) {
            header( 'HTTP/1.1 400 Bad Request', true, 400 );
            header( 'Content-Type: application/json' );
            echo json_encode( array(
                'message' => 'Prompt not found.',
                'code' => 1
            ) );
            exit;
        } else $prompt = end( $prompt );
        $prompt_config = Options::getOption( 'prompt_config_' . $prompt[ 'id_prompt' ] );
        if( empty( $prompt_config ) ) {
            header( 'HTTP/1.1 400 Bad Request', true, 400 );
            header( 'Content-Type: application/json' );
            echo json_encode( array(
                'message' => 'Prompt config not found.',
                'code' => 1
            ) );
            exit;
        } else $prompt_config = unserialize( end( $prompt_config ) );
        $args = array();
        self::parseTemplate( $prompt[ 'prompt' ], $args );
        $args = array_flip( $args );
        foreach( $prompt_config as $loop_arg => $loop_content ) {
            if( empty( $_POST[ 'message' ][ $loop_content[ 'name' ] ] ) ) {
                header( 'HTTP/1.1 400 Bad Request', true, 400 );
                header( 'Content-Type: application/json' );
                echo json_encode( array(
                    'message' => 'No argument specified for "' . $loop_content[ 'name' ] . '".',
                    'code' => 1
                ) );
                exit;
            } else {
                $args[ $loop_arg ] = $_POST[ 'message' ][ $loop_content[ 'name' ] ];
            }
        }
        $api_key = get_secured_option( 'general_api' );
        if( empty( $api_key ) ) {
            header( 'HTTP/1.1 400 Bad Request', true, 400 );
            header( 'Content-Type: application/json' );
            echo json_encode( array(
                'message' => "Can't connect to ChatGPT.",
                'code' => 1
            ) );
            exit;
        } else $api_key = $api_key[ 'value' ];
        $temp_request = $this->base_request;
        $temp_request[ 'messages' ][ 0 ][ 'content' ] .= self::fillTemplate( $prompt[ 'prompt' ], $args );
        $temp_request[ 'model' ] = $client[ 'model' ];
        $curl = curl_init( $this->api_url );
        curl_setopt_array( $curl, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $api_key
            ),
            CURLOPT_POSTFIELDS => json_encode( $temp_request )
        ) );
        $response = curl_exec( $curl );
        if( ! $response ) {
            header( 'HTTP/1.1 400 Bad Request', true, 400 );
            header( 'Content-Type: application/json' );
            echo json_encode( array(
                'message' => 'An error occured while trying to send the message.',
                'code' => 1
            ) );
            exit;
        }
        $response = json_decode( $response, true );
        if( isset( $response[ 'choices' ][ 0 ][ 'message' ][ 'content' ] ) ) {
            $tokens_in = $response[ 'usage' ][ 'prompt_tokens' ];
            $tokens_out = $response[ 'usage' ][ 'completion_tokens' ];
            $database->prepared_query(
                'UPDATE gpt_prompts SET utilisations = ? WHERE uuid = ?',
                'is',
                intval( $prompt[ 'utilisations' ] ) + 1,
                $prompt[ 'uuid' ]
            );
            $curl_infos = curl_getinfo( $curl );
            $add_state_state = Prompts::addStat( $prompt[ 'uuid' ], $client, $tokens_in, $tokens_out, $curl_infos[ 'total_time' ], array(
                'parameters' => $args,
                'response' => $response
            ), $database );
            Client::changeCredit( $client[ 'uuid' ], $tokens_in, $tokens_out, $database );
            if( ! $add_state_state ) {
                header( 'HTTP/1.1 400 Bad Request', true, 400 );
                header( 'Content-Type: application/json' );
                echo json_encode( array(
                    'message' => 'Configuration error. Please check all settings before continuing.',
                    'code' => 1
                ) );
                exit;
            }
            $content = $response[ 'choices' ][ 0 ][ 'message' ][ 'content' ];
            header( 'Content-Type: application/json' );
            echo json_encode( array(
                'message' => array(
                    'content' => $content,
                    'stat_uid' => $add_state_state
                ),
                'code' => 0
            ) );
            exit;
        } else if( isset( $response[ 'error' ][ 'message' ] ) ) {
            header( 'HTTP/1.1 400 Bad Request', true, 400 );
            header( 'Content-Type: application/json' );
            echo json_encode( array(
                'message' => $response[ 'error' ][ 'message' ],
                'code' => 1
            ) );
            exit;
        } else {
            header( 'HTTP/1.1 400 Bad Request', true, 400 );
            header( 'Content-Type: application/json' );
            echo json_encode( array(
                'message' => 'Response message mal-formed.',
                'response' => $response,
                'code' => 1
            ) );
        }
    }

    #[Route('/api/get/prompts', 'GET')]
    #[Security(access_all: true)]
    public function getAvailablesPrompts( string $token ) : void {
        list( $client, $decrypted_content ) = $this->getClient( $token );
        $do_not_share = array( 'id_prompt', 'prompt', 'utilisations' );
        global $database;
        $content = $database->prepared_query( 'SELECT * FROM gpt_prompts' );
        if( empty( $content ) ) {
            header( 'HTTP/1.1 400 Bad Request', true, 400 );
            header( 'Content-Type: application/json' );
            echo json_encode( array(
                'message' => 'Prompt not found.',
                'code' => 1
            ) );
            return;
        }
        header( 'Content-Type: application/json' );
        echo json_encode( array(
            'prompts' => empty( $content ) ? array() : array_map(
                function( $current_array ) use ( $do_not_share ) {
                    $temp_option = Options::getOption( 'prompt_config_' . $current_array[ 'id_prompt' ] );
                    if( ! empty( $temp_option ) ) {
                        $current_array[ 'config' ] = unserialize( $temp_option[ 'value' ] );
                    }
                    $temp_content = array_filter(
                        $current_array,
                        fn( $x ) => ! in_array( $x, $do_not_share ),
                        ARRAY_FILTER_USE_KEY
                    );
                    return $temp_content;
                },
                $content
            ),
            'code' => 0
        ) );
    }

}

// Silence is golden