// JavaScript Document by jacy
/**
 定义基本常量
*/
var RESULT_SUCCESS = 'success';
var RESULT_FAIL = 'fail';
var WeiPHP_RAND_COLOR = ["#ff6600","#ff9900","#99cc00","#33cc00","#0099cc","#3399ff","#9933ff","#cc3366","#333333","#339999","#ff6600","#ff9900","#99cc00","#33cc00","#0099cc","#3399ff","#9933ff","#cc3366","#333333","#339999","#ff6600","#ff9900","#99cc00","#33cc00","#0099cc","#3399ff","#9933ff","#cc3366","#333333","#339999"];

/***/
(function(){
	//异步请求提交表单
	//提交后返回格式json json格式 {'result':'success|fail',data:{....}}
	function doAjaxSubmit(form,callback){
		$.Dialog.loading();
		$.ajax({
			data:form.serializeArray(),
			type:'post',
			dataType:'json',
			url:form.attr('action'),
			success:function(data){
				$.Dialog.close();
				callback(data);
				}
			})
	}
	
	function initFixedLayout(){
		var navHeight = $('#fixedNav').height();
		$('#fixedContainer').height($(window).height()-navHeight);	
	}
	//通用banner
	function banner(isAuto,delayTime){
		var screenWidth = $('.container').width();
		var count = $('.banner li').size();
		$('.banner ul').width(screenWidth*count);
		$('.banner ul').height(screenWidth/2);
		$('.banner').height(screenWidth/2);
		$('.banner li').width(screenWidth).height(screenWidth/2);
		$('.banner li img').width(screenWidth).height(screenWidth/2);
		$('.banner li .title').css({'width':'98%','padding-left':'2%'})
		// With options
		$('.banner li .title').each(function(index, element) {
            $(this).text($(this).text().length>15?$(this).text().substring(0,15)+" ...":$(this).text());
        });
		var flipsnap = Flipsnap('.banner ul');
		flipsnap.element.addEventListener('fstouchend', function(ev) {
			$('.identify em').eq(ev.newPoint).addClass('cur').siblings().removeClass('cur');
		}, false);
		$('.identify em').eq(0).addClass('cur')
		if(isAuto){
			var point = 1;
			setInterval(function(){
				//console.log(point);
				flipsnap.moveToPoint(point);
				$('.identify em').eq(point).addClass('cur').siblings().removeClass('cur');
				if(point+1==$('.banner li').size()){
					point=0;
				}else{
					point++;
					}
				
				},delayTime)
		}
	}
	//相册效果
	function gallery(container,slideContainer){
		var screenWidth = $('.container').width();
		var count = $(container).find('li').size();
		var picWidth = screenWidth/5*3;
		var paddingleft = screenWidth/5;
		$(container).find('ul').width(picWidth*count);
		$(container).find('ul').css('padding-left',paddingleft);
		$(container).find('ul').height(screenWidth/2);
		$(container).height(picWidth);
		$(container).find('li').css({'padding':10,width:picWidth-20,height:picWidth-20});
		$(container).find('li img').width("100%").height("100%");
		var flipsnap = Flipsnap(slideContainer,{
			distance: picWidth
		});
		flipsnap.element.addEventListener('fstouchend', function(ev) {
			$(container).find('.identify em').eq(ev.newPoint).addClass('cur').siblings().removeClass('cur');
		}, false);
		$(container).find('.identify em').eq(0).addClass('cur')
		
	}
	//正方形图片预览
	function squarePicSlide(isAuto,delayTime,width,height,prevBtn,nextBtn){
		var count = $('.banner li').size();
		$('.banner ul').width(width*count);
		$('.banner ul').height(height);
		$('.banner').height(height);
		$('.banner li').width(width).height(height);
		$('.banner li img').width(width).css('min-height',height);
		$('.banner li .title').css({'width':'98%','padding-left':'2%'})
		// With options
		$('.banner li .title').each(function(index, element) {
            $(this).text($(this).text().length>15?$(this).text().substring(0,15)+" ...":$(this).text());
        });
		var flipsnap = Flipsnap('.banner ul');
		flipsnap.element.addEventListener('fstouchend', function(ev) {
			$('.identify em').eq(ev.newPoint).addClass('cur').siblings().removeClass('cur');
		}, false);
		$('.identify em').eq(0).addClass('cur');
		var point = 0;
		if(isAuto){
			
			setInterval(function(){
				//console.log(point);
				flipsnap.moveToPoint(point);
				},delayTime)
		}
		flipsnap.element.addEventListener('fstouchend', function(ev) {
			point = ev.newPoint;
			$('.identify em').eq(point).addClass('cur').siblings().removeClass('cur');
		}, false);
		$(prevBtn).click(function(){
			 if(flipsnap.hasPrev()){
				flipsnap.toPrev();
				point = point-1;
			 }else{
				flipsnap.moveToPoint(count-1);
				point = count-1;
				}
			$('.identify em').eq(point).addClass('cur').siblings().removeClass('cur');
			});
		$(nextBtn).click(function(){
			 if(flipsnap.hasNext()){
				flipsnap.toNext();
				point = point+1;
			 }else{
				flipsnap.moveToPoint(0);
				point = 0;
				}
			$('.identify em').eq(point).addClass('cur').siblings().removeClass('cur');
			
			});
	}
	//随机颜色
	function setRandomColor(selector){
		$(selector).each(function(index, element) {
			$(this).css('background-color',WeiPHP_RAND_COLOR[index]);
		});;
	}
	//显示分享提示
	function showShareTips(callback){
		var tempHtml = $('<div class="shareTips"><div class="tipsPic"></div><a class="close" href="javascript:;"></a></div>');
		$('body').append(tempHtml);
		$('.shareTips').click(function(){
			closeShareTips(callback);	
		})
	}
	function showShareFriend(callback){
		var tempHtml = $('<div class="shareTips"><div class="tips_friend"></div><a class="close" href="javascript:;"></a></div>');
		$('body').append(tempHtml);
		$('.shareTips').click(function(){
			closeShareTips(callback);	
		})
	}
	function showSubscribeTips(opts){
		if(opts.qrcode.length>5){
			var tempHtml = $('<div class="shareTips"><div class="tips_concern"></div><div class="qrcode"><img src="'+opts.qrcode+'"/><p>也可以长按二维码关注公众号</p></div><a class="close" href="javascript:;"></a></div>');
		}else{
			var tempHtml = $('<div class="shareTips"><div class="tips_concern"></div><a class="close" href="javascript:;"></a></div>');
		}
		$('body').append(tempHtml);
		$('.shareTips').click(function(){
			$('.shareTips').remove();
			if(opts.caalback)closeShareTips(opts.callback);	
		})
	}
	function closeShareTips(callback){
		$('.shareTips').remove();
		if(callback){
			callback();	
		}
	}
	//初始化分享数据
	/*参数
	*desc
	*link
	*title
	*imgUrl
	*
	*/
	function initWxShare(shareData){
		wx.ready(function(res){
			//alert('res:'+res);
			//分享
			wx.onMenuShareTimeline({
				title: shareData.desc, // 分享标题
				link: shareData.link, // 分享链接
				imgUrl: shareData.imgUrl, // 分享图标
				success: function () { 
					// 用户确认分享后执行的回调函数
				},
				cancel: function () { 
					// 用户取消分享后执行的回调函数
				}
			});
			wx.onMenuShareAppMessage({
				title: shareData.title, // 分享标题
				desc: shareData.desc, // 分享描述
				link: shareData.link, // 分享链接
				imgUrl: shareData.imgUrl, // 分享图标
				type: shareData.type, // 分享类型,music、video或link，不填默认为link
				dataUrl: shareData.dataUrl, // 如果type是music或video，则要提供数据链接，默认为空
				success: function () { 
					// 用户确认分享后执行的回调函数
				},
				cancel: function () { 
					// 用户取消分享后执行的回调函数
				}
			});
			wx.onMenuShareQQ({
				title: shareData.title, // 分享标题
				desc: shareData.desc, // 分享描述
				link: shareData.link, // 分享链接
				imgUrl: shareData.imgurl, // 分享图标
				success: function () { 
				   // 用户确认分享后执行的回调函数
				},
				cancel: function () { 
				   // 用户取消分享后执行的回调函数
				}
			});
		})
	}
	function back(){
		var hisLen = window.history.length;
		if(hisLen == 1){
			wx.closeWindow();
		}else{
			window.history.back();
		}
	}
	function showQrcode(title,url){
		var qrHtml = $('<div class="qrcode_dialog"><a href="javascript:;" class="close"></a><div class="content"><img src=""/><p></p></div></div>');
		$('img',qrHtml).attr('src','http://qr.liantu.com/api.php?text='+url);
		$('p',qrHtml).html(title);
		$('body').append(qrHtml);
		$('.close',qrHtml).click(function(){
			qrHtml.remove();
		})
	}
	//利用微信接口上传图片
	function wxChooseImg(_this,num,name,callback){
		wx.chooseImage({
			count: num, // 默认9
			sizeType: ['original', 'compressed'], // 可以指定是原图还是压缩图，默认二者都有
			sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
			success: function (res0) {
				var localIds = res0.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
				if(callback){
					callback(localIds);
				}else{
					wxUploadImg(localIds,name,_this);
				}
			}
		});
		
    }
	//利用微信接口上传图片到微信服务器
	function wxUploadImg(localIds,name,target){
		var localId = localIds.pop();
		$.Dialog.loading();
		wx.uploadImage({
			localId: localId, // 需要上传的图片的本地ID，由chooseImage接口获得
			isShowProgressTips: 0, // 默认为1，显示进度提示
			success: function (res) {
				$('textarea').val();
				
				$.get(SITE_URL+"/index.php?s=/Home/Weixin/downloadPic/media_id/"+res.serverId+".html",function(data){
					$.Dialog.close();
					if(data.result=="success"){
						var addImg = $('<div class="img_item"><em>X</em><input type="hidden" name="'+name+'" value="'+data.id+'"/><img src="'+data.picUrl+'"/></div>');
						addImg.insertBefore($(target));
						var uploadImgWidth = $('.muti_picture_row .img_item').width()-10;
						$('.muti_picture_row .img_item').height(uploadImgWidth).width(uploadImgWidth);
						$('em',addImg).click(function(){
							$(this).parent().remove();
						})
						
						if(localIds.length>0){
							wxUploadImg(localIds,name,target);
						}
					}
				})
			}
		});
	}
	//下拉刷新只需要在页面上配置
	//内容列表配置 id="pullContainer"
	//页码使用WeiPHP服务器返回的页码  在page中打开 
	//如：<div class="page" data-pullload="true"> {$_page|default=''} </div>
	function initLoadMorePage(){
		if($('.page').data('pullload')==true){
			$('.page').hide();
			var isLoading = false;
			var $loading = $('<div class="moreLoading"><em></em><br/>正在加载...</div>').hide();
			$loading.insertAfter('#pullContainer');
			$(window).scroll(function(){
				//console.log($('body').height());
				//console.log($(window).scrollTop());	
				var next = $('.page').find('.current').last().next('a.num');
				var nextUrl = next.attr('href');
				if(nextUrl && isLoading==false && $('body').height()<$(window).scrollTop()+$(window).height()+30){
					isLoading = true;
					$loading.show();
					$.get(nextUrl,function(data){
						var dataDom = $(data);
						var listDom = dataDom.find('#pullContainer');
						$('#pullContainer').append(listDom.html());
						isLoading = false;
						$loading.hide();
						$('.page').find('.current').next('a').addClass('current');
					});
				}else if(isLoading == false && isLoading==false && $('body').height()<$(window).scrollTop()+$(window).height()+30){
					$loading.html('没有更多了').show();
				}
				
			});
		}
	}
	//下拉刷新
	//每页拉去数
	var pageCount = 10;
	//是否正在加载
	var isLoading = false;
	//拉取时间戳参数 页码或lastId
	var lastId = 0;
	var minId =0;
	var maxId = 0;
	//类型 0按页码 1按lastId
	var loadType = 0;
	//请求地址
	var loadUrl;
	//是否还有更多
	var hasMore = true;
	//dom class
	var domClass;
	//容器
	var domContainer;
	//加载数据
	function loadMoreContent(){
		
		isLoading = true;
		$('.moreLoading').show();
		$('.noMore').hide();
		$.get(loadUrl,{"count":pageCount,"lastId":lastId,'minId':minId,'maxId':maxId},function(data){
				
			if($.trim(data)==""||data.indexOf('default_png')>0){
				hasMore = false;
				$('.noMore').show();
				$('.moreLoading').hide();
			}else{
				$('#'+domContainer).append(data);
				hasMore = true;
				$('.moreLoading').hide();
			}
			isLoading = false;
		});
	}
	//初始化微信api
	function initWxApi(){
		wx.config({
			debug: false,
			appId: WX_APPID, // 必填，公众号的唯一标识
			timestamp: WXJS_TIMESTAMP, // 必填，生成签名的时间戳
			nonceStr: NONCESTR, // 必填，生成签名的随机串
			signature: SIGNATURE,// 必填，签名，见附录1
			jsApiList: [
				'checkJsApi',
				'onMenuShareTimeline',
				'onMenuShareAppMessage',
				'onMenuShareQQ',
				'onMenuShareWeibo',
				'hideMenuItems',
				'showMenuItems',
				'hideAllNonBaseMenuItem',
				'showAllNonBaseMenuItem',
				'translateVoice',
				'startRecord',
				'stopRecord',
				'onRecordEnd',
				'playVoice',
				'pauseVoice',
				'stopVoice',
				'uploadVoice',
				'downloadVoice',
				'chooseImage',
				'previewImage',
				'uploadImage',
				'downloadImage',
				'getNetworkType',
				'openLocation',
				'getLocation',
				'hideOptionMenu',
				'showOptionMenu',
				'closeWindow',
				'scanQRCode',
				'chooseWXPay',
				'openProductSpecificView',
				'addCard',
				'chooseCard',
				'openCard'
				]
			});
		wx.error(function(res){
			//alert('js授权出错,请检查域名授权设置和参数是否正确');
		})
	}
	function moneyFormat(value){
		var float = parseFloat(value);
		float = Math.ceil(float*100);
		float = float/100;
		if(Number(float) === float && float % 1 === 0){
			float = float+".00";
		}
		return float;
	}
	function getListMaxId(className){
		var maxId = 0;
		$('.'+className).each(function(index, element) {
            if(parseInt($(this).data('lastid'))>maxId){
				maxId = $(this).data('lastid');
			}
        });
		return maxId;
	}
	function getListMinId(className){
		var minId = parseInt($('.'+className).eq(0).data('lastid'));
		$('.'+className).each(function(index, element) {
            if(parseInt($(this).data('lastid'))<minId){
				minId = $(this).data('lastid');
			}
        });
		return minId;
	}
	var WeiPHP = {
		doAjaxSubmit:doAjaxSubmit,
		setRandomColor:setRandomColor,
		initBanner:banner,
		gallery:gallery,
		squarePicSlide:squarePicSlide,
		initFixedLayout:initFixedLayout,
		showShareTips:showShareTips,//弹出提示分享指引
		showShareFriend:showShareFriend,//分享给朋友
		showSubscribeTips:showSubscribeTips,//提示关注公众号
		initLoadMore:function(opts){
			pageCount = opts.pageCount || 10;
			lastId = opts.lastId || 0;
			minId = opts.minId || 0;
			maxId = opts.maxId || 0;
			loadType = opts.loadType || 0;
			loadUrl = opts.loadUrl;
			domClass = opts.domClass || "contentItem";
			domContainer = opts.domContainer || "container";
			$(window).scroll( function() { 
				if(!isLoading && hasMore){
					if(loadType==0){
						lastId++; 
					}else{
						minId = getListMinId(domClass);
						maxId = getListMaxId(domClass);
						lastId = $('.'+domClass).last().data('lastid');
					}
					totalheight = parseFloat($(window).height()) + parseFloat($(window).scrollTop());  
					if ($(document).height() <= totalheight+50){
						loadMoreContent();
					} 
				}else if(hasMore == false){
					$('.noMmore').show();
					$('.moreLoading').hide();
				} 
			})
		},
		initWxShare:initWxShare,
		initWxApi:initWxApi,
		back:back,
		showQrcode:showQrcode,
		wxChooseImg:wxChooseImg,
		wxUploadImg:wxUploadImg,
		initLoadMorePage:initLoadMorePage,
		moneyFormat:moneyFormat,
		getListMinId:getListMinId,
		getListMaxId:getListMaxId
		
		
	};
	$.extend($,{
		WeiPHP: WeiPHP
	});
})();

/*
*/
$(function(){
	//初始化微信js api
	$.WeiPHP.initWxApi();
	//页面总是撑满屏幕
	$('.body').css('min-height',$(window).height());
	//
	$('.toggle_list .title').click(function(){
		$(this).parents('li').toggleClass("toggle_list_open");
		})
	$('.top_nav_a').click(function(){
		if(!$(this).hasClass('active')){
				$(this).next().show();
				$(this).addClass('active')
			}else{
				$(this).next().hide();
				$(this).removeClass('active')
				}
		});
	
	//打开成员详情
	$('.user_item').click(function(){
		var detail = $(this).find('.detail').html();
		var dialogHtml = $('<div class="user_dialog"><span class="close"></span><div>'+detail+'</div></div>');
		var closeHtml = $('.close',dialogHtml);
		closeHtml.click(function(){
			$.Dialog.close();
			});
		$.Dialog.open(dialogHtml);
		})
	//考试选择效果
	$(".testing li input[type='radio']").change(function(){
		var $icon = $(this).parent("label").find(".icon");
		if(!$icon.hasClass("selected"))$icon.addClass('selected');
		$(this).parents("li").siblings().find(".icon").removeClass("selected");
		
	});
	$(".testing li input[type='checkbox']").change(function(){
		var $icon = $(this).parent("label").find(".icon");
		console.log($(this).is(":checked"));
		if($(this).is(":checked")){
			$icon.addClass('selected');
			}else{
				$icon.removeClass('selected');
				}
		
		
		
	});
	$('.class_item .more').click(function(){
			$(this).parent().find('.summary').toggle();
			$(this).parent().find('.desc_all').toggle();
			$(this).html()=="查看更多"?$(this).html("收起"):$(this).html("查看更多");
		});
	//返回
	$(".top_back_btn").click(function(){
		var href = $(this).attr('href');
		if(href=='javascript:void(0);'||href==''||href=='###'||href=='#')	history.back(-1);
	});	
	/* 上传图片*/
	var uploadImgWidth = $('.muti_picture_row .img_item').width()-10;
	$('.muti_picture_row .img_item').height(uploadImgWidth).width(uploadImgWidth);
	$('.muti_picture_row .img_item em').click(function(){
		$(this).parent().remove();
	})
	/* 控制最小高度 */
	if($('.container').data('mh')){
		var mh = parseFloat($('.container').data('mh'))*$(window).height();
		$('.container').css({'min-height':mh})
	}
	//初始化为正方式
	$('.init_square').each(function(index, element) {
	   var img =  $(this).attr('src');
	   var image = new Image();
	   image.onload =function(){
		   $(element).height($(element).width()); 
	   }
       image.src = img;
    });
	//运行倒计时
	function countDownTimer(time){
			var ts = time; 
			var timer = setInterval(function(){
				var dd = parseInt(ts / 60 / 60 / 24, 10);//计算剩余的天数  
				var hh = parseInt(ts / 60 / 60 % 24, 10);//计算剩余的小时数  
				var mm = parseInt(ts / 60 % 60, 10);//计算剩余的分钟数  
				var ss = parseInt(ts % 60, 10);//计算剩余的秒数  
				dd = checkTime(dd);  
				hh = checkTime(hh);  
				mm = checkTime(mm);  
				ss = checkTime(ss);  
				ts--;
				$('#runCountDown .day').text(dd);
				$('#runCountDown .hour').text(hh);
				$('#runCountDown .min').text(mm);
				$('#runCountDown .sec').text(ss);
				if(dd==0 && hh==0 && mm==0 && ss==0){
					clearInterval(timer);
					window.location.reload();
				}
			},1000);		
	}
	function checkTime(i){    
	   if (i < 10) {    
		   i = "0" + i;    
		}    
	   return i;    
	}    
	if($('#runCountDown').data('time')){
		var time = parseInt($('#runCountDown').data('time'));
		if(time>0){
			countDownTimer(time)
		}
	}

})