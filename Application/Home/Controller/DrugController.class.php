<?php
namespace Home\Controller;
use Think\Controller;
header("Content-type:text/html;charset=utf-8");
set_time_limit(0);

/**
 * 采集药品控制器类
 * @author caizhendong <327917086@qq.com>
 * date 2014-11-10
 */
class DrugController extends Controller {
	/**
	 * 初始化
	 */
	public function _initialize(){
		import('Org.QueryList.QueryList');
		$this->drug_model = M('Drug');
	}

	/**
	 * 根据url采集药品详情
	 */
	public function item($url){
		if(!strpos($url, 'manual')){
			$url = substr($url, $start, strrpos($url, '.')).'/manual.htm';
		}
		$id = 0;
		$reg = array(
				"full_name" => array(".p-top .p-top-min ul li > h1","text"),
				"drug_name" => array(".p-mcon .p-mcons ul:eq(0) li:eq(1)","text"),
				"recipe" => array(".p-top .p-top-min ul li > div:eq(1)","text"),
				"drug_type" => array(".p-top-min ul li > div:eq(2)","text"),
				"medicare_type" => array(".p-top-min ul li > div:eq(3)","text"),
				"production_place" => array(".p-top-min ul li > div:eq(4)","text"),
				"approval_number" => array(".last .phonebox .r-rumbox","text"),
				"production_enterprise" => array(".last > span:eq(1)","text"),
				
				"purpose" => array(".p-mcon .p-mcons ul:eq(1) li:eq(1)","html"),
				"bases" => array(".p-mcon .p-mcons ul:eq(2) li:eq(1)","html"),
				"packing" => array(".p-mcon .p-mcons ul:eq(3) li:eq(1)","html"),
				"usage" => array(".p-mcon .p-mcons ul:eq(4) li:eq(1)","html"),
				"untoward_effect" => array(".p-mcon .p-mcons ul:eq(5) li:eq(1)","html"),
				"need_attention" => array(".p-mcon .p-mcons ul:eq(6) li:eq(1)","html"),
				"taboo" => array(".p-mcon .p-mcons ul:eq(7) li:eq(1)","html"),

				"women_use" => array(".p-mcon .p-mcons ul:eq(8) li:eq(1)","html"),
				"children_use" => array(".p-mcon .p-mcons ul:eq(9) li:eq(1)","html"),
				"older_user" => array(".p-mcon .p-mcons ul:eq(10) li:eq(1)","html"),
				"drug_interactions" => array(".p-mcon .p-mcons ul:eq(11) li:eq(1)","html"),
				"drug_action" => array(".p-mcon .p-mcons ul:eq(12) li:eq(1)","html")
		);
		$rang = "body";
		$hj = \QueryList::Query($url,$reg,$rang,'curl','utf-8');
		$data = $hj->jsonArr;

		$data[0]['collect_url'] = $url;
		if(empty($data[0]['drug_name'])) {
			return $id;
		}

		if(!($drug = $this->drug_model->where(array('full_name' => $data[0]['full_name']))->find())){
			if(!($id = $this->drug_model->add($data[0]))){
				\Think\Log::write($url.' faild', '', '', LOG_PATH.'collect.log');
			}
		}else{
			$id = $drug['id'];
		}

		return $id;
	}
}