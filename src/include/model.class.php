<?php   if(!defined('DEDEINC')) exit("Request Error!");
/**
 * 模型基类
 *
 * @version        $Id: model.class.php 1 13:46 2010-12-1 tianya $
 * @package        DedeCMS.Libraries
 * @copyright      Copyright (c) 2007 - 2018, DesDev, Inc.
 * @copyright      Copyright (c) 2020, DedeBIZ.COM
 * @license        https://www.dedebiz.com/license/v6
 * @link           https://www.dedebiz.com
 */

class Model
{
    var $dsql;
    var $db;

    function __construct()
    {
        $this->Model();
    }

    // 析构函数
    function Model()
    {
        global $dsql;
        if ($GLOBALS['cfg_mysql_type'] == 'mysqli')
        {
            $this->dsql = $this->db = isset($dsql)? $dsql : new DedeSqli(FALSE);
        } else {
            $this->dsql = $this->db = isset($dsql)? $dsql : new DedeSql(FALSE);
        }

    }

    // 释放资源
    function __destruct()
    {
        $this->dsql->Close(TRUE);
    }
}
