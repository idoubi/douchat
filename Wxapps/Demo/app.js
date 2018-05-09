var util = require('utils/util.js');
util.init();

App({
    globalData: {
        userInfo: null,
        title: '豆信',
        copyright: 'Copyright © 2015-2018 豆信 版权所有',
        about: '豆信是一个简洁、高效、优雅的微信开发框架，学习交流请加QQ群：473027882'
    },

  onLaunch: function () {
    var that = this;
    util.getSettings(function(res) {
        if (res && res.basic) {
            that.globalData.title = res.basic.title;
            that.globalData.copyright = res.basic.copyright;
            that.globalData.about = res.basic.about;
        }
    }, true);
  },

  onShow: function (options) {
      
  },

  // 设置导航文字
  setNavigationBarTitle: function(subTitle) {
      var title = this.globalData.title;
      if (subTitle) {
          title = subTitle + '--' + title;
      }
      wx.setNavigationBarTitle({
          title: title,
      });
  },
  getUserInfo: function(cb) {
      var that = this;
      // 获取用户信息的时候，强制检测登录态
      util.checkLogin({
          success: function () {
              util.getUserInfo(function (userInfo) {
                  that.globalData.userInfo = userInfo;
                  if (typeof cb == 'function') {
                      cb(userInfo);
                  }
              });
          },
          fail: function () {
              util.login(function (userInfo) {
                  that.globalData.userInfo = userInfo;
                  if (typeof cb == 'function') {
                      cb(userInfo);
                  }
              });
          }
      });
  }
});
