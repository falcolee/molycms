<?php
/**
 * Copyright (C) 2013-2014 www.kongphp.com All rights reserved.
 * Licensed http://www.gnu.org/licenses/lgpl.html
 * Author: wuzhaohuan <kongphp@gmail.com>
 */

class check{
	
	//验证是否email
	public static function is_email($email=''){
		return strlen($email) > 6 && strlen($email) <= 32 && preg_match("/^([A-Za-z0-9\-_.+]+)@([A-Za-z0-9\-]+[.][A-Za-z0-9\-.]+)$/", $email);
	}
	
	//手机号合法性验证
	public static function is_mobile($mobile = ''){
		if( preg_match("/^1[3458][0-9]{9}$/",$mobile) ){
			return true;
		}else{
			return false;
		}
	}
	
	//验证URL
	public static function is_url($url = ''){
		if( preg_match("/^(\w+:\/\/)?\w+(\.\w+)+.*$/",$url) ){
			return true;
		}else{
			return false;
		}
	}
	
	//验证QQ
	public static function is_qq($qq = 0){
		if( preg_match("/^[1-9][0-9]{4,9}$/",$qq) ){
			return true;
		}else{
			return false;
		}
	}
	
}
