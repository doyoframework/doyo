<?php

namespace Sdk;

use Common\Model;
use  Core\Util;

class WxService
{

    // $appid = 'wx7d116e5036ea484e';

    // $appsecret = '3ccc62383d6ac48fa82d8f1fe12a59a8';

    //原始id  gh_2386b2821411

    private $api_host = 'https://api.weixin.qq.com/cgi-bin';

    private $robot_id;

    public function __construct($robot_id)
    {
        $this->robot_id = $robot_id;
    }

    /**
     *
     * 群发消息
     *
     * @param $type
     * @param $content
     * @param $openIdList
     * @param $title
     * @param $description
     * @return mixed
     * @throws \Exception\HTTPException
     */
    public function batch_send($type, $content, $openIdList, $title, $description)
    {
        $result = array();
        if ($type != 'text') {

            $url = $this->api_host . '/message/mass/send?access_token=' . $this->access_token();

            if ($type == 'mpnews') {

                $data = array(
                    'touser' => $openIdList,
                    'mpnews' => array(
                        'media_id' => $content
                    ),
                    'msgtype' => 'mpnews',
                    'send_ignore_reprint' => 0
                );

                $data = json_encode($data, JSON_UNESCAPED_UNICODE);

                $result = Util::curl_request($url, 'POST', $data);

            } elseif ($type == 'voice') {

                $data = array(
                    'touser' => $openIdList,
                    'voice' => array(
                        'media_id' => $content
                    ),
                    'msgtype' => 'voice'
                );

                $data = json_encode($data, JSON_UNESCAPED_UNICODE);

                $result = Util::curl_request($url, 'POST', $data);

            } elseif ($type == 'image') { // 发送的图片消息

                $data = array(
                    'touser' => $openIdList,
                    'image' => array(
                        'media_id' => $content
                    ),
                    'msgtype' => 'image'
                );

                $data = json_encode($data, JSON_UNESCAPED_UNICODE);

                $result = Util::curl_request($url, 'POST', $data);

            } elseif ($type == 'video') { // 发送的视频消息

                $url = $this->api_host . '/media/uploadvideo?access_token=' . $this->access_token();
                $data = array(
                    'media_id' => $content,
                    'title' => $title,
                    'description' => $description
                );

                $data = json_encode($data, JSON_UNESCAPED_UNICODE);

                $video_data = Util::curl_request($url, 'POST', $data);

                $video_data = json_decode($video_data, true);

                $data = array(
                    'touser' => $openIdList,
                    'mpvideo' => array(
                        'media_id' => $video_data['media_id'],
                        'title' => $title,
                        'description' => $description
                    )
                );

                $data = json_encode($data, JSON_UNESCAPED_UNICODE);
                $result = Util::curl_request($url, 'POST', $data);
            }
        } else { // 发送的文本消息

            $url = $this->api_host . '/message/mass/send?access_token=' . $this->access_token();

            $data = array(
                'touser' => $openIdList,
                'msgtype' => 'text',
                'text' => array(
                    'content' => $content
                )
            );
            $data = json_encode($data, JSON_UNESCAPED_UNICODE);
            $result = Util::curl_request($url, 'POST', $data);
        }
        $content = json_decode($result, true);
        return $content;
    }

    /**
     * 查询服务号的AccessToken
     *
     * @return mixed
     * @throws \Exception\HTTPException
     */
    private function access_token()
    {
        $robot_id = $this->robot_id;

        $cache = Util::loadRedis('cache');

        $key = $robot_id . '_access_token';

        if (!$cache->exists($key)) {

            $m_robot = Model::Robot($robot_id);

            if (!$m_robot->exists) {
                throw Util::HTTPException('m_robot 不存在');
            }

            $url = $this->api_host . '/token?grant_type=client_credential&appid=' . $m_robot->app_id . '&secret=' . $m_robot->app_secret;

            $ret = Util::curl_request($url);

            $data = json_decode($ret, true);

            if (isset($data['errcode'])) {
                if ($data['errcode'] == '40013') {
                    throw Util::HTTPException('AppID错误，请检查配置信息。');
                } else if ($data['errcode'] == '40125') {
                    throw Util::HTTPException('AppSecret错误，请检查配置信息。');
                } else {
                    throw Util::HTTPException('网络错误，请稍候再试', -1, $data);
                }
            }

            $cache->set($key, $data['access_token'], 7200);
        }

        return $cache->get($key);
    }

    /**
     * 生成二维码(永久)
     *
     * @param $action_name
     * @param $scene_id
     * @return bool|mixed
     * @throws \Exception\HTTPException
     */
    public function qr_code_perpetual($action_name, $scene_id)
    {
        $url = $this->api_host . '/qrcode/create?access_token=' . $this->access_token();
        $data = array(
            'action_name' => $action_name,
            'action_info' => array(
                'scene' => array(
                    'scene_id' => $scene_id))
        );
        $data = json_encode($data, JSON_UNESCAPED_UNICODE);

        $result = Util::curl_request($url, 'POST', $data);

        $result = json_decode($result, true);

        return $result;
    }

    /**
     * 生成二维码(临时)
     *
     * @param $expire_seconds
     * @param $action_name
     * @param $action_info
     * @return bool|mixed
     * @throws \Exception\HTTPException
     */
    public function qr_code_temporary($expire_seconds, $action_name, $action_info)
    {
        $url = $this->api_host . '/qrcode/create?access_token=' . $this->access_token();

        $data = array(
            'expire_seconds' => $expire_seconds,
            'action_name' => $action_name,
            'action_info' => $action_info
        );
        $data = json_encode($data, JSON_UNESCAPED_UNICODE);

        $result = Util::curl_request($url, 'POST', $data);

        $result = json_decode($result, true);

        return $result;
    }

    /**
     * 获取二维码图片
     *
     * @param $ticket
     * @return bool|mixed
     */
    public function qr_ticket($ticket)
    {
        $url = $this->api_host . '/showqrcode?ticket=' . $ticket;

        $content = Util::curl_request($url, 'GET');

        return $content;
    }


    /**
     *  发送文字消息
     *
     * @param $openid
     * @param $type
     * @param $content
     * @return mixed
     * @throws \Exception\HTTPException
     *
     */
    public function sendmsg_text($openid, $type, $content)
    {
        $url = $this->api_host . '/message/custom/send?access_token=' . $this->access_token();

        $data = array(
            'touser' => $openid,
            'msgtype' => $type,
            'text' => array('content' => $content)
        );

        $data = json_encode($data, JSON_UNESCAPED_UNICODE);

        $result = Util::curl_request($url, 'POST', $data);

        $result = json_decode($result, true);

        return $result;

    }


    /**
     * 发送图片消息
     *
     * @param $openid
     * @param $type
     * @param $media_id
     * @return bool|mixed
     * @throws \Exception\HTTPException
     */
    public function sendmsg_image($openid, $type, $media_id)
    {
        $url = $this->api_host . '/message/custom/send?access_token=' . $this->access_token();
        $data = array(
            'touser' => $openid,
            'msgtype' => $type,
            'image' => array('media_id' => $media_id)
        );

        $data = json_encode($data, JSON_UNESCAPED_UNICODE);

        $result = Util::curl_request($url, 'POST', $data);

        $result = json_decode($result, true);

        return $result;
    }

    /**
     *
     *  发送语音消息
     *
     * @param $openid
     * @param $type
     * @param $media_id
     * @return bool|mixed
     * @throws \Exception\HTTPException
     */
    public function sendmsg_voice($openid, $type, $media_id)
    {
        $url = $this->api_host . '/message/custom/send?access_token=' . $this->access_token();
        $data = array(
            'touser' => $openid,
            'msgtype' => $type,
            'voice' => array(
                'media_id' => $media_id
            )
        );

        $data = json_encode($data, JSON_UNESCAPED_UNICODE);

        $result = Util::curl_request($url, 'POST', $data);

        $result = json_decode($result, true);

        return $result;
    }

    /**
     * 发送视频消息
     *
     *
     * @param $openid
     * @param $type
     * @param $media_id
     * @param $title
     * @param $description
     * @return bool|mixed
     * @throws \Exception\HTTPException
     */
    public
    function sendmsg_video($openid, $type, $media_id, $title = '', $description = '')
    {
        $url = $this->api_host . '/message/custom/send?access_token=' . $this->access_token();

        $data = array('touser' => $openid,
            'msgtype' => $type,
            'video' =>
                array(
                    'media_id' => $media_id,
                    'title' => $title,
                    'description' => $description
                )
        );

        $data = json_encode($data, JSON_UNESCAPED_UNICODE);

        $result = Util::curl_request($url, 'POST', $data);

        $result = json_decode($result, true);

        return $result;
    }

    /**
     * 发送音乐消息
     *
     * @param $openid
     * @param $type
     * @param $media_id
     * @param $music_title
     * @param $music_description
     * @param $hq_music_url
     * @param $thumb_media_id
     * @return bool|mixed
     * @throws \Exception\HTTPException
     */
    public
    function sendmsg_music($openid, $type, $media_id, $music_title, $music_description, $hq_music_url, $thumb_media_id)
    {
        $url = $this->api_host . '/message/custom/send?access_token=' . $this->access_token();
        $data = array('touser' => $openid,
            'msgtype' => $type,
            'music' => array(
                'media_id' => $media_id,
                'title' => $music_title,
                'description' => $music_description,
                'hqmusicurl' => $hq_music_url,
                'thumb_media_id' => $thumb_media_id
            ),
        );

        $data = json_encode($data, JSON_UNESCAPED_UNICODE);

        $result = Util::curl_request($url, 'POST', $data);

        $result = json_decode($result, true);

        return $result;
    }

    /**
     *  * 发送图文消息
     *
     *
     * @param $openid
     * @param $type
     * @param $media_id
     * @return bool|mixed
     * @throws \Exception\HTTPException
     */
    public
    function sendmsg_news($openid, $type, $media_id)
    {
        $url = $this->api_host . '/message/custom/send?access_token=' . $this->access_token();

        $data = array('touser' => $openid,
            'msgtype' => $type,
            'mpnews' => array(
                'media_id' => $media_id
            )
        );

        $data = json_encode($data, JSON_UNESCAPED_UNICODE);

        $result = Util::curl_request($url, 'POST', $data);

        $result = json_decode($result, true);

        return $result;
    }

    /**
     *
     * 发送卡卷
     *
     * @param $openid
     * @param $type
     * @param $card_id
     * @return bool|mixed
     * @throws \Exception\HTTPException
     */
    public
    function sendmsg_card($openid, $type, $card_id)
    {
        $url = $this->api_host . '/message/custom/send?access_token=' . $this->access_token();

        $data = array(
            'touser' => $openid,
            'msgtype' => $type,
            'wxcard' => array(
                'card_id' => $card_id
            )
        );

        $data = json_encode($data, JSON_UNESCAPED_UNICODE);

        $result = Util::curl_request($url, 'POST', $data);

        $result = json_decode($result, true);

        return $result;
    }

    /**
     * 新增永久素材
     *
     * @param $path
     * @param $type
     * @param string $title
     * @param string $introduction
     * @return bool|mixed
     * @throws \Exception\HTTPException
     */
    public
    function add_material($path, $type, $title = '', $introduction = '')
    {
        $url = $this->api_host . '/material/add_material?access_token=' . $this->access_token() . '&type=' . $type;

        $data = array(
            'media' => new \CURLFile($path['path']),
            'description' => json_encode(array(
                'title' => $title,
                'introduction' => $introduction
            ), JSON_UNESCAPED_UNICODE)
        );

        $content = Util::curl_request($url, 'FILE', $data);

        $content = json_decode($content, true);

        return $content;
    }

    /**
     * 清除接口调用次数
     *
     * @return bool|mixed
     * @throws \Exception\HTTPException
     */
    public
    function clear_req()
    {
        $url = $this->api_host . '/clear_quota?access_token=' . $this->access_token();

        $robot_id = $this->robot_id;

        $m_robot = Model::Robot($robot_id);

        if (!$m_robot->exists) {
            throw Util::HTTPException('m_robot 不存在');
        }


        $data = array(
            'appid' => $m_robot->app_id
        );
        $data = json_encode($data, JSON_UNESCAPED_UNICODE);

        $result = Util::curl_request($url, 'POST', $data);

        $result = json_decode($result, true);

        return $result;
    }


    /**
     *
     * *  获取用户列表
     *
     * @param null $next_openid
     * @return bool|mixed
     * @throws \Exception\HTTPException
     */
    public
    function get_user_list($next_openid = null)
    {
        if ($next_openid != null) {
            $url = $this->api_host . '/user/get?access_token={$this->access_token()}&next_openid={$next_openid}';
        } else {
            $url = $this->api_host . '/user/get?access_token=' . $this->access_token();
        }

        $content = Util::curl_request($url, 'GET');

        $content = json_decode($content, true);

        return $content;
    }


    /**
     * 查询用户信息
     *
     * @param $openid
     * @return bool|mixed
     * @throws \Exception\HTTPException
     */
    public
    function get_user_info($openid)
    {

        $url = $this->api_host . '/user/info?access_token=' . $this->access_token() . '&openid=' . $openid . '&lang=zh_CN ';

        $content = Util::curl_request($url, 'GET');

        $content = json_decode($content, true);

        return $content;

    }

    /**
     * 新增临时素材
     *
     *
     * @param $path
     * @param $type
     * @param $title
     * @param $introduction
     * @return bool|mixed
     * @throws \Exception\HTTPException
     */
    public
    function add_temporary_material($path, $type, $title = '', $introduction = '')
    {
        $url = $this->api_host . '/media/upload?access_token=' . $this->access_token() . '&type=' . $type;

        $data = array(
            'media' => new \CURLFile($path),
            'description' => json_encode(array(
                'title' => $title,
                'introduction' => $introduction
            ), JSON_UNESCAPED_UNICODE)
        );

        $content = Util::curl_request($url, 'FILE', $data);

        $content = json_decode($content, true);

        return $content;
    }

//    /**
//     * 新增music素材
//     *
//     * @param $dealership_id
//     * @param $title
//     * @param $description
//     * @param $thumb_media_id
//     * @param $url
//     * @return array
//     */
//    public function add_music_material($dealership_id, $title, $description, $thumb_media_id, $url)
//    {
//
//        $m_wx_material = Model::Material();
//
//        $data = array(
//            'dealership_id' => $dealership_id,
//            'name' => $title,
//            'description' => $description,
//            'type' => 'music',
//            'thumb_media_id' => $thumb_media_id,
//            'url' => $url,
//            'add_time' => time(),
//            'is_permanent' => 2
//        );
//
//        $insertId = $m_wx_material->insert($data);
//
//        return $insertId;
//
//    }

    /**
     * 获取临时素材
     *
     * @param $media_id
     * @return bool|mixed
     * @throws \Exception\HTTPException
     */
    public
    function get_temporary_material($media_id)
    {

        $url = $this->api_host . '/media/get?access_token=' . $this->access_token() . '&media_id=' . $media_id;

        $content = Util::curl_request($url);

        return $content;

    }

    /**
     * 删除永久素材
     *
     * @param $media_id
     * @return bool|mixed|string
     * @throws \Exception\HTTPException
     */
    public
    function delete_material($media_id)
    {
        $url = $this->api_host . '/material/del_material?access_token=' . $this->access_token();

        $data = array(
            'media_id' => $media_id
        );

        $data = json_encode($data, JSON_UNESCAPED_UNICODE);

        $content = Util::curl_request($url, 'POST', $data);

        $content = json_encode($content, true);

        return $content;
    }

    /**
     *  修改永久图文素材
     *
     * @param $media_id
     * @param $index
     * @param $arr
     * @return bool|mixed
     * @throws \Exception\HTTPException
     */
    public
    function updata_news($media_id, $index, $arr)
    {

        $url = $this->api_host . '/material/update_news?access_token=' . $this->access_token();
        $data = array(
            'media_id' => $media_id,
            'index' => $index,
            'articles' => array(
                'title' => $arr['title'],
                'thumb_media_id' => $arr['thumb_media_id'],
                'author' => $arr['author'],
                'digest' => $arr['digest'],
                'show_cover_pic' => $arr['show_cover_pic'],
                'content' => $arr['content'],
                'content_source_url' => $arr['content_source_url']
            )
        );

        $data = json_encode($data, JSON_UNESCAPED_UNICODE);

        $content = Util::curl_request($url, 'POST', $data);

        $content = json_decode($content, true);

        return $content;

    }


}