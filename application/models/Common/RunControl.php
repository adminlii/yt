<?php
class Common_RunControl {
	public static function run_control_config() {
		$table = 'run_control_config';
		
		$sql = "show tables like '{$table}';";
		$exist = Common_Common::fetchRow ( $sql );
		if (! $exist) {
			$sql = "
			CREATE TABLE IF NOT EXISTS `{$table}` (
			`run_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '运行ID',
			`platform` varchar(64) NOT NULL DEFAULT 'ebay' COMMENT '平台',
			`run_app` varchar(200) NOT NULL COMMENT '运行所属APP',
			`run_interval_minute` int(11) NOT NULL COMMENT '每次运行间隔多久的数据',
			`start_time` time NOT NULL COMMENT '运行有效的时间段',
			`end_time` time NOT NULL COMMENT '运行有效的终止时间段',
			`last_run_time` varchar(30) NOT NULL COMMENT '最后运行时间',
			`status` int(1) NOT NULL DEFAULT '1' COMMENT '状态，1：启用，0 禁用',
			`note` varchar(500) DEFAULT NULL COMMENT '说明',
			PRIMARY KEY (`run_id`),
			KEY `run_app` (`run_app`) USING BTREE,
			KEY `platform` (`platform`) USING BTREE
			) ENGINE=InnoDB AUTO_INCREMENT=465 DEFAULT CHARSET=utf8;
			";
			Common_Common::query ( $sql );
		}
		
		return $table;
	}
	/**
	 * 初始化配置
	 */
	public static function initRunConfig() {
		$db = Common_Common::getAdapter ();
		$table = self::run_control_config ();
		$sql = "select platform,run_app,MAX(run_interval_minute) from run_control GROUP BY platform,run_app order by platform";
		$rows = Common_Common::fetchAll ( $sql );
		foreach ( $rows as $row ) {
			if (empty ( $row ['run_app'] )) {
				continue;
			}
			$sql = "select * from {$table} where platform='{$row['platform']}' and run_app='{$row['run_app']}'";
			$exist = Common_Common::fetchRow ( $sql );
			if (! $exist) {
				$arr = array (
						'platform' => $row ['platform'],
						'run_app' => $row ['run_app'],
						'run_interval_minute' => '240',
						'start_time' => '00:00:00',
						'end_time' => '24:00:00',
						'last_run_time' => '-1day',
						'status' => '1',
						'note' => '' 
				);
				$db->insert ( $table, $arr );
			}
		}
	}
	
	
	/**
	 * 定时任务初始化
	 *
	 * @throws Exception
	 */
	public static function initAccount() {
		try {
			$con = array ();
			$pUsers = Service_PlatformUser::getByCondition ( $con );
			$table = self::run_control_config ();
			self::initRunConfig ();
			
			$db = Common_Common::getAdapter ();
			$sql = "delete  from run_control where run_app is null;";
			$db->query ( $sql );
			$sql = "delete  from run_control_config where run_app is null;";
			$db->query ( $sql );
			
			foreach ( $pUsers as $pUser ) {
				$sql = "select * from {$table} where platform='{$pUser['platform']}'";
				$rows = Common_Common::fetchAll ( $sql );
				foreach ( $rows as $row ) {
					if (empty ( $row ['run_app'] )) {
						continue;
					}
					$sql = "select * from run_control where user_account='{$pUser['user_account']}' and platform='{$pUser['platform']}' and run_app='{$row['run_app']}'";
					$exist = Common_Common::fetchRow ( $sql );
					if (! $exist) {
						$arr = array (
								'platform' => $pUser ['platform'],
								'company_code' => $pUser ['company_code'],
								'user_account' => $pUser ['user_account'],
								'run_app' => $row ['run_app'],
								'run_interval_minute' => $row ['run_interval_minute'],
								'start_time' => $row ['start_time'],
								'end_time' => $row ['end_time'],
								'last_run_time' => date ( 'Y-m-d H:i:s', strtotime ( $row ['last_run_time'] ) ),
								'status' => $row ['status'],
								'note'=>$row['note'],
								'date_create_sys' => date ( 'Y-m-d H:i:s' ) 
						);
						Common_Common::checkTableColumnExist ( 'run_control', 'date_create_sys' );
						Common_Common::checkTableColumnExist ( 'run_control', 'note' );
						$db->insert ( 'run_control', $arr );
					}
				}
			}
			Common_ApiProcess::log ( "==run control init finish==" );
		} catch ( Exception $e ) {
			Common_ApiProcess::log ( "==run control init err[" . $e->getMessage () . "]==" );
		}
	}
}