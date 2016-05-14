	
	<div class="box">
		<form action='index.php?do=4' method='post' onsubmit="return check_form();">
			<div>
				<div class="red_box" style='display:none' id='error_div'>
					<img src="images/error.gif" width="16" height="15" /><span id="error_info"></span>
				</div>
				<div class="gray_box">
					<div class="in_box">
						
							<table class="default">
								<colgroup><col width="100px">
								<col>
								</colgroup><tbody><tr>
									<th>数据库地址</th><td><input class="gray" type="text" name="dbhost" value="localhost"> 
									<label>MYSQL数据库的地址，本地默认：localhost</label></td>
								</tr>
								<tr>
									<th>数据库名称</th><td><input class="gray" type="text" name="dbname" value="molycms"> 
									<label class="fail" id="db_name_label" style="padding-left:24px;display:none">请填写正确的数据库名称</label></td>
								</tr>
								<tr>
									<th>账户</th><td><input class="gray" type="text" value="root" name="dbuser">
								<label class="fail"  style="display:none">账户不能为空</label></td>
								</tr>
								<tr>
									<th>密码</th><td><input class="gray" type="password" name="dbpw"></td>
								</tr>
								<tr>
									<th>数据库表前缀</th>
									<td><input class="gray" type="text" value="moly_" name="dbpre">
									<label class="fail"  style="display:none">数据库表前缀不能为空</label></td>
								</tr>
								<tr>
									<th>覆盖安装</th>
									<td><input name="cover" type="checkbox" value="1"></td>
								</tr>
							</tbody>
							</table>

							
							<hr>

							<table class="default">
								<colgroup><col width="100px">
								<col>
								</colgroup><tbody><tr>
									<th>管理员账号</th>
									<td>
										<input class="gray" type="text" name="adm_user" value="admin"><label class="fail"  style="display:none">管理员账号必需为2-16位有效字符</label>
									</td>
								</tr>
								<tr>
									<th>密码</th>
									<td>
										<input class="gray" placeholder='请输入8~32位的密码' type="password" name="adm_pass"><label class="fail"  style="display:none">密码格式不正确，字符在8-32位之间</label>
									</td>
								</tr>
								<tr>
									<th>再次确认</th>
									<td>
										<input class="gray" type="password" name="adm_repass"><label class="fail"  style="display:none">二次密码输入的不一致</label>
									</td>
								</tr>
							</tbody></table>


							<div id="install_status" style="display:none">
								<strong>安装进度</strong>
								<span id="install_status_info">正在安装,请稍后...</span>
								<div id="install_bar_bg"><span id="install_bar" style="width:0px;"></span></div>
							</div>
						
						</div>
				</div>
			</div>
			<p style="text-align:right">
				<input class="button" type="button" onclick="window.location.href = 'index.php?do=2';" value="上一步">
				<input class="button" type="submit" value="下一步"  />
			</p>
			</form>
	</div>
	
	<script type='text/javascript'>
		
	//检查表单信息
	function check_form()
	{
		$('label.error').hide();
		var checkObj   =
		{
			dbname   :/^.+$/i,
			dbuser	  :/^.+$/,
			dbpre	:/^.+$/,
			adm_user:/^.{2,16}$/i,
			adm_pass :/^.{8,32}$/i
		};

		for(val in checkObj)
		{
			var matchResult = $.trim($('[name="'+val+'"]').val()).match(checkObj[val]);
			if(matchResult == null)
			{
				$('[name="'+val+'"]').focus().next("label").show();
				return false;
			}
			else $('[name="'+val+'"]').focus().next("label").hide();
		}

		if($('[name="adm_repass"]').val() != $('[name="adm_pass"]').val())
		{
			$('[name="adm_repass"]').focus().next("label").show();
			return false;
		}
		else{
			$('[name="adm_repass"]').focus().next("label").hide();
		}
		
		return true;
	}

</script>
