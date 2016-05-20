<?php
class Process_InvoiceInfoProcess
{

    private $_err = array();
    
    private $_invoiceArr = array();
    
    public function _validate(){
        if(empty($this->_invoiceArr)){
            $this->_err[] = Ec::Lang('没有需要处理的申报信息');
        }
        $tmp_invoice_arr = array();
        foreach($this->_invoiceArr as $key=> $invoice){
            if(!empty($invoice['invoice_code'])){
                if(!isset($tmp_invoice_arr[$invoice['invoice_code']])){
                    $tmp_invoice_arr[$invoice['invoice_code']] = $invoice;
                    $con = array(
                            'company_code' => $invoice['company_code'],
                            'invoice_code' => $invoice['invoice_code']
                    );
                    $exist = Service_CsdInvoiceInfo::getByCondition($con);
                    if($exist){
                        $this->_err[$key][] = Ec::Lang('申报代码已经存在-->' . $invoice['invoice_code']);
                    }
                    
                }else{
                    $this->_err[$key][] = Ec::Lang('申报代码不可重复-->'.$invoice['invoice_code']);
                }
            }else{
                $this->_err[$key][] = Ec::Lang('申报代码不可为空');
            }  
            if(empty($invoice['invoice_enname'])){
                $this->_err[$key][] = Ec::Lang('申报品名不可为空');
                
            } 
            if(!preg_match('/^[0-9]+(\.[0-9]+)?$/',$invoice['invoice_unitcharge'])){
                $this->_err[$key][] = Ec::Lang('申报单价不可为空且需为数字');                
            } 
            if(!preg_match('/^[0-9]+(\.[0-9]+)?$/',$invoice['invoice_weight'])){
            	$this->_err[$key][] = Ec::Lang('申报重量不可为空且需为数字');
            }        
        }
        
    }
    
    public function addInvoiceInfoBatch($invoiceArr)
    {
        $return = array(
            'ask' => 0,
            'message' => 'Fail.'
        );
        $db = Common_Common::getAdapter();
        // print_r($db->getConfig());exit;
        $db->beginTransaction();
        try{
            $this->_invoiceArr = $invoiceArr;
            
            $this->_validate();
            if($this->_err){
                throw new Exception(Ec::Lang('数据不合法'));
            }
            foreach($invoiceArr as $invoice){
                $row = array(
                    'company_code' => $invoice['company_code'] ? $invoice['company_code'] : Common_Company::getCompanyCode(),
                    'invoice_code' => strtoupper($invoice['invoice_code']),
                    'invoice_cnname' => $invoice['invoice_enname'],
                    'invoice_enname' => $invoice['invoice_enname'],
                    'unit_code' => $invoice['unit_code'],
                    'invoice_unitcharge' => $invoice['invoice_unitcharge'],
                	'invoice_weight'=>$invoice['invoice_weight'],
                    'invoice_currencycode' => $invoice['invoice_currencycode'],
                    'hs_code' => $invoice['hs_code'],
                    'invoice_note' => $invoice['invoice_note'],
                    'invoice_url' => $invoice['invoice_url'],
                    'add_time' => date('Y-m-d H:i:s'),
                    'update_time' => date('Y-m-d H:i:s')
                );
                $con = array(
                    'company_code' => $invoice['company_code'],
                    'invoice_code' => $invoice['invoice_code']
                );
                $exist = Service_CsdInvoiceInfo::getByCondition($con);
                if(! $exist){
                    Service_CsdInvoiceInfo::add($row);
                }else{
                    unset($row['add_time']);
                    $exist = array_pop($exist);
                    Service_CsdInvoiceInfo::update($row, $exist['id'], 'id');
                }
            }
            $db->commit();
            $return['ask'] = 1;
            $return['message'] = 'Success';
        }catch(Exception $e){
            $db->rollback();
            $return['message'] = $e->getMessage();
        }
        $return['err'] = $this->_err;
        return $return;
    }
}