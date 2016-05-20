<?php 
function test1(){
    
    throw new Exception("error1",1);
}

function test2(){
    $return = array();
    try {
        test1();
    } catch (Exception $e) {
        $return['msg'] = $e->getMessage(); 
    }
    var_dump($return);
}

test2();
?>