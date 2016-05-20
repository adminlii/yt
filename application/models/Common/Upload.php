<?php
require_once("PHPExcel.php");
require_once("PHPExcel/Reader/Excel2007.php");
require_once("PHPExcel/Reader/Excel5.php");
// require_once ('PHPExcel/IOFactory.php');
class Common_Upload
{
    /**
     * 读取CSV文件
     * @param string $filePath
     * @return array
     */
    public static function readCSV($filePath)
    {
        $content = file_get_contents($filePath);
        $arr = preg_split('/\n/', $content);
        $data = array();
        foreach ($arr as $k => $v) {
            if ($v) {
                $data[] = explode(",", trim($v));
            }
        }
        return $data;
    }

    public  static function getExcelColumn(){
        $keyArr = array();
        $str = "A B C D E F G H I J K L M N O P Q R S T U V W X Y Z";
        $str = trim($str);
        // $str = preg_replace('/\s+/',' ',$str);
        $rs = preg_split('/\s+/', $str);
        foreach($rs as $v){
            $keyArr[] = $v;
        }
        foreach($rs as $v){
            foreach($rs as $vv){
                $keyArr[] = $v . $vv;
            }
        }
//         foreach($rs as $v){
//             foreach($rs as $vv){
//                 foreach($rs as $vvv){
//                     $keyArr[] = $v . $vv . $vvv;
//                 }
//             }
//         }
//         foreach($rs as $v){
//             foreach($rs as $vv){
//                 foreach($rs as $vvv){
//                     foreach($rs as $vvvv){
//                         $keyArr[] = $v . $vv . $vvv . $vvvv;
//                     }
//                 }
//             }
//         }
        return $keyArr;
    }
    /**
     * 读取EXCEL文件
     * @param string $filePath
     * @return array
     */
    public static function readEXCEL($filePath,$sheet=0,$ignoreEmptyLine=true,$keyResort=true)
    {
        if(!file_exists($filePath)){
            return "File Not Exists..";
        }
        
        // 设置内存缓存
        PHPExcel_Settings::setCacheStorageMethod();
        $PHPExcel = new PHPExcel();
        $PHPReader = new PHPExcel_Reader_Excel2007();
        if (!$PHPReader->canRead($filePath)) {
            $PHPReader = new PHPExcel_Reader_Excel5();
            if (!$PHPReader->canRead($filePath)) {
                return Ec::Lang('Invalid File');
            }
        }
        $PHPExcel = $PHPReader->load($filePath);
        if(is_int($sheet)){
            $currentSheet = $PHPExcel->getSheet($sheet);
        }else{
            $currentSheet = $PHPExcel->getSheetByName($sheet);
        }
       
        if(!$currentSheet){
            return Ec::Lang('Invalid Sheet');
        }
// 		$keyArr="A B C D E F G H I J K L M N O P Q R S T U V W X Y Z AA AB AC AD AE AF AG AH AI AJ AK AL AM AN AO AP AQ AR AS AT AU AV AW AX AY AZ BA BB BC BD BE BF BG BH BI BJ BK BL BM BN BO BP BQ BR BS BT BU BV BW BX BY BZ CA CB CC CD CE CF CG CH CI CJ CK CL CM CN CO CP CQ CR CS CT CU CV CW CX CY CZ DA DB DD DC DE DF DG DH DI DJ DK DL DM DN DO DP DQ DR DS DT DU DV DW DX DY DZ EA EB EC ED EE EF EG EH EI EJ EK EL EM EN EO EP EQ ER ES ET EU EV EW EX EY EZ FA FB FC FD FE FF FG FH FI FJ FK FL FM FN FO FP FQ FR FS FT FU FV FW FX FY FZ GA GB GC GD GE GF GG GH GI GJ GK GL GM GN GO GP GQ GR GS GT GU GV GW GX GY GZ HA HB HC HD HE HF HG HH HI HJ HK HL HM HN HO HP HQ HR HS HT HU HV HW HX HY HZ";
// 		$keyArr = explode(' ', $keyArr);
//         print_r($keyArr);exit;
		$keyArr = Common_Upload::getExcelColumn();
//         print_r($keyArr);exit;
		$keyArrFlip = array_flip($keyArr);
// 		print_r($keyArrFlip);exit;
		/**取得一共有多少列*/
        $maxColumn = $currentSheet->getHighestColumn();
        /**取得一共有多少行*/
        $rowCount = $currentSheet->getHighestRow();
        $result = array();
        for ($row = 1; $row <= $rowCount; $row++) {
            $totalLen = 0; //记录行总长度
            for ($column = $keyArrFlip['A']; $column <= $keyArrFlip[$maxColumn]; $column++) {
                $value = $currentSheet->getCell($keyArr[$column] . $row)->getValue();
                if (is_object($value)) {
                    $value = $value->__toString();
                }
                $result[$row][] = trim(preg_replace('/\n/', ' ', $value));
                $totalLen += strlen(trim($value));
            }
            if($ignoreEmptyLine){
                if ($totalLen == 0) unset($result[$row]); //去掉空行
            }            
        }
//         print_r($result);exit;
        return $keyResort?array_values($result):$result;
    }

    /**
     * 读取上传的excel文件
     * @param unknown_type $fileName
     * @param unknown_type $filePath
     * @return string|mixed|Ambigous <multitype:, string>
     */
    public static function readUploadFile($fileName, $filePath,$sheet=0)
    {
        $pathinfo = pathinfo($fileName);
        $fileData = array();
    
        if ( isset($pathinfo["extension"]) && $pathinfo["extension"] == "xls") {
            $fileData = Common_Upload::readEXCEL($filePath,$sheet);
            if (is_array($fileData)) {
                $result = array();
                $columnMap = array();
                foreach ($fileData[0] as $key => $value) {
                    if (isset($columnMap[$value])) {
                        $fileData[0][$key] = $columnMap[$value];
                    }
                }
                foreach ($fileData as $key => $value) {
                    if ($key == 0) {
                        continue;
                    }
                    foreach ($value as $vKey => $vValue) {
                        if ($fileData[0][$vKey] == "") continue;
                        /*                    if (mb_detect_encoding($vValue) != 'UTF-8') {
                         $vValue = mb_convert_encoding($vValue, 'UTF-8', 'gb2312');
                        }*/
//                         $vValue=htmlspecialchars($vValue);
//                         $vValue=str_replace(chr(10),"<br>",$vValue);
//                         $vValue=str_replace(chr(32),"&nbsp;",$vValue);
                        $vValue = trim(preg_replace('/\n/', ' ', $vValue));
                        $result[$key][$fileData[0][$vKey]] =$vValue;
                    }
                }
                return $result;
            }else{
                return $fileData;
            }
        }else{
            throw new Exception('文件格式不正确，请上传xls文件',1001);
            return '文件格式不正确，请上传xls文件';
        }
    }
    

}