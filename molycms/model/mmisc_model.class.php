<?php
// +----------------------------------------------------------------------
// | MOLYCMS
// +----------------------------------------------------------------------
//

defined('MOLYCMS_PATH') or exit(' Access Denied');

class mmisc extends model {
	
	/**
	 * DZ在线中文分词
	 * @param $title string 进行分词的标题
	 * @param $content string 进行分词的内容
	 * @param $encode string API返回的数据编码
	 * @return  string 得到的关键词字符串,多个以英文逗号隔开
	 */
	public function discuzSegment($title = '', $content = '', $encode = 'utf-8') {
		if (empty($title)) {
			return false;
		}
		//标题处理
		$title = rawurlencode(strip_tags(trim($title)));
		//内容处理
		$content = str_replace(' ', '', strip_tags($content));
		//在线分词服务有长度限制
		if (strlen($content) > 2400) {
			$content = mb_substr($content, 0, 2300, $encode);
		}
		//进行URL编码
		$content = rawurlencode($content);
		//API地址
		$url = 'http://keyword.discuz.com/related_kw.html?title=' . $title . '&content=' . $content . '&ics=' . $encode . '&ocs=' . $encode;
		//将XML中的数据,读取到数组对象中
		$xml_array = simplexml_load_file($url);
		$result = $xml_array->keyword->result;
		//分词数据
		$data = array();
		foreach ($result->item as $key => $value) {
			array_push($data, (string) $value->kw);
		}
		if (count($data) > 0) {
			echo implode(',', $data);
		} else {
			echo '';
		}
	}
	
	//邮件发送
	public function sendmail($username, $email, $subject, $message) {
		$mailconf = $this->kv->get('mail_cfg');
	
		if( empty($mailconf) ) return '未获取到邮件配置信息';
		
		if($mailconf['sendtype'] == 0) {			
			$subject = iconv('UTF-8', 'GBK', $subject);
			$message = iconv('UTF-8', 'GBK', $message);
			mail($email, $subject, $message, NULL, NULL);			
		} elseif($mailconf['sendtype'] == 1) {			
			$key = array_rand($mailconf['smtplist']);			
			$smtp = $mailconf['smtplist'][$key];
				
			$message = str_replace("<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />", "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=GBK\" />", $message);
			$subject = iconv('UTF-8', 'GBK', $subject);
			$message = iconv('UTF-8', 'GBK', $message);
			$username = iconv('UTF-8', 'GBK', $username);
			return moly_mail::send($smtp, $username, $email, $subject, $message);				
		}
	}
	
	public function get_email_site($str) {
		$email = array('url'=>'', 'name'=>'');
		switch($str) {
			case '163.com':
				$email['url'] = 'http://mail.163.com/';
				$email['name'] = '163';
				break;
			case '126.com':
				$email['url'] = 'http://mail.163.com/';
				$email['name'] = '163';
				break;
			case 'yeah.net':
				$email['url'] = 'http://mail.163.com/';
				$email['name'] = '163';
				break;
			case 'qq.com':
				$email['url'] = 'http://mail.qq.com/';
				$email['name'] = 'QQ';
				break;
			case 'yahoo.cn':
				$email['url'] = 'http://mail.cn.yahoo.com/';
				$email['name'] = 'Yahoo';
				break;
			case 'yahoo.com.cn':
				$email['url'] = 'http://mail.cn.yahoo.com/';
				$email['name'] = 'Yahoo';
				break;
			case 'sina.com':
				$email['url'] = 'http://mail.sina.com.cn/';
				$email['name'] = 'sina';
				break;
			case 'sina.cn':
				$email['url'] = 'http://mail.sina.com.cn/';
				$email['name'] = 'sina';
				break;
			case 'hotmail.com':
				$email['url'] = 'http://www.hotmail.com/';
				$email['name'] = 'Hotmail';
				break;
			case 'live.cn':
				$email['url'] = 'http://www.hotmail.com/';
				$email['name'] = 'Hotmail';
				break;
			case 'live.com':
				$email['url'] = 'http://www.hotmail.com/';
				$email['name'] = 'Hotmail';
				break;
			case 'gmail.com':
				$email['url'] = 'https://accounts.google.com/ServiceLogin?service=mail';
				$email['name'] = 'Gmail';
				break;
			case 'sohu.com':
				$email['url'] = 'http://mail.sohu.com/';
				$email['name'] = 'sohu';
				break;
			case '21cn.com':
				$email['url'] = 'http://mail.21cn.com/';
				$email['name'] = '21cn';
				break;
			case 'eyou.com':
				$email['url'] = 'http://www.eyou.com/';
				$email['name'] = 'eyou';
				break;
			case '188.com':
				$email['url'] = 'http://www.188.com/';
				$email['name'] = '188';
				break;
			case '263.net':
				$email['url'] = 'http://www.263.net/';
				$email['name'] = '263';
				break;
			case '139.com':
				$email['url'] = 'http://mail.10086.cn/';
				$email['name'] = '139';
				break;
			case 'tom.com':
				$email['url'] = 'http://mail.tom.com/';
				$email['name'] = 'Tom';
				break;
			case 'sogou.com':
				$email['url'] = 'http://mail.sogou.com/';
				$email['name'] = 'sogou';
				break;
			case 'foxmail.com':
				$email['url'] = 'http://www.foxmail.com/';
				$email['name'] = 'foxmail';
				break;
			case 'wo.com.cn':
				$email['url'] = 'http://mail.wo.com.cn/';
				$email['name'] = 'mail.wo.com.cn';
				break;
			default:
				$email['url'] = "http://www.".$str;
				$email['name'] = $str;
				break;
		}
		return $email;
	}
	
}
