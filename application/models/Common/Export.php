<?php
class Common_Export
{
    /**
     * @desc 执行SQL
     * @param string $sql
     * @return array
     * @throws Exception
     */
    public static function getDateBySql($sql = '')
    {
        $result = array('state' => 0, 'message' => '', 'data' => array());
        if (empty($sql)) {
            $result['message'] = 'SQL语句不能为空.';
            return $result;
        }

        try {
            Ec::showError($sql,'report_');
            $db = Common_Common::getAdapter();
            $rows = $db->fetchAll($sql);
            if (empty($rows)) {
                throw new Exception('没有找到记录.');
            }
            $result['state'] = 1;
            $result['data'] = $rows;
        } catch (Exception $e) {
            $result['message'] = $e->getMessage();
        }
        return $result;
    }

    /**
     * @desc 将数组转为CSV
     * @param array $data
     * @return string
     */
    public static function exportCsv($data = array())
    {
        $str = '';
        $keyRow = array();
        if (!empty($data) && is_array($data)) {
            foreach ($data as $key => $val) {
                foreach ($val as $k => $v) {
                    $k=str_replace(',','',$k);
                    $keyRow[$k] = '';
                    $str .= $k . ",";
                }
                break;
            }
            $str = trim($str, ',') . "\n";
            foreach ($data as $key => $val) {
                foreach ($keyRow as $k1 => $v1) {
                    $str .= str_replace(',', '，', (isset($val[$k1]) ? $val[$k1] : '')) . ",";
                }
                $str = trim($str, ',') . "\n";
            }
        }

        $str=iconv("UTF-8", "GBK", $str);
        return $str;
        //IGNORE
      //  return iconv("UTF-8", "GBK//IGNORE", $str);
    }

}