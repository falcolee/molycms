    <div class="box">
        <div>
            <div>
                <div class="red_box" style='display:none' id='error_div'>
                    <img src="images/error.gif" width="16" height="15" />环境检测未通过，无法断续安装，请检测配制好环境再试！
                </div>

                <div class="gray_box">
                    <div class="in_box">
                        <?php
                            include("checking.php");
                            $dirs = array(APP_NAME.'/config', APP_NAME.'/log', APP_NAME.'/runtime', APP_NAME.'/plugin', APP_NAME.'/view', 'upload');
                            $config = array('version'=>'5.0','writable'=>$dirs,'ext'=>array('gd','mysql','curl'));
                            $checking = new Checking($config);
                            $info = $checking->check();
                            $class=array(true=>'success',false=>'fail');
                        ?>
                        <b>环境检查：</b>
                        <p class="<?php echo $class[$info['version']];?>">PHP <?php echo PHP_VERSION;?></p>
                        <b>目录、文件权限检查:</b>
                        <?php 
                            foreach ($info['writable'] as $key => $value) {?>
                        <p class="<?php echo $class[$value];?>"><?php echo($key)?></p>
                        <?php }?>
                        <b>扩展配置依赖:</b>
                        <?php 
                            foreach ($info['ext'] as $key => $value) {?>
                        <p class="<?php echo $class[$value];?>"><?php echo($key)?></p>
                        <?php }?>
                    </div>
                </div>
            </div>
            <p style="text-align:right"><input class="button" type="button" onclick="window.location.href = 'index.php?step=1';" value="上一步"><input class="button" type="button" value="下一步" onclick="check_license();" /></p>
        </div>
    </div>
    <script type='text/javascript'>
    //检查协议阅读状态
    function check_license()
    {
        var passed = "<?php echo($class[$info['passed']])?>";
        if(passed == "success")
        {
            window.location.href='index.php?do=3';
        }
        else
        {
            document.getElementById('error_div').style.display = '';
        }
    }
</script>

