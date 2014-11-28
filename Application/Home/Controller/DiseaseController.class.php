<?php
namespace Home\Controller;
use Think\Controller;
header("Content-type:text/html;charset=utf-8");
set_time_limit(0);

/**
 * 采集疾病控制器类
 * @author caizhendong <327917086@qq.com>
 * date 2014-11-10
 */
class DiseaseController extends Controller {
	/**
	 * 初始化
	 */
	public function _initialize(){
		import('Org.QueryList.QueryList');
		$this->disease_model = M('Disease');
	}

	public function collect_by_departments_count(){
		//dump($this->get_collect_by_departments_url());die;
		$total = 0;
		$id = 2;
		$collect_by_part_url_arr = $this->get_collect_by_departments_url();
		$count = count($collect_by_part_url_arr);

		for($i=1;$i<54;$i++){
			if(empty($collect_by_part_url_arr[$i])) continue;
			$url = $collect_by_part_url_arr[$i]['collect_url'];
			$rang = ".jblist-con-ill ul li";
			$reg = array('url' => array('a', 'href'),'title' => array('a', 'text'));
			$hj = \QueryList::Query($url,$reg,$rang,'curl','utf-8');
			$data = $hj->jsonArr;

			$total += count($data);
		}
		echo $total;
	}

	public function collect_by_letter_count(){
		$total = 0;
		$collect_by_part_url_arr = $this->get_collect_by_part_url();
		$count = count($collect_by_part_url_arr);
	
		for($i=0;$i<26;$i++){
			$url = 'http://jib.xywy.com/html/'.chr(97+$i).'.html';
			$rang = ".jblist-con .ks-ill-txt ul li";
			$reg = array('url' => array('a', 'href'),'title' => array('a', 'text'));
			$hj = \QueryList::Query($url,$reg,$rang,'curl','utf-8');
			$data = $hj->jsonArr;

			$total += count($data);
		}
		echo $total;
	}

	/**
	 * 字段并发症文字改为标题
	 */
	public function change_neopathy_name_to_id($data){
		$disease_model = M('Disease');
		if(!empty($data['neopathy_names'])){
			$ids = array();
			foreach (explode(',', $data['neopathy_names']) as $v) {
				if($temp = $disease_model->field('id')->where(array('disease_name' => $v))->find()){
					$ids[] = $temp['id'];
				}
			}

			if(!empty($ids)){
				if(!$disease_model->where('id ='.$data['id'])->data(array('neopathy_ids' => implode(',', $ids)))->save()){
					\Think\Log::write($id.' faild', '', '', LOG_PATH.'neopathyUpdate.log');
				}
			}
		}
	}

	/**
	 * 通过循环部位采集疾病详情
	 */
	public function collect_by_letter(){
		G('begin');
		$id = I('get.id', 0);

		$url = 'http://jib.xywy.com/html/'.chr(97+$id).'.html';
		$rang = ".jblist-con .jblist-con-ear ul li";
		$reg = array('url' => array('a', 'href'),'title' => array('a', 'text'));
		$hj = \QueryList::Query($url,$reg,$rang,'curl','utf-8');
		$data = $hj->jsonArr;

		foreach ($data as $v) {
			if(false === strpos($v['title'], '.')){
				if(M('Disease')->field('id')->where(array('disease_name' => $v['title']))->find()){
					continue;
				}
			}
			$url_id =  substr($v['url'], strrpos($v['url'], '_')+1, -4);
			$this->item(array('id' => $url_id));
		}
		if($id < 26){
			echo $url.'症状采集完成！共用时'.G('begin','end').'s,采集完成'.(round((($id+1)/26.0), 4)*100).'%<br />';
			echo '正在采集http://jib.xywy.com/html/'.chr(97+(++$id)).'.html...<br />';
			echo "<meta http-equiv='Refresh' content='2; url=".__ACTION__."/id/".$id."' />";
		}else{
			echo '采集完成！';
		}
	}

	/**
	 * 通过循环部位采集疾病详情
	 */
	public function collect_by_part(){
		$collect_by_part_url_arr = $this->get_collect_by_part_url();
		$count = count($collect_by_part_url_arr);
		//dump($collect_by_part_url_arr);die;
		G('begin');
		$id = I('get.id', 0);
		if(0 == $id){
			$disease = M('Disease')->field('part_id')->order('id desc')->find();
			$id = empty($disease['part_id']) ? 0 : $disease['part_id'];
		}

		if(empty($collect_by_part_url_arr[$id]) && $id < 19){
			echo "<meta http-equiv='Refresh' content='2; url=".__ACTION__."/id/".++$id."' />";
		}
		$url = $collect_by_part_url_arr[$id]['collect_url'];
		$rang = ".jblist-con-ill ul li";
		$reg = array('url' => array('a', 'href'),'title' => array('a', 'text'));
		$hj = \QueryList::Query($url,$reg,$rang,'curl','utf-8');
		$data = $hj->jsonArr;

		foreach ($data as $v) {
			if(false === strpos($v['title'], '.')){
				if(M('Disease')->field('id')->where(array('disease_name' => $v['title']))->find()){
					continue;
				}
			}
			$url_id =  substr($v['url'], strrpos($v['url'], '_')+1, -4);
			$this->item(array('id' => $url_id, 'part_id' => $id));
		}
		if($id < 19){
			echo $url.'症状采集完成！共用时'.G('begin','end').'s,采集完成'.(round(($id/18.0), 4)*100).'%<br />';
			echo '正在采集'.$collect_by_part_url_arr[++$id]['collect_url'].'...<br />';
			echo "<meta http-equiv='Refresh' content='2; url=".__ACTION__."/id/".$id."' />";
		}else{
			echo '采集完成！';
		}

	}

	/**
	 * 采集疾病
	 */
	public function item($params){
		if(empty($params['id'])){
			return;
		}

		$url = 'http://jib.xywy.com/il_sii/gaishu/'.$params['id'].'.htm';
		$rang = "body";
		$reg = array(
				"departments_id" => array(".nav-bar > a:eq(2)","text"),
				"departments_pid" => array(".nav-bar > a:eq(1)","text"),
				"disease_name" => array(".wrap .jb-name","text"),
				"introduction" => array(".wrap .jib-articl-con p","html","a"),
				
				"is_yibao" => array(".wrap .articl-know:eq(0) > p:eq(0) span:eq(1)","text"),
				"ratio" => array(".wrap .articl-know:eq(0) > p:eq(1) span:eq(1)","text"),
				"susceptible" => array(".wrap .articl-know:eq(0) > p:eq(2) span:eq(1)","text"),
				"infection_way" => array(".wrap .articl-know:eq(0) > p:eq(3) span:eq(1)","text"),
				"neopathy_names" => array(".wrap .articl-know:eq(0) > p:eq(4) span:eq(1)","html","a"),

				"clinic_department" => array(".wrap .articl-know:eq(1) > p:eq(0) span:eq(1)","text"),
				"therapy_method" => array(".wrap .articl-know:eq(1) > p:eq(1) span:eq(1)","text"),
				"treatment_cycle" => array(".wrap .articl-know:eq(1) > p:eq(2) span:eq(1)","text"),
				"cure_rate" => array(".wrap .articl-know:eq(1) > p:eq(3) span:eq(1)","text"),
				"drug_ids" => array(".wrap .articl-know:eq(1) > p:eq(4) span:eq(1)","html"),
				"expense" => array(".wrap .articl-know:eq(1) > p:eq(5) span:eq(1)","text"),
				"warm_prompt" => array(".wrap .articl-know:eq(2) > p:eq(0)","text"),
				
		);
		$hj = \QueryList::Query($url,$reg,$rang,'curl','utf-8');
		$add_data = $hj->jsonArr;
		if(empty($add_data[0]['disease_name'])){
			return false;
		}
		$model = M('Disease');
		//判断是否存在
		if($model->field('id')->where(array('disease_name' => $add_data[0]['disease_name']))->find()){
			return false;
		}
		$departments_name = empty($add_data[0]['departments_id']) ? $add_data[0]['departments_pid'] : $add_data[0]['departments_id'];
		unset($add_data[0]['departments_pid']);
		$departments = M('Departments')->field('id')->where('name ="'.$departments_name.'"')->order('pid desc')->find();

		$add_data[0]['departments_id'] = $departments['id'];
		$add_data[0]['is_yibao'] = $add_data[0]['is_yibao'] == '是' ? 1 : 0;
		$add_data[0]['neopathy_names'] = empty($add_data[0]['neopathy_names']) ? '' : str_replace('&#160;', ',', $add_data[0]['neopathy_names']);

		//并发症id,这里获取的话会无限递归，等疾病都采集好了再写一个脚本把neopathy_names转换成neopathy_ids
		/*$add_data[0]['neopathy_ids'] = $this->get_neopathy_ids($add_data[0]['neopathy_ids']);*/

		//药品id
		$add_data[0]['drug_ids'] = $this->get_drug_ids($add_data[0]['drug_ids']);

		//症状id
		$add_data[0]['symptom_ids'] = $this->get_symptom_ids('http://jib.xywy.com/il_sii/symptom/'.$params['id'].'.htm');

		//检查id
		$add_data[0]['inspect_ids'] = $this->get_inspect_ids('http://jib.xywy.com/il_sii/inspect/'.$params['id'].'.htm');;

		//图说疾病json
		$add_data[0]['tushuojibing'] = $this->get_tushuojibing_json('http://jib.xywy.com/tushuojibing/'.$params['id'].'.htm');

		//病因、预防、并发症、症状、检查、诊断鉴别、治疗、护理
		$jib_arr = array('cause','prevent','neopathy','symptom','inspect','diagnosis','treat','nursing');
		$rang = ".jib-janj .jib-articl";
		foreach ($jib_arr as $jib) {
			$jib_url = 'http://jib.xywy.com/il_sii/'.$jib.'/'.$params['id'].'.htm';
			$reg = array($jib => array("","html","a"));
			$hj = \QueryList::Query($jib_url,$reg,$rang,'curl','utf-8');
			$add_data[0][$jib] = preg_replace('/(<strong .*?>[\s|\S]*?<\/strong>)/', '', $hj->jsonArr[0][$jib]);
		}
		
		//dump($add_data);die;
		$add_data[0]['collect_url'] = $url;
		$add_data[0]['part_id'] = isset($params['part_id']) ? $params['part_id'] : 0;

 		$model->startTrans();
		if(($id = $model->add($add_data[0]))){
			M('DiseaseSymptom')->addAll($disease_symptom);
			$model->commit();
			return $id;
		}else{
			$model->rollback();
			\Think\Log::write($url.' faild', '', '', LOG_PATH.'collect.log');
			return false;
		}
	}
	
	/**
	 * 获取药品id
	 */
	private function get_drug_ids($url){
		$hj = \QueryList::Query($url,array('title' => array('', 'text'),'url' => array('', 'href')),'a','curl','utf-8');
		$drug_data = $hj->jsonArr;
		$drug_ids = array();
		foreach ($drug_data as $val) {
			if (empty($val['title'])) {
				continue;
			}
			if (!($drug = M('Drug')->where(array('drug_name' => $val['title']))->find())) {
				$drug_controller = new \Home\Controller\DrugController();
				$drug_ids[] = $drug_controller->item($val['url']);
			}else{
				$drug_ids[] = $drug['id'];
			}
		}
		return implode(',', $drug_ids);
	}

	/**
	 * 获取检查id
	 */
	private function get_inspect_ids($url){
		$rang = ".more-zk ul:gt(0)";
		$reg = array('title' => array('li:eq(0) a:eq(0)', 'text'),
					 'url' => array('li:eq(0) a:eq(0)', 'href'),
					 'price' => array('li:eq(1)', 'text')
		);
		$hj = \QueryList::Query($url,$reg,$rang,'curl','utf-8');
		$data = $hj->jsonArr;

		$inspect_ids = array();
		foreach ($data as $val) {
			if (empty($val['title'])) {
				continue;
			}
			if (!($inspect = M('Inspect')->field('id,reference_price')->where(array('inspect_name' => $val['title']))->find())) {
				$inspect_controller = new \Home\Controller\InspectController();
				$inspect_ids[] = $inspect_controller->item($val['url'], $val['price']);
			}else{
				//由于在检查详情里面获取不到检查费用，故在这里更新
				if(empty($inspect['reference_price'])){
					M('Inspect')->where('id ='.$inspect['id'])->save(array('reference_price'=>$val['price']));
				}
				$inspect_ids[] = $inspect['id'];
			}
		}
		return implode(',', $inspect_ids);
	}

	/**
	 * 获取症状id
	 */
	private function get_symptom_ids($url){
		$rang = ".jib-articl > span a";
		$reg = array('title' => array('', 'text'),
				'url' => array('', 'href')
		);
		$hj = \QueryList::Query($url,$reg,$rang,'curl','utf-8');
		$data = $hj->jsonArr;

		$ids = array();
		foreach ($data as $val) {
			if (empty($val['title'])) {
				continue;
			}
			if (!($symptom = M('Symptom')->where(array('symptom_name' => $val['title']))->find())) {
				$symptom_controller = new \Home\Controller\SymptomController();
				$ids[] = $symptom_controller->item(array('id' => substr($val['url'], strrpos($val['url'], '/')+1, -12)));
			}else{
				$ids[] = $symptom['id'];
			}
		}
		return array_unique($ids);
	}

	/**
	 * 获取图说疾病
	 */
	private function get_tushuojibing_json($url){
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
    	return json_encode($data);
	}

	/**
	 * 疾病和症状数组
	 */
	private function get_disease_symptom($disease_id, $symptom){
		$return = array();
		if(!empty($disease_id) && !empty($symptom)){
			foreach ($symptom as $v){
				$return[] = array('disease_id' => $disease_id, 'symptom_id' => $v);
			}
		}
		return $return;
	}

	/**
	 * 获取按部位采集的数组
	 */
	private function get_collect_by_part_url(){
		$return = array();
		$parts = M('Part')->select();
		foreach ($parts as $v => $k) {
			if(!empty($k['pid'])){
				$pids[] = $k['pid'];
			}
		}
		$pids = array_unique($pids);
		foreach ($parts as $k => $v) {
			if(!in_array($v['id'], $pids)){
				$return[$v['id']] = $v;
			}
		}
		return $return;
	}

	/**
	 * 获取按部位采集的数组
	 */
	private function get_collect_by_departments_url(){
		$return = array();
		$parts = M('Departments')->select();
		foreach ($parts as $v => $k) {
			if(!empty($k['pid'])){
				$pids[] = $k['pid'];
			}
		}
		$pids = array_unique($pids);
		foreach ($parts as $k => $v) {
			if(!in_array($v['id'], $pids)){
				$return[$v['id']] = $v;
			}
		}
		return $return;
	}

	/**
	 * 获取并发症id
	 */
	private function get_neopathy_ids($url){
		$ids = '';
		$hj = \QueryList::Query($url,array('title' => array('', 'text'),'url' => array('', 'href')),'a','html','utf-8');
		$neopathy_data = $hj->jsonArr;
		$neopathy_ids = array();
		foreach ($neopathy_data as $v) {
			if (empty($v['title'])) {
				continue;
			}
			if (!($disease = $this->disease_model->where(array('disease_name' => $v['title']))->find())) {
				if(!($add_temp = $this->item(array('id' => substr($v['url'], strrpos($v['url'], '_')+1, -4))))){
					$neopathy_ids[] = $add_temp;
				}else{
					continue;
				}
			}else{
				$neopathy_ids[] = $disease['id'];
			}
		}
		$ids = empty($neopathy_ids) ? '' : implode(',', $neopathy_ids);
		return $ids;
	}
}