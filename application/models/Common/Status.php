<?php
class Common_Status
{
    /**
     * @库存批次锁状态
     * @param string $lang
     * @return array
     */
    public static function holdStatus($lang = 'zh_CN')
    {
        $tmp = array(
            'zh_CN' => array(
                '0' => '无锁',
                '1' => '盘点锁',
                '2' => '借领用锁',
            ),
            'en_US' => array(
                '0' => '无锁',
                '1' => '盘点锁',
                '2' => '借领用锁',
            )
        );
        if ($lang == 'auto') {
            $lang = Ec::getLang();
        }
        return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }

    /**
     * @param string $lang
     * @return array
     */
    public static function inventoryBatchStatus($lang = 'zh_CN')
    {
        $tmp = array(
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
        return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }


    /**
     * @订单状态
     * @param string $lang
     * @return array
     */
    public static function orderStatus($lang = 'zh_CN')
    {

        $tmp = array(
            'zh_CN' => array(
                '0' => '删除',
                '1' => '草稿',
                '2' => '确认',
                '3' => '异常',
                '4' => '已提交',
                '5' => '已打印',
                '6' => '已下架',
                '7' => '已打包',
                '8' => '已装袋',
                '9' => '装袋完成',
                '10' => '已加挂',
                '11' => '物流完成',
                '12' => '物流发货',
                '13' => '已签收',
            ),
            'en_US' => array(
                '0' => '删除',
                '1' => '草稿',
                '2' => '确认',
                '3' => '异常',
                '4' => '已提交',
                '5' => '已打印',
                '6' => '已下架',
                '7' => '已打包',
                '8' => '已装袋',
                '9' => '装袋完成',
                '10' => '已加挂',
                '11' => '物流完成',
                '12' => '物流发货',
                '13' => '已签收',
            )
        );
        if ($lang == 'auto') {
            $lang = Ec::getLang();
        }
        return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }

    /**
     * @desc 拦截状态
     * @param string $lang
     */
    public static function interceptOrderStatus($lang = 'zh_CN')
    {
        $tmp = array(
            'zh_CN' => array(
                '0' => '无',
                '1' => '已申请拦截',
                '2' => '拦截处理中',
                '3' => '拦截失败',
            ),
            'en_US' => array(
                '0' => '无',
                '1' => '已申请拦截',
                '2' => '拦截处理中',
                '3' => '拦截失败',
            )
        );
        if ($lang == 'auto') {
            $lang = Ec::getLang();
        }
        return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }

    /**
     * @desc 订单等待状态
     * @param string $lang
     */
    public static function orderWaitingStatus($lang = 'zh_CN')
    {
        $tmp = array(
            'zh_CN' => array(
                '0' => '',
                '1' => '物流处理',
            ),
            'en_US' => array(
                '0' => '',
                '1' => '物流处理',
            )
        );
        if ($lang == 'auto') {
            $lang = Ec::getLang();
        }
        return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }

    /**
     * @订单是否异常
     * @param string $lang
     * @return array
     */
    public static function orderProblemStatus($lang = 'zh_CN')
    {
        $tmp = array(
            'zh_CN' => array(
                '0' => '无',
                '1' => '有',
            ),
            'en_US' => array(
                '0' => 'No',
                '1' => 'Yes',
            )
        );
        if ($lang == 'auto') {
            $lang = Ec::getLang();
        }
        return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }

    /**
     * @订单异常类型
     * @param string $lang
     * @return array
     */
    public static function orderUnderReviewStatus($lang = 'zh_CN')
    {
        $tmp = array(
            'zh_CN' => array(
                '0' => '无',
                '1' => '余额不足',
                '2' => '库存不足',
                '3' => '已截单',
            ),
            'en_US' => array(
                '0' => '无',
                '1' => '余额不足',
                '2' => '库存不足',
                '3' => '已截单',
            )
        );
        if ($lang == 'auto') {
            $lang = Ec::getLang();
        }
        return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }

    /**
     * @订单类型
     * @param string $lang
     * @return array
     */
    public static function orderType($lang = 'zh_CN')
    {
        $tmp = array(
            'zh_CN' => array(
                '0' => '普通',
                '1' => '转仓',
                '2' => '退货',
            ),
            'en_US' => array(
                '0' => '普通',
                '1' => '转仓',
                '2' => '退货',
            )
        );
        if ($lang == 'auto') {
            $lang = Ec::getLang();
        }
        return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }

    /**
     * @订单下架类型
     * @param string $lang
     * @return array
     */
    public static function orderPickType($lang = 'zh_CN')
    {
        $tmp = array(
            'zh_CN' => array(
                '0' => '一票一件',
                '1' => '一票一件多个',
                '2' => '一票多件',
            ),
            'en_US' => array(
                '0' => '一票一件',
                '1' => '一票一件多个',
                '2' => '一票多件',
            )
        );
        if ($lang == 'auto') {
            $lang = Ec::getLang();
        }
        return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }

    /**
     * @订单装袋状态
     * @param string $lang
     * @return array
     */
    public static function bagStatus($lang = 'zh_CN')
    {
        $tmp = array(
            'zh_CN' => array(
                '0' => '草稿',
                '1' => '已完成',
                '2' => '已加挂',
                '3' => '物流完成',
                '4' => '已出货',
            ),
            'en_US' => array(
                '0' => '草稿',
                '1' => '已完成',
                '2' => '已加挂',
                '3' => '物流完成',
                '4' => '已出货',
            )
        );
        if ($lang == 'auto') {
            $lang = Ec::getLang();
        }
        return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }

    /**
     * @订单装袋状态
     * @param string $lang
     * @return array
     */
    public static function shipBatchStatus($lang = 'zh_CN')
    {
        $tmp = array(
            'zh_CN' => array(
                '0' => '草稿',
                '1' => '已完成',
                '2' => '已出货',
            ),
            'en_US' => array(
                '0' => '草稿',
                '1' => '已完成',
                '2' => '已出货',
            )
        );
        if ($lang == 'auto') {
            $lang = Ec::getLang();
        }
        return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }

    /**
     * @配货单 状态
     * @param string $lang
     * @return array
     */
    public static function pickStatus($lang = 'zh_CN')
    {
        $tmp = array(
            'zh_CN' => array(
                '0' => '草稿',
                '1' => '已完成',
            ),
            'en_US' => array(
                '0' => '草稿',
                '1' => '已完成',
            )
        );
        if ($lang == 'auto') {
            $lang = Ec::getLang();
        }
        return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }

    /**
     * @配货Item状态
     * @param string $lang
     * @return array
     */
    public static function pickDetailStatus($lang = 'zh_CN')
    {
        $tmp = array(
            'zh_CN' => array(
                '0' => '未配货',
                '1' => '已配货',
            ),
            'en_US' => array(
                '0' => '未配货',
                '1' => '已配货',
            )
        );
        if ($lang == 'auto') {
            $lang = Ec::getLang();
        }
        return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }


    /**
     * @配货Item状态
     * @param string $lang
     * @return array
     */
    public static function customerStatus($lang = 'zh_CN')
    {
        $tmp = array(
            'zh_CN' => array(
                '0' => '未注册完成',
                '1' => '已注册完成',
                '2' => '已激活',
                '3' => '停用',
            ),
            'en_US' => array(
                '0' => '未注册完成',
                '1' => '已注册完成',
                '2' => '已激活',
                '3' => '停用',
            )
        );
        if ($lang == 'auto') {
            $lang = Ec::getLang();
        }
        return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }

    /**
     * @运输方式
     * @param string $lang
     * @return array
     */
    public static function SMStatus($lang = 'zh_CN')
    {
        $tmp = array(
            'zh_CN' => array(
                '1' => '可用',
                '0' => '不可用',
            ),
            'en_US' => array(
                '1' => 'Enabled',
                '0' => 'disabled',
            )
        );
        if ($lang == 'auto') {
            $lang = Ec::getLang();
        }
        return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }

    /**
     * @YorN
     * @param string $lang
     * @return array
     */
    public static function YesOrNo($lang = 'zh_CN')
    {
        $tmp = array(
            'zh_CN' => array(
                '0' => '否',
                '1' => '是',
            ),
            'en_US' => array(
                '0' => 'No',
                '1' => 'Yes',
            )
        );
        if ($lang == 'auto') {
            $lang = Ec::getLang();
        }
        return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }

    /**
     * @借用领用状态
     * @param string $lang
     * @return array
     */
    public static function ceiveUseStatus($lang = 'zh_CN')
    {
        $tmp = array(
            'zh_CN' => array(
                '0' => '草稿',
                '1' => '确认',
                '2' => '完成'
            ),
            'en_US' => array(
                '0' => '草稿',
                '1' => '确认',
                '2' => '完成'
            )
        );
        if ($lang == 'auto') {
            $lang = Ec::getLang();
        }
        return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }

    /**
     * @借用领用类型
     * @param string $lang
     * @return array
     */
    public static function ceiveUseType($lang = 'zh_CN')
    {
        $tmp = array(
            'zh_CN' => array(
                '0' => '借用',
                '1' => '领用',
            ),
            'en_US' => array(
                '0' => '借用',
                '1' => '领用',
            )
        );
        if ($lang == 'auto') {
            $lang = Ec::getLang();
        }
        return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
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
                '1' => '新建',
                '5' => '在途',
                '6' => '收货中',
                '7' => '收货完成',
                '0' => '废弃',
            ),
            'en_US' => array(
                '1' => 'Open',
                '5' => 'Onway',
                '6' => 'Pending',
                '7' => 'Completed',
                '0' => 'Discard',
            )
        );
        if ($lang == 'auto') {
            $lang = Ec::getLang();
        }
        return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }
    /**
     * @ASN 入库单状态
     * @param string $lang
     * @return array
     */
    public static function receivingStatusAction()
    {
        return array(
            '1' => array(
                '<input type="button" class="asnDiscardBtn baseBtn opBtn" value="' . Ec::Lang('asn_discard') . '">',
                '<input type="button" class="asnVerifyBtn baseBtn opBtn" value="' . Ec::Lang('verify') . '">',
            ),
            '5' => array(
                '<input type="button" class="asnDiscardBtn baseBtn opBtn" value="' . Ec::Lang('asn_discard') . '">'
            ),
            '6' => array('<input type="button" class="asnForceFinishBtn baseBtn opBtn" value="' . Ec::Lang('asn_force_finish') . '">'),
            '7' => array(),
            '0' => array()
        );
    }

    /**
     * @ASN Detail 状态
     * @param string $lang
     * @return array
     */
    public static function receivingDetailStatus($lang = 'zh_CN')
    {
        $tmp = array(
            'zh_CN' => array(
                0 => '在途',
                1 => '收货中',
                2 => '收货完成',
            ),
            'en_US' => array(
                0 => '在途',
                1 => '收货中',
                2 => '收货完成',
            )
        );
        if ($lang == 'auto') {
            $lang = Ec::getLang();
        }
        return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }

    /**
     * @desc ASN 中转状态
     * @param string $lang
     * @return array
     */
    public static function receivingTransferStatus($lang = 'zh_CN')
    {
        $tmp = array(
            'zh_CN' => array(
                0 => '草稿',
                1 => '待处理',
                2 => '处理中',
                3 => '处理完成',
            ),
            'en_US' => array(
                0 => '草稿',
                1 => '待处理',
                2 => '处理中',
                3 => '处理完成',
            )
        );
        if ($lang == 'auto') {
            $lang = Ec::getLang();
        }
        return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }

    /**
     * @desc item 中转状态
     * @param string $lang
     * @return array
     */
    public static function receivingDetailTransferStatus($lang = 'zh_CN')
    {
        $tmp = array(
            'zh_CN' => array(
                0 => '草稿',
                1 => '处理完成',
            ),
            'en_US' => array(
                0 => '草稿',
                1 => '处理完成',
            )
        );
        if ($lang == 'auto') {
            $lang = Ec::getLang();
        }
        return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }

    /**
     * @质检单 状态
     * @param string $lang
     * @return array
     */
    public static function qualityControlStatus($lang = 'zh_CN')
    {
        $tmp = array(
            'zh_CN' => array(
                0 => '草稿',
                1 => '完成',
                2 => '已上架'
            ),
            'en_US' => array(
                0 => '草稿',
                1 => '完成',
                2 => '已上架'
            )
        );
        if ($lang == 'auto') {
            $lang = Ec::getLang();
        }
        return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }

    /**
     * @上架单Item状态
     * @param string $lang
     * @return array
     */
    public static function putawayDetailStatus($lang = 'zh_CN')
    {
        $tmp = array(
            'zh_CN' => array(
                0 => '草稿',
                1 => '已完成',
            ),
            'en_US' => array(
                0 => '草稿',
                1 => '已完成',
            )
        );
        if ($lang == 'auto') {
            $lang = Ec::getLang();
        }
        return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }

    /**
     * @盘点单状态
     * @param string $lang
     * @return array
     */
    public static function takeStockStatus($lang = 'zh_CN')
    {
        $tmp = array(
            'zh_CN' => array(
                1 => '盘点中',
                2 => '确认中',
                3 => '已完成',
            ),
            'en_US' => array(
                1 => '盘点中',
                2 => '确认中',
                3 => '已完成',
            )
        );
        if ($lang == 'auto') {
            $lang = Ec::getLang();
        }
        return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }
    
    /**
     * @供应商状态
     * @param string $lang
     * @return array
     */
    public static function supplierStatus($lang = 'zh_CN')
    {
    	$tmp = array(
    			'zh_CN' => array(
    					0 => '草稿',
    					1 => '审核',
    			),
    			'en_US' => array(
    					0 => '草稿',
    					1 => '审核',
    			)
    	);
    	if ($lang == 'auto') {
    		$lang = Ec::getLang();
    	}
    	return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }
    
    
/**
     * @采购单付款状态
     * @param string $lang
     * @return array
     */
    public static function purchasePayStatus($lang = 'zh_CN')
    {
    	$tmp = array(
    			'zh_CN' => array(
    					0 => '未付款',
    					1 => '已付款',
    			),
    			'en_US' => array(
    					0 => '未付款',
    					1 => '已付款',
    			)
    	);
    	if ($lang == 'auto') {
    		$lang = Ec::getLang();
    	}
    	return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }


    /**
     * @中转下架单状态
     * @param string $lang
     * @return array
     */
    public static function transferPackingStatus($lang = 'zh_CN')
    {
        $tmp = array(
            'zh_CN' => array(
                -1 => '删除',
                0 => '草稿',
                1 => '处理中',
                2 => '已完成',
            ),
            'en_US' => array(
                -1 => '删除',
                0 => '草稿',
                1 => '处理中',
                2 => '已完成',
            )
        );
        if ($lang == 'auto') {
            $lang = Ec::getLang();
        }
        return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }
    
    /**
     * @中转产品装箱单状态（新流程）
     * @param string $lang
     * @return array
     */
    public static function traPackingStatus($lang = 'zh_CN')
    {
        $tmp = array(
            'zh_CN' => array(
                -1 => '删除',
                0 => '草稿',
                1 => '装载中',
                2 => '装载完成',
                3 => '加挂订单',
                4 => '已发货',
            ),
            'en_US' => array(
                -1 => '删除',
                0 => '草稿',
                1 => '装载中',
                2 => '装载完成',
                3 => '加挂订单',
                4 => '已发货',
            )
        );
        if ($lang == 'auto') {
            $lang = Ec::getLang();
        }
        return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }


    /**
     * @中转产品装箱单审核状态（新流程）
     * @param string $lang
     * @return array
     */
    public static function traPackingVerifyStatus($lang = 'zh_CN')
    {
        $tmp = array(
            'zh_CN' => array(
                0 => '无',
                1 => '待审核',
                2 => '已审核',
                3 => '审核未通过',
            ),
            'en_US' => array(
                0 => '无',
                1 => '待审核',
                2 => '已审核',
                3 => '审核未通过',
            )
        );
        if ($lang == 'auto') {
            $lang = Ec::getLang();
        }
        return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }
    
    public static function getUserStatu($lang = 'zh_CN'){
    	$tmp = array(
    			'zh_CN' => array(
    					0 => '未激活',
    					1 => '激活',
    			),
    			'en_US' => array(
    					0 => '未激活',
    					1 => '激活',
    			)
    	);
    	if ($lang == 'auto') {
    		$lang = Ec::getLang();
    	}
    	return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }
    
    public static function getPurchasePaymentStatu($lang = 'zh_CN'){
    	$tmp = array(
    			'zh_CN' => array(
    					0 => '撤销(作废)',
    					1 => '待审批',
    					2 => '审批未通过',
    					3 => '已审核(待付款)',
    					4 => '已付款',
    			),
    			'en_US' => array(
    					0 => '撤销(作废)',
    					1 => '待审批',
    					2 => '审批未通过',
    					3 => '已审核(待付款)',
    					4 => '已付款',
    			)
    	);
    	if ($lang == 'auto') {
    		$lang = Ec::getLang();
    	}
    	return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }

    /**
     * @desc 异常处理通知单状态
     * @param string $lang
     * @return array
     */
    public static function receivingAbnormalStatus($lang = 'zh_CN'){
        $tmp = array(
            'zh_CN' => array(
                0 => '未处理',
                1 => '处理中',
                2 => '处理完成',
            ),
            'en_US' => array(
                0 => '未处理',
                1 => '处理中',
                2 => '处理完成',
            )
        );
        if ($lang == 'auto') {
            $lang = Ec::getLang();
        }
        return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }

    /**
     * @desc 异常处理通知单明细状态
     * @param string $lang
     * @return array
     */
    public static function receivingAbnormalItemStatus($lang = 'zh_CN'){
        $tmp = array(
            'zh_CN' => array(
                0 => '未处理',
                1 => '处理中',
                2 => '处理完成',
            ),
            'en_US' => array(
                0 => '未处理',
                1 => '处理中',
                2 => '处理完成',
            )
        );
        if ($lang == 'auto') {
            $lang = Ec::getLang();
        }
        return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }

    /**
     * @desc 退件状态
     * @param string $lang
     * @return array
     */
    public static function returnOrdersStatus($lang = 'zh_CN'){
        $tmp = array(
            'zh_CN' => array(
                1 => '待确认',
                2 => '待处理',
                3 => '处理完成',
                0 => '已作废',
            ),
            'en_US' => array(
                1 => '待确认',
                2 => '待处理',
                3 => '处理完成',
                0 => '已作废',
            )
        );
        if ($lang == 'auto') {
            $lang = Ec::getLang();
        }
        return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }
    /**
     * @ASN 入库单状态
     * @param string $lang
     * @return array
     */
    public static function returnOrdersAction()
    {
        return array(
                '1' => array(
                        '<input type="button" class="asnDiscardBtn baseBtn opBtn" value="' . Ec::Lang('asn_discard') . '">',
                        '<input type="button" class="asnVerifyBtn baseBtn opBtn" value="' . Ec::Lang('verify') . '">',
                ),
                '2' => array(
                        '<input type="button" class="asnDiscardBtn baseBtn opBtn" value="' . Ec::Lang('asn_discard') . '">'
                ),
                '3' => array(),
                '0' => array()
        );
    }
    /**
     * 产品状态
     * @param string $lang
     * @return array
     */
    public static function productStatus($lang = 'zh_CN')
    {
        $tmp = array(
            'zh_CN' => array(
                '1' => '可用',
                '2' => '草稿',
                '0' => '废弃',
            ),
            'en_US' => array(
                '1' => 'Useable',
                '2' => 'Open',
                '0' => 'Discard',
            )
        );
        if($lang == 'auto'){
            $lang = Ec::getLang();
        }
        return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }
    /**
     * 产品操作
     * @param string $lang
     * @return array
     */
    public static function productStatusAction()
    {
        return array(
            '0' => array(
                '<input type="button" class="asnDeleteBtn baseBtn opBtn" value="' . Ec::Lang('delete') . '">'
            ),
            '1' => array(),
            '2' => array(
                '<input type="button" class="asnVerifyBtn baseBtn opBtn" value="' . Ec::Lang('verify') . '">',
                '<input type="button" class="asnDiscardBtn baseBtn opBtn" value="' . Ec::Lang('asn_discard') . '">',
            )
        );
    }
    
    /**
     * 通用生效,失效状态
     */
    public static function effectiveStatus($lang = 'zh_CN') {
    	$tmp = array(
    			'zh_CN' => array(
    					'0' => '生效',
    					'1' => '失效',
    			),
    			'en_US' => array(
    					'0' => 'Enabled',
    					'1' => 'Disabled',
    			)
    	);
    	if($lang == 'auto'){
    		$lang = Ec::getLang();
    	}
    	return isset($tmp[$lang]) ? $tmp[$lang] : $tmp;
    }

}