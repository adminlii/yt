/**
 * 英文的提示信息
 */
var $locale_message_en = 
	[
	 	/*
	 	 * 系统提示
	 	 */
	 	{key:"sys_web_locale",					value:"en"},
		{key:"sys_server_error",				value:"server exception"},
		{key:"sys_service_timeout",				value:"server connection timeout"},
		{key:"sys_message_error",				value:"Prompt the exception information extraction, Code:{0}"},
		{key:"sys_post_error_01",				value:"Request an exception, please contact the relevant technical staff."},
		{key:"sys_date_error",					value:"The start timer is later than the end time,please set again!"},
		{key:"sys_blockui_wait",				value:"Please Wait..."},
		{key:"sys_please_selected",				value:"Please Selected"},
		{key:"sys_common_wait",					value:"Is trying to request, please wait..."},
		{key:"sys_common_processing",			value:"Is trying to processing, please wait..."},
		{key:"sys_save_success",				value:"Successfully saved!"},
		{key:"sys_edit_success",				value:"Successfully modified!"},
		{key:"sys_add_success",					value:"Successfully added!"},
		{key:"sys_no_data",						value:"No Data"},
		{key:"sys_undefined",					value:"Undefined"},
		{key:"sys_can_not_be_empty", 			value:"Can not be empty"},
		{key:"sys_confirm_the_operation",		value:"Confirm the operation?"},
		{key:"sys_confirm_delete", 				value:"Are you sure you want to delete?"},
		{key:"sys_really_confirm", 				value:"Really confirm ?"},
		{key:"sys_fuzzy_srarch",				value:"Fuzzy search"},
		{key:"sys_multiple_search_values",		value:"Enter {0}, each separated by a space "},
		{key:"test", 							value:"This is a test of the configuration information, placeholder: {0} more placeholders: {1}"},
		{key:"back_to_top", 					value:"Back to top"},
		{key:"go_to_bottom", 					value:"Go to bottom"},
		{key:"copy_success", 					value:"Copy success"},
		{key:"sys_log", 						value:"Log"},
		
		
		/*
		 * 公共
		 */
		{key:"processed", 						value:"Processed"},
		{key:"no_processing", 					value:"No processing"},
		{key:"sync", 							value:"Sync"},
		{key:"synchronized", 					value:"Synced"},
		{key:"unsynchronized", 					value:"Unsynchronized"},
		{key:"sys_common_operate", 				value:"Operate"},
		{key:"sys_common_delete", 				value:"Del"},
		{key:"sys_common_add", 					value:"Add"},
		{key:"sys_common_create", 				value:"Create"},
		{key:"sys_common_edit", 				value:"Edit"},
		{key:"sys_common_view", 				value:"View"},
		{key:"sys_common_audit", 				value:"Audit"},
		{key:"sys_common_return", 				value:"Return"},
		{key:"sys_common_submit", 				value:"Submit"},
		{key:"sys_common_through", 				value:"Through"},
		{key:"sys_common_fail", 				value:"Fail"},
		{key:"sys_common_choose", 				value:"Choose"},
		{key:"sys_common_complete",				value:"Complete"},
		{key:"sys_common_status", 				value:"Status"},
		{key:"sys_common_status_desc", 			value:"Status description"},
		{key:"sys_common_log_information", 		value:"Log information"},
		{key:"sys_currency",					value:"Currency"},
		{key:"sys_cancel",						value:"Cancel"},
		{key:"sys_ignore",						value:"Ignore"},
		{key:"sys_account",						value:"Account"},
		{key:"sys_all",							value:"All"},
		{key:"sys_start_time",					value:"StartTime"},
		{key:"sys_end_time",					value:"EndTime"},
		{key:"sys_last_updated",				value:"LastUpdated"},
		{key:"sys_operation_log", 				value:"Operation Log"},
		{key:"sys_property", 					value:"Property"},
		{key:"synchronized_to_eBay", 			value:"SyncToEbay"},
		{key:"sys_send_mail", 					value:"Email"},
		{key:"sys_tips", 						value:"SystemTips"},
		{key:"sys_inventory",					value:"Inventory"},
		{key:"sys_created_time",				value:"CreatedTime"},
		{key:"sys_published_time",				value:"PublishedTime"},
		{key:"sys_Integer",						value:"Integer"},
		
		{key:"week_1", 				value:"Mo"},
		{key:"week_2", 				value:"Tu"},
		{key:"week_3", 				value:"We"},
		{key:"week_4", 				value:"Th"},
		{key:"week_5", 				value:"Fr"},
		{key:"week_6", 				value:"Sa"},
		{key:"week_7", 				value:"Su"},
		
		{key:"months_1", 				value:"Jan"},
		{key:"months_2", 				value:"Feb"},
		{key:"months_3", 				value:"Mar"},
		{key:"months_4", 				value:"Apr"},
		{key:"months_5", 				value:"May"},
		{key:"months_6", 				value:"Jun"},
		{key:"months_7", 				value:"Jul"},
		{key:"months_8", 				value:"Aug"},
		{key:"months_9", 				value:"Sep"},
		{key:"months_10", 				value:"Oct"},
		{key:"months_11", 				value:"Nov"},
		{key:"months_12", 				value:"Dec"},
		
		{key:"select_time", 			value:"SelectTime"},
		{key:"hour", 					value:"Hour"},
		{key:"minute", 					value:"Minute"},
		{key:"second", 					value:"Second"},
		{key:"millisecond", 			value:"Millisecond"},
		{key:"currenttime", 			value:"CurrentTime"},
		{key:"datermine", 				value:"Determine"},
		{key:"sys_warehouse", 			value:"Warehouse"},
		{key:"sys_more", 				value:"More"},
		{key:"sys_hide", 				value:"Hide"},
		
		
		
		/*
		 * 首页
		 */
		{key:"home_my_panel", 						value:"My panel"},
		{key:"home_statistics_panel_displays", 		value:"Statistics panel displays"},
		{key:"home_hide_statistics_panel", 			value:"Hide statistics panel"},
		{key:"home_no_panel_data", 					value:"No panel data"},
		{key:"home_bulletin_board", 				value:"Bulletin Board"},
		{key:"home_no_bulletin", 					value:"No bulletin"},
		{key:"home_bulletin", 						value:"Bulletin"},
		{key:"home_bulletin_hidden", 				value:"Bulletin hidden"},
        {key:"home_my_task_panel", 					value:"My tasks"},
        {key:"home_refresh", 						value:"Refresh"},
        {key:"home_statistics_panel", 				value:"Statistics panel"},
        {key:"home_statistics_panel_date", 			value:"Date"},
        {key:"home_statistics_panel_today", 		value:"Today"},
        {key:"home_statistics_panel_yesterday", 	value:"Yesterday"},
        {key:"home_no_task_data", 					value:"No task data"},

		/*
		 * 客户中心
		 */
		
		{key:"customer_surplus", 				value:"<span class='admin_ziti'>{0}</span> RMB"},
		{key:"customer_surplus_error", 			value:"<span class='admin_ziti' style='font-size:20px;color:#D92B3F;'>loading failure</span> "},
		{key:"customer_menu_null", 				value:"Please at least check the menu"},
		{key:"customer_menu_max", 				value:"The most commonly used menu can not exceed {0}"},
		{key:"customer_no_announcement",        value:"No system announcement"},
		{key:"customer_no_tasks",               value:"No Tasks & messages"},
		{key:"customer_show_more_tasks",        value:"Show more Tasks & Messages"},
		
		/*
		 * 充值
		 */
		{key:"customer_recharge_type",        				value:"请选择充值平台"},
		{key:"customer_recharge_amount_is_empty",        	value:"Please enter the recharge amount "},
		{key:"customer_recharge_amount_limit",        		value:"Recharge amount not less than 10 (up to two decimal places) "},
		{key:"customer_recharge_currency_code_rmb",        	value:"¥ (RMB)"},
		{key:"customer_recharge_currency_code_usd",        	value:"$ (USD)"},
		{key:"customer_recharge_confirm",        			value:"You used to confirm {0} recharge {1} ？"},
		
		/*
		 * 订单
		 */
		{key:"please_select_orders", 				value:"Please select orders."},
		{key:"orders_can_only_choose_one", 			value:"Orders can only choose one."},
		{key:"hide_excessive_sku",        			value:"HideExcessive"},
		{key:"show_remaining_sku",        			value:"ShowRemaining"},
		{key:"order_code_title",        			value:"OrderCode"},
		{key:"order_details",						value:"OrderDetails"},
		{key:"Change_the_address_and_eBay_SKU",		value:"Change the address and Ebay SKU"},
		{key:"cancel_the_changes",					value:"Cancel the changes"},
		{key:"the_product_already_exists_can_not_be_repeated_choice",		value:"The product already exists, can not be repeated choice"},
		{key:"re_edit",														value:"Re-edit"},
		{key:"return_orders_are_finished_editing_the_list",					value:"Return orders are finished editing the list"},
		{key:"remove_the_product",					value:"Remove the product?"},
		{key:"to_confirm_the_operation",			value:"To confirm the operation?"},
		{key:"do_not_send",							value:"Do not send"},
		{key:"cancel_not_send",						value:"Cancel don't send"},
		{key:"search_order_number",					value:"<span>The current total of </span><b style='color:red;font-weight:bold;padding:0 2px;'>{0}</b> orders, has been chosen <b class='checked_count'>0</b>"},
		{key:"buyer_id", 							value:"BuyerID"},
		{key:"buyer_name", 							value:"BuyerName"},
		{key:"seller", 								value:"Seller"},
		{key:"warehouse_order_code_short", 			value:"WH Code"},
		{key:"reference_number", 					value:"Ref NO"},
		{key:"orders_operation_log", 				value:"Orders Operation Log"},
		{key:"syncing_to_service", 					value:"Syncing to “{0}” "},
		{key:"synchronized_to_service", 			value:"Synchronized to “{0}”"},
		
		{key:"paid", 								value:"Paid"},
		{key:"mail_orders_have_clients", 			value:"Order a customer e-mail messages"},
		{key:"orders_contain_auction_commodity", 	value:"Orders contain “auction” commodity"},
		{key:"orders_contain_one_prices_commodity", value:"Orders contain “one prices” commodity"},
		{key:"marked_shipped_and_synchronized_to_eBay",		value:"Marked shipped and synchronized to eBay"},
		{key:"shipped_to_manually_tag_on_eBay",				value:"Shipped to manually tag on eBay"},
		{key:"INR", 										value:"INR"},
		{key:"SNAD", 										value:"SNAD"},
		{key:"UPI", 										value:"UPI"},
		{key:"cancel_transaction", 							value:"Cancel transaction"},
		{key:"the_order_exists_dispute", 					value:"The order exists dispute({0})"},
		{key:"have_been_evaluated", 						value:"Have been evaluated({0})"},
		{key:"paypal_refund_already_exists", 				value:"Paypal refund already exists(Subtotal:{0})"},
		{key:"order_desc", 									value:"OrderDesc"},
		{key:"service_notes", 								value:"ServiceNotes"},
		{key:"exception_information", 						value:"Execption"},
		
		{key:"order_amount", 						value:"OrderAmount"},
		{key:"order_total", 						value:"Total"},
		{key:"freight", 							value:"Freight"},
		{key:"turnover", 							value:"Turnover"},
		{key:"transaction", 						value:"Transaction"},
		{key:"platform_fees", 						value:"PayPal fees"},
		
		{key:"country_name", 						value:"Country"},
		{key:"distribution_platform", 				value:"Platform Delivery"},
		{key:"ship_warehouse", 						value:"Shipping Warehouse"},
		{key:"unallocated",							value:"Unallocated"},
		{key:"warehouse_shipping", 					value:"Warehouse Shipping"},
		{key:"trackingNo", 							value:"Tracking NO"},
		{key:"returnTrackingNo", 					value:"Return Tracking NO"},
		
		{key:"s_country_name", 						value:"CC"},
		{key:"s_distribution_platform", 			value:"PD"},
		{key:"s_ship_warehouse", 					value:"WH"},
		{key:"s_warehouse_shipping", 				value:"WD"},
		{key:"s_trackingNo", 						value:"TR#"},
		
		{key:"none", 								value:"None"},
		{key:"order_date_create", 					value:"Create"},
		{key:"order_date_pay", 						value:"Pay"},
		{key:"order_date_audit", 					value:"Audit"},
		{key:"order_date_delivery", 				value:"Dispatch"},
		
		{key:"product_not_shipped", 				value:"Product not shipped"},
		{key:"normal_processing_products", 			value:"Normal processing products"},
		{key:"unit_price",			 				value:"Price"},
		{key:"warehouse_sku", 						value:"WH SKU"},
		{key:"click_to_view_inventory", 			value:"Click to view inventory"},
		{key:"rma_management", 						value:"RMA"},
		{key:"unpaid", 								value:"Unpaid"},
		{key:"create_order", 						value:"CreateOrder"},
		{key:"query_association", 					value:"QueryAssociation"},
		
		{key:"orders_of_positions_rules", 			value:"Orders of positions Rules"},
		{key:"greater_than_or_equal", 				value:"Greater than or equal"},
		{key:"greater_than", 						value:"Greater than"},
		{key:"less_than_or_equal", 					value:"Less than or equal"},
		{key:"less_than", 							value:"Less than"},
		{key:"delete_the_rule", 					value:"Delete the rule?"},
		{key:"designated_account", 					value:"Designated account"},
		{key:"specify_the_type_of_transport", 		value:"Specify the type of transport"},
		{key:"designated_country", 					value:"Designated country"},
		{key:"specified_range", 					value:"Specified range"},
		{key:"has_selected_x_SKU", 					value:"Has selected {0} SKU"},
		{key:"specified_items", 					value:"Specified Items"},
		{key:"please_select_warehouse", 			value:"Please select warehouse"},
		
		{key:"orders_audit_rules", 					value:"Orders audit rules"},
		{key:"view_address", 						value:"ViewAddress"},
		{key:"view_amounts", 						value:"ViewAmounts"},
		{key:"please_enter_tag_name", 				value:"Please select or add a tag name."},
		{key:"confirm_delete_custom_tags", 			value:"Sure you want to to delete Tags?"},
		{key:"confirm_marker_orders_shipped", 		value:"Confirm the selection marker orders shipped?"},
		{key:"guestbook", 							value:"Guestbook"},
		{key:"all_stock", 							value:"All orders out of stock."},
		{key:"part_stock", 							value:"SKU=>{0}(ETA:{1}) lack of {2};"},
		{key:"product_details", 					value:"Product Details"},
		{key:"confirm_order_on_platform_marked_shipments",			value:"Confirm that the order has been done on the platform marked shipments？"},
		{key:"service_level",						value:"ServiceLevel"},
		{key:"amazon_mark_delivery_01",						value:"Marked delivery, and sync to Amazon "},
		{key:"amazon_mark_delivery_02",						value:"Update the tracking number and other information, waiting for synchronization "},
		{key:"amazon_mark_delivery_03",						value:"Mark shipments, waiting for a response Amazon "},
		{key:"amazon_mark_delivery_04",						value:"Shipped at Amazon manually tag"},
		{key:"hand_success_code_x",							value:"Creating successful, single number: {0}"},
		{key:"order_double_click_copy_code",				value:"On the 'OrderCode' Click to copy a single number"},
		{key:"order_code",									value:"OrderCode"},
		{key:"common_upload_tips",							value:"Success: {0}.&nbsp;&nbsp;&nbsp;&nbsp;Failure:{1}"},
		{key:"refund_amount_secondary_confirmation",		value:"Refund amount, the excess amount of orders, ask whether to continue?"},
		{key:"rma_cancel_order_tips",						value:"If prompted to cut a single failure, try to remove the [Cancel Order]"},
		{key:"unpaid_to_be_shipped_tips",					value:"When payment orders will not be shipped into the audit order, make sure the customer has paid. \n Doing so may result in customer payment but without delivery! \n To confirm that the order is selected to be shipped into the audit it?"},
		{key:"make_sure_the_order_removed_from_the_exported_in_excel",		value:"Make sure the order removed from the exported in excel"},
		{key:"confirm_cancel_order",						value:"To check the order confirmation Intercept it?"},
		{key:"confirm_cnaecl_order_excel_del",				value:"If you Intercept, make sure that the order to remove \n from the exported excel in Confirmation to continue?"},
		{key:"intercept_after_transfection_Not_ready",		value:"Intercept after transfection Not-ready"},
		{key:"please_enter_intercept_reason",				value:"Please enter Intercept reason"},
		{key:"order_process_result",						value:"Result"},
		
		{key:"order_verify_result_01",						value:"There are {0} orders not submitted to the warehouse "},
		{key:"order_verify_result_03",						value:"Successful <span class='status_3'>{0}</span> Article, "},
		{key:"order_verify_result_02",						value:"Failure <span class='status_7'>{0}</span> Article,"},
		{key:"order_verify_result_04",						value:"Where insufficient inventory <span class='status_6'>{0}</span> Article"},
		{key:"order_verify_result_05",						value:"Order processing failed,Reason"},
		{key:"order_verify_result_06",						value:"Successful order processing, WarehouseCode"},
		{key:"re_manually_choose_warehouse",				value:"Re ManuallyChooseWarehouse"},
		{key:"audit_warehouse_orders",						value:"Audit warehouse orders"},
		{key:"order_split",									value:"Order Split"},
		
		{key:"please_select_at_least_two_orders",			value:"Please select at least two orders"},
		{key:"combined_orders_address_tips",				value:"Combined orders address information is as follows "},
		{key:"confirm_combined_orders_tips",				value:"Order confirmation will choose to merge?"},
		{key:"auto_combined_orders_tips",					value:"The system will automatically compare and merge"},
		{key:"confirm_auto_combined_orders_tips",			value:"Order confirmation will be selected automatically merge?"},
		{key:"manually_choose_warehouse_tips",				value:"If you specify the order, the system will specify the order which not set position in accordance with the rules already set a good position to points of positions. \n If you do not specify the order will be shipped all orders audit has been set in accordance with rules of good points positions positions, \n Are you sure? "},
		{key:"manually_choose_warehouse_wait_tips",			value:"Orders are automatically assigned warehouse and transportation, please wait ..."},
		{key:"manually_choose_warehouse_result",			value:"The results are as follows: success {0}, fail {1}"},
		{key:"manually_choose_warehouse_fail",				value:"The following is the order allocation failure"},
		{key:"manually_choose_warehouse_success",			value:"The following is the distribution of successful orders"},
		
		{key:"SKU_does_not_exist",						value:"SKU does not exist"},
		{key:"SKU_already_exist_num_of_cumulative",		value:"SKU: {0} already exists, whether the number of cumulative"},
		
		{key:"track_inTransit",							value:"InTransit"},
		{key:"track_exception",							value:"Exception"},
		{key:"track_fail",								value:"Fail"},
		{key:"track_delivered",							value:"Delivered"},
		
		{key:"mark_shipping_error",						value:"Please select shipping movements mark"},
		{key:"hand_labeled_shipments",					value:"Manually tagged shipments."},
		
		{key:"order_allot_contain",						value:"Contain characters"},
		{key:"order_allot_uncontain",					value:"Not contain characters"},
		{key:"order_allot_sku_like_error_001",			value:"Please add data"},
		{key:"order_allot_sku_like_error_002",			value:"Please specify the matching string"},
		{key:"order_tag_title",							value:"Tag"},
		{key:"out_of_stock",							value:"Out of stock"},
		
		/*
		 * RMA
		 */
		{key:"select_rma_reson",        			value:"Select the RMA reason!"},
		{key:"rma_reson_connot_mod",        		value:"RMA reason: the “{0}” cannot be modified"},
		{key:"display_name_connot_empty",   		value:"‘Display Name’ can not be empty! "},
		{key:"display_name_connot_more_than_32",	value:"‘Display Name’ can not be more than 32 bytes (16 characters)!"},
		{key:"content_connot_empty",        		value:"‘Content’ can not be empty!"},
		{key:"content_connot_more_than_200",        value:"‘Content’ can not be more than 200 bytes (100 characters)!"},
		{key:"wrong_action",        				value:"Wrong action!"},
		{key:"Rreturn_for_resending",        			value:"Rreturn for resending"},
		{key:"Create_warehouse_return",        		value:"Create warehouse return"},
		{key:"Create_warehouse_return_confirm",     value:"You recognized the need to create a 'warehouse return'?"},
		
		{key:"select_returned_num", 				value:"Selected Returned of({0})"},
		{key:"returned_operation_01", 				value:"Determine the selected pieces void return it? "},
		{key:"returned_operation_02", 				value:"Determine the selected back pieces resend it? "},
		{key:"returned_operation_03", 				value:"Determine the selected items marked as storage retire it? "},
		{key:"returned_operation_04", 				value:"Sorry, orders can only choose one operation! "},
		{key:"returned_operation_05", 				value:"Sorry, you can only modify customer return orders created! "},
		{key:"confirm_returned", 					value:"Single pieces confirmation retreat"},
		{key:"edit_returned", 						value:"Single pieces Edit"},
		{key:"rma_operat_select_error_tips", 		value:"Please select the type of operation!"},
        {key:"returned_operation_06", 				value:"Sure will be selected out finished？"},

		/*
		 * ebay消息
		 */
		{key:"assignment_type",        				value:"Assignment Type"},
		{key:"add_custom_type",        				value:"Add custom type"},
		{key:"batch_reply",        					value:"Batch Reply"},
		{key:"bulk_distribution",        			value:"Bulk distribution"},
		{key:"mark_reply",        					value:"Mark Reply"},
		{key:"revocation_reply",        			value:"Revocation Reply "},
		
		{key:"unread",        						value:"Unread"},
		{key:"read",        						value:"Read"},
		{key:"receive_time",        				value:"Receive"},
		{key:"response_time",        				value:"Response"},
		
		{key:"choose_template",        				value:"Choose a template"},
		{key:"custom_type_cannot_empty",    		value:"“Custom Types” can not be empty."},
		{key:"custom_type_cannot_than_100", 		value:"“Custom Types” length can not exceed 100 characters."},
		{key:"please_select_message", 				value:"Please select at least one message."},
		{key:"mark_selected_messages_as_read", 						value:"Mark selected messages as read?"},
		{key:"mark_successfully_and_error", 						value:"Mark successfully <span style='color:#1B9301;'> {0} </span> a marked failure <span style='color:red;'> {1} </span> a, select the record failed to mark news, errors were: <br/> "},
		{key:"batch_marking_success", 								value:"Batch marking success"},
		{key:"ebay_message_only_separate", 							value:"eBay message only separate revocation does not support batch operations! "},
		{key:"revocation_failed_because", 							value:"Revocation failed because:"},
		{key:"delete_the_selected_messages_as_read", 				value:"Delete the selected messages as read?"},
		{key:"messages_view_reply", 								value:"Messages View / Reply"},
		{key:"please_select_the_progress", 							value:"Please select the progress!"},
		{key:"the_presence_of_the_operator_message", 				value:"The message has not been replaced in the presence of the operator, please check."},
		{key:"reply_not_to_ebay", 									value:"Reply not to eBay"},
		{key:"reply_to_succeed_goto_next_message", 					value:"Reply to succeed, whether directly check the next message?"},
		{key:"sure_you_want_to_reply_to_the_message_is_marked_as", 	value:"Sure you want to reply to the message is marked as?"},
		{key:"mark_replies_success_goto_next_message", 				value:"Mark replies success, whether directly check the next message?"},
		{key:"allocation_succeeds_goto_next_message", 				value:"Allocation succeeds, whether directly check the next message?"},
		{key:"customer_order_history", 								value:"Customer order history"},
		{key:"you_have_completed_a_batch_close_the_page", 			value:"<b>You have completed a batch mail reply</b>(<span style='color:#008000;'>Click 'OK' to close the page</span>)."},
		{key:"the_email_account_information_does_not_exist", 		value:"The e-mail account information does not exist"},
		{key:"there_is_no_message", 								value:"There is no message"},
		{key:"email_letter",										value:"Letter"},
		
		/*
		 * 站内信
		 */
		{key:"abnormal", 									value:"Abnormal"},
		{key:"blocked", 									value:"Blocked"},
		{key:"blocking", 									value:"Blocking"},
		{key:"station_letters_synced_not_operate", 			value:"The station is believed to have synchronized, you can not operate."},
		{key:"please_select_station_letters", 				value:"Please select Station Letters."},
		{key:"intercept_abnormal", 							value:"Intercept abnormal, please try again later."},
		{key:"successfully_intercepted", 					value:"Successfully intercepted Station Letters <b style='color:red;'>{0}</b>."},
		{key:"station_letters", 							value:"Station letters"},
		
		/*
		 * 消息模板
		 */
		{key:"category_under_no_template", 					value:"该分类下还没有消息模板，请点击“添加模板”进行添加"},
		{key:"let_the_confirmation_message_template_failure", 				value:"Let the confirmation message template '<span style='color:red;'>failure</span>'?"},
		{key:"confirmation_by_the_audit_the_message_template", 				value:"Confirmation '<span style='color:red;'>by the audit</span>' the message template?"},
		{key:"no_next", 									value:"No next"},
		{key:"no_category", 								value:"No Category"},
		{key:"please_select_message_category", 				value:"Please select a template categories in the '<b>Message Templates</b>'."},
		{key:"sure_you_want_to_delete_the_template", 		value:"Sure you want to '<span style='color:red;'>delete</span>' the template?"},
		
		/*
		 * 客户评价
		 */
		{key:"view_contacts_message", 							value:"Contacts message"},
		{key:"please_select_at_least_one_evaluation", 			value:"Please select at least one evaluation"},
		
		/*
		 * CASE
		 */
		{key:"go_paypal", 				value:"Go PayPal"},
		{key:"process", 				value:"Process"},
		{key:"CASE_problem_solving", 	value:"CASE problem solving"},
		
		/*
		 * 费用
		 */
		{key:"please_fill_in_the_Paypal_transaction_id", 					value:"Please fill in the Paypal Transaction_ID."},
		{key:"paypal_transaction_id_does_not_matches_the_rule_length", 		value:"Paypal transaction number does not meet the length rules, please check."},
		{key:"paypal_transaction_id_does_not_matches_the_rule_combination", value:"Paypal transaction number is a combination of letters and numbers, please check."},
		{key:"please_select_paypal_account", 					value:"Please select Paypal account."},
		{key:"please_select_the_trading_period", 				value:"Please select the trading period."},
		{key:"start_time_must_be_before_the_end_of_time", 		value:"Start time must be before the end of time."},
		{key:"intervals_not_exceed_x_hours.", 					value:"Intervals not exceed {0} hours."},
		
		{key:"fee_type", 					value:"feeType"},
		{key:"charge_type", 				value:"chargeType"},
		{key:"cargo_type", 					value:"cargoType"},
		{key:"volume_weight_billing", 		value:"volumeWeightBilling"},
		{key:"isTracking", 					value:"isTracking"},
		
		{key:"total_fee", 					value:"totalFee"},
		{key:"basic_fee", 					value:"basicFee"},
		{key:"surcharge", 					value:"Surcharge"},
		{key:"handling_charges", 			value:"handlingCharges"},
		
		{key:"registration_fee", 			value:"registrationFee"},
		{key:"fuel_surcharge", 				value:"fuelSurcharge"},
		
		{key:"billing_weight", 				value:"billingWeight"},
		{key:"the_single_most_important", 	value:"singleMostImportant"},
		{key:"number_in_pieces", 			value:"numberInPieces"},
		{key:"item_selling_price", 			value:"Price"},
		
		/*
		 * 系统设置
		 */
		{key:"please_select_the_account", 				value:"Please select the account you want to bind the store"},
		{key:"service_binding_shops", 					value:"Service Binding shops"},
		{key:"binding_shops_operator", 					value:"Binding shops operator"},
		{key:"service_binding", 						value:"Service Binding"},
		{key:"operator_binding", 						value:"Operator binding"},
		{key:"please_enter_eaby_shops_account", 		value:"Please enter eBay shops account"},
		{key:"please_enter_a_shops_referred", 			value:"Please enter a shops referred"},
		{key:"please_enter_a_display_name", 			value:"Please enter a display name"},
		{key:"authorized_account", 						value:"Authorized account"},
		{key:"failed_to_get_SessionID", 				value:"Failed to get SessionID"},
		{key:"get_token_failed", 						value:"Get Token Failed"},
		
		{key:"reauthorization", 						value:"Reauthorization"},
		{key:"task_initialization", 					value:"Task initialization"},
		{key:"task_initialization_confirm", 			value:"Confirm the account re-initialize the timer task"},
		{key:"new_ebay_account_authorization", 			value:"New eBay account Authorization"},
		{key:"eBay_account_reauthorization", 			value:"eBay account reauthorization"},

		{key:"please_enter_aliexpress_shops_account", 	value:"Please enter Aliexpress shops account"},
		{key:"please_enter_temp_authorization_code", 	value:"Please enter Aliexpress temporary authorization code"},
		{key:"please_enter_app_key", 					value:"Please enter Aliexpress APP Key"},
		{key:"please_enter_app_signature", 				value:"Please enter Aliexpress APP Signature"},
		
		{key:"new_aliexpress_account_authorization", 	value:"New Aliexpress account Authorization"},
		{key:"aliexpress_account_reauthorization", 		value:"Aliexpress account reauthorization"},
		
		{key:"all_account", 							value:"All account"},
		{key:"updated_product_relationship_confirm", 	value:"Updated product relationship?"},
		{key:"del_sku_correspondence_between_confirm_01",	value:"SKU：{0};\ncorresponding sub-SKU：{1};\nan account：{2}\n"},
		{key:"del_sku_correspondence_between_confirm_02",	value:"Confirm that you want to delete this combination a relationship?"},
		{key:"delete_the_sub_sku",						value:"You sure you want to delete the sub-SKU it?"},
		
		{key:"online_sku",								value:"OnlineSKU"},
		{key:"recently_replenishment_time",				value:"RecentlyReplenishmentTime"},
		{key:"replenishment_log",						value:"ReplenishmentLog"},
		{key:"manually_update",							value:"ManuallyUpdate"},
		{key:"join_replenishment_blacklist",			value:"JoinReplenishmentBlacklist"},
		{key:"lifting_replenishment_blacklist",			value:"LiftingReplenishmentBlacklist"},
		
		{key:"check_the_replenishment_products_and_set",					value:"Please check the products you need to set replenishment, and set the number of replenishment"},
		{key:"set_up_the_product_to_determine_replenishment_quantities",	value:"Set up the product to determine replenishment quantities?"},
		{key:"operation_successful_product",								value:"Operation successful"},
		{key:"operation_failed_product",									value:"Operation failed"},
		{key:"reasons_for_failure",											value:"Reasons for failure"},
		{key:"set_the_number_of_the_batch_replenishment",					value:"Set the number of the batch replenishment"},
		{key:"you_sure_you_want_to_sync_to_eBay",							value:"You sure you want to sync to eBay?"},
		{key:"the_unified_set_replenishment_quantity",						value:"The unified set replenishment quantity"},
		{key:"please_check_the_products_need_to_be_synchronized",			value:"Please check the products need to be synchronized"},
		{key:"there_is_no_need_to_synchronize_the_item",					value:"There is no need to synchronize the Item"},
		{key:"replenishment_number",										value:"Replenishment number"},
		{key:"blacklist",													value:"Blacklist"},
		{key:"the_sku_blacklist",											value:"The SKU: {0} blacklist?"},
		{key:"the_sku_released_from_the_blacklist",							value:"The SKU: {0} released from the blacklist?"},
		{key:"to_download_the_latest_data_to_determine_local",				value:"To download the latest data to determine local?"},
		{key:"please_check_the_product",									value:"Please check the product"},
		{key:"recently_100",												value:"Recently 100"},
		{key:"integer_negative_means no_automatic_replenishment",			value:"Integer, negative means no automatic replenishment"},
		{key:"not_automatic_replenishment",									value:"Not automatic replenishment"},
		{key:"trial_run",													value:"Join trial run"},
		{key:"trial_run_remove",											value:"Remove"},
		{key:"trial_run_remove_confirm",									value:"The SKU: {0} released from the trial run?"},
		
		{key:"pls_select_off_shelf_product",								value:"Please select the off-shelf products"},
		{key:"pls_select_shelves_product",									value:"Please select the shelves product"},
		{key:"confirm_off_shelf_product",									value:"To determine the product off-shelf it?"},
		{key:"off_shelf_product",											value:"Product off-shelf"},
		{key:"shelves_product",												value:"Product shelves"},
		{key:"shelves_set",													value:"Shelves setting"},
		{key:"off_shelf_set",												value:"Off-shelf setting"},
		{key:"confirm_shelves_qty",											value:"Determine the settings shelves qty?"},
		{key:"confirm_off_shelf_qty",										value:"Determine the settings Off-shelf qty?"},
		
		/*
		 * 折扣
		 */
		{key:"view_history_discounts",									value:"View History Discounts"},
		
		/*
		 * 客户管理
		 */
		{key:"customer_details",										value:"Customer Details"},
		{key:"switch_to_a_single_search",								value:"Switch to a single search"},
		{key:"switch_to_batch_search",									value:"Switch to Batch Search"},
		{key:"please_select_customers",									value:"Please select customers"},
		{key:"please_fill_in_the_title",								value:"Please fill in the title"},
		{key:"please_fill_in_the_content",								value:"Please fill in the content"},
		{key:"exception_handling_as_follows",							value:"Exception handling as follows"},
		{key:"choose_a_template",										value:"Choose a template"},
		{key:"notice_of_cancellation_station_letters",					value:"Message will later batch synchronization on eBay, if you need to cancel, please unsynchronized before operation, after synchronization, will not cancel .. \n agrees Click 'OK' button, otherwise click 'Cancel' button "},
		{key:"customer_management_does_not_support_operator", 				value:"Note that the client does not support the message template management [operator], double-check the contents of your letter!"},
		{key:"new_shipping_method_platform", 				value:"Customer Selected Shipping Method"},
		
		
	];