<?php
require_once('config.php');

/**
 * Query Amazon about a particular book by ISBN and obtain metadata.
 * The author disclaims all copyright and places this in the public domain.
 *
 * Amazon's Terms of Use for this service require you to:
 * - Send no more than 1 request every second
 * - Direct traffic to them in some way. You can use the URL provided in the
 *   resulting metadata to achieve this.
 */
class ISBNFetcher {
  public static function getMetadata($isbn) {
    $host = 'ecs.amazonaws.com';
    $path = '/onca/xml';

    $args = array(
      'AssociateTag' => 'chimbori05-20',
      'AWSAccessKeyId' => AWS_ACCESS_KEY_ID,
      'IdType' => 'ISBN',
      'ItemId' => $isbn,
      'Operation' => 'ItemLookup',
      'ResponseGroup' => 'Medium',
      'SearchIndex' => 'Books',
      'Service' => 'AWSECommerceService',
      'Timestamp' => gmdate('Y-m-d\TH:i:s\Z'),
      'Version'=> '2009-01-06'
    );

    ksort($args);
    $parts = array();
    foreach(array_keys($args) as $key) {
      $parts[] = $key . "=" . $args[$key];
    }

    // Construct the string to sign
    $stringToSign = "GET\n" . $host . "\n" . $path . "\n" . implode("&", $parts);
    $stringToSign = str_replace('+', '%20', $stringToSign);
    $stringToSign = str_replace(':', '%3A', $stringToSign);
    $stringToSign = str_replace(';', urlencode(';'), $stringToSign);

    // Sign the request
    $signature = hash_hmac("sha256", $stringToSign, AWS_SECRET_KEY, TRUE);

    // Base64 encode the signature and make it URL safe
    $signature = base64_encode($signature);
    $signature = str_replace('+', '%2B', $signature);
    $signature = str_replace('=', '%3D', $signature);

    // Construct the URL
    $url = 'https://' . $host . $path . '?' . implode("&", $parts) . "&Signature=" . $signature;

    $rawData = @file_get_contents($url);
    $statusLine = $http_response_header[0];
    preg_match('{HTTP\/\S*\s(\d{3})}', $statusLine, $match);
    $status = (int) $match[1];

    if ($status !== 200) {
      return (object) array('Error' =>
        (object) array(
          'Code' => $status,
          'Message' => $status
        )
      );
    }

    $metadata = simplexml_load_string($rawData);
    if (isset($metadata->Items->Request->Errors)) {
      return $metadata->Items->Request->Errors;
    } else {
      return $metadata->Items->Item;
    }
  }
}
?>
