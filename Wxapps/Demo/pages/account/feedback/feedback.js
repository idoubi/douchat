var app = getApp();
var util = require('../../../utils/util.js');
util.init();

Page({
  data: {
    userInfo: null,
    loading: false
  },

  onLoad: function (options) {
    var that = this;
    app.getUserInfo(function(userInfo) {
        that.setData({
            userInfo: userInfo
        });
    });
  },
  onShow: function () {
      app.setNavigationBarTitle('我的资料');
  },
  updateProfile: function (event) {
      var that = this;
      var relname = event.detail.value.relname;
      var mobile = event.detail.value.mobile;
      var signature = event.detail.value.signature;

        that.setData({
            loading: true
        });

        util.request({
            url: 'updateProfile',
            method: 'post',
            data: {
                relname: relname,
                mobile: mobile,
                signature: signature
            },
            success: function(res) {
                if (res && res.errcode == 0) {
                    util.showModal('更新个人资料成功', '提示', function() {
                        util.getUserInfo(function(userInfo) {
                            //console.log(userInfo);
                        }, true);
                        wx.navigateBack();                        
                    });
                } else {
                    util.showModal('更新个人资料失败');
                }
            },
            fail: function() {
                util.showModal('更新个人资料失败');
            }
        });
  }
});