<?php
if (!defined('DEDEINC')) {
    exit("Request Error!");
}
/**
 * 调用最新评论
 *
 * @version        $Id: feedback.lib.php 1 9:29 2010年7月6日Z tianya $
 * @package        DedeCMS.Taglib
 * @copyright      Copyright (c) 2020, DedeBIZ.COM
 * @license        https://www.dedebiz.com/license
 * @link           https://www.dedebiz.com
 */

/*>>dede>>
<name>会员评论内容</name>
<type>全局标记</type>
<for>V55,V56,V57</for>
<description>用于调用最新评论</description>
<demo>
{dede:feedback}
 <ul>
  <li class='fbtitle'>[field:username function="(@me=='guest' ? '游客' : @me)"/] 对 [field:title/] 的评论：</li>
  <li class='fbmsg'> <a href="plus/feedback.php?aid=[field:aid/]" class='fbmsg'>[field:msg /]</a></li>
 </ul>
{/dede:feedback}
</demo>
<attributes>
    <iterm>row:调用评论条数</iterm> 
    <iterm>titlelen:标题长度</iterm>
    <iterm>infolen:评论长度</iterm>
</attributes> 
>>dede>>*/

function lib_feedback(&$ctag, &$refObj)
{
    global $dsql;

    $attlist = "row|12,titlelen|24,infolen|100,orderby|";
    FillAttsDefault($ctag->CAttribute->Items, $attlist);
    extract($ctag->CAttribute->Items, EXTR_SKIP);
    $innertext = trim($ctag->GetInnerText());
    $totalrow = $row;
    $revalue = '';
    if (empty($innertext)) {
        $innertext = GetSysTemplets('tag_feedback.htm');
    }
    $order = " ORDER BY id DESC";
    if (!empty($orderby)) {
        switch ($orderby) {
            case 'good':
                $order = " ORDER BY good DESC";
                break;
            case 'time':
                $order = " ORDER BY dtime DESC";
                break;
            default:
                $order = " ORDER BY id DESC";
                break;
        }
    }
    $wsql = " WHERE fb.ischeck=1 AND fb.fid=0 ";

    if (get_class($refObj) === "Archives" && isset($refObj->Fields["id"])) {
        $wsql .= " AND fb.aid='{$refObj->Fields["id"]}'";
    }

    $equery = "SELECT fb.*,mb.userid,mb.face as mface,mb.spacesta,mb.scores,mb.sex FROM `#@__feedback` fb
    LEFT JOIN `#@__member` mb on mb.mid = fb.mid $wsql $order LIMIT 0 , $totalrow";

    $ctp = new DedeTagParse();
    $ctp->SetNameSpace('field', '[', ']');
    $ctp->LoadSource($innertext);

    $dsql->Execute('fb', $equery);
    while ($arr = $dsql->GetArray('fb')) {
        $arr['face'] = empty($arr['mface']) ? $GLOBALS['cfg_cmspath'] . '/static/img/avatar.png' : $arr['mface'];
        $arr['title'] = cn_substr($arr['arctitle'], $titlelen);
        $arr['msg'] = jsTrim(Html2Text($arr['msg']), $infolen);
        foreach ($ctp->CTags as $tagid => $ctag) {
            $ctp->Assign($tagid, $arr[$ctag->GetName()]);
        }
        $revalue .= $ctp->GetResult();
    }
    return $revalue;
}

function jsTrim($str, $len)
{
    $str = preg_replace("/{quote}(.*){\/quote}/is", '', $str);
    $str = str_replace('&lt;br/&gt;', ' ', $str);
    $str = cn_substr($str, $len);
    $str = preg_replace("#['\"\r\n]#", "", $str);
    return $str;
}
