var app = getApp();
var util = require('../../../utils/util.js');
util.init();

Page({
  data: {
    userInfo: null,
    copyright: '',
    loading: false,
    windowHeight: 0,
    windowWidth: 0,
    limit: 10,
    diaryList: null,
    writeDiary: false,    
    modifyDiarys: false
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
    var that = this;
    app.setNavigationBarTitle('我的日记');
    that.setData({
        copyright: app.globalData.copyright
    }); 
    util.request({
        url: 'getDiaryList',
        method: 'get',
        success: function(res) {
            console.log(res)
            if (res && res.items) {
                that.setData({
                    diaryList: res.items
                });
            }
        }
    });
    wx.getSystemInfo({
        success: (res) => {
            that.setData({
                windowHeight: res.windowHeight,
                windowWidth: res.windowWidth
            })
        }
    })
  },
  toAddDiary: function () {
      var that = this;
      that.setData({
          writeDiary: true,
          nowTitle: '',
          nowContent: '',
          nowId: ''
      });
  },
  toModifyDiary: function(event) {
      var that = this;
      var nowTile = event.target.dataset.title;
      var nowContent = event.target.dataset.content;
      var nowId = event.target.dataset.id;
      that.setData({
          modifyDiarys: true,
          nowTitle: nowTile,
          nowContent: nowContent,
          nowId: nowId
      });
  },
  closeLayer: function () {
      var that = this;
      that.setData({
          writeDiary: false
      })
  },
  addDiary: function(e) {
      var that = this;
      var title = e.detail.value.title;
      var content = e.detail.value.content;
      if (title == "" || content == "") {
          util.showTip("标题或内容必填", "loading");
          return;
      }
      util.request({
          url: 'addDiary',
          method: 'post',
          data: {
              title: title,
              content: content
          },
          success: function (res) {
              if (res && res.errcode == 0) {
                  util.showTip('日记添加成功', 'success', function () {
                      that.onShow();
                      that.setData({
                          writeDiary: false 
                      });
                  });
              } else {
                  util.showTip('日记添加失败', 'loading');
              }
          },
          fail: function () {
              util.showTip('日记添加失败', 'loading');
          }
      });
  },
  deleteDiary: function(event) {
      var that = this;
      var objectId = event.target.dataset.id;
      wx.showModal({
          title: '操作提示',
          content: '确定要删除？',
          success: function (res) {
              if (res.confirm) {
                util.request({
                    url: 'deleteDiary',
                    method: 'post',
                    data: {
                        id: objectId
                    },
                    success: function(res) {
                        util.showTip('删除成功');
                        that.onShow();
                    },
                    fail: function() {
                        util.showTip('删除失败', 'loading');
                    }
                });
              }
          }
      });
  },
  modifyDiary: function (e) {
      //修改日记
      var that = this;
      var id = that.data.nowId;
      var title = e.detail.value.title;
      var content = e.detail.value.content;
      if (title == "" || content == "") {
          util.showTip("标题或内容必填", "loading");
          return;
      }
      util.request({
          url: 'editDiary',
          method: 'post',
          data: {
              id: id,
              title: title,
              content: content
          },
          success: function(res) {
              if (res && res.errcode == 0) {
                  util.showTip('日记修改成功', 'success', function () {
                      that.onShow();
                      that.setData({
                          modifyDiarys: false
                      })
                  });
              } else {
                  util.showTip('日记修改失败', 'loading');
              }
          },
          fail: function() {
              util.showTip('日记修改失败', 'loading');
          }
      });
  },
  closeAddLayer: function () {
      var that = this;
      that.setData({
          modifyDiarys: false
      });
  },
  noneWindows: function () {
      var that = this;
      that.setData({
          writeDiary: "",
          modifyDiarys: ""
      })
  },
});