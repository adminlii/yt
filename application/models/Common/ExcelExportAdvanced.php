<?php
/**
 * 根据模板导出数据
 */
require_once ("PHPExcel.php");
require_once ("PHPExcel/Reader/Excel5.php");
require_once ("PHPExcel/Reader/Excel2007.php");
require_once ("PHPExcel/Writer/Excel5.php");
require_once ("PHPExcel/Writer/Excel2007.php");
class Common_ExcelExportAdvanced
{

    private $objExcel = null;
    private $sheetIndex = 0;
    private $excel2003Col = null;

    public function __construct()
    {
        
        // 创建一个处理对象实例
        $objExcel = new PHPExcel();
        $this->objExcel = $objExcel;
        $this->excel2003Col = Common_ExcelExport::$excel2003Col;
        // 创建文件格式写入对象实例
        $this->objExcel->getProperties()
            ->setCreator("ecargo")
            ->setLastModifiedBy("MaxOu")
            ->setTitle("OfficeXLSTestDocument")
            ->setSubject("OfficeXLSTestDocument,Demo")
            ->setDescription("Testdocument,generatedbyPHPExcel.")
            ->setKeywords("officeexcelPHPExcel")
            ->setCategory("Test");
    }

    public function getExcelObj(){
        return $this->objExcel;
    }
    // 读取模板
    public function getTemplateFile($filePath)
    {
        if(!file_exists($filePath)){
            throw new Exception('模板文件不存在');
        }
        // $new_sheet = $objPHPExcel->getActiveSheet();
        
        $PHPReader = new PHPExcel_Reader_Excel2007();
        
        if(! $PHPReader->canRead($filePath)){
            $PHPReader = new PHPExcel_Reader_Excel5();
            if(! $PHPReader->canRead($filePath)){
                throw new Exception("Invalid File.");
            }
        }
        
        $objReader = $PHPReader->load($filePath);

        $sheetNames = $objReader->getSheetNames();
        foreach($sheetNames as $sheetName){
            $template = $objReader->getSheetByName($sheetName);
            $template->setTitle(str_replace('模板', '', $sheetName));
            $this->objExcel->addExternalSheet($template); // our template is now
                                                              // in the
        }
        $this->objExcel->removeSheetByIndex(0);
//         print_r($this->objExcel->getSheetNames()) ;exit;
        
        return $this;
    }
    
    public function createSheet($sheetName){
        if($this->objExcel->getSheetByName($sheetName)){
            throw new Exception('工作表已经存在:'.$sheetName);
        }
        $this->sheetIndex++;
        return $this->objExcel->createSheet($this->sheetIndex)->setTitle($sheetName);
    }
    
    // 保存文件
    public function save5($filePath)
    {
        if(! preg_match('/\.xls/', $filePath)){
            $filePath .= '.xls';
        }
        $objWriter = new PHPExcel_Writer_Excel5($this->objExcel); // 用于其他版本格式
        $objWriter->save($filePath);
        return $filePath;
    }
    
    // 保存文件
    public function save2007($filePath)
    {
        if(! preg_match('/\.xlsx/', $filePath)){
            $filePath .= '.xlsx';
        }
        $objWriter = new PHPExcel_Writer_Excel2007($this->objExcel); // 用于其他版本格式
        $objWriter->save($filePath);
        return $filePath;
    }
    
    /**
     * 数据填充
     * @param string $sheetName
     * @param array $dataList
     * @param int $startRowNum 从第几行开始填充
     * @throws Exception
     */
    public function fillDataTemplate($sheetName,$dataList,$startRowNum=2){

        $sheetNameArr = $this->objExcel->getSheetNames();
        if(! in_array($sheetName, $sheetNameArr)){
            throw new Exception('excel内不存在sheet：' . $sheetName);
        }
        $objActSheet = $this->objExcel->getSheetByName($sheetName);
        
        // print_r($dataList);exit;
        foreach($dataList as $kk => $column){
            $startNum = 0;
            foreach($column as $k => $v){
                $start = $this->excel2003Col[$startNum];
                $objActSheet->setCellValueExplicit($start . ($kk + $startRowNum), $v); // 字符串内容
                $startNum ++;
            }
        }        
    }
    /**
     * 
     * @param string $dimension
     */
    public function setBorder($objActSheet,$dimension){
        
//         print_r($objActSheet);exit;
        if($objActSheet instanceof PHPExcel_Worksheet){

            $objActSheet->getStyle($dimension)->applyFromArray(
                    array(
                            'font' => array(
                                    'bold' => false
                            ),
                            'alignment' => array(
                                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                            ),
                            'borders' => array(
                                    'left' => array(
                                            'style' => PHPExcel_Style_Border::BORDER_THIN
                                    ),
                                    'right' => array(
                                            'style' => PHPExcel_Style_Border::BORDER_THIN
                                    ),
                                    'bottom' => array(
                                            'style' => PHPExcel_Style_Border::BORDER_THIN
                                    ),
                                    'top' => array(
                                            'style' => PHPExcel_Style_Border::BORDER_THIN
                                    )
                            )
                    )
            );
            return $objActSheet;
        }else{
            throw new Exception('内部错误，传递的参数不是PHPExcel_Worksheet对象');
        }
    }
    /**
     * 设定背景色
     * @param unknown_type $objActSheet
     * @param unknown_type $dimension
     * @return unknown
     */
    public function setBg($objActSheet,$dimension){
        if($objActSheet instanceof PHPExcel_Worksheet){
            $objActSheet->getStyle($dimension)->applyFromArray(array(
                    'font' => array(
                            'bold' => true
                    ),
                    'fill' => array(
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            
                            'startcolor' => array(
                                    'argb' => PHPExcel_Style_Color::COLOR_YELLOW
                            ),
                            'endcolor' => array(
                                    'argb' => 'FFFFFFFF'
                            )
                    )
            
            ));
            return $objActSheet;
        }else{
            throw new Exception('内部错误，传递的参数不是PHPExcel_Worksheet对象');
        }
        
    }
    /**
     * 数据填充
     * @param string $sheetName
     * @param array $dataList
     * @param int $startRowNum
     * @return unknown
     */
    public  function fillData($sheetName,$dataList){
        $startRowNum=2;
        $objActSheet = $this->objExcel->getSheetByName($sheetName);
        if(! $objActSheet){ // sheet不存在，创建一个sheet
            $objActSheet = $this->createSheet($sheetName);
        }
        // 设置当前活动sheet的名称
        
        $columns = $dataList[0];
        // print_r($columns);exit;
        $startNum = 0;
        foreach($columns as $v => $v0){
            $start = $this->excel2003Col[$startNum];
            
            $currCol = $start;
            $currRow = '1';
            $currDemension = $currCol.$currRow;
            
            $objActSheet->getStyle($start)
                ->getAlignment()
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objActSheet->getStyle($start)
                ->getAlignment()
                ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            
            if(preg_match('/^(\{([0-9\-]+)\|([0-9\-]+)\})(.*)$/', $v, $m)){
                // $objActSheet->getColumnDimension($start)->setAutoSize(true);//
                // 内容自适应
//                 print_r($m);exit;
                $objActSheet->getColumnDimension($start)->setWidth($m[2]); // 宽度
                $objActSheet->getRowDimension(1)->setRowHeight($m[3]); // 高度
                                                                       // print_r($m);exit;
                $objActSheet->setCellValueExplicit($currDemension, $m[4]); // 字符串内容
            }elseif(preg_match('/^(\{([0-9\-]+)\})(.*)$/', $v, $m)){
                // $objActSheet->getColumnDimension($start)->setAutoSize(true);//
                // 内容自适应
//                 print_r($m);exit;
                $objActSheet->getColumnDimension($start)->setWidth($m[2]); // 宽度
                
                $objActSheet->setCellValueExplicit($currDemension, $m[3]); // 字符串内容
            }else{
//                 print_r($m);exit;
                $objActSheet->getColumnDimension($start)->setAutoSize(true); // 内容自适应
                $objActSheet->getColumnDimension($start)->setWidth(15); // 宽度
                $objActSheet->setCellValueExplicit($currDemension, $v); // 字符串内容
            }
            $this->setBorder($objActSheet, $currDemension);
            $this->setBg($objActSheet, $currDemension);
            $startNum ++;
        }
        // exit;
        foreach($dataList as $kk => $column){
            $startNum = 0;
            foreach($column as $k => $v){
                
                if(preg_match('/^(\{([0-9\-]+)\|([0-9\-]+)\})(.*)$/', $k, $m)){
                    $width = $m[2];
                    $height = $m[3];
                    $objActSheet->getRowDimension($kk + $startRowNum)->setRowHeight($m[3]); // 高度
                                                                                                // print_r($m);exit;
                }
                
                $start = $this->excel2003Col[$startNum];
                $objActSheet->setCellValueExplicit($start . ($kk + $startRowNum), $v); // 字符串内容
                $objActSheet->getStyle($start . ($kk + $startRowNum))->getAlignment()->setWrapText(true); //长度不够显示的时候 是否自动换行
                
                $this->setBorder($objActSheet, $start . ($kk + $startRowNum));
                $startNum ++;
            }
        }
        //取得一共多少列
//         $maxColumn = $objActSheet->getHighestColumn();
        /**取得一共有多少行*/
//         $rowCount = $objActSheet->getHighestRow();
//         print_r($maxColumn);
//         print_r($rowCount);
//         exit;
        return $objActSheet;
    }
    
    /**
     * 测试用例
     * 第一步：初始化
     * 第二步：数据填充
     * 第三步：文件保存
     */
    public static function demo(){
        try{
            // 1.
            $excelTemplateObj = new Common_ExcelExportAdvanced();
            
            // 删除无效sheet
            $excelTemplateObj->getExcelObj()->removeSheetByIndex(0);
            // 2.
            // 填充sheet
            $sheetName = 'item';
            
            $dataList = array();
            $dataList[] = array(
                '{20}01' => 'EA' . time(),//{20}表示设定cell的宽度为20
                '{20|50}02' => 'http://www.temtop.net/AU/EA156/EA156-HEAD.html	',//{20|50}表示设定cell的宽度为20,50高
                '{-1|50}03' => 'http://i1272.photobucket.com/albums/y397/temtopau/EA158/1_zps39d08e51.jpg'//{-1|50}表示设定cell的宽度为自动,50高
            );
            $excelTemplateObj->fillData($sheetName, $dataList);
            
            // 3.
            $savePath = APPLICATION_PATH . '/../public/ttt';
            $savePath = $excelTemplateObj->save2007($savePath);
            echo $savePath;
            echo '---finish---';
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }

    /**
     * 测试用例
     * 第一步：获取模板
     * 第二步：数据填充
     * 第三步：文件保存
     */
    public static function demoTemplate(){
        try{
                // 1.
            $filePath = APPLICATION_PATH . '/../public/AU.xls';
            $excelTemplateObj = new Common_ExcelExportAdvanced();
            $excelTemplateObj->getTemplateFile($filePath);
            // 2.
            $dataList = array();
            $dataList[] = array(
                'EA' . time(),
                'http://www.temtop.net/AU/EA156/EA156-HEAD.html	',
                'http://i1272.photobucket.com/albums/y397/temtopau/EA158/1_zps39d08e51.jpg'
            );
            
            $sheetName = 'item';
            $excelTemplateObj->fillDataTemplate($sheetName, $dataList);
            // 3.
            $savePath = APPLICATION_PATH . '/../public/AU2007';
            $savePath = $excelTemplateObj->save2007($savePath);
            // 3.
            $savePath = APPLICATION_PATH . '/../public/AU2003';
            $savePath = $excelTemplateObj->save5($savePath);
            
            echo '---finish---';
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }
}