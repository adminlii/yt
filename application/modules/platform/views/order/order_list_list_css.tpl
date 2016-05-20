<style>
<!--
#module-container .guild h2 {
	cursor: pointer;
	background: none repeat scroll 0% 0% transparent;
}

#module-container .guild h2.act {
	cursor: pointer;
	background: none repeat scroll 0% 0% #fff;
}

#search_more_div {
	display: none;
}

#search_more_div div {
	padding-top: 8px;
}

.input_text {
	width: 150px;
}
#priceFrom,#priceEnd{width:135px;}
.fast_link {
	padding: 8px 10px;
}

.fast_link a {
	margin: 5px 8px 5px 0;
}

.order_detail {
	width: 100%;
	border-collapse: collapse;
}

.order_detail td {
	border: 1px solid #ccc;
}

.opDiv .baseBtn {
	margin-right: 5px;
}

#tag_form_div {
	display: none;
}

#tag_form {
	position: relative;
}

#biaojifahuo_div {
	display: none;
}

.baseExport {
	float: right;
}

#tag_select ul {
	line-height: 22px;
}

.deleteTag {
	float: right;
}
.Tab ul li {
    padding: 0 0px;
}
.Tab ul li li {
	background: none repeat scroll 0 0 #FFFFFF;
	border: 0 none;
	float: none;
	height: 26px;
	line-height: 26px;
}

.define_item {
	display: none;
}

.dialog_div {
	display: none;
}

.set_it_for_allot {
	display: none;
}

#allot_condition_div p {
	line-height: 22px;
	width: 300px;
}

.del_oac_btn {
	
}

.pagination {
	text-align: right;
	height: auto;
}

#aaaaaaaaaaa td {
	height: 1px;
	line-height: 1px;
	overflow: hidden;
	padding-top: 0;
	padding-bottom: 0px;
}

.orderSetWarehouseShipBtn,.orderSetWarehouseShipAutoBtn ,.batchOrderMerge ,.biaojifahuo ,.platform_shipped{
	float: right;
}

.order_line span {
	padding-left: 0px;
}

.status_3 {
	
}

.status_6 {
	color: #0090E1;
}

.status_7 {
	color: red;
}

.checked_count {
	padding: 0 2px;
	color: red;
}

.opration_area {
	height: auto;
}

.opDiv {
	padding: 0px 0 4px 0;
}

.logIcon,.loadLogIcon {
	background: url(/images/sidebar/bg_mailSch.gif) center center;
	display: block;
	height: 16px;
	vertical-align: middle;
	white-space: nowrap;
	width: 18px;
}

.hideIcon{
	display: none;
}

.order_line .logIcon {
	padding: 0px;
}

.operator_note_span {
	
}

.recordNo,.qty,.sku,.unitPrice,.warehouseSkuWrap {
	width: 20%;
	float: left;
}

.recordNo{
	width:135px;
}

.qty{
	width: 50px;
}

.sku {
	width: 20%;
}
.warehouseSkuWrap {
	width: 20%;
}
.unitPrice{
	width: 50px;
}
.ellipsis {
	display: block;
	width: 100%; /*对宽度的定义,根据情况修改*/
	overflow: hidden;
	white-space: nowrap;
	text-overflow: ellipsis;
}

.ellipsis:after {
	/*content: "...";*/
}
#searchForm .input_text_select{width:100px;}

.order_status_tag_container{
	position:absolute;
	z-index:10;
	background:#FFFFFF;
	text-align: center;
    width: 100%;
	left:0;
	display: none;
}
.order_status_tag_container dt{
	border-left: 1px solid #CCCCCC;
	border-right: 1px solid #CCCCCC;
	border-top: 1px solid #CCCCCC;
	border-bottom: 1px solid #CCCCCC;
}

/* 更多的搜索条件--样式开始 */
.table-module-seach{
	background: none repeat scroll 0 0 #D1E7FC;
    border: 2px solid #D1E7FC;
    table-layout: fixed;
    width: 80%;
}			

.table-module-seach td{
	border-bottom: 1px solid #FFFFFF;
	border-right: 1px solid #FFFFFF;
	line-height: 25px;
}
	
.table-module-seach-title{
	width: 89px;
	text-align: right;
}
.table-module-seach-float{
	-moz-border-bottom-colors: none;
    -moz-border-left-colors: none;
    -moz-border-right-colors: none;
    -moz-border-top-colors: none;
	background-color: #FFFFFF;
    border-color: #D9D9D9;
    border-image: none;
    border-style: none solid solid;
    border-width: medium 1px 1px;
    right:5px;
    width: 35%;
	min-width: 500px;
    position: fixed;
    top: -450px;
    z-index: 105;
	border-top: 1px solid #CCCCCC;
    border-left: 1px solid #CCCCCC;
    border-right: 1px solid #CCCCCC;
    border-bottom: 1px solid #CCCCCC;
}
.table-module-seach-float-content{
	max-height: 300px;
	overflow: auto;
}
#search_more_div .table-module-seach-float{
	padding-top: 0px;
}
#search_more_div .table-module-seach-float div{
	padding-top: 1px;
}
.table-module-seach-float h2 {
    border-bottom: 1px solid #E3E3E3;
    font-size: 12px;
    font-weight: bold;
    height: 30px;
    line-height: 30px;
    padding: 0 10px;
}
.give_up_1{color:red;}
.give_up_0{color:green;}

.Tab .sub_status_container{font-weight:normal;
    background: none repeat scroll 0 0 #FFFFFF;
    display: none;
    left: 0;
    position: absolute;
    text-align: center;
    border: 1px solid #CCCCCC;
    border-bottom: 0 none;
    margin-left:-1px;
    width: 100%;
    z-index: 101;
}
.statusTag{display:block;padding:0 11px;}
.Tab a:hover{color:#000;}
.Tab .chooseTag{font-weight:bold;color:#000;}
#module-container .Tab  .sub_status_container dt{	
    border-bottom: 1px solid #CCCCCC;	
}
#module-container .Tab  .sub_status_container a{font-weight: normal;padding:0;}
#module-container .Tab  .sub_status_container a:hover{color:#000;}

#module-container .Tab  .sub_status_container .selected{font-weight:bold;color:#000;}


/* 更多的搜索条件--样式结束 */
-->
#tag_select  .li_1{
	 background: none repeat scroll 0 0 #FFFFFF;
}
#tag_select  .li_2{
	 background: none repeat scroll 0 0 #F1F3E7;
}
#tag_select li.selected {
    background: none repeat scroll 0 0 #ccc;
}
#tag_select li.udt_li {
	cursor: pointer;
}
#tag_select a{
	text-transform: none;
}

.sys_tips_span{
	display: none;
}
.other_filters_tag{
	cursor: pointer;
	color: #0090e1;
	text-decoration: none;
	font-weight: bold;
}
.order_outofstock{
	display: none;
	width: 16px;
	margin-left: 1px;
	margin-right: 2px;
	cursor: pointer;
}
</style>

<link type="text/css" rel="stylesheet" href="/css/public/layout.css" />

<style>
.feedback_tips {
    background: url("/images/base/bg_tips01.png") no-repeat scroll 8px 5px #FFFFE0;
    border: 1px solid #FAE2BA;
    display:none;
    padding: 2px 10px 2px 30px;
    margin-top: 6px;
}

.feedback_tips_text {
    float: left;
    padding-right: 20px;
    width: 95%;
}

.feedback_tips_text p {
    line-height: 22px;
}

.tips_close {
    float: left;
    height: 22px;
    padding-top: 2px;
    width: 35px;
}
/* 隐藏的复制粘贴区域 */
.zclip{
	/*
	background-color: red;
	opacity:0.2;
		*/
	display: none;
}
.order_verify_result_div{
   font-size:14px;
}
</style>
<style>
.sku,.warehouseSkuWrap {
	float: left;
	text-align: left;
}

.sku {
	width: 30%;
}

.warehouseSkuWrap {
	width: 35%;
}
</style>