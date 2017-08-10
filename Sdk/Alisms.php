<?php
use Sms\Request\V20160927 as Sms;

class Alisms {

    public function send($phone, $code, $expireTime) {

        include_once 'aliyun-php-sdk-sms/aliyun-php-sdk-core/Config.php';
        
        $iClientProfile = DefaultProfile::getProfile("cn-hangzhou", "lgrmmQGPtBQHLAeK", "ant3sJXn5xhIMrAGUb7Tmv5LooJm3I");
        $client = new DefaultAcsClient($iClientProfile);
        $request = new Sms\SingleSendSmsRequest();
        $request->setSignName("创意元素"); /* 签名名称 */
        $request->setTemplateCode("SMS_44890007"); /* 模板code */
        $request->setRecNum($phone); /* 目标手机号 */
        $json = "{\"code\":\"{$code}\", \"expireTime\":\"{$expireTime}\"}";
        $request->setParamString($json); /* 模板变量，数字一定要转换为字符串 */
        try {
            $response = $client->getAcsResponse($request);
            // print_r($response);
        } catch ( ClientException $e ) {
            // print_r($e->getErrorCode());
            // print_r($e->getErrorMessage());
        } catch ( ServerException $e ) {
            // print_r($e->getErrorCode());
            // print_r($e->getErrorMessage());
        }
    
    }

}

?>