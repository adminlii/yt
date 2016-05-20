<script type="text/javascript">
    EZ.down='下载模板EXCEL';
    EZ.uploadCsv='批量生成Html';
    EZ.url = '/product/template/';
    EZ.getListData = function (json) {
        var html = '';
        var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;
        $.each(json.data, function (key, val) {
            html += (key + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";
            html += "<td class='ec-center'>" + (i++) + "</td>";

            html += "<td >" + val.E1 + "</td>";
            html += "<td >" + val.E3 + "</td>";
            html += "<td><a href=\"javascript:uploadCsvForm(" + val.E0 + ")\">" +
                    EZ.uploadCsv + "</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"javascript:downById(" + val.E0 + ")\">(" + EZ.down + ")</a></td>";

            html += "<td><a href=\"javascript:editById(" + val.E0 + ")\">" + EZ.edit + "</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"javascript:deleteById(" + val.E0 + ")\">" + EZ.del + "</a></td>";
            html += "</tr>";
        });
        return html;
    }

    $(function(){
        initData(0);
        $("#ez-wms-edit-dialog").dialog({
            autoOpen : false,
            width : 500,
            height :'auto',
            modal : true,
            show : "slide",
            buttons : {
                '确定' : function() {
                    var html='';
                    if($("#E1","#uploadForm").val()=='' ||$("#E02","#uploadForm").val()==''){
                      alertTip("模板名称以及模板文件不能为空.");
                        return false;
                    }
                    $("#uploadForm").submit().resetForm();
                    $(this).dialog('close');
                },
                '取消' : function() {
                    $(this).dialog('close');
                }
            }
        });

        $("#ez-uploadCsvForm-dialog").dialog({
            autoOpen : false,
            width : 500,
            height :'auto',
            modal : true,
            show : "slide",
            buttons : {
                '确定' : function() {
                    $("#uploadCsvForm").submit().resetForm();
                    $(this).dialog('close');
                },
                '取消' : function() {
                    $(this).dialog('close');
                }
            }
        });

        $("#ez-fileList-dialog").dialog({
            autoOpen : false,
            width : 500,
            height :'auto',
            modal : true,
            show : "slide",
            buttons : {
                '取消' : function() {
                    $(this).dialog('close');
                }
            }
        });

        <{if isset($fileList)}>
        $("#ez-fileList-dialog").dialog('open');
        <{/if}>
    });
    function upload(){
        $("#ez-wms-edit-dialog").dialog('open');
    }

    function uploadCsvForm(id){
        $("#E0",'#uploadCsvForm').val(id);
        $("#ez-uploadCsvForm-dialog").dialog('open');
    }

    function downById(id){
        window.open( EZ.url+'down/paramId/'+id);
    }

    function success(){
        alert('sss');
    }

</script>
<style>
    .dialog-module td{
        height:30px;
    }
    .fileList{
        width:90%;
        height:auto;
    }
    .fileList li{
        float:left;
        width:30%;
    }
</style>
<div id="module-container">
    <div id="ez-wms-edit-dialog" title="上传产品模板" style="display:none;">
        <div style="width:80%;padding:8px">使用说明:
        <br>占位符格式为：<%productName%>
        <br>

        </div>
        <form id="uploadForm" method="post" action="/product/template/edit"  enctype="multipart/form-data">
            <table class="dialog-module" border="0" cellpadding="0" cellspacing="0">
                <tbody>
                <input type="hidden" name="E0" id="E0" value=""/>

                <tr>
                    <td class="dialog-module-title">文件名称:</td>
                    <td><input type="text" name="E1" id="E1" validator="required" err-msg="<{t}>require<{/t}>" class="input_text"/><span class="msg">*</span></td>
                </tr>
                <tr>
                    <td class="dialog-module-title">上传模板:</td>
                    <td><input type="file" name="E02" id="E02" validator="required" err-msg="<{t}>require<{/t}>" class="input_text"/><span class="msg" style="margin-left:80px">*</span></td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>


    <div id="ez-uploadCsvForm-dialog" title="批量产生Html" style="display:none;">
        <div style="width:80%;padding:8px">使用说明:
            <br>上传成功后，系统提供生成的模板下载！
            <br>文字加粗：&lt;bold&gt;想要加粗的文字&lt;/bold&gt;
            <br>红色字体：&lt;red&gt;想要加粗的文字&lt;/red&gt;
            <br>蓝色字体：&lt;blue&gt;想要加粗的文字&lt;/blue&gt;
            <br>只支持.xls文件
            <br>
        </div>
        <form id="uploadCsvForm" method="post" action="/product/template/upload"  enctype="multipart/form-data">
            <table class="dialog-module" border="0" cellpadding="0" cellspacing="0">
                <tbody>
                <input type="hidden" name="E0" id="E0" value=""/>
                <tr>
                    <td class="dialog-module-title">上传模板:</td>
                    <td><input type="file" name="E2" id="E2" validator="required" err-msg="<{t}>require<{/t}>" class="input_text"/><span class="msg">*</span></td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>


    <div id="ez-fileList-dialog" title="已生成模板文件,请下载" style="display:none;">
        <div class="fileList">
            <ul>
                <{if isset($fileList)}>
                <{foreach from=$fileList key=k item=url}>
                <li><a target="_blank" href="/product/template/down-html/fileName/<{$url}>">产品<{$k+1}></a></li>
                <{/foreach}>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <li style="text-align: left"><a href="">【打包下载】</a></li>
                <{/if}>
            </ul>
        </div>
    </div>

    <div id="search-module">
        <form id="searchForm" name="searchForm" class="submitReturnFalse">
            <div style="padding:0">

                文件名称：<input type="text" name="E1" id="E1" class="input_text keyToSearch"/>
                &nbsp;&nbsp;<input type="button" value="<{t}>search<{/t}>" class="baseBtn submitToSearch"/>
                &nbsp;&nbsp;<input type="button" id="" value="添加模板文件" onclick="upload();" class="baseBtn"/>
                <span style="color: green">←这里可以添加一个新的刊登模板,添加以后，系统会生成模板文件（EXCEL），以后您只需要填写EXCEL即可批量生成HTML</span>
            </div>
            <div style="padding-top: 20px">
                <p>流程1：我有新模板→1,<a>添加模板文件</a>，2，<a>下载模板EXCEL</a>3，<a>在EXCEL填写模板内容</a>4，上传EXCEL<a>批量生成HTML</a></p>
                <p>流程2：已有模板，只要批量生成，参考流程1，省去第一步</p>
                <p style="color: red">注意事项：在模板EXCEL填写数据时，每一行数据都将会生成一个TXT文本，TXT文本名是模板第一列填写的内容，同一EXCEL第一列内容禁止重复</p>
            </div>
            <div style="padding-top: 10px;color:red;font-weight:bold;"><{if isset($message)}><{$message}><{/if}></div>
        </form>
    </div>

    <div id="module-table">
        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-module">
            <tr class="table-module-title">
                <td width="3%" class="ec-center">NO.</td>

                <td>文件名称</td>
                <td>创建时间</td>
                <td>批量模板操作</td>
                <td><{t}>operate<{/t}></td>
            </tr>
            <tbody id="table-module-list-data"></tbody>
        </table>
    </div>
    <div class="pagination"></div>

</div>
