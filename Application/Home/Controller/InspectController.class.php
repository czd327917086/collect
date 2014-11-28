<?php
namespace Home\Controller;
use Think\Controller;
header("Content-type:text/html;charset=utf-8");
set_time_limit(0);

/**
 * 采集检查控制器类
 * @author caizhendong <327917086@qq.com>
 * date 2014-11-10
 */
class InspectController extends Controller {
	/**
	 * 初始化
	 */
	public function _initialize(){
		import('Org.QueryList.QueryList');
		$this->model = M('Inspect');
	}

	/**
	 * 按字母采集所有检查
	 */
	public function index(){
		G('begin');
		for($i = 0; $i < 26; $i++) {
			$url = 'http://jck.xywy.com/'.chr(97+$i).'.html';
			$add_data = array();
	
			$reg = array("url"=>array("a:eq(0)","href"));
			$rang = ".matvwss dl dd";
			$hj = \QueryList::Query($url,$reg,$rang,'curl','utf-8');
			$arr = $hj->jsonArr;

			foreach ($arr as $v) {
				$this->item($v['url']);
			}
		}
		G('end');
		echo '采集检查完成。共用时'.G('begin','end').'s';
	}

	/**
	 * 根据url采集检查详情
	 */
	function item($url,$reference_price=0){
		$id = 0;
		$reg = array(
				"inspect_name"=>array("h1","text"),
				"introduce"=>array("dl:eq(0) dd","html","a"),
				"normal_value"=>array("dl:eq(1) dd","html","a"),
				"clinical_value"=>array("dl:eq(2) dd","html","a"),
				"need_attention"=>array("dl:eq(3) dd","html","a"),
				"checking_process"=>array("dl:eq(4) dd","html","a")
		);
		$rang = ".ddsm";
		$hj = \QueryList::Query($url,$reg,$rang,'html','utf-8');
		$data = $hj->jsonArr;

		$data[0]['reference_price'] = $reference_price;
		$data[0]['collect_url'] = $url;
		if(!empty($data[0]['inspect_name']) && !$this->model->where(array('inspect_name' => $data[0]['inspect_name']))->find()){
			if(!($id = $this->model->add($data[0]))){
				\Think\Log::write($url.' faild', '', '', LOG_PATH.'collect.log');
			}
		}
		return $id;
	}
}