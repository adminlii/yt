Dim currPath
currPath = createobject("Scripting.FileSystemObject").GetFile(Wscript.ScriptFullName).ParentFolder.Path
Dim command,phpfile
'调用的php文件'
phpfile = "AliexpressLoadOrder.php"
command = "php " & currPath & "\..\" & phpfile

'MsgBox currPath&"EbayLoadOrder.bat /start"'
set ws=wscript.createobject("wscript.shell")  
'ws.run currPath&"_loadShopifyProduct.bat /start",0'
'ws.run("php " & currPath & "..\loadShopifyProduct.php")'
'ws.run("php E:\Zend\workspaces\ebTest\auto\loadShopifyProduct.php"),0'
ws.run(command),0
'MsgBox command'

 