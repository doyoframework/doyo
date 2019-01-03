<?php

namespace Core;

class BaseEntity
{

    /**
     * 使用的数据库配置
     *
     * @var
     */
    var $DB_CONFIG;

    /**
     * 表前缀
     *
     * @var
     */
    var $TABLE_PREFIX;

    /**
     * 主键的key
     *
     * @var string
     */
    var $PRIMARY_KEY;

    /**
     * 主键的val
     *
     * @var int
     */
    var $PRIMARY_VAL = 0;

}