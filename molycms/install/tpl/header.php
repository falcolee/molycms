<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>MOLYCMS安装向导</title>
<link rel="stylesheet" href="images/style.css" />
<script type='text/javascript' src='images/jquery.min.js'></script>
<script type="text/javascript">
//安装进度
function jsShow(s) {
	$("#cont").append(s+"<br>").scrollTop(9999);
}
</script>
</head>
<body>
<div class="round_box" style="margin-top:10px;padding:20px;">
	<div class="box"  style="border-bottom:#cdcdcd 1px solid;padding:0 10px;"><img src="images/guide.png"></div>
	<div class="box" style="height:20px;padding-top:10px;padding-botoom:10px;">
	
	<?php
			$step = isset($_GET['do'])?$_GET['do']:1;
			if($step>5)$step = 5;
			if($step==1){$step_1="current";$step_2="";$step_3="";$step_4="";}
			else if($step==2) {$step_1="passed";$step_2="current";$step_3="";$step_4="";}
			else if($step==3) {$step_1="passed";$step_2="passed";$step_3="current";$step_4="";}
			else if($step==4) {$step_1="passed";$step_2="passed";$step_3="passed";$step_4="current";}
			else if($step==5) {
				$step_1="passed";$step_2="passed";$step_3="passed";$step_4="passed";
			}
		?>
	<ul class="step">
				<li class="<?php echo $step_1;?>"><span>许可协议</span></li>
				<li class="<?php echo $step_2;?>"><span>检查安装环境</span></li>
				<li class="<?php echo $step_3;?>"><span>创建数据库</span></li>
				<li class="<?php echo $step_4;?>"><span>安装完成</span></li>
		</ul>
	</div>
