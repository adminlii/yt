<?php
/**************************************************************
 *
*    使用特定function对数组中所有元素做处理
*    @param  string  &$arr     要处理的字符串
*    @param  string  $function   要执行的函数
*    @return boolean $apply_to_keys_also     是否也应用到key上
*    @access public
*
*************************************************************/
function arrayRecursive(&$arr, $function, $apply_to_keys_also = false)
{
    static $recursive_counter = 0;
    if (++$recursive_counter > 1000) {
        die('possible deep recursion attack');
    }
    foreach ($arr as $key => $value) {
        if (is_array($value)) {
            arrayRecursive($arr[$key], $function, $apply_to_keys_also);
        } else {
            $arr[$key] = $function($value);
        }
         
        if ($apply_to_keys_also && is_string($key)) {
            $new_key = $function($key);
            if ($new_key != $key) {
                $arr[$new_key] = $arr[$key];
                unset($arr[$key]);
            }
        }
    }
    $recursive_counter--;
}

/**************************************************************
 *
*    将数组转换为JSON字符串（兼容中文）
*    @param  array   $arr      要转换的数组
*    @return string      转换得到的json字符串
*    @access public
*
*************************************************************/
function to_json($arr) {
    arrayRecursive($arr, 'urlencode', false);
    $json = json_encode($arr);
    return urldecode($json);
}

function now($day=false){
    return $day?date('Y-m-d'):date('Y-m-d H:i:s');
}
/**
 * 拆分字符串获取门牌号
 * @param unknown_type $add
 */
function getDoorPlat($add){
    if(preg_match('/[0-9]/', $add)){
        $prefix = 1;//1,从第一个数字开始，2，从最后一个数字开始
    
        if($prefix==1){
            if(preg_match('/[0-9]+(.*)?$/', $add,$m)){
                $doorplate = $m[0];
            }
    
        }else{
            if(preg_match_all('/[0-9]+/', $add,$m)){
                //取最后一个数字开始
                $tmp = array_pop($m[0]);
    
                preg_match('/'.$tmp.'([^0-9]+)?$/', $add,$mm);
                $doorplate = $mm[0];
            }
        }
        $newAdd = str_replace($doorplate, '', $add);
        $updateRow = array(
                'Street1' => $newAdd,
                'Street2' => '',
                'Street3' => '',
                'doorplate' => $doorplate
        );
     
        print_r($updateRow);
    }
}

/**
 * 时间切片
 * @param unknown_type $start
 * @param unknown_type $end
 * @param unknown_type $count
 * @return multitype:multitype:number string
 */
function splitDate($start,$end,$count=2){
    $dateArr = array();
    $start = strtotime($start);
    $end = strtotime($end);
    $between = $end-$start;
    $avg = $between/$count;
    for($i=0;$i<$count;$i++){
        $aStart = date('Y-m-d\TH:i:s',$start + $avg * $i-60*5*($i==0?0:1));//5分钟叠加
//         $aStart = date('Y-m-d\TH:i:s',$start + $avg * $i);
        $aEnd = date('Y-m-d\TH:i:s', $start + $avg * ($i+1));
        $aBetween = (strtotime($aEnd)-strtotime($aStart));
        $rs = array(
                'start' => $aStart,
                'end' => $aEnd,
                'between'=>$aBetween,
        );
        $dateArr[] = $rs;
    }
    return $dateArr;
}

/**
 * 测试时间切片
 */
function splitDateTest(){
    $start = '2014-06-25T16:45:00';
    $end = date('Y-m-d\TH:i:s');
    $dateArr = splitDate($start,$end,2);
    $rs = array();
    while(count($dateArr)>0){
        $arr = array_shift($dateArr);
        if($arr['between']>3600){
            $split = splitDate($arr['start'],$arr['end'],2);
            foreach($split as $v){
                array_push($dateArr, $v);
            }
            //         print_r($dateArr);exit;
        }else{
            $rs[] = $arr;
            //         print_r($arr);
        }
    }
    print_r($rs);
    exit;
}
/**
 * 字符串转为驼峰命名
 * @param unknown_type $str
 * @return unknown
 */
function hump($str){
//     $str = lcfirst($str);    
    $str = preg_replace('/([A-Z])/e',"strtolower('-\\1')",$str);
    $str = trim($str,'- :');
    return $str;
}

function array2xml($info, &$xml)
{
    foreach($info as $key => $value){
        if(is_array($value)){
            if(! is_numeric($key)){
                $subnode = $xml->addChild("$key");
                array2xml($value, $subnode);
            }else{
                $subnode = $xml->addChild("item_$key");
                array2xml($value, $subnode);
            }
        }else{
            $xml->addChild("$key", htmlspecialchars("$value"));
        }
    }
}

function array2xmlNew($info, &$xml)
{
    foreach($info as $key => $value){
        if(is_array($value)){
            if(is_numeric($key)){
                $key = array_pop(array_keys($value));
                $value = array_pop($value);
            }
            $subnode = $xml->addChild("{$key}");
            array2xmlNew($value, $subnode);
        }else{
            $xml->addChild("{$key}", htmlspecialchars("$value"));
        }
    }
}

/**
 * XML编码
 * @param mixed $data 数据
 * @param string $root 根节点名
 * @param string $item 数字索引的子节点名
 * @param string $attr 根节点属性
 * @param string $id   数字索引子节点key转换的属性名
 * @param string $encoding 数据编码
 * @return string
 */
function xml_encode($data, $root='root', $item='item', $attr='', $id='id', $encoding='utf-8') {
	if(is_array($attr)){
		$_attr = array();
		foreach ($attr as $key => $value) {
			$_attr[] = "{$key}=\"{$value}\"";
		}
		$attr = implode(' ', $_attr);
	}
	$attr   = trim($attr);
	$attr   = empty($attr) ? '' : " {$attr}";
	$xml    = "<?xml version=\"1.0\" encoding=\"{$encoding}\"?>";
	$xml   .= "<{$root}{$attr}>";
	$xml   .= data_to_xml($data, $item, $id);
	$xml   .= "</{$root}>";
	return $xml;
}

/**
 * 数据XML编码
 * @param mixed  $data 数据
 * @param string $item 数字索引时的节点名称
 * @param string $id   数字索引key转换为的属性名
 * @return string
 */
function data_to_xml($data, $item='item', $id='id') {
	$xml = $attr = '';
	foreach ($data as $key => $val) {
		if(is_numeric($key)){
			$id && $attr = " {$id}=\"{$key}\"";
			$key  = $item;
		}
		$xml    .=  "<{$key}{$attr}>";
		$xml    .=  (is_array($val) || is_object($val)) ? data_to_xml($val, $item, $id) : $val;
		$xml    .=  "</{$key}>";
	}
	return $xml;
}

function filter_input_m($data,$filter_method = false){
	$filter_method = $filter_method===false?FILTER_METHOD:$filter_method;
	//如果过滤方法列表为空直接跳出
	if(empty($filter_method))
		return $data;
	if(is_array($data)&&!empty($data)){
		foreach ($data as $k=>$v){
			$data[$k] = filter_input_m($v,$filter_method);
		}
	}else if(is_string($data)){
		$filter_methodArr = strpos($filter_method, ',')===false?$filter_method:explode(',',$filter_method);
		if(!empty($filter_methodArr)){
			foreach ($filter_methodArr as $fmav){
				if(function_exists($fmav)){
					$data = call_user_func($fmav, $data);
				}
			}
		}
	}
	return $data;
}

function create_guid() {
	$charid = strtoupper(md5(uniqid(mt_rand(), true)));
	$hyphen = chr(45);// "-"
	$uuid = ''// "{"
	.substr($charid, 0, 8).$hyphen
	.substr($charid, 8, 4).$hyphen
	.substr($charid,12, 4).$hyphen
	.substr($charid,16, 4).$hyphen
	.substr($charid,20,12)
	.'';// "}"
	return $uuid;
}
//换号规则
function change_no($shipper_no){
	$_shipper_no = array();
	$jiaquan     = array(8,6,4,2,3,5,9,7);
	$sum = 0;
	//加上第九位验证
	for($i = 0 ;$i <8;$i++){
		if($i==7)
			$isexit = $shipper_no%10;
		else{
			$isexit = intval($shipper_no/pow(10,7-$i));
			$shipper_no =$shipper_no%pow(10,7-$i);
		}
		$_shipper_no[] = $isexit;
		$sum += $isexit*$jiaquan[$i];
	}
	$yushu = 11-$sum%11;
	if($yushu==11){
		$yushu = 5;
	}else if($yushu==10){
		$yushu = 0;
	}
	$_shipper_no[] = $yushu;
	return join('', $_shipper_no);
}

function xml_filter_c($value){

	$_value = str_replace("&", "&amp;", $value);
	$_value = str_replace("<", "&lt;", $_value);
	$_value = str_replace(">", "&gt;", $_value);
	$_value = str_replace('"', "&quot;", $_value);
	$_value = str_replace("'", "&apos;", $_value);
	return $_value;
}

function xml_filter(&$value,$k){

	$_value = str_replace("&", "&amp;", $value);
	$_value = str_replace("<", "&lt;", $_value);
	$_value = str_replace(">", "&gt;", $_value);
	$_value = str_replace('"', "&quot;", $_value);
	$_value = str_replace("'", "&apos;", $_value);
	$value = $_value;
}

function xml_filterInArr($arr){
	array_walk_recursive($arr,'xml_filter');
	return $arr;
}