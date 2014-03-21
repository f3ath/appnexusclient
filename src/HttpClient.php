<?php
namespace F3\AppNexusClient;

use F3\CurlWrapper\Curl;
use InvalidArgumentException;
use RuntimeException;

class HttpClient
{
    private $curl;

    /**
     * __construct
     *
     * @param F3\CurlWrapper\Curl $curl
     */
    public function __construct(Curl $curl)
    {
        $this->curl = $curl;
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
        $json = $this->curl->exec(1, true);
        $response = json_decode($json);
        if (!isset($response->response)) {
            throw new RuntimeException(sprintf('Unexpected response: %s', $json));
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
