<?php
namespace Engine;

use Engine\PHPMailer\PHPMailer;

class MailEngine {

    private $mail;

    public function __construct() {

        $this->mail = new PHPMailer();
        
        // 是否启用smtp的debug进行调试 开发环境建议开启 生产环境注释掉即可 默认关闭debug调试模式
        $this->mail->SMTPDebug = 0;
        
        // 使用smtp鉴权方式发送邮件，当然你可以选择pop方式 sendmail方式等 本文不做详解
        // 可以参考http://phpmailer.github.io/PHPMailer/当中的详细介绍
        $this->mail->isSMTP();
        
        // smtp需要鉴权 这个必须是true
        $this->mail->SMTPAuth = true;
        
        // 链接qq域名邮箱的服务器地址
        $this->mail->Host = MAIL_SERVER_HOST;
        
        // 设置使用ssl加密方式登录鉴权
        $this->mail->SMTPSecure = 'ssl';
        
        // 设置ssl连接smtp服务器的远程服务器端口号 可选465或587
        $this->mail->Port = MAIL_SERVER_PORT;
        
        // 设置smtp的helo消息头 这个可有可无 内容任意
        $this->mail->Helo = 'Helo';
        
        // 设置发件人的主机域 可有可无 默认为localhost 内容任意，建议使用你的域名
        $this->mail->Hostname = MAIL_SERVER_HOST;
        
        // 设置发送的邮件的编码 可选GB2312 我喜欢utf-8 据说utf8在某些客户端收信下会乱码
        $this->mail->CharSet = 'UTF-8';
        
        // 设置发件人姓名（昵称） 任意内容，显示在收件人邮件的发件人邮箱地址前的发件人姓名
        $this->mail->FromName = MAIL_SERVER_USER_NAME;
        
        // smtp登录的账号 这里填入字符串格式的qq号即可
        $this->mail->Username = MAIL_SERVER_USER;
        
        // smtp登录的密码 这里填入“独立密码” 若为设置“独立密码”则填入登录qq的密码 建议设置“独立密码”
        $this->mail->Password = MAIL_SERVER_PSWD;
        
        // 设置发件人邮箱地址 这里填入上述提到的“发件人邮箱”
        $this->mail->From = MAIL_SERVER_USER;
        
        // 邮件正文是否为html编码 注意此处是一个方法 不再是属性 true或false
        $this->mail->isHTML(true);
    
    }

    /**
     * 发送邮件
     */
    public function send($to, $title, $body, $attachment = array()) {
        $this->mail->clearAllRecipients();
        $this->mail->clearAddresses();
        $this->mail->clearAttachments();
        $this->mail->clearBCCs();
        $this->mail->clearCCs();
        $this->mail->clearCustomHeaders();
        $this->mail->clearReplyTos();

        if (is_array($to)) {
            foreach ( $to as $address ) {
                $this->mail->addAddress($address);
            }
        } else {
            $this->mail->addAddress($to);
        }
        
        $this->mail->Subject = $title;
        
        $this->mail->Body = $body;
        
        foreach ( $attachment as $path => $name ) {
            $this->mail->addAttachment($path, $name);
        }
        
        file_put_contents('/tmp/swoole.process.log', 'mail send: ' . $title . "\n\n\n", FILE_APPEND);
        
        return $this->mail->send();
    
    }

}

?>