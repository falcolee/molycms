<?php
defined('KONG_PATH') || exit;

$arr = array();
$arr[] = array('name' => 'MAYBECMS', 'url' => 'http://www.maybecms.com');
$this->kv->set('maybe_links', $arr);
