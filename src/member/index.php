<?php
/**
 * @version        $Id: index.php 1 8:24 2010年7月9日Z tianya $
 * @package        DedeCMS.Member
 * @copyright      Copyright (c) 2007 - 2020, DesDev, Inc.
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require_once(dirname(__FILE__)."/config.php");

$uid=empty($uid)? "" : RemoveXSS($uid); 
if(empty($action)) $action = '';
if(empty($aid)) $aid = '';

$menutype = 'mydede';
if ( preg_match("#PHP (.*) Development Server#",$_SERVER['SERVER_SOFTWARE']) )
{
    if ( $_SERVER['REQUEST_URI'] == dirname($_SERVER['SCRIPT_NAME']) )
    {
        header('HTTP/1.1 301 Moved Permanently');
        header('Location:'.$_SERVER['REQUEST_URI'].'/');
    }
}
//会员后台
if($uid=='')
{
    $iscontrol = 'yes';
    if(!$cfg_ml->IsLogin())
    {
        include_once(dirname(__FILE__)."/templets/index-notlogin.htm");
    }
    else
    {
        $minfos = $dsql->GetOne("SELECT * FROM `#@__member_tj` WHERE mid='".$cfg_ml->M_ID."'; ");
        $minfos['totaluse'] = $cfg_ml->GetUserSpace();
        $minfos['totaluse'] = number_format($minfos['totaluse']/1024/1024,2);
        if($cfg_mb_max > 0) {
            $ddsize = ceil( ($minfos['totaluse']/$cfg_mb_max) * 100 );
        }
        else {
            $ddsize = 0;
        }

        require_once(DEDEINC.'/channelunit.func.php');

        /* 最新文档8条 */
        $archives = array();
        $sql = "SELECT arc.*, category.namerule, category.typedir, category.moresite, category.siteurl, category.sitepath, mem.userid
        FROM #@__archives arc
        LEFT JOIN #@__arctype category ON category.id=arc.typeid
        LEFT JOIN #@__member mem ON mem.mid=arc.mid
        WHERE arc.arcrank > -1
        ORDER BY arc.sortrank DESC LIMIT 8";
        $dsql->SetQuery($sql);
        $dsql->Execute();
        while ($row = $dsql->GetArray())
        {
            $row['htmlurl'] = GetFileUrl($row['id'], $row['typeid'], $row['senddate'], $row['title'], $row['ismake'], $row['arcrank'], $row['namerule'], $row['typedir'], $row['money'], $row['filename'], $row['moresite'], $row['siteurl'], $row['sitepath']);
            $archives[] = $row;
        }

        /** 调用访客记录 **/
        $_vars['mid'] = $cfg_ml->M_ID;
        
        if(empty($cfg_ml->fields['face']))
        {
            $cfg_ml->fields['face']=($cfg_ml->fields['sex']=='女')? 'templets/images/dfgirl.png' : 'templets/images/dfboy.png';
        }

        /** 我的收藏 **/
        $favorites = array();
        $dsql->Execute('fl',"SELECT * FROM `#@__member_stow` WHERE mid='{$cfg_ml->M_ID}'  LIMIT 5");
        while($arr = $dsql->GetArray('fl'))
        {
            $favorites[] = $arr;
        }
        
        /** 欢迎新朋友 **/
        $sql = "SELECT * FROM `#@__member` ORDER BY mid DESC LIMIT 3";
        $newfriends = array();
        $dsql->SetQuery($sql);
        $dsql->Execute();
        while ($row = $dsql->GetArray()) {
            $newfriends[] = $row;
        }
        
        /** 有没新短信 **/
        $pms = $dsql->GetOne("SELECT COUNT(*) AS nums FROM #@__member_pms WHERE toid='{$cfg_ml->M_ID}' AND `hasview`=0 AND folder = 'inbox'");    
        
        $dpl = new DedeTemplate();
        $tpl = dirname(__FILE__)."/templets/index.htm";
        $dpl->LoadTemplate($tpl);
        $dpl->display();
    }
}