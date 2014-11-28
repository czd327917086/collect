<?php
namespace Home\Controller;
use Think\Controller;
header("Content-type:text/html;charset=utf-8");

/**
 * 采集控制器类
 * @author caizhendong <327917086@qq.com>
 * date 2014-11-10
 */
class IndexController extends Controller {
	/**
	 * 初始化
	 */
	public function _initialize(){
		import('Org.QueryList.QueryList');
	}

    public function index(){
//     	$disease_controller = new \Home\Controller\DiseaseController();
//     	$content = file_get_contents(RUNTIME_PATH.'/logs/collect2.log');
//     	preg_match_all('#.*?collect_by_part/id/(\d*)[\s\S]*?gaishu/(\d*)#', $content, $match);
//     	foreach ($match[2] as $k => $v){
//     		$disease_controller->item(array('id' => $v, 'part_id' => $match[1][$k]));
//     	}
//     	echo 'success';die;
//     	//dump($match);die;
//     	$disease_controller = new \Home\Controller\DiseaseController();
//     	$disease = M('Disease')->field('id,neopathy_names')->where('neopathy_names !=""')->order('id')->select();
//     	foreach ($disease as $v) {
//     		$disease_controller->change_neopathy_name_to_id($v);
//     	}
    	
    	//$disease_controller->collect_by_departments_count();
    	//$disease_controller->item(array('id' => 9073));
        $this->display();
    }

    /**
     * 部位和科室采集
     */
    function collect(){
    	G('begin');
    	if($_REQUEST['type'] == 'part'){
    		$model = M('part');
    		$url = "http://jib.xywy.com/html/toubu.html";
    	}elseif($_REQUEST['type'] == 'departments'){
    		$model = M('departments');
    		$url = "http://jib.xywy.com/html/neike.html";
    	}

	    $reg = array("title"=>array("a:eq(0)","text"),"url"=>array("a:eq(0)","href"));
	    $rang = "ul.jbk-nav-list li.pr";
	    $hj = \QueryList::Query($url,$reg,$rang,'curl','utf-8');
	    $arr = $hj->jsonArr;
	    
	    foreach ($arr as $k => $v) {
	    	if(!($data = $model->where(array('name' => $v['title']))->find())){
	    		$id = $model->add(array('name' => $v['title'], 'collect_url' => completion_url($v['url'])));
	    	}else{
	    		$id = $data['id'];
	    	}
	    	$reg = array("title"=>array("a:eq(0)","text"),"url"=>array("a:eq(0)","href"));
	    	$rang = "ul.jbk-nav-list li.pr:eq(".$k.") ul.jbk-sed-menu li";
	    	$temp = \QueryList::Query($url,$reg,$rang,'curl','utf-8');
	    	$temp_arr = $temp->jsonArr;
	    	if(!empty($temp_arr)){
	    		foreach ($temp_arr as $val){
	    			if(!$model->where(array('name' => $val['title']))->find()){
	    				$model->add(array('pid' => $id, 'name' => $val['title'], 'collect_url' => completion_url($val['url'])));
	    			}
	    		}
	    	}
	    }
	    G('end');
	    echo '采集完成！共用时'.G('begin','end').'s';
    }

    function downimg(){
    	$url = 'http://jib.xywy.com/tushuojibing/6986.htm';
    	$rang = ".jib-photo-box";
    	$reg = array('web_img_index' => array('div a img', 'src'),
    				 'url' => array('div a', 'href'),
    				 'title' => array('p', 'text')
    	);
    	$hj = \QueryList::Query($url,$reg,$rang,'curl','utf-8');
    	$data = $hj->jsonArr;

    	foreach($data as $key => $val){
    		$data[$key]['local_img_index'] = save_image($val['web_img_index']);
    		$data[$key]['url'] = completion_url($val['url']);
    		$rang = ".thumb-box .thumb-list ul li";
    		$reg = array('web_img' => array('a img', 'src'));

    		$hj = \QueryList::Query($data[$key]['url'],$reg,$rang,'curl','utf-8');
    		$data[$key]['img_list'] = $hj->jsonArr;
    		foreach ($data[$key]['img_list'] as $k => $v){
    			$data[$key]['img_list'][$k]['local_img'] = save_image($v['web_img']);
    		}
    	}
    	echo json_encode($data);
    	return json_encode($data);
    }
}