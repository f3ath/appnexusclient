<?php
namespace F3\AppNexusClient;

use PHPCurl\CurlWrapper\Curl;
use InvalidArgumentException;
use RuntimeException;

class HttpClient
{
    const CONTENT_TYPE_JSON = 'application/json';

    /**
     * curl
     *
     * @var Curl
     */
    private $curl;

    /**
     * __construct
     *
     * @param Curl $curl
     */
    public function __construct(Curl $curl = null)
    {
        $this->curl = $curl ?: new Curl();
    }

    /**
     * Do raw HTTP GET call
     *
     * @param string $url
     * @param array $post POST data
     * @param array $headers
     *
     * @return object response
     */
    public function get($url, array $post = array(), array $headers = array())
    {

        return $this->call(HttpMethod::GET,$url,$post,$headers);

    }

    /**
     * Do raw HTTP POST call
     *
     * @param string $url
     * @param array $post POST data
     * @param array $headers
     *
     * @return object response
     */
    public function post($url, array $post = array(), array $headers = array())
    {

        return $this->call(HttpMethod::POST,$url,$post,$headers);

    }

    /**
     * Do raw HTTP PUT call
     *
     * @param string $url
     * @param array $post POST data
     * @param array $headers
     *
     * @return object response
     */
    public function put($url, array $post = array(), array $headers = array())
    {

        return $this->call(HttpMethod::PUT,$url,$post,$headers);

    }

    /**
     * Do raw HTTP DELETE call
     *
     * @param string $url
     * @param array $post POST data
     * @param array $headers
     *
     * @return object response
     */
    public function delete($url, array $post = array(), array $headers = array())
    {

        return $this->call(HttpMethod::DELETE,$url,$post,$headers);

    }

    /**
     * Do raw HTTP call
     *
     * @param string $method
     * @param string $url
     * @param array $post POST data
     * @param array $headers
     *
     * @return object response
     */
    public function call($method, $url, array $post = array(), array $headers = array())
    {
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
        );
        switch ($method) {
            case HttpMethod::GET:
                $options[CURLOPT_POST] = false;
                break;
            case HttpMethod::POST:
                $options[CURLOPT_POST] = true;
                $options[CURLOPT_POSTFIELDS] = json_encode($post);
                break;
            case HttpMethod::PUT:
                $options[CURLOPT_CUSTOMREQUEST] = 'PUT';
                $options[CURLOPT_POSTFIELDS] = json_encode($post);
                break;
            case HttpMethod::DELETE:
                $options[CURLOPT_CUSTOMREQUEST] = 'DELETE';
                $options[CURLOPT_POSTFIELDS] = json_encode($post);
                break;
            default:
                throw new InvalidArgumentException(sprintf('Invalid method: %s', $method));
        }

        $this->curl->init();
        $this->curl->setOptArray($options);
        $rawResponse = $this->curl->exec(1, true);
        $contentType = $this->curl->getInfo(CURLINFO_CONTENT_TYPE);
        if (strpos($contentType, self::CONTENT_TYPE_JSON) === false) {
            return $rawResponse;
        }
        $response = json_decode($rawResponse);
        if (!isset($response->response)) {
            throw new RuntimeException(sprintf('Unexpected response: %s', $rawResponse));
        }
        $response = $response->response;
        if ('OK' == @$response->status) {
            return $response;
        } elseif ('NOAUTH' == @$response->error_id) {
            throw new TokenExpiredException($response);
        }
        throw new ServerException($response);
    }
}
