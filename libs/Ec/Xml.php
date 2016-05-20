<?php
class Ec_Xml extends Zend_Config_Writer_Xml
{
    /**
     * 数组转xml输出
     * (non-PHPdoc)
     * @see Zend_Config_Writer_Xml::render()
     */
    public function render()
    {
        $xml = new SimpleXMLElement('<root/>');
        $sectionName = $this->_config->getSectionName();
        
        if(is_string($sectionName)){
            $child = $xml->addChild($sectionName);
            
            $this->_addBranch($this->_config, $child, $xml);
        }else{
            foreach($this->_config as $sectionName => $data){
                
                if(! ($data instanceof Zend_Config)){
                    $xml->addChild($sectionName, (string)$data);
                }else{
                    $child = $xml->addChild($sectionName);
                    $this->_addBranch($data, $child, $xml);
                }
            }
        }
        
        $dom = dom_import_simplexml($xml)->ownerDocument;
        $dom->formatOutput = true;
        
        $xmlString = $dom->saveXML();
        
        return $xmlString;
    }
}
