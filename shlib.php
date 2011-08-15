<?php
	require_once ("simple_html_dom.php");
	$shlib_search_url = "http://ipac.library.sh.cn/ipac20/ipac.jsp?index=ISBN&term="; 
	$isbn = $_GET["isbn"];
	$url = $_GET["url"];
	//'7505722468';

    if ($isbn != '') {
        get_stat_all($isbn);
    } elseif ($url != '') {
        $isbn = get_isbn($url);
    }else {
        echo "2";
    }


function get_isbn($url){
    //get douban webpage
    $doc = file_get_html($url);
    //find isbn of the book
    foreach ( $doc->find('div[id=info]') as $div){
        $pos = strrpos($div->plaintext, 'ISBN:');
        if ($pos >= 0){
            $isbn = substr(trim($div->plaintext), -13);
            get_stat($isbn);
            break;
        }
    }

}

function get_stat($isbn){
    global $shlib_search_url;
	$doc = file_get_html($shlib_search_url . $isbn);
	$flag=0;	

    //找到存书信息的表格
	foreach ($doc->find('tr[height="15"]') as $tr ){
        //找到状态这一列
		$status= $tr->children(3)->children(0)->innertext;
        //跟“归还”做比较
        if (strcmp(trim($status), "&#24402;&#36824;") == 0) {
            $flag = 1;
            break;
        }
	}

    echo $flag;

}

function get_stat_all($isbn){
    global $shlib_search_url;
	$doc = file_get_html($shlib_search_url . $isbn);
	$data = array();
	$i=0;	

	foreach ($doc->find('tr[height="15"]') as $tr ){
		$place = $tr->children(1)->children(0)->innertext;
		$info= $tr->children(2)->children(0)->innertext;
		$status= $tr->children(3)->children(0)->innertext;
		$data[] = array('place'=>$place,'i'=>$info,'s'=>$status);
		$i++;
	}
		

	$arr = array('ok'=>$i,'data'=>$data);
	$json_string = json_encode($arr);
	echo $json_string;
}
?>
