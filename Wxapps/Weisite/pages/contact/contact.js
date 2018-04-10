var app = getApp()
Page({

  /**
   * 页面的初始数据
   */
  data: {
    info:{},
    copyright: '',
    blist: {},
    tcolor: ''
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function () {
    var that = this
    var uniacid = app.siteInfo.uniacid;
    this.setData({
      copyright: app.globalData.copyright
    })
    //初始化导航数据
    app.util.request({
      url: 'entry/wxapp/contact',
      data: {
        m: 'yyf_company',
        uniacid: uniacid
      },
      cachetime: 0,
      success: function (res) {
        if (!res.data.errno) {
          that.setTabBar();
          that.setData({
            info: res.data.data
           })
          wx.setNavigationBarTitle({
            title: '联系我们',
          }) 
        }
      },
      fail: function (res) {
        failGo(res.message);
      }
    });
  },

  calling: function() {
    wx.makePhoneCall({
      phoneNumber: this.data.info.phone, //此号码并非真实电话号码，仅用于测试
      success: function () {
        console.log("拨打电话成功！")
      },
      fail: function () {
        console.log("拨打电话失败！")
      }
    })
  },

  gomap: function () {
    wx.openLocation({
      latitude: Number(this.data.info.jing),
      longitude: Number(this.data.info.wei),
      address: this.data.info.address
    })  
  },

  onShareAppMessage: function (res) {
    return {
      title: this.data.catname,
      path: '/yyf_company/pages/contact/contact'
    }
  },
  
  setTabBar: function () {
    var blist = wx.getStorageSync('barlist');
    var that = this;
    console.log('11');
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
    var url = currentPage.route
    var blist = this.data.blist;
    var pageArr = url.split('/');
    if (pageArr[pageArr.length - 1] == 'contact') {
      blist['isCurrentPage'] = true;
    }
    var barArr = new Array(blist.m1_path, blist.m2_path, blist.m3_path, blist.m4_path);
    var currentNum = 0;
    for (var x in barArr) {
      if (barArr[x] == 'contact') {
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