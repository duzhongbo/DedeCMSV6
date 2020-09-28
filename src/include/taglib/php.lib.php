<?php
if (!defined('DEDEINC'))
    exit('Request Error!');
/**
 * 
 *
 * @version        $Id: php.lib.php1 9:29 2010年7月6日Z tianya $
 * @package        DedeCMS.Taglib
 * @copyright      Copyright (c) 2007 - 2018, DesDev, Inc.
 * @copyright      Copyright (c) 2020, DedeBIZ.COM
 * @license        https://www.dedebiz.com/license/v6
 * @link           https://www.dedebiz.com
 */
 
 /*>>dede>>
<name>PHP代码标签</name>
<type>全局标记</type>
<for>V55,V56,V57</for>
<description>调用PHP代码</description>
<demo>
{dede:php}
$a = "dede";
echo $a;
{/dede:php}
</demo>
<attributes>
</attributes> 
>>dede>>*/
 
function lib_php(&$ctag, &$refObj)
{
    global $dsql;
    global $db;
    $phpcode = trim($ctag->GetInnerText());
    if ($phpcode == '')
        return '';
    ob_start();
    extract($GLOBALS, EXTR_SKIP);
    @eval($phpcode);
    $revalue = ob_get_contents();
    ob_clean();
    return $revalue;
}