<?php
class Common_Type
{
    public static function display($lang = 'zh_CN')
    {
        $display = array(
            'zh_CN' => array(
                '0' => '隐藏',
                '1' => '显示',
            ),
            'en_US' => array(
                '0' => 'Hide',
                '1' => 'Show',
            )
        );
        if ($lang == 'auto') {
            $lang = Ec::getLang();
        }
        return isset($display[$lang]) ? $display[$lang] : $display;
    }

    public static function status($lang = 'zh_CN')
    {
        $status = array(
            'zh_CN' => array(
                '0' => '不可用',
                '1' => '可用',
            ),
            'en_US' => array(
                '0' => 'disabled',
                '1' => 'Enabled',
            )
        );
        if ($lang == 'auto') {
            $lang = Ec::getLang();
        }
        return isset($status[$lang]) ? $status[$lang] : $status;
    }
    
    /**
     * 退件类型
     * @param unknown_type $lang
     * @return multitype:multitype:string
     */
    public static function rmaStatus($lang = 'zh_CN')
    {
    	$status = array(
    			'zh_CN' => array(
    					'0' => '审核未通过',
    					'1' => '待审核',
    					'2' => '已审核',
    					'3' => '已退款',
    					'4' => '退款失败',
    			),
    			'en_US' => array(
    					'0' => '审核未通过',
    					'1' => '待审核',
    					'2' => '已审核',
    					'3' => '已退款',
    					'4' => '退款失败',
    			)
    	);
    	if ($lang == 'auto') {
    		$lang = Ec::getLang();
    	}
    	return isset($status[$lang]) ? $status[$lang] : $status;
    }
    
    /**
     * 退款类型
     * @param unknown_type $lang
     * @return multitype:multitype:string
     */
    public static function rmaRefundType($lang = 'zh_CN')
    {
    	$status = array(
    			'zh_CN' => array(
    					'1' => '部分退款',
    					'0' => '全额退款',
    			),
    			'en_US' => array(
    					'1' => '部分退款',
    					'0' => '全额退款',
    			)
    	);
    	if ($lang == 'auto') {
    		$lang = Ec::getLang();
    	}
    	return isset($status[$lang]) ? $status[$lang] : $status;
    }
    
    /**
     * paypal付款记录处理状态
     * @param unknown_type $lang
     * @return multitype:multitype:string
     */
    public static function paypalTransactionProcessType($lang = 'zh_CN')
    {
    	$status = array(
    			'zh_CN' => array(
    					'0' => '未关联',
    					'1' => '已关联',
    					'2' => '忽略',
    			),
    			'en_US' => array(
    					'0' => '未关联',
    					'1' => '已关联',
    					'2' => '忽略',
    			)
    	);
    	if ($lang == 'auto') {
    		$lang = Ec::getLang();
    	}
    	return isset($status[$lang]) ? $status[$lang] : $status;
    }
    
    public static function warehouseVirtual()
    {
    	$tmp = array(
    			0 => '自营',
    			1 => '第三方',
    	);
    	return $tmp;
    }
    
    public static function warehouseType()
    {
    	$tmp = array(
    			0 => '标准',
    			1 => '中转',
    	);
    	return $tmp;
    }
    
    public static function warehouseService()
    {
    	$tmp = array(
    			'4PX' => '递四方',
    			'SUDA' => '速达',
    			'BIRDS' =>'鸟系统'
    	);
    	return $tmp;
    }
    
    /**
     * @运输类别
     * @param string $lang
     * @return array
     */
    public static function SMClass($lang = 'zh_CN')
    {
    	$tmp = array(
    			'zh_CN' => array(
    					'CRE' => '快递',
    					'CRP' => '挂号',
    					'CPS' => '平邮',
    			),
    			'en_US' => array(
    					'CRE' => '快递',
    					'CRP' => '挂号',
    					'CPS' => '平邮',
    			)
    	);
    	if ($lang == 'auto') {
    		$lang = Ec::getLang();
    	}
    	return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }
    
    /**
     * @运输方式支持货物类型
     * @param string $lang
     * @return array
     */
    public static function smsSupportedType($lang = 'zh_CN')
    {
    	$tmp = array(
    			'zh_CN' => array(
    					'0' => '包裹&信封',
    					'1' => '包裹',
    					'2' => '信封',
    			),
    			'en_US' => array(
    					'0' => '包裹&信封',
    					'1' => '包裹',
    					'2' => '信封',
    			)
    	);
    	if ($lang == 'auto') {
    		$lang = Ec::getLang();
    	}
    	return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }
    
    /**
     * @运输类型
     * @param string $lang
     * @return array
     */
    public static function SMType($lang = 'zh_CN')
    {
    	$tmp = array(
    			'zh_CN' => array(
    					'0' => '快递',
    					'1' => '空运',
    					'2' => '海运',
    					'3' => '小包',
    			),
    			'en_US' => array(
    					'0' => '快递',
    					'1' => '空运',
    					'2' => '海运',
    					'3' => '小包',
    			)
    	);
    	if ($lang == 'auto') {
    		$lang = Ec::getLang();
    	}
    	return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }
    
    /**
     * @费用类型
     * @param string $lang
     * @return array
     */
    public static function smtFeeType($lang = 'zh_CN')
    {
    	$tmp = array(
    			'zh_CN' => array(
    					'1' => '单票计费',
    					'2' => '总单计费',
    			),
    			'en_US' => array(
    					'1' => '单票计费',
    					'2' => '总单计费',
    			)
    	);
    	if ($lang == 'auto') {
    		$lang = Ec::getLang();
    	}
    	return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }
    
    /**
     * @计费类型
     * @param string $lang
     * @return array
     */
    public static function smtType($lang = 'zh_CN')
    {
    	$tmp = array(
    			'zh_CN' => array(
    					'1' => '区间计费',
    					'2' => '首重计费',
    			),
    			'en_US' => array(
    					'1' => '区间计费',
    					'2' => '首重计费',
    			)
    	);
    	if ($lang == 'auto') {
    		$lang = Ec::getLang();
    	}
    	return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }
    
    /**
     * @客户使用标志(配置黑白名单)
     * @param string $lang
     * @return array
     */
    public static function smsCustomerType($lang = 'zh_CN')
    {
    	$tmp = array(
    			'zh_CN' => array(
    					'0' => '不开放',
    					'1' => '开放',
    			),
    			'en_US' => array(
    					'0' => '不开放',
    					'1' => '开放',
    			)
    	);
    	if ($lang == 'auto') {
    		$lang = Ec::getLang();
    	}
    	return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }
    
    /**
     * @ASN 类型
     * @param string $lang
     * @return array
     */
    public static function receivingType($lang = 'zh_CN')
    {
    	$tmp = array(
    			'zh_CN' => array(
    					'0' => '标准',
//     					'3' => '中转',
//     					'1' => '订单退货',
//     					'2' => '指定产品退件',
//     					'4' => '采购',
//     					'5' => '特采'
    			),
    			'en_US' => array(
    					'0' => 'Standard',
//     					'3' => 'Transit',
//     					'1' => 'Orders Returns',
//     					'2' => 'the product back pieces',
//     					'4' => 'purchase',
//     					'5' => 'Special Purchase',
    			)
    	);
    	if ($lang == 'auto') {
    		$lang = Ec::getLang();
    	}
    	return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }

    /**
     * @ASN 类型
     * @param string $lang
     * @return array
     */
    public static function packageType($lang = 'zh_CN')
    {
    	$tmp = array(
    			'zh_CN' => array(
    					'ow' => '自带包装',
    					'rw' => 'Ruston发泡袋',
    			),
    			'en_US' => array(
    					'ow' => '自带包装',
    					'rw' => 'Ruston发泡袋',
    			)
    	);
    	if ($lang == 'auto') {
    		$lang = Ec::getLang();
    	}
    	return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }
    /**
     * @ASN 交货方式
     * @param string $lang
     * @return array
     */
    public static function incomeType($lang = 'zh_CN')
    {
		$tmp = array (
				'zh_CN' => array (
						'0' => '自送',
						'1' => '揽收' 
				),
				'en_US' => array (
						'0' => '自送',
						'1' => '揽收' 
				) 
		);
		if ($lang == 'auto') {
			$lang = Ec::getLang ();
		}
		return isset ( $tmp [$lang] ) ? $tmp [$lang] : $tmp;
	}
    /**
     * @ASN 入库单状态
     * @param string $lang
     * @return array
     */
    public static function receivingStatus($lang = 'zh_CN')
    {
    	$tmp = array(
    			'zh_CN' => array(
    					'0' => '删除',
    					'1' => '草稿',
    					'2' => '确认',
    					'3' => '待审核',
    					'4' => '审核',
    					'5' => '在途',
    					'6' => '收货中',
    					'7' => '收货完成'
    			),
    			'en_US' => array(
    					'0' => 'delete',
    					'1' => 'draft',
    					'2' => 'confirm',
    					'3' => 'Pending',
    					'4' => 'Review',
    					'5' => 'onway',
    					'6' => 'Receipting',
    					'7' => 'Receipt complete'
    			)
    	);
    	if ($lang == 'auto') {
    		$lang = Ec::getLang();
    	}
    	return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }
    
    public static function qcType($lang = 'zh_CN'){
    	$tmp = array(
    			'zh_CN' => array(
    					0 => '免检',
    					1 => '检验',
    					2 => '特采',
    			),
    			'en_US' => array(
    					0 => 'Exemption',
    					1 => 'Examine',
    					2 => 'Special mining',
    			)
    	);
    	if ($lang == 'auto') {
    		$lang = Ec::getLang();
    	}
    	return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }
    
    
    public static function qcStatus($lang = 'zh_CN'){
    	$tmp = array(
    			'zh_CN' => array(
    					0 => '草稿',
    					1 => '完成',
    					2 => '已上架',
    			),
    			'en_US' => array(
    					0 => 'Draft',
    					1 => 'Complete',
    					2 => 'Has been added',
    			)
    	);
    	if ($lang == 'auto') {
    		$lang = Ec::getLang();
    	}
    	return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }
    
    /**
     * @desc 入库搜索类型
     * @param string $lang
     * @return array
     */
    public static function receivedType($lang = 'zh_CN')
    {
    	$tmpArr = array(
    			'zh_CN' => array(
    					'1' => '入库单号',
    					'2' => '客户参考号',
    					//    '3' => '产品代码',
    					'4' => '采购单号',
    					'5' => '跟踪单号',
    			),
    			'en_US' => array(
    					'1' => 'ASNCode',
    					'2' => 'referenceNo',
    					//     '3' => 'BarCode',
    					'4' => 'POCode',
    					'5' => 'trackingNumber',
    			)
    	);
    	if ($lang == 'auto') {
    		$lang = Ec::getLang();
    	}
    	return isset($tmpArr[$lang]) ? $tmpArr[$lang] : $tmpArr;
    }
    
    /**
     * @desc 库存类型
     * @param string $lang
     * @return array
     */
    public static function inventoryBatchType($lang = 'zh_CN')
    {
    	$tmpArr = array(
    			'zh_CN' => array(
    					'0' => '标准',
    					'1' => '不良品',
    			),
    			'en_US' => array(
    					'0' => '标准',
    					'1' => '不良品',
    			)
    	);
    	if ($lang == 'auto') {
    		$lang = Ec::getLang();
    	}
    	return isset($tmpArr[$lang]) ? $tmpArr[$lang] : $tmpArr;
    }
    
    /**
     * @desc 供应商等级
     * @param string $lang
     * @return array
     */
    public static function supplierLevel($lang = 'zh_CN'){
        $tmp = array(
                'zh_CN' => array(
                        'A' => 'A',
                        'B' => 'B',
                        'C' => 'C',
                        'D' => 'D',
                ),
                'en_US' => array(
                        'A' => 'A',
                        'B' => 'B',
                        'C' => 'C',
                        'D' => 'D',
                )
        );
        if ($lang == 'auto') {
            $lang = Ec::getLang();
        }
        return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }
    
    /**
     * @desc 供应商类型
     * @param string $lang
     * @return array
     */
    public static function supplierType($lang = 'zh_CN'){
        $tmp = array(
                'zh_CN' => array(
                        1 => '零售',
                        2 => '批发',
                        3 => '生产商',
                        4 => '通用虚拟',
                ),
                'en_US' => array(
                        1 => '零售',
                        2 => '批发',
                        3 => '生产商',
                        4 => '通用虚拟',
                )
        );
        //     	$tmp = array(
        //     			'zh_CN' => array(
        //     					'零售' => '零售',
        //     					'批发' => '批发',
        //     					'批发商' => '批发商',
        //     					'生产商' => '生产商',
        //     					'通用虚拟' => '通用虚拟',
        //     			),
        //     			'en_US' => array(
        //     					'零售' => '零售',
        //     					'批发' => '批发',
        //     					'批发商' => '批发商',
        //     					'生产商' => '生产商',
        //     					'通用虚拟' => '通用虚拟',
        //     			)
        //     	);
        if ($lang == 'auto') {
            $lang = Ec::getLang();
        }
        return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }
    
    /**
     * @desc 供应商结算方式
     * @param string $lang
     * @return array
     */
    public static function SupplierAccountType($lang = 'zh_CN'){
        $tmp = array(
                'zh_CN' => array(
                        1 => '货到付款',
                        2 => '款到发货',
                        3 => '周期结算',
                        4 => '售后付款',
                        5 => '默认方式',
                ),
                'en_US' => array(
                        1 => '货到付款',
                        2 => '款到发货',
                        3 => '周期结算',
                        4 => '售后付款',
                        5 => '默认方式',
                )
        );
        if ($lang == 'auto') {
            $lang = Ec::getLang();
        }
        return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }
    
    /**
     * @desc 供应商结算方式
     * @param string $lang
     * @return array
     */
    public static function SupplierAccountChar($lang = 'zh_CN'){
        $tmp = array(
                'zh_CN' => array(
                        '日' => '日结',
                        '月' => '月结',
                        '周' => '周结',
                        '百分比' => '百分比结算',
                ),
                'en_US' => array(
                        '日' => '日结',
                        '月' => '月结',
                        '周' => '周结',
                        '百分比' => '百分比结算',
                )
        );
        if ($lang == 'auto') {
            $lang = Ec::getLang();
        }
        return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }
    
    /**
     * @desc 支付方式
     * @param string $lang
     * @return array
     */
    public static function SupplierpayType($lang = 'zh_CN'){
        $tmp = array(
                'zh_CN' => array(
                        1 => '现金',
                        2 => '在线',
                        3 => '银行卡',
                        	
                ),
                'en_US' => array(
                        1 => '现金',
                        2 => '在线',
                        3 => '银行卡',
                )
        );
        if ($lang == 'auto') {
            $lang = Ec::getLang();
        }
        return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }
    
    /**
     * @desc 支付平台
     * @param string $lang
     * @return array
     */
    public static function SupplierPlatformType($lang = 'zh_CN'){
        $tmp = array(
                'zh_CN' => array(
                        1 => 'paypal',
                        2 => '财付通',
                        3 => '支付宝',
                        4 => '快钱',
                ),
                'en_US' => array(
                        1 => 'paypal',
                        2 => '财付通',
                        3 => '支付宝',
                        4 => '快钱',
                )
        );
        if ($lang == 'auto') {
            $lang = Ec::getLang();
        }
        return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }

    /**
     * @配货单(下架单)类型
     * @param string $lang
     * @return array
     */
    public static function pickType($lang = 'zh_CN')
    {
        $tmp = array(
                'zh_CN' => array(
                        '0' => '一票一件',
                        '1' => '一票多件',
                ),
                'en_US' => array(
                        '0' => '一票一件',
                        '1' => '一票多件',
                )
        );
        if ($lang == 'auto') {
            $lang = Ec::getLang();
        }
        return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }
    
    public static function warehouseAreaType($lang = 'zh_CN')
    {
    	$tmpArr = array(
    			'zh_CN' => array(
    					'1' => '标准',
    					'2' => '不良品',
    					'3' => '退货区',
    					'4' => '中转区',
    			),
    			'en_US' => array(
    					'1' => 'A',
    					'2' => 'B',
    					'3' => 'C',
    					'4' => 'D',
    			)
    	);
    	if ($lang == 'auto') {
    		$lang = Ec::getLang();
    	}
    	return isset($tmpArr[$lang]) ? $tmpArr[$lang] : $tmpArr;
    }
    
    /**
     * @desc  仓库操作模式类型
     * @param string $lang
     * @return array
     */
    public static function warehouseOperationModeType($lang = 'zh_CN'){
    	$tmp = array(
    			'zh_CN' => array(
    					1 => '收货模式',
    			),
    			'en_US' => array(
    					1 => '收货模式',
    			)
    	);
    	if ($lang == 'auto') {
    		$lang = Ec::getLang();
    	}
    	return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }
    
    /**
     * @desc 采购单建立方式
     * @param string $lang
     * @return array
     */
    public static function purchaseOrdersCreateType($lang = 'zh_CN'){
    	$tmp = array(
    			'zh_CN' => array(
    					0 => '系统生成',
    					1 => '人工建立',
    			),
    			'en_US' => array(
    					0 => '系统生成',
    					1 => '人工建立',
    			)
    	);
    	if ($lang == 'auto') {
    		$lang = Ec::getLang();
    	}
    	return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }
    
    /**
     * @desc 采购单补货方式
     * @param string $lang
     * @return array
     */
    public static function purchasePOType($lang = 'zh_CN'){
    	$tmp = array(
    			'zh_CN' => array(
    					0 => '缺货入库',
    					1 => '警报入库',
    					2 => '特采入库',
    					3 => '正常入库',
    					4 => '样品采购入库',
    			),
    			'en_US' => array(
    					0 => '缺货入库',
    					1 => '警报入库',
    					2 => '特采入库',
    					3 => '正常入库',
    					4 => '样品采购入库',
    			)
    	);
    	if ($lang == 'auto') {
    		$lang = Ec::getLang();
    	}
    	return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }
    
    /**
     * 创建采购单，供应商运输方式
     */
    public static function getSupplierMethod($lang = 'zh_CN'){
    	$tmpArr = array(
    			'zh_CN' => array(
    					1 => '自提',
    					2 => '快递',
    			),
    			'en_US' => array(
    					1 => '自提',
    					2 => '快递',
    			)
    	);
    	if ($lang == 'auto') {
    		$lang = Ec::getLang();
    	}
    	return isset($tmpArr[$lang]) ? $tmpArr[$lang] : $tmpArr;
    }
    

    /**
     * @ASN 类型
     * @param string $lang
     * @return array
     */
    public static function cancelStatus($lang = 'zh_CN')
    {
        $tmp = array(
            'zh_CN' => array(
                '0' => '无',
                '1' => '拦截中',
                '2' => '拦截成功',
                '3' => '拦截失败'
            ),
            'en_US' => array(
                '0' => '',
                '1' => 'Pending',
                '2' => 'Success',
                '3' => 'Fail'
            )
        );
        if($lang == 'auto'){
            $lang = Ec::getLang();
        }
        return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }
    
    public static function userType($lang = 'zh_CN')
    {
    	$status = array(
    			'zh_CN' => array(
    					'0' => '子账户',
    					'1' => '管理员',
    			),
    			'en_US' => array(
    					'0' => 'Subaccount',
    					'1' => 'Admin',
    			)
    	);
    	if ($lang == 'auto') {
    		$lang = Ec::getLang();
    	}
    	return isset($status[$lang]) ? $status[$lang] : $status;
    }
    /**
     * @desc 退件处理类型
     * @param string $lang
     * @return array
     */
    public static function returnOrdersProcessType($lang = 'zh_CN')
    {
        $tmpArr = array(
                'zh_CN' => array(
                        '1' => '退件入库',
//                         '2' => '销毁',
                ),
                'en_US' => array(
                        '1' => '退件入库',
//                         '2' => '销毁',
                )
        );
        if ($lang == 'auto') {
            $lang = Ec::getLang();
        }
        return isset($tmpArr[$lang]) ? $tmpArr[$lang] : $tmpArr;
    }

    /**
     * @desc 是否质检
     * @param string $lang
     * @return array
     */
    public static function isQc($lang = 'zh_CN'){
        $tmp = array(
                'zh_CN' => array(
                        0 => '否',
                        1 => '是',
                ),
                'en_US' => array(
                        0 => '否',
                        1 => '是',
                )
        );
        if ($lang == 'auto') {
            $lang = Ec::getLang();
        }
        return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }
    /**
     * @desc 质检时当异常时,处理指令
     * @param string $lang
     * @return array
     */
    public static function exceptionProcessInstruction($lang = 'zh_CN'){
        $tmp = array(
                'zh_CN' => array(
                        0 => '无',
                        1 => '重新上架',
                        2 => '退回',
                        3 => '销毁',
                ),
                'en_US' => array(
                        0 => '无',
                        1 => '重新上架',
                        2 => '退回',
                        3 => '销毁',
                )
        );
        if ($lang == 'auto') {
            $lang = Ec::getLang();
        }
        return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }
    /**
     * @desc 质检时当异常时,处理指令
     * @param string $lang
     * @return array
     */
    public static function returnOrdersType($lang = 'zh_CN'){
        $tmp = array(
                'zh_CN' => array(
                        //  0 => '无',
                        1 => '物流退回',
                        2 => '订单信息不准确',
                        3 => '订单拦截',
                        4 => '其它',
                ),
                'en_US' => array(
                        //   0 => '无',
                        1 => '物流退回',
                        2 => '订单信息不准确',
                        3 => '订单拦截',
                        4 => '其它',
                )
        );
        if ($lang == 'auto') {
            $lang = Ec::getLang();
        }
        return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }
    /**
     * 账户历史记录类型
     * @param unknown_type $lang
     * @return multitype:multitype:string
     */
    public static function customerBalanceLogType($lang = 'zh_CN'){
        $tmp = array(
                'zh_CN' => array(
                        0 => '冻结',
                        1 => '解冻',
                        2 => '扣款',
                        3 => '入款',
                ),
                'en_US' => array(
                        0 => '冻结',
                        1 => '解冻',
                        2 => '扣款',
                        3 => '入款',
                )
        );
        if ($lang == 'auto') {
            $lang = Ec::getLang();
        }
        return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }
    

    public static function saleType($lang = 'zh_CN')
    {
        $status = array(
                'zh_CN' => array(
                        'return' => '退仓订单',
                        'sale' => '正常订单',
                ),
                'en_US' => array(
                        'return' => 'return',
                        'sale' => 'sale',
                )
        );
        if ($lang == 'auto') {
            $lang = Ec::getLang();
        }
        return isset($status[$lang]) ? $status[$lang] : $status;
    }

    /**
     * @desc 异常通知单类型
     * @param string $lang
     * @return array
     */
    public static function receivingAbnormalType($lang = 'zh_CN')
    {
        $tmpArr = array(
                'zh_CN' => array(
//                         '0' => '',
                        '1' => '销毁',
                        '2' => '重新上架',
//                         '3' => '退货',
//                         '4' => '不良品退货',
                ),
                'en_US' => array(
//                         '0' => '',
                        '1' => '销毁',
                        '2' => '重新上架',
//                         '3' => '退货',
//                         '4' => '不良品退货',
                )
        );
        if ($lang == 'auto') {
            $lang = Ec::getLang();
        }
        return isset($tmpArr[$lang]) ? $tmpArr[$lang] : $tmpArr;
    }
    /**
     * @desc 退件处理类型
     * @param string $lang
     * @return array
     */
    public static function returnOrdersCreateType($lang = 'zh_CN')
    {
        $tmpArr = array(
            'zh_CN' => array(
                '0' => '买家退件',
                '1' => '服务商退件'
            ),
            'en_US' => array(
                '0' => '退件入库',
                '1' => '服务商退件'
            )
        );
        if($lang == 'auto'){
            $lang = Ec::getLang();
        }
        return isset($tmpArr[$lang]) ? $tmpArr[$lang] : $tmpArr;
    }
    
	/**
	 * 业务类型
	 * @param unknown_type $lang
	 * @return multitype:multitype:string
	 */
    public static function businessType($lang = 'zh_CN')
    {
    	$status = array(
    			'zh_CN' => array(
    					'1' => '入库单',
    					'2' => '订单',
    					'3' => '退件',
    			),
    			'en_US' => array(
    					'1' => 'Receiving Order',
    					'2' => 'Delivery Order',
    					'3' => 'Return Merchandise Authorization',
    			)
    	);
    	if ($lang == 'auto') {
    		$lang = Ec::getLang();
    	}
    	return isset($status[$lang]) ? $status[$lang] : $status;
    }

    /**
     * 订单来源
     */
    public static function orderCreateCode($lang = 'zh_CN'){
        $tmpArr = array(
            'zh_CN' => array(
                'w' => '网站',
                'a' => 'API',
                'p' => '平台',
                'W' => '网站',
                'A' => 'API',
                'P' => '平台'
            ),
            'en_US' => array(
                'w' => '网站',
                'a' => 'API',
                'p' => '平台',
                'W' => '网站',
                'A' => 'API',
                'P' => '平台'
            )
        );
        if($lang == 'auto'){
            $lang = Ec::getLang();
        }
        return isset($tmpArr[$lang]) ? $tmpArr[$lang] : $tmpArr;
    }
    
}