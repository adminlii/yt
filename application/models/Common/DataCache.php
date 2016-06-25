<?php

class Common_DataCache
{

    /*
     * 清除全部缓存
     */
    public static function clean($subDir = '', $directoryLevel = 0)
    {
        $cache = Ec::cache($subDir, $directoryLevel);
        return $cache->clean('all');
    }

    /*
     * 获取user_right_action数据
     * @param $operation bool 0=>返回缓存数据; 1=>先清除再重写缓存数据
     * @return array/null
     */
    public static function getUserRightAction($operation = 0)
    {
        $cacheName = 'user_right_action';
        $cache = Ec::cache();
        if ($operation == 1) {
            $isRemove = $cache->remove($cacheName);
        }
        if (!$result = $cache->load($cacheName)) {
            $result = Service_UserRightAction::getAll();
            $cache->setLifetime(24 * 3600); // 设置时间，为空则永久
            $cache->save($result, $cacheName);
        }
        return $result;
    }


    public static function getUserResource($operation = 0)
    {
        $cacheName = 'user_right_action_source';
        $cache = Ec::cache();
        if ($operation == 1) {
            $cache->remove($cacheName);
        }
        if (!$result = $cache->load($cacheName)) {
            $result = self::getUserRightAction();
            foreach ($result as $k => $v) {
                $result[$v['ura_module'] . ':' . $v['ura_controller']] = $v['ura_module'] . ':' . $v['ura_controller'];
            }
            $cache->setLifetime(24 * 3600);
            $cache->save($result, $cacheName);
        }
        return $result;
    }

    public static function getUserMenu($operation = 0)
    {
        $cacheName = 'user_menu';
        $cache = Ec::cache();
        if ($operation == 1) {
            $cache->remove($cacheName);
        }
        if (!$result = $cache->load($cacheName)) {
            $results = Service_UserMenu::getByCondition(array(), '*', 0, 0, 'um_sort asc');
            foreach ($results as $k => $v) {
                $result[$v['um_id']] = $v;
            }
            $cache->setLifetime(24 * 3600);
            $cache->save($result, $cacheName);
        }
        return $result;
    }

    public static function getUserRight($operation = 0)
    {
        $cacheName = 'user_right';
        $cache = Ec::cache();
        
        if ($operation == 1) {
            $cache->remove($cacheName);
        }
        if (!$result = $cache->load($cacheName)) {
            $results = Service_UserRight::getByCondition(array(), '*', 0, 0, 'ur_sort asc');
            foreach ($results as $k => $v) {
                $result[$v['ur_id']] = $v;
            }
            $cache->setLifetime(24 * 3600);
            $cache->save($result, $cacheName);
        }
        return $result;
    }

    public static function getSystem($operation = 0)
    {
        $cacheName = 'user_system';
        $cache = Ec::cache();
        if ($operation == 1) {
            $cache->remove($cacheName);
        }
        if (!$result = $cache->load($cacheName)) {
            $results = Service_UserSystem::getByCondition(array(), '*', 0, 0, 'us_sort asc');
            foreach ($results as $k => $v) {
                $result[$v['us_id']] = $v;
            }
            $cache->setLifetime(24 * 3600);
            $cache->save($result, $cacheName);
        }
        return $result;
    }

    public static function getDepartment($operation = 0)
    {
        $cacheName = 'user_department';
        $cache = Ec::cache();
        if ($operation == 1) {
            $cache->remove($cacheName);
        }
        if (!$result = $cache->load($cacheName)) {
            $results = Service_UserDepartment::getByCondition(array(), '*', 0, 0, 'ud_sort asc');
            foreach ($results as $k => $v) {
                $result[$v['ud_id']] = $v;
            }
            $cache->setLifetime(24 * 3600);
            $cache->save($result, $cacheName);
        }
        return $result;
    }

    public static function getUserPosition($operation = 0)
    {
        $cacheName = 'user_position';
        $cache = Ec::cache();
        if ($operation == 1) {
            $cache->remove($cacheName);
        }
        if (!$result = $cache->load($cacheName)) {
            $results = Service_UserPosition::getByCondition(array(), '*', 0, 0, 'up_id asc');
            foreach ($results as $k => $v) {
                $result[$v['up_id']] = $v;
            }
            $cache->setLifetime(24 * 3600);
            $cache->save($result, $cacheName);
        }
        return $result;
    }

    public static function getUserPositionLevel($operation = 0)
    {
        $cacheName = 'user_position_level';
        $cache = Ec::cache();
        if ($operation == 1) {
            $cache->remove($cacheName);
        }
        if (!$result = $cache->load($cacheName)) {
            $results = Service_UserPositionLevel::getAll();
            foreach ($results as $k => $v) {
                $result[$v['upl_id']] = $v;
            }
            $cache->setLifetime(72 * 3600);
            $cache->save($result, $cacheName);
        }
        return $result;
    }
    /**
    public static function getCountry($operation = 0)
    {
    	$cacheName = 'country';
    	$cache = Ec::cache();
    	if ($operation == 1) {
    		$cache->remove($cacheName);
    	}
    	if (!$result = $cache->load($cacheName)) {
    		$results = Service_Country::getByCondition(array(), '*', 0, 0, array('country_name_en asc', 'country_sort asc'));
    		foreach ($results as $k => $v) {
    			$result[$v['country_id']] = $v;
    		}
    		$cache->setLifetime(72 * 3600);
    		$cache->save($result, $cacheName);
    	}
    	return $result;
    }
    */
    public static function getWarehouse($operation = 0, $warehouseId = 0)
    {
    	$cacheName = 'warehouse';
    	$cache = Ec::cache('warehouse');
    	if ($operation == 1) {
    		$cache->remove($cacheName);
    	}
    	if (!$result = $cache->load($cacheName)) {
    		$results = Service_Warehouse::getAll();
    		foreach ($results as $k => $v) {
    			$result[$v['warehouse_id']] = $v;
    		}
    		$cache->setLifetime(72 * 3600);
    		$cache->save($result, $cacheName);
    	}
    	if ($warehouseId) {
    		$result = $result[$warehouseId];
    	}
    	return $result;
    }
    
    public static function getCountry($operation = 0){
    	$cacheName = 'country';
    	$cache = Ec::cache('country');
    	if ($operation == 1) {
    		$cache->remove($cacheName);
    	}
    	if (!$result = $cache->load($cacheName)) {
    		$results = Service_IddCountry::getByCondition(array(), '*',0,0, 'country_code');
    		foreach ($results as $k => $v) {
    			$result[$v['country_code']] = $v;
    		}
    		$cache->setLifetime(72 * 3600);
    		$cache->save($result, $cacheName);
    	}
    	return $result;
    }
    
    public static function getWarehouseSimple($operation = 0, $warehouseId = 0)
    {
    	$cacheName = 'warehouse_simple';
    	$cache = Ec::cache('warehouse');
    	if ($operation == 1) {
    		$cache->remove($cacheName);
    	}
    	if (!$result = $cache->load($cacheName)) {
    		$results = Service_Warehouse::getByCondition(array(), array('warehouse_code', 'warehouse_id'));
    		foreach ($results as $k => $v) {
    			$result[$v['warehouse_id']] = $v['warehouse_code'];
    		}
    		$cache->setLifetime(72 * 3600);
    		$cache->save($result, $cacheName);
    	}
    	if ($warehouseId) {
    		$result = $result[$warehouseId];
    	}
    	return $result;
    }
    
    public static function getWarehouseArea($operation = 0)
    {
    	$cacheName = 'warehouse_area';
    	$cache = Ec::cache('warehouse');
    	if ($operation == 1) {
    		$cache->remove($cacheName);
    	}
    	if (!$result = $cache->load($cacheName)) {
    		$results = Service_WarehouseArea::getByCondition(array('company_code'=>Common_Company::getCompanyCode()), array('wa_code', 'wa_name', 'warehouse_id'), 0, 0, array('wa_code'));
    		foreach ($results as $k => $v) {
    			$result[$v['wa_code']] = $v;
    		}
    		$cache->setLifetime(24 * 3600);
    		$cache->save($result, $cacheName);
    	}
    	return $result;
    }
    
    public static function getLocationType($operation = 0)
    {
    	$cacheName = 'location_type';
    	$cache = Ec::cache('warehouse');
    	if ($operation == 1) {
    		$cache->remove($cacheName);
    	}
    	if (!$result = $cache->load($cacheName)) {
    		$results = Service_LocationType::getByCondition(array('company_code'=>Common_Company::getCompanyCode()), array('lt_code', 'lt_description', 'lt_status', 'warehouse_id'), 0, 0, array('lt_code'));
    		foreach ($results as $k => $v) {
    			$result[$v['lt_code']] = $v;
    		}
    		$cache->setLifetime(24 * 3600);
    		$cache->save($result, $cacheName);
    	}
    	return $result;
    }

    public static function getCurrency($operation = 0)
    {
        $cacheName = 'currency';
        $cache = Ec::cache('common');
        if ($operation == 1) {
            $cache->remove($cacheName);
        }
        if (!$result = $cache->load($cacheName)) {
            $results = Service_Currency::getByCondition(array(), '*', 0, 0, array('currency_code'));
            foreach ($results as $k => $v) {
                $result[$v['currency_code']] = $v;
            }
            $cache->setLifetime(24 * 3600);
            $cache->save($result, $cacheName);
        }
        return $result;
    }

    public static function getShippingMethodSimple($operation = 0)
    {
        $cacheName = 'shipping_method_simple';
        $cache = Ec::cache('ship');
        if ($operation == 1) {
            $cache->remove($cacheName);
        }
        if (!$result = $cache->load($cacheName)) {
            $results = Service_ShippingMethod::getByCondition(array(), array('sm_id', 'sm_code','sm_name_cn', 'warehouse_id', 'sm_status'), 0, 0, array('sm_code','sm_sort'));
            foreach ($results as $k => $v) {
                $result[$v['sm_id']] = $v;
            }
            $cache->setLifetime(24 * 3600);
            $cache->save($result, $cacheName);
        }
        return $result;
    }

    public static function getProductCategory($operation = 0)
    {
        $cacheName = 'product_category';
        $cache = Ec::cache('common');
        if ($operation == 1) {
            $cache->remove($cacheName);
        }
        if (!$result = $cache->load($cacheName)) {
            $results = Service_ProductCategory::getByCondition(array(), array('pc_id', 'pc_name_en','pc_shortname', 'warehouse_id','pc_name'), 0, 0, array('pc_sort_id'));
            foreach ($results as $k => $v) {
                $result[$v['pc_id']] = $v;
            }
            $cache->setLifetime(72 * 3600);
            $cache->save($result, $cacheName);
        }
        $lang = Ec::getLang(1);
        foreach($result as $k=>$v){
            $v['pc_name'] = $v['pc_name'.$lang];
            $result[$k] = $v;
        }
        return $result;
    }
    
    /**
     * 供应商基础数据缓存
     * @param unknown_type $operation
     * @return Ambigous <mixed, false, boolean, string, unknown>
     */
    public static function getSupplier($company_code, $operation = 0)
    {
//     	$cacheName = 'supplier';
//     	$cache = Ec::cache('ship');
//     	if ($operation == 1) {
//     		$cache->remove($cacheName);
//     	}
//     	if (!$result = $cache->load($cacheName)) {
    		$results = Service_Supplier::getByCondition(array('company_code'=>$company_code), array('supplier_id','supplier_code','supplier_name','level','supplier_type','supplier_teamwork_type','supplier_main_category_id','supplier_status','buyer_id'), 0, 0, array());
    		foreach ($results as $k => $v) {
    			$result[$v['supplier_id']] = $v;
    		}
//     		$cache->setLifetime(24 * 3600);
//     		$cache->save($result, $cacheName);
//     	}
    	return $result;
    }
    
    public static function getPurchaseOrderStatus($operation = 0)
    {
    	$cacheName = 'purchase_order_status';
    	$cache = Ec::cache('common');
    	if ($operation == 1) {
    		$cache->remove($cacheName);
    	}
    	if (!$result = $cache->load($cacheName)) {
    		$results = Service_PurchaseOrderStatus::getByCondition(array(), array('po_staus', 'po_staus_code','name_cn', 'name_en'), 0, 0, array('po_staus'));
    		foreach ($results as $k => $v) {
    			$result[$v['po_staus']] = $v;
    		}
    		$cache->setLifetime(72 * 3600);
    		$cache->save($result, $cacheName);
    	}
    	return $result;
    }

    /**
     * 增值服务
     */
    public static function getValueAddedType($operation = 0)
    {
    	$cacheName = 'value_added_type';
    	$cache = Ec::cache('common');
    	if ($operation == 1) {
    		$cache->remove($cacheName);
    	}
    	if (!$result = $cache->load($cacheName)) {
    		$results = Service_ValueAddedType::getByCondition(array('vat_status' => '0'), array('vat_code', 'vat_business_type', 'vat_name_en', 'vat_name_cn'));
    		foreach ($results as $k => $v) {
    			$result[$v['vat_code']] = $v;
    		}
    		$cache->setLifetime(72 * 3600);
    		$cache->save($result, $cacheName);
    	}
    	return $result;
    }
    
    /**
     * 所有额外服务种类
     * @param unknown_type $operation
     * @return Ambigous <mixed, false, boolean, string, unknown>
     */
    public static function getAtdExtraserviceKindAll($operation = 0){
    	$cacheName = 'atd_extraservice_kind';
    	$cache = Ec::cache('atd_extraservice_kind');
    	if ($operation == 1) {
    		$cache->remove($cacheName);
    	}
    	if (!$result = $cache->load($cacheName)) {
    		$results = Service_AtdExtraserviceKind::getAll();
    		foreach ($results as $k => $v) {
    			$result[$v['extra_service_kind']] = $v;
    		}
    		$cache->setLifetime(72 * 3600);
    		$cache->save($result, $cacheName);
    	}
    	return $result;
    }
    
    /**
     * 网站可用额外服务
     * @param unknown_type $operation
     * @return Ambigous <mixed, false, boolean, string, unknown>
     */
    public static function getAtdExtraserviceKindForWeb($operation = 0){
    	$cacheName = 'atd_extraservice_kind';
    	$cache = Ec::cache('atd_extraservice_kind');
    	if ($operation == 1) {
    		$cache->remove($cacheName);
    	}
    	if (!$result = $cache->load($cacheName)) {
    		$results = Service_AtdExtraserviceKind::getCondition(array('extra_service_webvisible' => 'Y'));
    		foreach ($results as $k => $v) {
    			$result[$v['extra_service_kind']] = $v;
    		}
    		$cache->setLifetime(72 * 3600);
    		$cache->save($result, $cacheName);
    	}
    	return $result;
    }
    
    /**
     * 销售产品
     * @param unknown_type $operation
     * @return Ambigous <mixed, false, boolean, string, unknown>
     */
    public static function getProductKind($operation = 0){
    	$cacheName = 'csi_productkind';
    	$cache = Ec::cache('csi_productkind');
    	if ($operation == 1) {
    		$cache->remove($cacheName);
    	}
    	if (!$result = $cache->load($cacheName)) {
    		$results = Service_CsiProductkind::getAll();
    		foreach ($results as $k => $v) {
    			$result[$v['product_code']] = $v;
    		}
    		$cache->setLifetime(72 * 3600);
    		$cache->save($result, $cacheName);
    	}
    	return $result;
    }
    
    /**
     * 货物类型
     * @param unknown_type $operation
     * @return Ambigous <mixed, false, boolean, string, unknown>
     */
    public static function getCargoType($operation = 0){
    	$cacheName = 'atd_cargo_type';
    	$cache = Ec::cache('atd_cargo_type');
    	if ($operation == 1) {
    		$cache->remove($cacheName);
    	}
    	if (!$result = $cache->load($cacheName)) {
    		
    		$sql = "select * from atd_mail_cargo_type";
    		// TODO DB2
    		$db = Common_Common::getAdapterForDb2();
    		$results = $db->fetchAll($sql);
    		foreach ($results as $k => $v) {
    			$result[$v['mail_cargo_code']] = $v;
    		}
    		$cache->setLifetime(72 * 3600);
    		$cache->save($result, $cacheName);
    	}
    	return $result;
    }
    
    /**
     * 汇率的转换
     */
    public static function getHuilv($operation = 0){
    	$cacheName = 'my_huilv';
    	$cache = Ec::cache('my_huilv');
    	if ($operation == 1) {
    		$cache->remove($cacheName);
    	}
    	if (!$result = $cache->load($cacheName)) {
    		$result=Common_Common::getHuilv();
    		$cache->setLifetime(72 * 3600);
    		$cache->save($result, $cacheName);
    	}
    	return $result;
    }
}