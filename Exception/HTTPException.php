<?php

namespace Exception;

class HTTPException extends \Exception
{

    /**
     * 扩展信息
     *
     * @var array
     */
    private $errData = null;

    /**
     * 错误码
     *
     * @var int
     */
    private $errCode = -1;

    public function __construct($message, $errCode = -1, $errData = null)
    {
        if ($errData != null) {
            $this->errData = $errData;
        }

        $this->errCode = $errCode;

        parent::__construct($message);

    }

    /**
     * 获取扩展信息
     *
     * @return array
     */
    public function errData()
    {
        return $this->errData;
    }

    /**
     * 返回错误码
     *
     * @return int
     */
    public function errCode()
    {
        return $this->errCode;
    }
}

