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

    public function __construct($message, $data = null)
    {

        if ($data != null) {
            $this->data = $data;
        }

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

}