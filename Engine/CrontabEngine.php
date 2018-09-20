<?php

namespace Engine;

use Core\Util;

class CrontabEngine
{

    private $handle;

    private $tags;

    /**
     * CrontabEngine constructor.
     * @param $tags
     * @param string $config
     * @throws \Exception\HTTPException
     */
    public function __construct($tags, $config)
    {

        $this->handle = Util::loadRedis($config);

        $this->tags = $tags;

    }

    /**
     * 任务列表
     *
     * @return array
     */
    public function listed()
    {
        $members = $this->handle->setMembers($this->tags);

        $data = array();

        foreach ($members as $member) {
            $key = $this->tags . '_' . $member;

            if ($this->handle->exists($key)) {

                $data[$member] = json_decode($this->handle->get($key), true);

            } else {

                $this->del($member);

            }

        }

        return $data;
    }

    /**
     * @param $member
     * @param $op
     * @param $param
     * @param $expire
     * @throws \Exception\HTTPException
     */
    public function exec($member, $op, $param, $expire)
    {

        if (!is_array($param)) {
            throw Util::HTTPException('param not array');
        }

        $data = array();
        $data['op'] = $op;
        $data['param'] = $param;
        $data['CREATE'] = time();
        $data['EXPIRE'] = $expire;

        $exists = $this->handle->setIsMember($this->tags, $member);

        if (!$exists) {
            $this->handle->setAdd($this->tags, $member);
        }

        $key = $this->tags . '_' . $member;

        $this->handle->set($key, json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    /**
     * 删除一个定时任务
     *
     * @param $member
     */
    public function del($member)
    {
        $exists = $this->handle->setIsMember($this->tags, $member);

        if (!$exists) {
            return;
        }

        $this->handle->setRem($this->tags, $member);

        $key = $this->tags . '_' . $member;

        $this->handle->delete($key);

    }

}

