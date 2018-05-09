var app = getApp();
var util = require('../../utils/util.js');
util.init();

Page({
  data: {
    userInfo: null,
    copyright: ''
  },
  onLoad: function (options) {
    var that = this;
    that.setData({
        copyright: app.globalData.copyright
    });
    app.getUserInfo(function(userInfo) {
        that.setData({
            userInfo: userInfo
        });
    });
  },
  onShow: function () {
      app.setNavigationBarTitle('账户中心');
  },
  about: function (e) {
      wx.showModal({
          title: '提示',
          content: app.globalData.about || '',
          showCancel: false
      });
  }
});