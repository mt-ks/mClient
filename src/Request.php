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
    public array $_curl    = [];

    /**
     * Post as json format
     * @var bool
     */
    protected bool  $jsonPost = false;

    /**
     * @var mixed
     */
    private $identifier;


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
     * @return Request
     */
    public function setProxy($proxy) : Request
    {
        $this->addCurlOptions(CURLOPT_PROXY,$proxy);
        return $this;
    }

    /**
     * @param $userAgent
     * @return Request
     */
    public function setUserAgent($userAgent) : Request
    {
        $this->addCurlOptions(CURLOPT_USERAGENT,$userAgent);
        $this->addHeader('User-Agent',$userAgent);
        return $this;
    }

    /**
     * @param $cookieString
     * @return Request
     */
    public function setCookieString($cookieString) : Request
    {
        $this->addCurlOptions(CURLOPT_COOKIE,$cookieString);
        return $this;
    }

    /**
     * @param $cookieFile
     * @param null $cookieJar
     * @return $this
     */
    public function setCookieFile($cookieFile,$cookieJar = null) : Request
    {
        $this->addCurlOptions(CURLOPT_COOKIEFILE,$cookieFile);
        $this->addCurlOptions(CURLOPT_COOKIEJAR, $cookieJar ?? $cookieFile);
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
    public function hasUri() : bool
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
    public function hasPosts() : bool
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

    /**
     * @return array
     */
    public function getCurlOptions() : array
    {
        return $this->_curl;
    }

    public function hasExtraCurlOptions() : bool
    {
        return ($this->_curl && count($this->_curl) > 0);
    }

    public function setJsonPost($bool) : self
    {
        $this->jsonPost = $bool;
        return $this;
    }

    public function isJsonPost() : bool {
        return $this->jsonPost;
    }

    public function setIdentifierParams($data) : Request
    {
        $this->identifier = $data;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getIdentifierParams()
    {
        return $this->identifier;
    }


    /**
     * @return HttpInterface
     * @throws JsonException
     */
    public function execute() : HttpInterface
    {
        return new HttpInterface($this);
    }


}