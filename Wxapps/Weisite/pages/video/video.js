var app = getApp();
var WxParse = require('../../../wxParse/wxParse.js');
Page({

  /**
   * 页面的初始数据
   */
  data: {
    article: {},
    copyright: '',
    id: 0,
    blist: {},
    tcolor: ''
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    this.setData({
      copyright: app.globalData.copyright
    })
    var id = options.id;
    var that = this;
    this.setData({
      id: id
    });
    app.util.footer(that);

    app.util.request({
      url: 'entry/wxapp/content',
      data: {
        m: 'yyf_company',
        id: id
      },
      cachetime: 0,
      success: function (res) {
        if (!res.data.errno) {
          that.setTabBar(id)
          WxParse.wxParse('article1', 'html', res.data.data.content, that, 5);
          that.setData({
            article: res.data.data
          });
          wx.setNavigationBarTitle({
            title: res.data.data.title,
          })
        }
      },
      fail: function (res) {
        failGo(res.message);
      }
    });

  },

  onShareAppMessage: function (res) {
    return {
      title: this.data.article.title,
      path: '/yyf_company/pages/content/content?id=' + id
    }
  },
  setTabBar: function (id) {
    var blist = wx.getStorageSync('barlist');
    var that = this;
    if (!blist) {
      setTimeout(function () {
        that.setTabBar()
      }, 200)
    }
    this.setData({
      blist: blist,
      tcolor: blist.tcolor
    })
    wx.setNavigationBarColor({
      frontColor: '#ffffff',
      backgroundColor: blist.tcolor,
    })
    var pages = getCurrentPages()
    var currentPage = pages[pages.length - 1]
    var blist = this.data.blist;
    var options = currentPage.options

    if (options.id == id) {
      blist['isCurrentPage'] = true;
    }
    var barArr = new Array(blist.m1_path, blist.m2_path, blist.m3_path, blist.m4_path);
    var currentNum = 0;
    for (var x in barArr) {
      if (barArr[x] == 'a' + id) {
        currentNum = parseInt(x) + 1;
      }
    }
    blist['currentNum'] = currentNum;
    this.setData({
      blist: blist
    })
  },
  tel: function () {
    var phone = this.data.blist.phone
    wx.makePhoneCall({
      phoneNumber: phone,
    })
  },
  driver: function () {
    wx.openLocation({
      latitude: Number(this.data.blist.jing),
      longitude: Number(this.data.blist.wei),
      address: this.data.blist.address
    })
  }





})