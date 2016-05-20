<?php
class Common_CustomerFeeProcess
{
	/**
	 * 人民币币值
	 * @var unknown_type
	 */
	public static $currency_rmb = 'RMB';
	
	/**
	 * @desc 同步实时汇率
	 * @return array/String
	 */
	public  static function GetCurrency()
	{
		if (!function_exists('curl_init')) {
			return 'server not install curl';
		}
		$url = "http://download.finance.yahoo.com/d/quotes.csv?e=.csv&f=sl1d1t1&s=USDCNY=x+EURCNY=x+AUDCNY=x+GBPCNY=x+HKDCNY=x";
		$handle = curl_init($url);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ($handle, CURLOPT_CONNECTTIMEOUT, 300);
		$result = curl_exec($handle);
		@curl_close($handle);
		$rows = explode("\n", $result);
		$curr=array();
		if(is_array($rows)){
			foreach($rows as $row){
				$rArr=explode(',',$row);
				if(isset($rArr[1])){
					$curr[str_replace('"',"",str_ireplace('CNY=X','',$rArr[0]))]=$rArr[1];
				}
			}
		}
		return $curr;
	}
	
    /**
     * @desc 币种与金额转换
     * @param $value 转换金额
     * @param $orgCode 原始货币CODE
     * @param $disCode 目的货币CODE
     * @param int $orgRate 自定义原货币汇率
     * @param int $disRate 自定义目的原货币汇率
     * @return array/string  string=>则表示转换失败
     */
    public static function changeCurrency($value, $orgCode, $disCode, $orgRate = 0, $disRate = 0)
    {

        $orgCode = strtoupper($orgCode);
        $disCode = strtoupper($disCode);
        $currencyRows = Service_Currency::getByCondition(array(), array('currency_code', 'currency_rate'));
        foreach ($currencyRows as $key => $val) {
            $currencyRows[$val['currency_code']] = $val['currency_rate'];
        }
        if ($orgRate != 0 && $orgRate != '') {
            $org_rate = $orgRate;
        } else {
            $org_rate = isset($currencyRows[$orgCode]) ? $currencyRows[$orgCode] : 0;
        }
        if ($disRate != 0 && $disRate != '') {
            $dis_rate = $disRate;
        } else {
            $dis_rate = isset($currencyRows[$disCode]) ? $currencyRows[$disCode] : 0;
        }
        if ($orgCode == $disCode) {
            $result['value'] = $value;
            $result['currency_code'] = $disCode;
            $result['org_code'] = $orgCode;
            $result['org_rate'] = $org_rate;
            $result['dis_rate'] = $org_rate;
            $result['rate'] = 1;
            return $result;
        }

        if (!isset($currencyRows[$orgCode]) || !isset($currencyRows[$disCode])) {
            $result = 'Can not find origin/destination currency code';
        } else {
            //取小数后两位，不作四舍五入
            $result['org_rate'] = $org_rate;
            $result['dis_rate'] = $dis_rate;
            $result['rate'] = sprintf("%.5f", $org_rate / $dis_rate);
            $result['value'] = sprintf("%.3f", $value * $result['rate']);
            $result['currency_code'] = $disCode;
            $result['org_code'] = $orgCode;
        }
        return $result;
    }

}