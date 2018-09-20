<?php
use Aliyun\Core\Config;
use Aliyun\Core\Profile\DefaultProfile;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;

Config::load();

class Alisms {

    public function send($phone, $signName, $templateCode, $param) {

        $iClientProfile = DefaultProfile::getProfile(ALI_SMS_REGION_ID, ALI_SMS_ACCESS_KEY_ID, ALI_SMS_ACCESS_SECRET);
        
        DefaultProfile::addEndpoint(ALI_SMS_REGION_ID, ALI_SMS_REGION_ID, "Dysmsapi", "dysmsapi.aliyuncs.com");
        
        $client = new DefaultAcsClient($iClientProfile);
        
        $request = new SendSmsRequest();
        $request->setSignName($signName); /* 签名名称 */
        $request->setTemplateCode($templateCode); /* 模板code */
        $request->setPhoneNumbers($phone); /* 目标手机号 */
        $request->setTemplateParam(json_encode($param)); /* 模板变量，数字一定要转换为字符串 */
        
        return $client->getAcsResponse($request);
    
    }

}