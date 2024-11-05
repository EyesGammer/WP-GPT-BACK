<?php

namespace src;

use CurlHandle;

class StripeAPI {

    private string $api_key;
    private string $api_url="https://api.stripe.com/v1";
    private \CurlHandle|null $curl=null;

    public function __construct( string $api_key, ?string $api_url=null ) {
        $this->api_key = $api_key;
        if( ! empty( $api_url ) ) {
            $this->api_url = $api_url;
        }
        if( ( $temp = curl_init() ) ) {
            $this->curl = $temp;
        }
    }

    /**
     * Retrieve Stripe session using session id
     *
     * @param string $session_id
     * @return array
     */
    public function retrieveSession( string $session_id ) : array {
        if( ! empty( $this->curl ) ) {
            curl_setopt_array( $this->curl, array(
                CURLOPT_URL => $this->api_url . "/checkout/sessions/$session_id",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . $this->api_key,
                )
            ) );
            $response = curl_exec( $this->curl );
            curl_close( $this->curl );
            $json_response = json_decode( $response, true );
            if(
                ! empty( $json_response[ 'payment_intent' ] ) &&
                ! empty( $json_response[ 'amount_total' ] ) &&
                ! empty( $json_response[ 'customer_details' ][ 'email' ] )
            ) {
                return array(
                    'amount' => intval( $json_response[ 'amount_total' ] ) / 100,
                    'intent' => $json_response[ 'payment_intent' ],
                    'email' => $json_response[ 'customer_details' ][ 'email' ]
                );
            }
            return array();
        } else return array();
    }

}

// Silence is golden