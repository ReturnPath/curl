<?php

/**
 * Parses the response from a Curl request into an object containing
 * the response body and an associative array of headers
 *
 * @package curl
 * @author Sean Huber <shuber@huberry.com>
**/
class CurlResponse {
    
    /**
     * The body of the response without the headers block
     *
     * @var string
    **/
    public $body = '';
    
    /**
     * An associative array containing the response's headers
     *
     * @var array
    **/
    public $headers = array();
    
    /**
     * Accepts the result of a curl request as a string
     *
     * <code>
     * $response = new CurlResponse(curl_exec($curl_handle));
     * echo $response->body;
     * echo $response->headers['Status'];
     * </code>
     *
     * @param string $response
    **/
    public function __construct($response) {
        # Headers
        $pattern = '#HTTP/\d\.\d.*?$.*?\r\n\r\n#ims';
        
        # Extract headers from response
        preg_match_all($pattern, $response, $matches);
        $headers = split("\r\n", str_replace("\r\n\r\n", '', array_pop($matches[0])));
        
        # Remove headers from the response body
        $this->body = preg_replace($pattern, '', $response);
        
        # Extract the version and status from the first header
        $version_and_status = array_shift($headers);
        preg_match('#HTTP/(\d\.\d)\s(\d\d\d)\s(.*)#', $version_and_status, $matches);
        $this->headers['Http-Version'] = $matches[1];
        $this->headers['Status-Code'] = $matches[2];
        $this->headers['Status'] = $matches[2].' '.$matches[3];
        
        # Convert headers into an associative array
        foreach ($headers as $header) {
            preg_match('#(.*?)\:\s(.*)#', $header, $matches);
            $this->headers[$matches[1]] = $matches[2];
        }
    }
    
    /**
     * Returns the response body
     *
     * <code>
     * $curl = new Curl;
     * $response = $curl->get('google.com');
     * echo $response;  # => echo $response->body;
     * </code>
     *
     * @return string
    **/
    public function __toString() {
        return $this->body;
    }
    
}