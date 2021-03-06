<?php
namespace Woldy\ddsdk\Components;
use Httpful\Exception\ConnectionErrorException;
use Httpful\Request;
use Log;
class Util {
    public static function try_http_query($response,$retry=3,$exit=true,$url=''){
      $resp='';
      try {
           $resp= $response->send();
      } catch ( \Exception $e) {
          if($retry<1){
            if($exit){
              die("网络不稳啊".$url."\n");
            }else{
              Log::info("这次请求不太行，正在重试".$response->uri."\n");
              return false;
            }
          }else{
            Log::info("这次请求不太行，正在重试".$response->uri."\n");
            return self::try_http_query($response,--$retry,$exit,$url);
          }
      }
      $response=$resp;
      // if ($response->hasErrors()){
      //
      // }
      // if(!is_object($response->body)){
      //     $response->body=json_decode($response->body);
      // }



      if(isset($response->body->errcode)){
        if ($response->body->errcode == 90002){
            if(isset($response->uri)){
                Log::info("好像超限了，60秒后重试".$response->uri."\n");
            }
          sleep(60);
          return self::try_http_query($response,3,$exit,$url);
        }elseif ($response->body->errcode!=0) {
          Log::info($response);
        }
      }




      return $response;
    }
}
