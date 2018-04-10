var app = getApp();
Page({
  data: {
    form:{},
    copyright: '',
    t1v:'',
    t2v: '',
    t3v: '',
    t4v: '',
    rv:'',
    cv:'',
    av:'',
    av1:'',
    blist: {},
    tcolor: ''
  },
  onLoad: function (options) {
   
    var uniacid = app.siteInfo.uniacid;
    this.setData({
      copyright: app.globalData.copyright
    })
    var that = this;
    app.util.request({
      url: 'entry/wxapp/form',
      data: {
        m: 'yyf_company',
        uniacid: uniacid
      },
      cachetime: 0,
      success: function (res) {
        if (!res.data.errno) {
          that.setTabBar();
          that.setData({
            form: res.data.data
          })
          wx.setNavigationBarTitle({
            title: res.data.data.catname,
          })
         
        }
      }
    });
  },
 
  notice: function(str){
    wx.showModal({
      title: str,
      content: '',
      success: function (res) {
      }
    })
  }, 
  notice1: function (str) {
    wx.showModal({
      title: '',
      content: str,
      success: function (res) {
      }
    })
  }, 
  t1: function (e) {
    console.log(e.detail.value);
    this.setData({
      t1v: e.detail.value
    })
  },
  t2: function (e) {
    this.setData({
      t2v: e.detail.value
    })
  },
  t3: function (e) {
    this.setData({
      t3v: e.detail.value
    })
  },
  t4: function (e) {
    this.setData({
      t4v: e.detail.value
    })
  },
  radioChange: function (e) {
    this.setData({
      rv: e.detail.value
    })
  },
  checkChange: function (e) {
    this.setData({
      cv: e.detail.value
    })
  },
  textareaBlur: function (e) {
    this.setData({
      av: e.detail.value
    })
  },
  submitClick: function (e) {
    var istrue=true;
    var test=this.data;
    
    
    if(test.t1v=='' && test.form.t1full=='1'){
      this.notice(test.form.t1name+'不能为空!');
      istrue=false;
      return false;
    }
    if (test.t2v == '' && test.form.t2full == '1') {
      this.notice(test.form.t2name + '不能为空!');
      istrue = false;
      return false;
    }
    if (test.t3v == '' && test.form.t3full == '1') {
      this.notice(test.form.t3name + '不能为空!');
      istrue = false;
      return false;
    }
    if (test.t4v == '' && test.form.t4full == '1') {
      this.notice(test.form.t4name + '不能为空!');
      istrue = false;
      return false;
    }

    if (test.rv == '' && test.form.rfull == '1') {
      this.notice(test.form.rname + '不能为空!');
      istrue = false;
      return false;
    }
    if (test.cv == '' && test.form.cfull == '1') {
      this.notice(test.form.cname + '不能为空!');
      istrue = false;
      return false;
    }
    if (test.rv == '' && test.form.rfull == '1') {
      this.notice(test.form.rname + '不能为空!');
      istrue = false;
      return false;
    }
    if (test.av == '' && test.form.afull == '1') {
      this.notice(test.form.aname + '不能为空!');
      istrue = false;
      return false;
    }

    if(istrue){
      var uniacid = app.siteInfo.uniacid;
      var that = this;
      app.util.footer(that);
      var sendtime = wx.getStorageSync('sendtime');
      if (sendtime == '') {
        sendtime=0;
      }
      //var sendMailUrl = this.testUrl('entry/wxapp/sendmail', { m: 'yyf_company' }) + 'm=yyf_company';
      app.util.request({
        url: 'entry/wxapp/formadd',
        data: {
          m: 'yyf_company',
          uniacid: uniacid,
          t1v: that.data.t1v,
          t1name: that.data.form.t1name,
          t2v: that.data.t2v,
          t2name: that.data.form.t2name,
          t3v: that.data.t3v,
          t3name: that.data.form.t3name,
          t4v: that.data.t4v,
          t4name: that.data.form.t4name,
          rv: that.data.rv,
          rname: that.data.form.rname,
          cv: that.data.cv,
          cname: that.data.form.cname,
          av: that.data.av,
          aname: that.data.form.aname,
          sendtime: sendtime
          
        },
        cachetime: 0,
        success: function (res) {
          if (!res.data.errno) {
            if (res.data.errno==1){
              that.notice(res.data.message);
            }else{
              var timestamp = Date.parse(new Date());
              timestamp = timestamp / 1000;
              wx.setStorageSync('sendtime', timestamp);
              that.notice1(that.data.form.successtext);
            }
          }
        }
      });
    }

  },
  onShareAppMessage: function (res) {
    return {
      title: this.data.catname,
      path: '/yyf_company/pages/message/message'
    }
  },
  setTabBar: function () {
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
    var url = currentPage.route
    var blist = this.data.blist;
    var pageArr = url.split('/');
    if (pageArr[pageArr.length - 1] == 'message') {
      blist['isCurrentPage'] = true;
    }
    var barArr = new Array(blist.m1_path, blist.m2_path, blist.m3_path, blist.m4_path);
    var currentNum = 0;
    for (var x in barArr) {
      if (barArr[x] == 'message') {
        currentNum = parseInt(x) + 1;
      }
    }
    blist['currentNum'] = currentNum;
    this.setData({
      blist: blist
    })
  },
  testUrl:function (action, querystring) {

    var url = app.siteInfo.siteroot + '?i=' + app.siteInfo.uniacid + '&t=' + app.siteInfo.multiid + '&v=' + app.siteInfo.version + '&from=wxapp&';

    if (action) {
      action = action.split('/');
      if (action[0]) {
        url += 'c=' + action[0] + '&';
      }
      if (action[1]) {
        url += 'a=' + action[1] + '&';
      }
      if (action[2]) {
        url += 'do=' + action[2] + '&';
      }
    }
    // if (querystring) {
    //   for (param in querystring) {
    //     if (param && querystring[param]) {
    //       url += 'param=' + querystring[param] + '&';
    //     }
    //   }
    // }
    return url;
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