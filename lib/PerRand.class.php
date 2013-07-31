<?php
//计算概率方法
class PerRand
{
	public static function getRandResultKey($data)
	{
		if(!is_array($data) || !$data)return FALSE;
		$data = array_filter($data, 'Utility::arrayFilterFloat');
		$getEveryPercentArr = array_values($data);
		$totalPercent = array_sum($getEveryPercentArr);
		if($totalPercent > 1)return FALSE;
		$ration = Utility::getChangeIntRation($getEveryPercentArr);
	    $randCheckList = self::_createRandResultArray($data, $ration);
	    $randNum = self::_getRandNum(1, $ration);
	    return self::_checkIsHit($randNum, $randCheckList);
	}

	private static function _createRandResultArray($data, $times)
	{
		$lastEnd = 1;
	    foreach ($data as $key => $value) 
	    {
	        $value = $value * $times;
	        if(!$value)continue;
	        $begin = $lastEnd;
	        $end   = $lastEnd + $value - 1;
	        $randCheckList[$key] = array($begin ,$end);
	        $lastEnd = $end + 1;
	    }
	    return $randCheckList;
	}

	private static function _getRandNum($begin = 1, $end)
	{
		return mt_rand($begin, $end);
	}

	private static function _checkIsHit($randNum, $checkArray)
	{
		if(!$randNum || !is_array($checkArray))return FALSE;
		foreach ($checkArray as $key => $value) {
	        if($randNum > $value[0] && $randNum <= $value[1])
	        {
	          return $key;
	        }
	    }
	    return FALSE;
	}
}