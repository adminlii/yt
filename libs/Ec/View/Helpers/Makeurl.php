<?php
// 文档说明开始
// 编写自定义的Helper类
// 编写自定义的Helper类很容易，只要遵循以下几个原则即可：
// 类名必须是 Zend_View_Helper_*，*是helper的名称。例如，你在写一个名为“Makeurl”的类，类名将至少是"Makeurl"，
// 另外你还应该给类名加上前缀，建议将“Ec_View_Helpers”作为前缀的一部份：“Ec_View_Helpers_Makeurl”。

//-------------非常重要----------- （注意大小写）你将需要将前缀（不包含下划线）传递给addHelperPath() 或 setHelperPath()。

// 类中必须有一个public的方法，该方法名与helper类名相同。这个方法将在你的模板调用"$this->makeurl()"时执行。
// 在我们的“specialPurpose”例子中，相应的方法声明可以是 “public function makeurl()”。
// 一般来说，Helper类不应该echo或print或有其它形式的输出。它只需要返回值就可以了。返回的数据应当被转义。
// 类文件的命名应该是helper方法的名称，比如在"makeurl"例子中，文件要存为“Makeurl.php”。
// 把helper类的文件放在你的helper路径下， Zend_View就会自动加载，实例化，持久化，并执行。

// 文档说明结束

// 三点类文件名称，类名称，类中helper方法，保持某种程度上的一致。


class Ec_View_Helpers_Makeurl  extends Zend_View_Helper_Abstract{
	public $view;
	public function setView(Zend_View_Interface $views) {
		$this->view = $views;
	}
	
	/**
	 * 构造URL函数
	 *
	 * @param string $controller
	 * @param string $action
	 * @param array $param
	 */
	public function makeurl($module="default",$controller='index',$action='index',$param=''){
		$url = array('module'=>$module,
					 'controller'=>$controller,
					 'action'=>$action,
		);
		if (empty($param)) {
			return $this->view->url($url,null,true);
		}
		else 
		{
			foreach ($param as $k=>$v){
				$url[$k] = $v;
			}
		}
		return $this->view->url($url,null,true);
	}
}
?>
