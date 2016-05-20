<?php
//分页助手
class Ec_View_Helpers_Pagination extends Zend_View_Helper_Abstract {
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
	public function pagination($num, $perpage, $curpage, $mpurl) {
		$page = 5;
				
		$multipage = '';
		$mpurl = preg_replace("/(\/)$/","",$mpurl);
		$realpages = 1;
		if ($num > $perpage) {
			$offset = 2;
			$realpages = @ceil ( $num / $perpage );
			$pages = $realpages;
			if ($page > $pages) {
				$from = 1;
				$to = $pages;
			} else {
				$from = $curpage - $offset;
				$to = $from + $page - 1;
				if ($from < 1) {
					$to = $curpage + 1 - $from;
					$from = 1;
					if ($to - $from < $page) {
						$to = $page;
					}
				} elseif ($to > $pages) {
					$from = $pages - $page + 1;
					$to = $pages;
				}
			}
			$multipage = '<div class="pagination" id="pagenavi">';
			$urlplus =  '';
			if ($curpage - $offset > 1 && $pages > $page) {
				$multipage .= "<a ";
				
				$multipage .= "href=\"{$mpurl}/page/1{$urlplus}\"";
				
				$multipage .= " class=\"first\">1 ...</a>";
			}
			if ($curpage > 1) {
				$multipage .= "<a ";
				
				$multipage .= "href=\"{$mpurl}/page/" . ($curpage - 1) . "$urlplus\"";
				
				$multipage .= " class=\"prev\">&lsaquo;&lsaquo;</a>";
			}
			for($i = $from; $i <= $to; $i ++) {
				if ($i == $curpage) {
					$multipage .= '<span class="current">' . $i . '</span>';
				} else {
					$multipage .= "<a ";
					
					$multipage .= "href=\"{$mpurl}/page/$i{$urlplus}\"";
					
					$multipage .= ">$i</a>";
				}
			}
			if ($curpage < $pages) {
				$multipage .= "<a ";
				
				$multipage .= "href=\"{$mpurl}/page/" . ($curpage + 1) . "{$urlplus}\"";
				
				$multipage .= " class=\"next\">&rsaquo;&rsaquo;</a>";
			}
			if ($to < $pages) {
				$multipage .= "<a ";
				
				$multipage .= "href=\"{$mpurl}/page/$pages{$urlplus}\"";
				
				$multipage .= " class=\"last\">... $realpages</a>";
			}
			if ($multipage) {
				$multipage .= '<label style="margin-left: 5px;">Total<span style="font-weight: bold;">'.$num.'</span></label>' ;
			}
		}
		$multipage .= '</div>';
		return $multipage;
	}
}
?>
