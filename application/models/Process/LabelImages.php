<?php

/**
 * @desc 获取标签图片
 */
class Process_LabelImages
{

    /**
     * @desc 获取页面全部图片
     * @param string $html
     * @return array
     */
    public static function getImagesArrByHtml($html = '')
    {
        $list = array();    //这里存放结果map
        $c1 = preg_match_all('/<img\s.*?>/', $html, $m1);  //先取出所有img标签文本
        for ($i = 0; $i < $c1; $i++) {    //对所有的img标签进行取属性
            $c2 = preg_match_all('/(\w+)\s*=\s*(?:(?:(["\'])(.*?)(?=\2))|([^\/\s]*))/', $m1[0][$i], $m2);   //匹配出所有的属性
            for ($j = 0; $j < $c2; $j++) {    //将匹配完的结果进行结构重组
                $list[$i][$m2[1][$j]] = !empty($m2[4][$j]) ? $m2[4][$j] : $m2[3][$j];
            }
        }
        return $list;
    }

    /**
     * @desc 将图片转为Base64
     * @param string $html
     * @param string $host
     * @return mixed|string
     */
    public static function imagesUrlToBase64($html = '', $host = '')
    {
        //先取出所有img标签文本
        preg_match_all('/<img\s.*?>/', $html, $m1);
        $imagesArr = isset($m1[0]) ? $m1[0] : array();
        if (!empty($imagesArr)) {
            foreach ($imagesArr as $img) {
                preg_match('/<img.+src=\"?(.+\.(jpg|gif|bmp|bnp|png))\"?.+>/i', $img, $match);
                //preg_match('/<img.+src=\"?(.+\.(bmp))\"?.+>/i',$html,$match);
                $src = isset($match[1]) ? $match[1] : '';
                if (!empty($src)) {
                    $imgType = strrchr($src, '.');
                    $imgType = str_replace(".", "", $imgType);
                    $imgType = empty($imgType) ? 'png' : $imgType;
                    $pathUrl = $host . $src;
                    $content = file_get_contents($pathUrl);
                    $newSrc = "data:image/{$imgType};base64," . base64_encode($content);
                    $html = str_ireplace($src, $newSrc, $html);
                }
            }
        }
        return $html;
    }

}