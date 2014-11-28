<?php
namespace Home\Controller;
use Think\Controller;
header("Content-type:text/html;charset=utf-8");
set_time_limit(0);

/**
 * 采集症状控制器类
 * @author caizhendong <327917086@qq.com>
 * date 2014-11-10
 */
class SymptomController extends Controller {
	/**
	 * 初始化
	 */
	public function _initialize(){
		import('Org.QueryList.QueryList');
	}

	/**
	 * 按字母采集所有症状
	 */
	public function index(){
		G('begin');
		$id = I('get.id', 0);
		$check = I('get.check', 0);
		//判断中断之前采集到哪个url
		if($check){
			$symptom = M('Symptom')->field('symptom_name')->order('id desc')->find();
			if(!empty($symptom)){
				$id = ord(get_first_char($symptom['symptom_name']))-65;
				if($id < 0 || $id > 25){
					$id = ord(get_first_char(mb_substr($symptom['symptom_name'], 1, 1, 'utf-8')))-65;
				}
			}
		}

		$list_url = 'http://zzk.xywy.com/p/'.chr(97+$id).'.html';
		$add_data = array();

		$reg = array("title"=>array("a:eq(0)","text"),"url"=>array("a:eq(0)","href"));
		$rang = ".ks-zm-list li";
		$hj = \QueryList::Query($list_url,$reg,$rang,'curl','utf-8');
		$arr = $hj->jsonArr;

		foreach ($arr as $k => $v) {
			$this->item(array('id' => ltrim(strstr($v['url'], '_', true), '/')));
		}
		G('end');
		if ($id < 26) {
			$id++;
			echo $list_url.'症状采集完成！共用时'.G('begin','end').'s,采集完成'.(round((($id+1)/26.0), 4)*100).'%<br />';
			echo '正在采集http://zzk.xywy.com/p/'.chr(97+$id).'.html...<br />';
			echo "<meta http-equiv='Refresh' content='2; url=".__ACTION__."/id/".$id."' />";
		} else {
			echo '采集完成！';
		}
	}

	/**
	 * 根据url采集单个症状
	 */
	public function item($params){
		$id = 0;
		$url = completion_url($params['id'].'_jieshao.html', 'zzk');

		//采集症状标题
		$reg = array('title'=>array(".jb-name","text"));
		$rang = "body .mt5";
		$hj = \QueryList::Query($url,$reg,$rang,'curl','utf-8');
		$data = $hj->jsonArr;

		if(empty($data[0]['title'])){
			return $id;
		}
		//判断是否已存在
		if(!($symptom = M('Symptom')->field('id')->where(array('symptom_name' => $data[0]['title']))->find())){
			$add_data['symptom_name'] = $data[0]['title'];
		}else{
			return $symptom['id'];
		}

		//采集介绍、正常值、临床意义、注意事项、检查过程
		$relate = array('introduce' => '_jieshao.html',
						'pathogen' => '_yuanyin.html',
						'prevent' => '_yufang.html',
						'inspect' => '_jiancha.html',
						'antidiastole' => '_zhenduan.html');
		
		$rang = "div.zz-janj";
		foreach ($relate as $key => $val){
			$reg = array($key=>array(".zz-articl","html","a"));
			$hj = \QueryList::Query(completion_url($params['id'].$val, 'zzk'),$reg,$rang,'curl','utf-8');
			$data = $hj->jsonArr;
			$add_data[$key] = preg_replace('/(<strong .*?>.*?<\/strong>)/', '', $data[0][$key]);
		}

		//采集检查id
		$add_data['inspect_ids'] = $this->get_inspect_ids(completion_url($params['id'].'_jiancha.html', 'zzk'));

		//采集常用药品
		$add_data['drug_ids'] = $this->get_drug_ids(completion_url($params['id'].'_yao.html', 'zzk'));

		$add_data['collect_url'] = $url;
		if(!($id = M('Symptom')->data($add_data)->add())){
			\Think\Log::write($url.' faild', '', '', LOG_PATH.'collect.log');
		}
		return $id;
	}

	/**
	 * 采集症状下的检查id
	 */
	private function get_inspect_ids($url){
		$reg = array("title"=>array("","text"),"url"=>array("","href"));
		$rang = ".zz-articl p.mt5 a";
		$hj = \QueryList::Query($url,$reg,$rang,'curl','utf-8');
		$data = $hj->jsonArr;

		//组装检查id
		$inspect_ids = array();
		foreach ($data as $v) {
			if($inspect = M('Inspect')->field('id')->where(array('inspect_name' => $v['title']))->find()){
				$inspect_ids[] = $inspect['id'];
			}else{
				$inspect_controller = new \Home\Controller\InspectController();
				if($inspect_id = $inspect_controller->item($v['url'])){
					$inspect_ids[] = $inspect_id;
				}
			}
		}
		return implode(',', $inspect_ids);
	}

	/**
	 * 采集症状下常用药品
	 */
	private function get_drug_ids($url){
		$drug_ids = array();
		$rang = "div.city-drugbox .drug-pic-rec";
		$reg = array("title"=>array("p:eq(0) a.gre","text"),"url"=>array("p:eq(0) a.gre","href"));
		$hj = \QueryList::Query($url,$reg,$rang,'curl','utf-8');
		$data = $hj->jsonArr;

		if(!empty($data)){
			foreach ($data as $drug) {
				if(!($drug_data = M('Drug')->field('id')->where(array('full_name' => $drug['title']))->find())){
					$drug_controller = new \Home\Controller\DrugController();
					$drug_ids[] = $drug_controller->item(completion_url($drug['url'], 'yao'));
				}else{
					$drug_ids[] = $drug_data['id'];
				}
			}
		}
		return implode(',', $drug_ids);
	}
}