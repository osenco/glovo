<?php

namespace Osen\Glovo;

class Client {
  const API_BASE_URL_PRODUCTION = "https://api.glovoapp.com/b2b";
  const API_BASE_URL_STAGING = "https://stageapi.glovoapp.com/b2b";

  /**
   * @param $request
   *
   * @return false|resource
   * @throws Exception
   */
  private static function build_request( $request ) {
    if (!extension_loaded( "curl" )) {
      throw new Exception( "cURL extension not found. You need to enable cURL in your php.ini or another configuration you have." );
    }

    if (!isset( $request["method"] )) {
      throw new Exception( "No HTTP METHOD specified" );
    }

    if (!isset( $request["auth"] )) {
      throw new Exception( "No auth specified" );
    }

    if (!isset( $request["uri"] )) {
      throw new Exception( "No URI specified" );
    }

    $headers = [];

    array_push( $headers, "Content-Type: application/json" );

    $connect = curl_init();

    //curl_setopt( $connect, CURLOPT_VERBOSE, true );

    curl_setopt( $connect, CURLOPT_USERAGENT, "Glovo PHP SDK /v" . Service::VERSION );
    curl_setopt( $connect, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $connect, CURLOPT_SSL_VERIFYPEER, true );
    curl_setopt( $connect, CURLOPT_USERPWD, $request["auth"]["apiKey"] . ":" . $request["auth"]["apiSecret"] );
    curl_setopt( $connect, CURLOPT_CAINFO, realpath( dirname( __FILE__ ) . '/../data/ca-certificates.crt' ) );
    curl_setopt( $connect, CURLOPT_CUSTOMREQUEST, $request["method"] );
    curl_setopt( $connect, CURLOPT_HTTPHEADER, $headers );

    if (isset ( $request["params"] ) && is_array( $request["params"] ) && count( $request["params"] ) > 0) {
      $request["uri"] .= ( strpos( $request["uri"], "?" ) === false ) ? "?" : "&";
      $request["uri"] .= self::build_query( $request["params"] );
    }

    $baseUri = ( $request["sandbox"] ) ? self::API_BASE_URL_STAGING : self::API_BASE_URL_PRODUCTION;

    curl_setopt( $connect, CURLOPT_URL, $baseUri . $request["uri"] );

    if (isset( $request["data"] )) {
      $request["data"] = json_encode( $request["data"] );

      if (function_exists( 'json_last_error' )) {
        $json_error = json_last_error();
        if ($json_error != JSON_ERROR_NONE) {
          throw new Exception( "JSON Error [{$json_error}] - Data: " . $request["data"] );
        }
      }

      curl_setopt( $connect, CURLOPT_POSTFIELDS, $request["data"] );
    }

    return $connect;
  }

  /**
   * @param $request
   *
   * @return array
   * @throws Exception
   */
  public static function exec( $request ) {
    $connect = self::build_request( $request );

    $api_result = curl_exec( $connect );
    $api_http_code = curl_getinfo( $connect, CURLINFO_HTTP_CODE );

    if ($api_result === false) {
      throw new Exception ( curl_error( $connect ) );
    }

    $response = array(
      "status" => $api_http_code,
      "response" => json_decode( $api_result, true )
    );

    if ($response['status'] != 200) {
      throw new Exception ( $response['response']['error'], $response['status'] );
    }

    curl_close( $connect );

    return $response['response'];
  }

  private static function build_query( $params ) {
    if (function_exists( "http_build_query" )) {
      return http_build_query( $params, "", "&" );
    } else {
      $elements = [];

      foreach ($params as $name => $value) {
        $elements[] = "{$name}=" . urlencode( $value );
      }

      return implode( "&", $elements );
    }
  }
}