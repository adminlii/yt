<?php

class Ec_ActionTool
{

    private $_list = array();

    public function addAcl()
    {
        $dirname = APPLICATION_PATH . "/modules";
        $this->ListDir($dirname);
        $this->fenxi();

    }

    private function ListDir($dirname)
    {
        $Ld = dir($dirname);
        $list = array();
        while (false !== ($entry = $Ld->read())) {
            $checkdir = $dirname . "/" . $entry;
            if (is_dir($checkdir) && !preg_match("[^\.]", $entry)) {
                // echo "<li><p>".$checkdir."</p></li>";
                $this->ListDir($checkdir);
            } else {
                //找出所有Controller.php文件
                if ($entry != "." && $entry != ".." && preg_match('/Controller\.php$/', $checkdir)) {
                    //  echo "<li><p>".$entry."</p></li>";
                    $this->_list[] = $checkdir;
                }
            }
        }
        $Ld->close();
    }

    private function chuli($str)
    {
        $str = preg_replace('/([A-Z])/', '-\\1', $str);
        $str = strtolower($str);
        $str = trim($str, "-");
//         $str = preg_replace('/-/', '', $str);
        return $str;
    }

    public function fenxi()
    {
        $actionArr = array();
        foreach ($this->_list as $filePath) {

            $contents = file_get_contents($filePath);

            preg_match_all('/class\s+([A-Za-z0-9]+)_([A-Za-z0-9]+)Controller\s+extends/', $contents, $model_controller);

            // print_r($model_controller);

            $module = isset($model_controller[1][0]) ? $model_controller[1][0] : '';
            $module = $this->chuli($module);

            $controller = isset($model_controller[2][0]) ? $model_controller[2][0] : '';
            $controller = $this->chuli($controller);

            preg_match_all('/public(\s+)function(\s+)([0-9A-Za-z]+)Action/', $contents, $matches);


            foreach ($matches[3] as $v) {
                $action = $this->chuli($v);
                if($module=='default'){
                    continue;
                }
                $con = array(
                    'ura_module' => $module,
                    'ura_controller' => $controller,
                    'ura_action' => $action
                );


                                $rows = Service_UserRightAction::getByCondition($con);
                                $pri = 0;
                                if (empty($rows)) {
                                    $row = array(
                                            'ura_title' => "{$controller}/{$action}待修改",
                                            'ura_title_en' => "{$controller}/{$action}待修改",
                                            'ura_module' => $module,
                                            'ura_controller' => $controller,
                                            'ura_action' => $action
                                    );
                                    $pri = Service_UserRightAction::add($row);
                                }
                                $actionArr[] = array(
                                        'ura_module' => $module,
                                        'ura_controller' => $controller,
                                        'ura_action' => $action,
                                        'isNew' => $pri ? "Y" : "N"
                                );

            }
        }
        return $actionArr;
    }
}