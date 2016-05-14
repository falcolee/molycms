<?php defined('KONG_PATH') || exit; ?><!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>操作提示！</title>
<style type="text/css">
body { _margin:0; _height:100%; /*IE6 BUG*/ }
.aui_outer { text-align:left;width:300px;zoom:1;margin:0 auto;position:absolute;top:44%;left:50%;margin:-87px 0 0 -225px; }
table.aui_border, table.aui_dialog { border:0; margin:0; border-collapse:collapse; width:100%; }
.aui_nw, .aui_n, .aui_ne, .aui_w, .aui_c, .aui_e, .aui_sw, .aui_s, .aui_se, .aui_header, .aui_tdIcon, .aui_main, .aui_footer { padding:0; }
.aui_header, .aui_buttons button { font: 12px/1.11 'Microsoft Yahei', Tahoma, Arial, Helvetica, STHeiti; _font-family:Tahoma,Arial,Helvetica,STHeiti; -o-font-family: Tahoma, Arial; }
.aui_title { overflow:hidden; text-overflow: ellipsis; }
.aui_main { text-align:left; min-width:9em; min-width:0\9/*IE8 BUG*/; }
.aui_content { display:inline-block; *zoom:1; *display:inline; text-align:left; border:none 0;}
.aui_icon { vertical-align: middle; }
.aui_icon div { width:48px; height:48px; margin:10px 0 10px 10px; background-position: center center; background-repeat:no-repeat; }
.aui_buttons { padding:8px; text-align:right; white-space:nowrap; }
.aui_buttons button { margin-left:15px; padding: 6px 8px; cursor: pointer; display: inline-block; text-align: center; line-height: 1; *padding:4px 10px; *height:2em; letter-spacing:2px; font-family: Tahoma, Arial/9!important; width:auto; overflow:visible; *width:1; color: #333; border: solid 1px #999; border-radius: 5px; background: #DDD; filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#FFFFFF', endColorstr='#DDDDDD'); background: linear-gradient(top, #FFF, #DDD); background: -moz-linear-gradient(top, #FFF, #DDD); background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#FFF), to(#DDD)); text-shadow: 0px 1px 1px rgba(255, 255, 255, 1); box-shadow: 0 1px 0 rgba(255, 255, 255, .7),  0 -1px 0 rgba(0, 0, 0, .09); -moz-transition:-moz-box-shadow linear .2s; -webkit-transition: -webkit-box-shadow linear .2s; transition: box-shadow linear .2s; }
.aui_buttons button::-moz-focus-inner{ border:0; padding:0; margin:0; }
.aui_buttons button:focus { outline:none 0; border-color:#426DC9; box-shadow:0 0 8px rgba(66, 109, 201, .9); }
.aui_buttons button:hover { color:#000; border-color:#666; }
.aui_buttons button:active { border-color:#666; filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#DDDDDD', endColorstr='#FFFFFF'); background: linear-gradient(top, #DDD, #FFF); background: -moz-linear-gradient(top, #DDD, #FFF); background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#DDD), to(#FFF)); box-shadow:inset 0 1px 5px rgba(66, 109, 201, .9), inset 0 1px 1em rgba(0, 0, 0, .3); }
.aui_buttons button[disabled] { cursor:default; color:#666; background:#DDD; border: solid 1px #999; filter:alpha(opacity=50); opacity:.5; box-shadow:none; }
button.aui_state_highlight { color: #FFF; border: solid 1px #1c6a9e; background: #2288cc; filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#33bbee', endColorstr='#2288cc'); background: linear-gradient(top, #33bbee, #2288cc); background: -moz-linear-gradient(top, #33bbee, #2288cc); background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#33bbee), to(#2288cc)); text-shadow: -1px -1px 1px #1c6a9e; }
button.aui_state_highlight:hover { color:#FFF; border-color:#0F3A56; }
button.aui_state_highlight:active { border-color:#1c6a9e; filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#33bbee', endColorstr='#2288cc'); background: linear-gradient(top, #33bbee, #2288cc); background: -moz-linear-gradient(top, #33bbee, #2288cc); background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#33bbee), to(#2288cc)); }
/* common end */

.aui_inner { background:#FFF; }
.aui_outer, .aui_inner { border:1px solid rgba(0, 0, 0, .7); border:1px solid #333\9; }
.aui_border { box-shadow: inset 0 0 1px rgba(255, 255, 255, .9); }
.aui_nw, .aui_ne, .aui_sw, .aui_se { width:8px; height:8px; }
.aui_nw, .aui_n, .aui_ne, .aui_w, .aui_e, .aui_sw, .aui_s, .aui_se { background:rgba(0, 0, 0, .4); background:#000\9!important; filter:alpha(opacity=40); }

.aui_outer:active { box-shadow:none; }
.aui_titleBar { position:relative; height:100%; }
.aui_title { height:28px; line-height:27px; padding:0 28px 0 10px; text-shadow:0 1px 0 rgba(255, 255, 255, .7); background-color:#edf5f8; font-weight:bold; color:#95a7ae; font-family: Tahoma, Arial/9!important; background-color:#bdc6cd; background: linear-gradient(top, #edf5f8, #bdc6cd); background: -moz-linear-gradient(top, #edf5f8, #bdc6cd); background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#edf5f8), to(#bdc6cd)); filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#edf5f8', endColorstr='#bdc6cd'); border-top:1px solid #edf5f8; border-bottom:1px solid #b6bec5; }

.aui_content { color:#666; }
.aui_buttons { background-color:#F6F6F6; border-top:solid 1px #DADEE5; }
</style>
</head>
<body>
	
	<div class="aui_outer">
		<table class="aui_border">
		<tbody>
			<tr><td class="aui_nw"></td><td class="aui_n"></td><td class="aui_ne"></td></tr>
		<tr>
			<td class="aui_w"></td>
			<td class="aui_c">
			<div class="aui_inner">
				<table class="aui_dialog">
					<tbody>
					<tr>
						<td class="aui_header" colspan="2">
						<div class="aui_titleBar">
						<div class="aui_title" style="cursor: move; display: block;"><?php echo $status ? '操作成功' : '操作失败';?>：</div>
						</div>
						</td>
					</tr>
					<tr>
						<td class="aui_icon" style="display: none;">
						<div class="aui_iconBg" style="background: none repeat scroll 0% 0% transparent;"></div>
						</td>
						<td class="aui_main" style="width: auto; height: auto; visibility: visible;">
							<div class="aui_content" style="padding: 10px 15px;margin:15px 0;">
								<div class="m_con">
									<?php echo $message;?>
									<div id="jump"></div>
								</div>
							</div>
						</td>
					</tr>
					<tr>
						<td class="aui_footer" colspan="2">
							<div class="aui_buttons"><button class="aui_state_highlight" onclick="jumpurl()" type="button">立即跳转</button></div>
						</td>
					</tr>
					</tbody>
				</table>
			</div>
			</td>
			<td class="aui_e"></td>
		</tr>
		<tr><td class="aui_sw"></td><td class="aui_s"></td><td class="aui_se" style="cursor: se-resize;"></td></tr>
		</tbody>
		</table>
	</div>
	<?php if($jumpurl != -1) { ?>
	<script type="text/javascript">
	var dot = '', t;
	var jump = document.getElementById("jump");
	var time = <?php echo $delay;?>;
	function jumpurl(){
		<?php echo $jumpurl == 'history.back()' ? 'history.back()' : 'location.href = "'.$jumpurl.'"';?>;
	}
	function display(){
		dot += '.';
		if(dot.length > 6) dot = '.';
		jump.innerHTML = (time--) + '秒后自动跳转' + dot;
		if(time == -1) {
			clearInterval(t);
			jumpurl();
		}
	}
	display();
	t = setInterval(display, 1000);
	</script>
	<?php } ?>
</body>
</html>