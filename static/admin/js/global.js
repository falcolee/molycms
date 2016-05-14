window.isIE6 = window.VBArray && !window.XMLHttpRequest;
window.maybeAjax = {
		//加载半透明效果
		loading : function() {
			maybeAjax.remove();
			window.isIE6 && maybeAjax.unObj();
			$("body").prepend('<div class="ajaxoverlay"></div><div class="ajaxtips"><div class="ajaximg"></div></div>');
			if(window.isIE6) $(".ajaxoverlay").css({"width":document.documentElement.clientWidth, "height":document.documentElement.clientHeight});
			$(window).resize(maybeAjax.setTopLeft);
			maybeAjax.setTopLeft();
		},
		
		//隐藏object,select
		unObj : function() {
			$("object,select").each(function(){
				if($(this).css("visibility") != "hidden") $(this).attr("_maybe_bugs_visibility_maybe_", $(this).css("visibility")).css("visibility", "hidden");
			});
		},

		//显示object,select
		disObj : function() {
			$("[_maybe_bugs_visibility_maybe_]").each(function(){
				$(this).css("visibility", $(this).attr("_maybe_bugs_visibility_maybe_")).removeAttr("_maybe_bugs_visibility_maybe_");
			});
		},

		//删除半透明框和提示框
		remove : function() {
			document.onkeydown = null;
			window.isIE6 && maybeAjax.disObj();
			$(".ajaxoverlay,.ajaxtips").remove();
		},

		//关闭
		close : function() {
			$(".ajaxtips").animate({top:0}, 250, maybeAjax.remove);
		},
		
		//设置提示框位置
		setTopLeft : function(H) {
			if($(".ajaxtips").length == 0) return;
			$(".ajaxtips").css({"top":maybeAjax.getHeight(H), "left":maybeAjax.getWidth()});
		},

		getHeight : function(H) {
			if(window.isIE6) {
				return document.documentElement.scrollTop+(document.documentElement.clientHeight-$(".ajaxtips").height())/2-(typeof H == 'number' ? H : 0);
			}else{
				return ($(".ajaxoverlay").height()-$(".ajaxtips").height())/2-(typeof H == 'number' ? H : 0);
			}
		},

		getWidth : function() {
			return ($(".ajaxoverlay").width()-$(".ajaxtips").width())/2;
		},

		//设置提示框动画
		setTopAn : function(H) {
			var T = maybeAjax.getHeight(H);
			$(".ajaxtips").css({"top":0, "left":maybeAjax.getWidth()}).animate({top:T+10}, 150).animate({top:T-20}, 150).animate({top:T}, 150);
		},
		
		//写入对话框代码
		tipsHtml : function(str) {
			if($(".ajaxtips").length == 0) maybeAjax.loading();
			$(".ajaxtips").html(str);

			$(".ajaxbox").width("auto");
			var W = $(".ajaxbox").width()+5;
			$(".ajaxbox").css({"width":(W>850?850:(W<180?180:W))});
		},
		
		//调试程序
		debug : function(data) {
			var msg = "<div style='width:100%;overflow:auto;'><b>" + data.kp_error + "</b></div>";
			maybeAjax.tipsHtml('<div class="ajaxbox bfalse">'+ msg +'<u>\u6211\u77E5\u9053\u4E86</u></div>');
			maybeAjax.setTopAn();

			$(".ajaxtips u").click(maybeAjax.close);
		},
		
		alert:function(data){
			window.maybeData = data = toJson(data);
			if(window.maybeExit) return;

			maybeAjax.tipsHtml('<div class="ajaxbox b'+ (data.err==0 ? true : false) +'"><i></i><b>'+ data.msg +'</b><u>\u6211\u77E5\u9053\u4E86</u></div>');
			maybeAjax.setTopAn();

			$(".ajaxtips u").click(function(){
				maybeAjax.close();
				if(!window.maybeName && data.name != '') $("[name='"+data.name+"']").focus();
			});
			if(!window.maybeErr && data.err==0){
				setTimeout(maybeAjax.close, 1000);
			}
		},
		
		//确定框
		confirm : function(msg, func) {
			maybeAjax.tipsHtml('<div class="ajaxbox bnote"><i></i><b>'+ msg +'</b></div>');
			$(".ajaxbox").append('<p class="cf"><a id="noA" class="but3">取消</a><a id="okA" class="but3">确认</a></p>');
			maybeAjax.setTopAn();

			$("#noA,#okA").attr("href","javascript:;");
			$("#noA").click(maybeAjax.close);
			$("#okA").click(function(){ maybeAjax.remove(); func(); });

			document.onkeydown = function(e) {
				var e = window.event || e;
				var k = e.which || e.keyCode;
				if(k == 27) {
					maybeAjax.close();
				}else if(k == 13) {
					maybeAjax.remove();
					func();
				}
			}
		},
		
		//提交表单
		submit : function(selector, callback) {
			$(selector).submit(function(){
				maybeAjax.postd($(this).attr("action"), $(this).serialize(), callback);
				return false;
			});
		},
		
		// POST数据
		postd:function(url, param, callback){
			maybeAjax.post(url, param, (!callback ? maybeAjax.alert : callback));
		},
		
		//提交数据
		post : function(url, param, callback) {
			$.ajax({
				type	: "POST",
				cache	: false,
				url		: url,
				data	: param,
				success	: callback,
				error	: function(html){
					alert("ajax提交数据失败，代码:"+ html.status +"，请稍候再试或更换浏览器");
				}
			});
		},

		//获取数据
		get : function(url, callback) {
			$.ajax({
				type	: "GET",
				cache	: true,
				url		: url,
				success	: callback,
				error	: function(html){
					alert("ajax获取数据失败，代码:"+ html.status +"，请稍候再试或更换浏览器");
				}
			});
		},
}

//dialog
$.maybeDialog = function(options) {
	if(options == "open") { $("#maybedialog").show(); return false;
	}else if(options == "close") { $("#maybedialog").hide(); return false;
	}else if(options == "remove") { $("#maybedialog").remove(); $(window).off("resize", resize_position); return false;
	}else if($("#maybedialog").length) { alert("已存在一个对话框了，不允许再创建!"); return false; }
	var objd, tval, dx, dy, sx, sy, objH, objW, bWidth, bHeight, left, top, maxLeft, maxTop, newH, newW;
	var defaults = {
		title:"标题",
		open:true,
		modal:true,
		resizable:true,
		width:600,
		height:300,
		top:"center",
		left:"center",
		zIndex:199,
		minW:300,
		minH:150,
		remove:true
	};
	var o = $.extend(defaults, options);

	//init
	$("body").append('<div id="maybedialog"><div id="maybedialogbox"><div id="maybedialog_title"><span></span><a href="javascript:;">close</a></div><div id="maybedialog_content"><div style="padding:8px">玩命加载中...</div></div><div id="maybedialog_button"><input type="button" value="确定" class="but1 ok"><input type="button" value="取消" class="but1 close"></div></div></div>');

	objd = $("#maybedialogbox");
	if(o.content) $("#maybedialog_content").html(o.content);
	$("#maybedialog_title span").html(o.title);
	if(o.open) { $("#maybedialog").show(); }else { $("#maybedialog").hide(); }
	if(o.modal) {
		$("#maybedialog").prepend('<div id="maybeoverlay"></div>');
		$("#maybeoverlay").css({"z-index":o.zIndex-1, "width":document.documentElement.clientWidth, "height":document.documentElement.clientHeight});
	}

	//resizable
	if(o.resizable) objd.append('<div id="maybedialog_resizable_n"></div><div id="maybedialog_resizable_e"></div><div id="maybedialog_resizable_s"></div><div id="maybedialog_resizable_w"></div><div id="maybedialog_resizable_nw"></div><div id="maybedialog_resizable_ne"></div><div id="maybedialog_resizable_sw"></div><div id="maybedialog_resizable_se"></div>');

	//初始位置
	objd.css({"width":o.width, "height":o.height, "z-index":o.zIndex});
	if(o.top == "center") {objd.css("top",getTop())}else{objd.css("top",o.top)}
	if(o.left == "center") {objd.css("left",getLeft())}else{objd.css("left",o.left)}
	_setH();

	//触发拖动
	$("#maybedialog_title,#maybedialog_resizable_n,#maybedialog_resizable_e,#maybedialog_resizable_s,#maybedialog_resizable_w,#maybedialog_resizable_nw,#maybedialog_resizable_ne,#maybedialog_resizable_sw,#maybedialog_resizable_se").mousedown(function(e){
		objd = $(this).parent();
		$("html,body,#maybedialog").css("user-select","none");
		document.onselectstart = objd[0].onselectstart = function(){return false};
		if(!tval) tval = $(this).attr("id");
		dx=e.pageX,dy=e.pageY,sx=objd.position().left,sy=objd.position().top,objH=objd.height(),objW=objd.width(),bWidth=document.documentElement.clientWidth,bHeight=document.documentElement.clientHeight;
	});

	//关闭拖动
	$(document).mouseup(function(){
		if(objd) {
			$("html,body,#maybedialog").css("user-select","auto");
			document.onselectstart = objd[0].onselectstart = function(){return true};
		}
		if(tval) tval = null;
	});

	function _setH() { $("#maybedialog_content").css("height", objd.height()-$("#maybedialog_title").height()-$("#maybedialog_button").height()-7); }
	function _n(e) { top=e.pageY-(dy-sy); newH = dy-top+objH; if(newH>o.minH && top>=0) objd.css({"top": top, "height": newH}); _setH(); }
	function _e(e) { left=e.pageX-(dx-sx); newW=left-sx+objW; if(newW>o.minW && e.pageX<bWidth-(objW-(dx-sx-1))) objd.css({"width": newW}); }
	function _s(e) { top=e.pageY-(dy-sy); newH=top-sy+objH; if(newH>o.minH && e.pageY<bHeight-(objH-(dy-sy-1))) objd.css({"height": newH}); _setH(); }
	function _w(e) { left=e.pageX-(dx-sx); newW=objW-(left-sx); if(newW>o.minW && left>=0) objd.css({"left": left, "width": newW}); }
	function getTop(){return Math.max(0, (document.documentElement.clientHeight-objd.height())/2);}
	function getLeft(){return Math.max(0, (document.documentElement.clientWidth-objd.width())/2);}

	//获得鼠标指针在页面中的位置
	$(document).mousemove(function(e){
		switch(tval) {
			case "maybedialog_title":
				left=e.pageX-(dx-sx), top=e.pageY-(dy-sy), maxLeft=bWidth-objd.width()-2, maxTop=bHeight-objd.height()-2;
				left = Math.max(0, Math.min(maxLeft, left)); top = Math.max(0, Math.min(maxTop, top)); objd.css({"left": left, "top": top});
				break;
			case "maybedialog_resizable_n":
				_n(e); break;
			case "maybedialog_resizable_e":
				_e(e); break;
			case "maybedialog_resizable_s":
				_s(e); break;
			case "maybedialog_resizable_w":
				_w(e); break;
			case "maybedialog_resizable_nw":
				_n(e); _w(e); break;
			case "maybedialog_resizable_ne":
				_n(e); _e(e); break;
			case "maybedialog_resizable_sw":
				_s(e); _w(e); break;
			case "maybedialog_resizable_se":
				_s(e); _e(e); break;
		}
	});

	var resize_position = function() {
		var obj=$("#maybedialogbox"), p=obj.position(), objW=obj.width(), objH=obj.height(), bodyW=document.documentElement.clientWidth, bodyH=document.documentElement.clientHeight;
		$("#maybeoverlay").css({"width":bodyW, "height":bodyH});
		if(p.left+objW+2 > bodyW) obj.css("left", Math.max(bodyW-objW-2, 0));
		if(p.top+objH+2 > bodyH) obj.css("top", Math.max(bodyH-objH-2, 0));
	}
	$(window).on("resize", resize_position);

	//关闭
	$("#maybedialog_title a,#maybedialog_button .close").click(function(){
		if(o.remove) { $.maybeDialog("remove"); }else{ $.maybeDialog("close"); }
	});
};

$(function() {
	//AJAX删除
	$('.J_ajax_del').on('click', function (e) {
            e.preventDefault();
            var $_this = this,
                $this = $($_this),
                url = $this.prop('href');
	            var msg = "确定删除?";
	            if(typeof($(this).data("msg"))!="undefined"){
	                msg = $(this).data("msg");
	            }
            art.dialog({
                title: false,
                icon: 'question',
                content: msg,
                follow: $_this,
                close: function () {
                    $_this.focus(); //关闭时让触发弹窗的元素获取焦点
                    return true;
                },
                ok: function () {
                	$.ajax({
        				type	: "POST",
        				cache	: false,
        				url		: url,
        				success	: function(data){
        					data = toJson(data);
        					if(data.err==0) setTimeout("window.location.reload()",1000);
        				},
        				error	: function(html){
        					alert("提交数据失败，代码:"+ html.status +"，请稍候再试");
        				}
        			});
                },
                cancelVal: '关闭',
                cancel: true
            });
       	});
	$(".J_ajax_submit_btn").on('click',function(e){
		e.preventDefault();
	    var $_this = this,
	        $this = $($_this),
	        val = $this.html(),
	        action = $this.attr('data-action');
	    	
	        var msg = "确定执行"+val+"操作?";
	        if(typeof($(this).data("msg"))!="undefined"){
	            msg = $(this).data("msg");
	        }
	    art.dialog({
	        title: false,
	        icon: 'question',
	        content: msg,
	        follow: $_this,
	        close: function () {
	            $_this.focus(); //关闭时让触发弹窗的元素获取焦点
	            return true;
	        },
	        ok: function () {
	        	$("#myform").attr("action", action).submit();
	        },
	        cancelVal: '关闭',
	        cancel: true
	    });
	});
});

//重新刷新页面，使用location.reload()有可能导致重新提交
function reloadPage(win) {
    var location = win.location;
    location.href = location.pathname + location.search;
}

//页面跳转
function redirect(url) {
    location.href = url;
}
//html转json
function toJson(data) {
	var json = {};
	try{
		json = eval("("+data+")");

		if(json.kp_error) {
			window.maybeExit = true;	// 用来终止程序执行
		}else{
			window.maybeExit = false;
		}
	}catch(e){
		alert(data);
	}
	return json;
}

//加载JS
function maybeLoadJs() {
	var args = arguments;

	//循环加载JS
	var load = function(i) {
		if(typeof args[i] == 'string') {
			var file = args[i];

			// 不重复加载
			var tags = document.getElementsByTagName('script');
			for(var j=0; j<tags.length; j++) {
				if(tags[j].src.indexOf(file) != -1) {
					if(i < args.length) load(i+1);
					return;
				}
			}

			var script = document.createElement("script");
				script.type = "text/javascript";
				script.src = file;

			// callback next
			if(i < args.length) {
				// Attach handlers for all browsers
				script.onload = script.onreadystatechange = function() {
					if(!script.readyState || /loaded|complete/.test(script.readyState)) {
						// Handle memory leak in IE
						script.onload = script.onreadystatechange = null;

						// Remove the script (取消移除，判断重复加载时需要读 script 标签)
						//if(script.parentNode) { script.parentNode.removeChild(script); }

						// Dereference the script
						script = null;

						load(i+1);
					}
				};
			}
			document.getElementsByTagName('head')[0].appendChild(script);
		}else if(typeof args[i] == 'function') {
			args[i]();
			if(i < args.length) {
				load(i+1);
			}
		}
	}

	load(0);
}

//加载CSS
function maybeLoadCss(file) {
	// 不重复加载
	var tags = document.getElementsByTagName('link');
	for(var j=0; j<tags.length; j++) {
		if(tags[j].href.indexOf(file) != -1) {
			return false;
		}
	}

	var link = document.createElement("link");
	link.rel = "stylesheet";
	link.type = "text/css";
	link.href = file;
	document.getElementsByTagName('head')[0].appendChild(link);
}