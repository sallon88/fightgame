<?php
//显示装备列表
include $_SERVER['DOCUMENT_ROOT'].'/init.inc.php';

$userId     = isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : 0;

try {
	$res = Equip_Info::getEquipByUserId($userId);
	$code = 0;
	$msg = 'OK';
} catch (Exception $e) {
	$code = 1;
	$msg = '未查询到装备信息';
}
