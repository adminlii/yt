<div id="content" xmlns="http://www.w3.org/1999/html">
<style>
.module-title {
    font-weight: bold;
    padding-right: 5px;
    text-align: right;
}
</style>
    <div class="depotState_title">


        <img align="absmiddle" style="margin-right:5px;" src="/images/pack/icon_depot01.gif">
        <span style="font-size:14px; font-weight:bold;"><{t}>customerManager<{/t}></span>
        &nbsp;&nbsp;&gt;&nbsp;&nbsp;<{t}>customerDetail<{/t}>：
        <a style="color:#666; text-decoration:underline;" href="#"><{t}>customerCode<{/t}>：<{$customer.customer_code}></a>

    </div>

    <div class="depotState" style="margin-top:0px;">

        <table width="100%" cellspacing="0" cellpadding="0" border="0" class="depotForm">
        	<tbody>
        		<tr>
        			<td width="10%" class="depot_path"><{t}>customerCode<{/t}>：</td>
	                <td width="20%"><b><{$customer.customer_code}></b></td>
	                <td width="12%"  class="depot_path">First Name：</td>
	                <td width="22%"><{$customer.customer_firstname}></td>
	                <td class="depot_path"><{t}>status<{/t}>：</td>
                	<td>
                		<span style="color: <{if $customer.customer_status eq 2}>#1B9301;<{else}>red;<{/if}>">
                			<b><{$statusArr[$customer.customer_status]}></b>
                		</span>
                	</td>
        		</tr>
        		<tr>
        			<td class="depot_path"><{t}>companyName<{/t}>：</td>
                	<td><{$customer.customer_company_name}></td>
	                <td class="depot_path">Last Name：</td>
                	<td class="word_green"><{$customer.customer_lastname}></td>
	                <td class="depot_path"><{t}>registerStatus<{/t}>：</td>
                	<td><{$regStatus[$customer.reg_step]}></td>
        		</tr>
        		<tr>
        			<td class="depot_path"><{t}>BusinessName<{/t}>：</td>
                	<td><{$customer.trade_name}></td>
	                <td class="depot_path"><{t}>telphone<{/t}>：</td>
                	<td><{$customer.customer_telephone}></td>
	                <td class="depot_path"><{t}>registerTime<{/t}>：</td>
                	<td><{$customer.customer_reg_time}></td>
        		</tr>
        		<tr>
        			<td class="depot_path"><{t}>BusinessCode<{/t}>：</td>
                	<td><{$customer.trade_co}></td>
	                <td class="depot_path"><{t}>fax<{/t}>：</td>
                	<td><{$customer.customer_fax}></td>
	                <td class="depot_path"><{t}>updateTime<{/t}>：</td>
                	<td><{$customer.customer_update_time}></td>
        		</tr>
        		<tr>
        			<td class="depot_path"><{t}>currencyCode<{/t}>：</td>
                	<td><{$customer.customer_currency}></td>
	                <td class="depot_path">email：</td>
                	<td><{$customer.customer_email}></td>
	                <td class="depot_path"><{t}>lastPassTime<{/t}>：</td>
                	<td><{$customer.password_update_time}></td>
        		</tr>
        		<tr>
        			<td class="depot_path">
        				<{t}>AccountAmount<{/t}>：
        			</td>
                	<td>
                		<b style="font-size:14px;color: #1B9301;"><{$balance.cb_value}></b>
                	</td>
	                <td class="depot_path"><{t}>ElectronicSignatures<{/t}>：</td>
                	<td><{$customer.customer_signature}></td>
	                <td  class="depot_path"><{t}>lastLoginTime<{/t}>：</td>
                	<td><{$customer.last_login_time}></td>
        		</tr>
        		<tr>
        			<td class="depot_path" >
        				<{t}>Amountfrozen<{/t}>：
        			</td>
                	<td >
                		<b style="font-size:14px;color: red;"><{$balance.cb_hold_value}></b>
                	</td>
	                <td class="depot_path"><{t}>customerlogoUrl<{/t}>：</td>
                	<td><{$customer.customer_logo}></td>
	                <td class="depot_path"></td>
                	<td></td>
        		</tr>
        		<tr>
        			<td class="depot_path"><{t}>SalesRepresentative<{/t}>：</td>
                	<td><{$seller.user_name}></td>
	                <td  class="depot_path"><{t}>CustomerServiceRepresentative<{/t}>：</td>
                	<td><{$server.user_name}></td>
	                <td class="depot_path"></td>
                	<td></td>
        		</tr>
        	</tbody>
        	
        	<!-- 
        	@Author：Frank
        	@Date：2014-3-14 2:47:35
        	@Note：使用新的排版，进行屏蔽
            <tbody><tr>
                <td width="10%" class="depot_path"><{t}>customerCode<{/t}>：</td>
                <td width="20%"><{$customer.customer_code}></td>
                <td width="12%"  class="depot_path">First Name：</td>
                <td width="22%"><{$customer.customer_firstname}></td>
                <td width="10%"  class="depot_path">Last Name：</td>
                <td width="26%" class="word_green"><{$customer.customer_lastname}></td>
            </tr>
            <tr>
                <td  class="depot_path">email：</td>
                <td><{$customer.customer_email}></td>
                <td  class="depot_path"><{t}>currencyCode<{/t}>：</td>
                <td><{$customer.customer_currency}></td>
                <td  class="depot_path"><{t}>telphone<{/t}>：</td>
                <td><{$customer.customer_telephone}></td>
            </tr>
            <tr>
                <td  class="depot_path"><{t}>fax<{/t}>：</td>
                <td><{$customer.customer_fax}></td>
                <td  class="depot_path"><{t}>status<{/t}>：</td>
                <td><{$statusArr[$customer.customer_status]}></td>
                <td  class="depot_path"><{t}>companyName<{/t}>：</td>
                <td><{$customer.customer_company_name}></td>
            </tr>
            <tr>
                <td  class="depot_path"><{t}>BusinessName<{/t}>：</td>
                <td><{$customer.trade_name}></td>
                <td  class="depot_path"><{t}>BusinessCode<{/t}>：</td>
                <td><{$customer.trade_co}></td>
                <td  class="depot_path"><{t}>customerlogoUrl<{/t}>：</td>
                <td><{$customer.customer_logo}></td>
            </tr>
            <tr>
                <td  class="depot_path"><{t}>SalesRepresentative<{/t}>：</td>
                <td><{$seller.user_code}></td>
                <td  class="depot_path"><{t}>CustomerServiceRepresentative<{/t}>：</td>
                <td><{$server.user_code}></td>
                <td  class="depot_path"><{t}>ElectronicSignatures<{/t}>：</td>
                <td><{$customer.customer_signature}></td>
            </tr>
            <tr>
                <td  class="depot_path"><{t}>registerStatus<{/t}>：</td>
                <td><{$regStatus[$customer.reg_step]}></td>
                <td  class="depot_path"><{t}>registerTime<{/t}>：</td>
                <td><{$customer.customer_reg_time}></td>
                <td  class="depot_path"><{t}>lastLoginTime<{/t}>：</td>
                <td><{$customer.last_login_time}></td>
            </tr>
            <tr>
                <td  class="depot_path"><{t}>AccountAmount<{/t}>：</td>
                <td><{$balance.cb_value}></td>
                <td  class="depot_path"><{t}>Amountfrozen<{/t}>：</td>
                <td><{$balance.cb_hold_value}></td>
                <td  class="depot_path"></td>
                <td></td>
            </tr>
            <tr>
                <td  class="depot_path"><{t}>updateTime<{/t}>：</td>
                <td><{$customer.customer_update_time}></td>
                <td  class="depot_path"><{t}>lastPassTime<{/t}>：</td>
                <td><{$customer.password_update_time}></td>
                <td  class="depot_path"></td>
                <td></td>
            </tr>
            </tbody>
             -->
        </table>



        <div class="clr"></div>
    </div>

    <div class="depotState" style="margin-bottom: 15px;">
        <div class="depotTab2">
            <ul>
                <li class="chooseTag">
                    <a href="javascript:void(0);" id='tab_address' class='tab' style="cursor:pointer"><{t}>customerAddress<{/t}></a>
                </li>
            </ul>
        </div>

        <div class="depot_locus" style="border-top:none; width:95%;margin-bottom: 15px;">
			<div class="tabContent" id="address" >
				<{foreach from=$addressList item=row}>
	                 <table width="350px;" style="float: left;margin-left: 10px;" border="0" cellpadding="0" cellspacing="0" class="table-module" >
	                 	<tr class="table-module-title" style="display: none;"><td class="ec-center"></td><td class="ec-center"></td></tr>
	                    <tr class="table-module-title">
	                        <td class="module-title" width="85"><{t}>addressType<{/t}>：</td>
	                        <td class="ec-center"><b><{$addressTypeArr[$row.cab_type]}></b></td>
	                    </tr>
	                    <tbody>
	                    	<tr class='table-module-b1'>
	                    		<td class="module-title"><{t}>companyName<{/t}>：</td>
	                    		<td><{$row.cab_company}></td>
	                    	</tr>
	                    	<tr class='table-module-b2'>
	                    		<td class="module-title">姓：</td>
	                    		<td><{$row.cab_lastname}></td>
	                    	</tr>
	                    	<tr class='table-module-b1'>
	                    		<td class="module-title">名：</td>
	                    		<td><{$row.cab_firstname}></td>
	                    	</tr>
	                    	<tr class='table-module-b2'>
	                    		<td class="module-title"><{t}>country<{/t}>：</td>
	                    		<td><{$row.country_name_en}></td>
	                    	</tr>
	                    	<tr class='table-module-b1'>
	                    		<td class="module-title"><{t}>province<{/t}>：</td>
	                    		<td><{$row.cab_state}></td>
	                    	</tr>
	                    	<tr class='table-module-b2'>
	                    		<td class="module-title"><{t}>city<{/t}>：</td>
	                    		<td><{$row.cab_city}></td>
	                    	</tr>
	                    	<tr class='table-module-b1'>
	                    		<td class="module-title"><{t}>address<{/t}>1：</td>
	                    		<td><{$row.cab_street_address1}></td>
	                    	</tr>
	                    	<tr class='table-module-b2'>
	                    		<td class="module-title"><{t}>address<{/t}>2：</td>
	                    		<td><{$row.cab_street_address2}></td>
	                    	</tr>
	                    	<tr class='table-module-b1'>
	                    		<td class="module-title"><{t}>postalCode<{/t}>：</td>
	                    		<td><{$row.cab_postcode}></td>
	                    	</tr>
	                    	<tr class='table-module-b2'>
	                    		<td class="module-title"><{t}>phone<{/t}>：</td>
	                    		<td><{$row.cab_phone}></td>
	                    	</tr>
	                    	<tr class='table-module-b1'>
	                    		<td class="module-title"><{t}>mobilePhone<{/t}>：</td>
	                    		<td><{$row.cab_cell_phone}></td>
	                    	</tr>
	                    	<tr class='table-module-b2'>
	                    		<td class="module-title"><{t}>fax<{/t}>：</td>
	                    		<td><{$row.cab_fax}></td>
	                    	</tr>
	                    	<tr class='table-module-b1'>
	                    		<td class="module-title">Email:</td>
	                    		<td><{$row.cab_email}></td>
	                    	</tr>
	                    </tbody>
	                </table>
				<{/foreach}>
           	</div>

        </div>
        <div style="clear: both;">
        </div>

    </div>

</div>

<script type="text/javascript">
    $(function(){
        $(".tab").click(function(){
            $(".tabContent").hide();

            $(this).parent().removeClass("chooseTag");
            $(this).parent().siblings().addClass("chooseTag");
            $("#"+$(this).attr("id").replace("tab_","")).show();
        });
    })
</script>