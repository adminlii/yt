<?php
class Service_ExcelDefinedProcess {
	public function createExcelDefinedTransaction($row, $details) {
		$result = array (
				"ask" => 0,
				"message" => "Operation Fail" 
		);
		$db = Common_Common::getAdapter ();
		$db->beginTransaction ();
		try {
			$result = $this->createExcelDefined ( $row, $details );
			$db->commit ();
		} catch ( Exception $e ) {
			$db->rollback ();
			$result = array (
					"ask" => 0,
					"message" => $e->getMessage (),
					'errorCode' => $e->getCode () 
			);
		}
		return $result;
	}
	public function createExcelDefined($row, $details) {
		if(empty($details)){
			throw new Exception('请上传模板文件');
		}
		$con = array('excel_defined_name'=>$row['excel_defined_name'],'defined_type'=>$row['defined_type'],'app_code'=>$row['app_code'],'company_code'=>$row['company_code']);
		$exists = Service_ExcelDefined::getByCondition($con);
		if(!empty($exists)){
			throw new Exception ( '模板名称已经存在' );
		}
		if (! $excel_defined_id = Service_ExcelDefined::add ( $row )) {
			throw new Exception ( 'Inner Error' );
		}
		foreach ( $details as $detail ) {
			$detail ['excel_defined_id'] = $excel_defined_id;
			if (! Service_ExcelDefinedDetail::add ( $detail )) {
				throw new Exception ( 'Inner Error' );
			}
		}
		
		return $result = array (
				"ask" => 1,
				"message" => "操作成功!",
				'excel_defined_id' => $excel_defined_id 
		);
	}
	
	public function getExcelTpl($excelDefinedId){
		$excelDefinedRow = Service_ExcelDefined::getByField($excelDefinedId,'excel_defined_id');
		$con = array('excel_defined_id'=>$excelDefinedId);
		$detailRows = Service_ExcelDefinedDetail::getByCondition($con);
		$arr = array();
		if($excelDefinedRow['defined_type']==2){
			foreach($detailRows as $row){
// 				$arr[$row['column_name']] = $row['excel_column_no'].'_'.$row['excel_column_name'];
				$arr[$row['column_name']] = $row['excel_column_name'];
			}
		}else{
			foreach($detailRows as $row){
				$arr[$row['excel_column_no'].'_'.$row['excel_column_name']] = $row['column_name'];
			}
		}
		return $arr;
	}
}