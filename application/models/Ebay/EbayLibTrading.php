<?php
require_once 'eBaySession.php';
require_once 'eBaySoapSession.php';
require_once 'XmlHandle.php';

function & XML_Serialize_Trading(&$data, $level = 0, $prior_key = NULL){
    if($level == 0){ ob_start();}
    while(list($key, $value) = each($data))
        if(!strpos($key, ' attr')) #if it's not an attribute
        #we don't treat attributes by themselves, so for an empty element
        # that has attributes you still need to set the element to NULL

        if(is_array($value) and array_key_exists(0, $value)){
        XML_Serialize_Trading($value, $level, $key);
    }else{
        $tag = $prior_key ? $prior_key : $key;
        echo str_repeat("\t", $level),'<',$tag;
        if(array_key_exists("$key attr", $data)){ #if there's an attribute for this element
            while(list($attr_name, $attr_value) = each($data["$key attr"]))
                echo ' ',$attr_name,'="',htmlspecialchars($attr_value),'"';
            reset($data["$key attr"]);
        }

        if(is_null($value)) echo " />\n";
        elseif(!is_array($value)) echo '>',htmlspecialchars($value),"</$tag>\n";
        else echo ">\n",XML_Serialize_Trading($value, $level+1),str_repeat("\t", $level),"</$tag>\n";
    }
    reset($data);
        if($level == 0){ $str = &ob_get_contents(); ob_end_clean(); return $str; }
}
/**
 * 基类
 * @author Administrator
 *
 */
class Ebay_EbayLibTrading
{

    protected $_config = array(
        'token' => '',
        'devid' => '',
        'appid' => '',
        'certid' => '',
        'serverurl' => '',
        'version' => '823',
        'siteid' => '0',
    );


    public function __construct($config = array())
    {
        $this->_config = array_merge($this->_config,$config);
//         print_r($this->_config);
    }    

    /**
     * 入口方法
     * @param unknown_type $callName
     * @param unknown_type $param
     * @return Ambigous <NULL, multitype:>
     */
    public function request($callName, $param)
    {
        $requestXml = $this->getXmlContent($callName,$param);
//         header('Content-Type:text/xml');
//         echo $requestXml;exit;
        $session = new eBaySession($this->_config['token'], $this->_config['devid'], $this->_config['appid'], $this->_config['appid'], $this->_config['serverurl'], $this->_config['version'], $this->_config['siteid'], $callName);
        $responseXml = $session->sendHttpRequest($requestXml);
//         echo $responseXml;exit;
        $data = XML_unserialize($responseXml);
//         print_r($data);exit;
        return $data;
    }

    /**
     * 构造XML
     * @param unknown_type $callName
     * @param unknown_type $param
     * @return string
     */
    public function getXmlContent($callName,$param)
    {
        ini_set("display_errors", "OFF");
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<{$callName}Request xmlns=\"urn:ebay:apis:eBLBaseComponents\">\n".trim(XML_Serialize_Trading($param,0))."\n</{$callName}Request>";
        return $xml;
    }
}