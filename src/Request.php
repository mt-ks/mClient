<?php


namespace MClient;
use InvalidArgumentException;
use JsonException;
use RuntimeException;

/**
 * Class Request
 * @package MClient
 */
class Request
{

    /**
     * Request URI
     * @var string
     */
    protected string $uri     = '';

    /**
     * Http Posts
     * @var array
     */
    protected array $_posts   = [];

    /**
     * Url Query
     * @var array
     */
    protected array $_params  = [];

    /**
     * Request Headers
     * @var array
     */
    protected array $_headers = [];

    /**
     * Extra curl options
     * @var array
     */
    protected array $_curl    = [];

    /**
     * Post as json format
     * @var bool
     */
    protected bool  $jsonPost = false;

    /**
     * Response
     * @var string
     */
    protected ?string $requestResponse = null;

    /**
     * Response Headers
     * @var array
     */
    protected array $requestResponseHeaders = [];

    public function __construct($address)
    {
        $this->setUri($address);
    }

    /**
     * @param $address
     */
    protected function setUri($address) : void
    {
        $this->uri = $address;
    }

    /**
     * @param $proxy
     * @return $this
     */
    public function setProxy($proxy) : self
    {
        $this->addCurlOptions(CURLOPT_PROXY,$proxy);
        return $this;
    }

    /**
     * @return string
     */
    public function getRequestUri() : string
    {
        return $this->uri.$this->getRequestParams();
    }

    /**
     * @return bool
     */
    protected function hasUri() : bool
    {
        return ($this->uri !== '');
    }

    /**
     * @param $key
     * @param $value
     * @return self
     */
    public function addPost($key,$value): self
    {
        $this->_posts[$key] = $value;
        return $this;
    }


    /**
     * @return bool
     */
    protected function hasPosts() : bool
    {
        return ($this->_posts && count($this->_posts) > 0);
    }


    /**
     * @return string
     * @throws JsonException
     */
    public function getRequestPosts() : string
    {
        if ($this->isJsonPost()):
            return json_encode($this->_posts, JSON_THROW_ON_ERROR);
        endif;
        return http_build_query($this->_posts);
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function addParam($key,$value) : self
    {
        $this->_params[$key] = $value;
        return $this;
    }

    /**
     * @return bool
     */
    protected function hasParams() : bool {
        return ($this->_params && count($this->_params) > 0);
    }

    /**
     * @return null|string
     */
    public function getRequestParams() : ?string
    {
        return $this->hasParams() === true ?  '?'.http_build_query($this->_params) : null;
    }

    /**
     * @param $key
     * @param $value
     * @return self
     */
    public function addHeader($key,$value) : self
    {
        $this->_headers[$key] = $value;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasHeader() : bool
    {
        return ($this->_headers && count($this->_headers) > 0);
    }

    /**
     * @return array
     */
    public function getRequestHeaders() : array
    {
        $headers = [];
        foreach ($this->_headers as $key => $value) {
            $headers[] = sprintf('%s: %s', $key, $value);
        }

        return $headers;
    }

    /**
     * @param $curlKey
     * @param $value
     * @return $this
     */
    public function addCurlOptions($curlKey, $value) : self
    {
        $this->_curl[$curlKey] = $value;
        return $this;
    }

    protected function hasExtraCurlOptions() : bool
    {
        return ($this->_curl && count($this->_curl) > 0);
    }

    public function setJsonPost($bool) : void
    {
        $this->jsonPost = $bool;
    }

    public function isJsonPost() : bool {
        return $this->jsonPost;
    }


    /**
     * @return $this
     * @throws JsonException
     */
    public function execute() : self
    {
        if (!$this->hasUri())
        {
            throw new InvalidArgumentException('Please set uri');
        }
        $curl = curl_init();
        $options = [
            CURLOPT_URL => $this->getRequestUri(),
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_FOLLOWLOCATION => TRUE,
            CURLOPT_HEADER         => TRUE,
        ];

        if ($this->hasHeader()):
            $options[CURLOPT_HTTPHEADER] = $this->getRequestHeaders();
        endif;


        if ($this->hasPosts()) :
            $options[CURLOPT_POST] = TRUE;
            $options[CURLOPT_POSTFIELDS] = $this->getRequestPosts();
        endif;

        if ($this->hasExtraCurlOptions()):
            foreach ($this->_curl as $key => $value){
                $options[$key] = $value;
            }
        endif;
        curl_setopt_array($curl,$options);
        $resp = curl_exec($curl);
        $header_len = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $header = substr($resp, 0, $header_len);
        $header = $this->getHeadersFromResponse($header);
        $resp = (substr($resp, $header_len));
        curl_close($curl);

        $this->requestResponse = $resp;
        $this->requestResponseHeaders = $header;
        return $this;
    }



    /**
     * @return array
     */
    public function getResponseHeaders() : array
    {
        return $this->requestResponseHeaders;
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public function getHeaderLine($key)
    {
        if (is_array($this->requestResponseHeaders) && isset($this->requestResponseHeaders[$key])) {
            return $this->requestResponseHeaders[$key];
        }
        return null;
    }

    /**
     * @return string
     */
    public function getResponse() : string
    {
        return $this->requestResponse;
    }


    /**
     * @param bool $assoc
     * @return mixed
     * @throws JsonException
     */
    public function getDecodedResponse($assoc = true)
    {
        if (!$this->requestResponse)
        {
            throw new RuntimeException('No Response From Server');
        }
        return json_decode($this->requestResponse, $assoc, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @param $response
     * @return array
     */
    protected function getHeadersFromResponse($response) : array
    {
        $headers = [];

        $header_text = substr($response, 0, strpos($response, "\r\n\r\n"));

        foreach (explode("\r\n", $header_text) as $i => $line) {
            if ($i === 0) {
                $headers['http_code'] = $line;
            } else {
                [$key, $value] = explode(': ', $line);

                $headers[$key] = $value;
            }
        }

        return $headers;
    }



}