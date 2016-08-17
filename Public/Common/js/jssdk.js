/**
 * 关闭微信浏览器
 * @author 艾逗笔<765532665@qq.com>
 */
function closeWindow() {
  WeixinJSBridge.call('closeWindow');
}

/**
 * 分享朋友圈
 * @author 艾逗笔<765532665@qq.com>
 */
function onMenuShareTimeline(share_data) {
  wx.onMenuShareTimeline(share_data);
}

/**
 * 分享给朋友
 * @author 艾逗笔<765532665@qq.com>
 */
function onMenuShareAppMessage(share_data) {
  wx.onMenuShareAppMessage(share_data);
}

/**
 * 分享到手机QQ
 * @author 艾逗笔<765532665@qq.com>
 */
function onMenuShareQQ(share_data) {
  wx.onMenuShareQQ(share_data);
}

/**
 * 隐藏右上角菜单
 */
function hideOptionMenu() {
  wx.hideOptionMenu();
}

/**
 * 微信支付
 * @author 艾逗笔<765532665@qq.com>
 */
function pay(price, orderid, notify, extra, callback) {
  var url = JSON_PAY;      // 获取支付参数地址
  var data = {                        // 请求参数
      price : price,
      orderid : orderid,
      notify : notify
  };  
  console.log(url);
  console.log(data);       
  $.ajax({                // 发送ajax请求获取调起支付参数
      url : url,
      type : 'post',
      dataType : 'json',
      data : data,
      success : function(data) {
          var json_obj = JSON.parse(data);    // 将返回的参数转换为json对象
          wx.chooseWXPay({                    // 调起支付
              timestamp: json_obj.timeStamp, 
              nonceStr: json_obj.nonceStr, 
              package: json_obj.package, 
              signType: json_obj.signType, 
              paySign: json_obj.paySign, 
              success: function (res) {
                  callback(extra);
              },
              error : function() {
                  alert('支付失败');
              }
          });
      },
      error : function() {
          alert('发送支付请求失败');
      }
  });
}

/**
 * ajax提交数据函数
 * @param String url 处理数据的地址
 * @param Array data 提交的数据
 * @param String successMsg 提交数据成功的提示信息
 * @param String errorMsg 提交数据失败的提示信息
 */
function ajax(url,data,successFunc,errorFunc){
  $.ajax({
    url:url,
    type:"post",
    dataType:"json",
    data:data,
    success:function(data){
      successFunc(data);
    },
    error:function(){
      errorFunc(data);
    }
  });
}