<?php


namespace MClient;

class MultiRequest
{
    /**
     * @var Request[]
     */
    public array $requests = [];

    private array $defaultOptions = [
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_FOLLOWLOCATION => 1,
        CURLOPT_MAXREDIRS => 5,
        CURLOPT_CONNECTTIMEOUT => 30,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_HEADER => TRUE
    ];

    /**
     * @param Request $request
     */
    public function add(Request $request)
    {
        $this->requests[] = $request;
        return $this;
    }

    /**
     * @param Request $request
     * @return array
     */
    private function curlToArray(Request $request) : array
    {
        $options = [];
        $options[CURLOPT_URL] = $request->getRequestUri();

        if ($request->hasHeader()):
            $options[CURLOPT_HTTPHEADER] = $request->getRequestHeaders();
        endif;

        if ($request->hasPosts()):
            $options[CURLOPT_POST] = TRUE;
            $options[CURLOPT_POSTFIELDS] = $request->getRequestPosts();
        endif;

        foreach ($this->defaultOptions as $k => $v):
            $options[$k] = $v;
        endforeach;

        if ($request->hasExtraCurlOptions()):
            foreach ($request->_curl as $key => $value){
                $options[$key] = $value;
            }
        endif;

        return $options;

    }

    /**
     * @return MultiResponse[]
     */
    public function execute(): array
    {
        $multiHandle = curl_multi_init();
        $multiCurl   = [];
        $result      = [];
        $requestTMP  = [];
        foreach ($this->requests as $key => $request) {
            $multiCurl[$key] = curl_init();
            $requestTMP[$key] = $request;
            curl_setopt_array($multiCurl[$key],$this->curlToArray($request));
            curl_multi_add_handle($multiHandle, $multiCurl[$key]);
        }
        $index = null;
        do {
            curl_multi_exec($multiHandle,$index);
        } while($index > 0);

        foreach($multiCurl as $k => $ch) {
            $header_len = curl_getinfo($ch,CURLINFO_HEADER_SIZE);
            $content    = curl_multi_getcontent($ch);
            $response   = (substr($content, $header_len));
            $header_content = substr($content, 0,$header_len);


            $result[$k] = (new MultiResponse($response,$header_content,$requestTMP[$k]));

            curl_multi_remove_handle($multiHandle, $ch);
        }
        curl_multi_close($multiHandle);
        return $result;
    }

}