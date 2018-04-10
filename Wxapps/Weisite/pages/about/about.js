var app = getApp();
var WxParse = require('../../wxParse/wxParse.js');
Page({

  /**
   * 页面的初始数据
   */
  data: {
    content:{},
    catname:'',
    tid: 0,
    copyright:'',
    blist:{}
  },
  
  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var tid = options.tid;
    var uniacid = app.siteInfo.uniacid;
    this.setData({
      tid: tid,
      copyright : app.globalData.copyright
    })
    var that = this;
    app.util.request({
      url: 'entry/wxapp/about',
      data: {
        m: 'yyf_company',
        tid: tid,
        uniacid: uniacid
      },
      cachetime: 0,
      success: function (res) {
        if (!res.data.errno) {
          that.setTabBar(tid)
          WxParse.wxParse('article1', 'html', res.data.data.content, that, 5);
          that.setData({
            content: res.data.data.content,
            catname: res.data.data.catname
          })
          wx.setNavigationBarTitle({
            title: res.data.data.catname,
          })
          
        }
      },
      fail: function (res) {

      }
    });
  },
  
  onShareAppMessage: function (res) {
    return {
      title: this.data.catname,
      path: '/yyf_company/pages/about/about?tid='+this.data.tid
    }
  },

  setTabBar: function (tid) {
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
    
    if (options.tid == tid) {
      blist['isCurrentPage'] = true;
    }
    var barArr = new Array(blist.m1_path, blist.m2_path, blist.m3_path, blist.m4_path);
    var currentNum = 0;
    for (var x in barArr) {
      if (barArr[x] == tid) {
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