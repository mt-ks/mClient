<?php


namespace MClient;


class MultiResponse
{
    private ?string $response;
    private array $header = [];
    private array $cookies = [];
    private Request $request;
    public function __construct($response,$header,$request)
    {
        $this->response = $response;
        $this->request  = $request;
        $this->parseHeader($header);
    }

    /**
     * @return string
     */
    public function getResponseText() : string
    {
        return $this->response;
    }

    /**
     * @param bool $assoc
     * @return mixed
     */
    public function getDecodedResponse($assoc = true)
    {
        return json_decode($this->response,true);
    }

    private function parseHeader($header) : void
    {
        $parseLine = explode("\n",$header);
        foreach ($parseLine as $k => $line){
            if ($k === 0):
                $this->header["http_status"] = $line;
                continue;
            endif;
            if (strpos($line,":"))
            {
                [$key, $value] = explode(":",$line);
                $this->header[$key] = (string)$value;
                if (strtolower($key) === 'set-cookie')
                {
                    $this->cookies[] = $value;
                }
            }
        }
    }

    /**
     * @return HttpCookies
     */
    public function getCookies(): HttpCookies
    {
        return new HttpCookies($this->cookies);
    }


    /**
     * @param $key
     * @return mixed
     */
    public function getHeader($key)
    {
        return $this->header[$key] ?? null;
    }

    /**
     * @return array
     */
    public function getHeaders() : array
    {
        return $this->header;
    }

    public function getRequest() : Request {
        return $this->request;
    }

    public function getIdentifierParams()
    {
        return $this->request->getIdentifierParams();
    }



}