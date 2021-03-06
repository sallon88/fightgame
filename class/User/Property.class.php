<?php
class User_Property{
	
	CONST TABLE_NAME = 'property_info';
	
	/** 属性增强咒符*/
	CONST ATTRIBUTE_ENHANCE = 1;
	CONST ATTRIBUTE_ENHANCE_PRICE = 10;
	/** 双倍咒符 */
	CONST DOUBLE_HARVEST = 2;
	CONST DOUBLE_HARVEST_PRICE = 10;
	/** 挂机咒符 */
	CONST AUTO_FIGHT = 3;
	CONST AUTO_FIGHT_PRICE = 10;
	/** 装备打造咒符 */
	CONST EQUIP_FORGE = 4;
	/** 装备成长咒符 */
	CONST EQUIP_GROW = 5;
	
	//上古遗迹没考虑清楚,暂时没做
	
	/**
	 * @param int $userId	用户ID
	 * @param int $type		符咒类别
	 * @return 购买符咒
	 */
	public static function addAmulet($userId, $type)
	{
		if(!$userId || !$type)return FALSE;
		
		//是否有足够元宝
		$userInfo = User_Info::getUserInfoByUserId($userId);
//		if($userInfo['ingot'] < self::$type.'_PRICE')return FALSE;
		
		$sql = "UPDATE " . self::TABLE_NAME . " SET `property_num` =  `property_num` + 1 WHERE user_id = $userId AND property_id = $type";
//		echo $sql;exit;
		$res = MySql::query($sql);
	}
	
	/**
	 * @param int $userId	用户ID
	 * @param int $type		符咒类别
	 * @return 使用符咒
	 */
	public static function UseAmulet($userId, $type)
	{
		if(!$userId || !$type)return FALSE;
		
		$num = self::getPropertyNum($userId, $type);
		if($num <= 0)return FALSE;
		
		$sql = "UPDATE " . self::TABLE_NAME . " SET `property_num` =  `property_num` - 1 WHERE user_id = $userId AND property_id = $type";
//		echo $sql;exit;
		$res = MySql::query($sql);
	}
	
	/**
	 * @param int $userId	用户ID
	 * @param int $type		符咒类别
	 * @return 获取某符咒数量
	 */
	public static function getPropertyNum($userId, $type)
	{
		if(!$userId || !$type)return FALSE;
		
		$num = MySql::selectOne(self::TABLE_NAME, array('user_id' => $userId, 'property_id' => $type), array('property_num'));
		return $num['property_num'];
	}
	
	public static function createPropertylist($userId, $type){
		if(!$userId || !$type)return FALSE;
		
		$res = MySql::insert(self::TABLE_NAME, array('user_id' => $userId, 'property_id' => $type));
		return $res;
	}
	
	/**
	 * 显示拥有符咒信息
	 *
	 * @param int $userId	用户ID
	 * @return 显示拥有符咒信息
	 */
	public static function getUserProperty($userId)
	{
		if(!$userId)return FALSE;
		
		$propertyInfo = MySql::select(self::TABLE_NAME, array('user_id' => $userId), array('property_id', 'property_num'));
		
		if(!is_array($propertyInfo))return FALSE;
		
		return $propertyInfo;
	}
	
	/**
	 * 购买背包上限
	 * @param int $userId
	 * @return 购买背包上限
	 */
	public static function buyPackNum($userId)
	{
		if(!$userId)return FALSE;
		
		//验证是否已到最大购买数
		$userInfo = User_Info::getUserInfoByUserId($userId);
		if($userInfo['pack_num'] >= User::DEFAULT_PACK_MAX)return FALSE;
		
		//验证是否有足够元宝购买
		if($userInfo['ingot'] < User::PACK_PRICE)return FALSE;
		
		$res_num = User_Info::updateSingleInfo($userId, 'pack_num', 5, '+');//增加包裹数
		$res_ingot = User_Info::updateSingleInfo($userId, 'ingot', User::PACK_PRICE, '-');//减少相应元宝 
		
		if($res_ingot && $res_num)return TRUE;
	}
	
	/**
	 * 购买好友上限
	 * @param int $userId
	 * @return 购买好友上限
	 */
	public static function buyFriendNum($userId)
	{
		if(!$userId)return FALSE;
		
		//验证是否已到最大购买数
		$userInfo = User_Info::getUserInfoByUserId($userId);
		if($userInfo['friend_num'] >= User::DEFAULT_FRIEND_MAX)return FALSE;
		
		//验证是否有足够元宝购买
		if($userInfo['ingot'] < User::FRIEND_PRICE)return FALSE;
		
		$res_num = User_Info::updateSingleInfo($userId, 'friend_num', 1, '+');//增加包裹数
		$res_ingot = User_Info::updateSingleInfo($userId, 'ingot', User::FRIEND_PRICE, '-');//减少相应元宝 
		
		if($res_ingot && $res_num)return TRUE;
	}
	
	/**
	 * 购买人宠上限
	 * @param int $userId
	 * @return 购买人宠上限
	 */
	public static function buyPetNum($userId)
	{
		if(!$userId)return FALSE;
		
		//验证是否已到最大购买数
		$userInfo = User_Info::getUserInfoByUserId($userId);
		if($userInfo['pet_num'] >= User::DEFAULT_PET_MAX)return FALSE;
		
		//验证是否有足够元宝购买
		if($userInfo['ingot'] < User::PET_PRICE)return FALSE;
		
		$res_num = User_Info::updateSingleInfo($userId, 'pet_num', 1, '+');//增加包裹数
		$res_ingot = User_Info::updateSingleInfo($userId, 'ingot', User::PET_PRICE, '-');//减少相应元宝 
		
		if($res_ingot && $res_num)return TRUE;
	}
	
	/**
	 * 购买PK次数
	 * @param int	$userId		用户ID
	 * @param int	$num		购买次数,默认为1
	 * @return 购买PK次数
	 */
	public static function buyPkNum($userId, $num = FALSE)
	{
		if(!$userId)return FALSE;
		
		$num = isset($num)?$num:"1";
		
		//根据流水表检测已经购买次数
		$alreadyBuyNum = 2;
		if($alreadyBuyNum >= User::PK_BUY_NUM)return FALSE;
		
		$res_num = User_Info::updateSingleInfo($userId, 'pk_num', $num, '+');
		$res_ingot = User_Info::updateSingleInfo($userid, 'ingot', User::PET_PRICE * $num, '-');
		
		if($res_num && $res_ingot)return TRUE;
	}
		
	/** 
	 * @return 使用属性增强咒符*/
	public static function useAttributeEnhance($userId)
	{
		if(!$userId)return FALSE;
		
		//是否已经在用
		$isUse = self::isuseAttributeEnhance($userId);
		if(!empty($isUse))return FALSE;
		
		//是否还有存数
		$isHave = self::getPropertyNum($userId, self::ATTRIBUTE_ENHANCE);
		if(empty($isHave))return FALSE;
		
		$res = MySql::insert('attribute_enhance', array('user_id' => $userId, 'add_time' => time()));
		$res_num = self::UseAmulet($userId, self::ATTRIBUTE_ENHANCE);
		
		if(!$res && !$res_num)return FALSE;
		return $res;
	}
	/** 检测是否在使用属性增强符咒
	 * @return 检测是否在使用属性增强符咒*/
	public static function isuseAttributeEnhance($userId)
	{
		if(!$userId)return FALSE;
		
		$res = MySql::selectOne('attribute_enhance', array('user_id' => $userId));
		
		if(!$res){//没有记录
			return FALSE;
		}elseif( (time() - User::ATTEIBUTEENHANCETIME) > $res['add_time'] ){//删除过期记录
			MySql::delete('attribute_enhance', array('user_id' => $userId));
			return FALSE;
		}else{
			return TRUE;
		}
	}
	
	/** 
	 * @return 使用双倍符咒*/
	public static function useDoubleHarvest($userId)
	{
		if(!$userId)return FALSE;
		
		//是否已经在用
		$isUse = self::isuseDoubleHarvest($userId);
		if(!empty($isUse))return FALSE;
		
		//是否还有存数
		$isHave = self::getPropertyNum($userId, self::DOUBLE_HARVEST);
		if(empty($isHave))return FALSE;
		
		$res = MySql::insert('double_harvest', array('user_id' => $userId, 'add_time' => time()));
		$res_num = self::UseAmulet($userId, self::DOUBLE_HARVEST);
		return $res;
	}
	/** 检测是否在使用挂机符咒
	 * @return 检测是否在使用双倍符咒*/
	public static function isuseDoubleHarvest($userId)
	{
		if(!$userId)return FALSE;
		
		$res = MySql::selectOne('double_harvest', array('user_id' => $userId));
		
		if(!$res){//没有记录
			return FALSE;
		}elseif( (time() - User::DOUBLEHARVESTTIME) > $res['add_time'] ){//删除过期记录
			MySql::delete('double_harvest', array('user_id' => $userId));
			return FALSE;
		}else{
			return TRUE;
		}
	}
	
	/** 
	 * @return 使用挂机符咒*/
	public static function useAutoFight($userId)
	{
		if(!$userId)return FALSE;

		//是否已经在用
		$isUse = self::isuseAutoFight($userId);
		if(!empty($isUse))return FALSE;

		//是否还有存数
		$isHave = self::getPropertyNum($userId, self::AUTO_FIGHT);
		if(empty($isHave))return FALSE;

		$res = MySql::insert('auto_fight', array('user_id' => $userId, 'add_time' => time()));
		$res_num = self::UseAmulet($userId, self::AUTO_FIGHT);
		return $res;
	}
	/** 检测是否在使用挂机符咒
	 * @return 检测是否在使用挂机符咒*/
	public static function isuseAutoFight($userId)
	{
		if(!$userId)return FALSE;
		
		$res = MySql::selectOne('auto_fight', array('user_id' => $userId));
		
		if(!$res){//没有记录
			return FALSE;
		}elseif( (time() - User::AUTOFIGHTTIME) > $res['add_time'] ){//删除过期记录
			MySql::delete('auto_fight', array('user_id' => $userId));
			return FALSE;
		}else{
			return TRUE;
		}
	}
	
	/**
	 * 装备成长咒符
	 */
	
	/**
	 * 装备打造咒符
	 */
		
	/**
	 * 上古遗迹(宝箱)
	 */
}