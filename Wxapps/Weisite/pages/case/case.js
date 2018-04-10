var app = getApp()
Page({

  /**
   * 页面的初始数据
   */
  data: {
    list: [],
    lastid: 0,
    tid: 0,
    catname: '',
    copyright: '',
    cats: [],
    currentTabsIndex: 0,
    fid: 0,
    currentCatId: 0,
    fatherId: 0,
    blist: {},
    tcolor: '',
    cattype:'0'
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var tid = options.tid;
    var fid = options.fid
    this.setData({
      tid: tid,
      fid: fid,
      currentCatId: tid
    })

    this.loadData(0);
    this.setData({
      copyright: app.globalData.copyright
    })
    this.setTabBar(tid)
  },

  loadMore: function (event) {
    var id = event.currentTarget.dataset.lastid
    this.loadData(id);
  },
  catClick: function (event) {
    var cid = event.currentTarget.dataset.cid;
    var fid = event.currentTarget.dataset.fid;
    this.setData({
      tid: cid,
      fid: fid
    })
    this.loadData(0);
  },

  loadData: function (lastid) {
    var that = this
    app.util.footer(that);
    var uniacid = app.siteInfo.uniacid;
    var tid = that.data.tid;
    var fid = that.data.fid;
    app.util.request({
      url: 'entry/wxapp/news',
      data: {
        m: 'yyf_company',
        lastid: lastid,
        uniacid: uniacid,
        tid: tid,
        fid: fid
      },
      cachetime: 0,
      success: function (res) {
        if (!res.data.errno) {
          that.setData({
            catname: res.data.data.catname,
            currentTabsIndex: res.data.data.currentIndex,
            fatherId: res.data.data.fatherId
           
          })
          that.setData({
            cats: res.data.data.cats,
            cattype: res.data.data.type
          })
          wx.setNavigationBarTitle({
            title: res.data.data.catname
          })
          var len = res.data.data.list.length;
          var currentCatId = res.data.data.currentCatId;
          if (that.data.currentCatId != currentCatId) {
            console.log(that.data.currentCatId + '---' + currentCatId);
            that.setData({
              list: [],
              currentCatId: currentCatId
            })
          } else {
            console.log(1111);

          }


          if (len > 0) {
            that.setData({
              lastid: res.data.data.list[len - 1].id
            })
            var arr = that.data.list
            var newarr = arr.concat(res.data.data.list);
            that.setData({
              list: newarr
            })
          }
        }
      },
      fail: function (res) {
        failGo(res.message);
      }
    });
  },

  onShareAppMessage: function (res) {
    return {
      title: this.data.catname,
      path: '/yyf_company/pages/case/case?tid=' + this.data.fatherId
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
  },
  navigateMini: function (event) {

    var sid = event.currentTarget.dataset.sid;
 
    var appid = this.data.list[sid].appid;
    var pageaddress = this.data.list[sid].pageaddress;
    wx.navigateToMiniProgram({
      appId: appid,
      path: pageaddress,
      success(res) {
        console.log('11');
      }
    })
  }
})