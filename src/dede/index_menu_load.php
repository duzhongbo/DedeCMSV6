<?php
/**
 * 载入菜单
 *
 * @version        $Id: index_menu_load.php 1 8:48 2010年7月13日Z tianya $
 * @package        DedeCMS.Administrator
 * @copyright      Copyright (c) 2007 - 2018, DesDev, Inc.
 * @copyright      Copyright (c) 2020, DedeBIZ.COM
 * @license        https://www.dedebiz.com/license/v6
 * @link           https://www.dedebiz.com
 */
require_once(dirname(__FILE__).'/config.php');
AjaxHead();
if($openitem != 100)
{
    require(dirname(__FILE__).'/inc/inc_menu.php');
    require(DEDEADMIN.'/inc/inc_menu_func.php');
    GetMenus($cuserLogin->getUserRank(),'main');
    exit();
}
else
{
    $openitem = 0;
    require(dirname(__FILE__).'/inc/inc_menu_module.php');
    require(DEDEADMIN.'/inc/inc_menu_func.php');
    GetMenus($cuserLogin->getUserRank(),'module');
    exit();
}