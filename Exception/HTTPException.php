<?php

namespace Exception;

class HTTPException extends \Exception
{

    /**
     * 扩展信息
     *
     * @var array
     */
    private $data = null;

    /**
     * 错误码
     *
     * @var int
     */
    private $code = -1;

    public function __construct($message, $code = -1, $data = null)
    {
        if ($data != null) {
            $this->data = $data;
        }

        $this->code = $code;

        parent::__construct($message);

    }

    /**
     * 获取扩展信息
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * 返回错误码
     *
     * @return int
     */
    public function code()
    {
        return $this->code;
    }
}
