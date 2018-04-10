var app = getApp();
var WxParse = require('../../wxParse/wxParse.js');
var util = require('../../utils/util.js');
Page({

    /**
     * 页面的初始数据
     */
    data: {
        article: {},
        copyright: '',
    },

    /**
     * 生命周期函数--监听页面加载
     */
    onLoad: function (options) {
        const self = this;
        // cid表示分类，aid表示文章
        let [pid = '', aid = '', page = self.data.page] = [options.pid, options.aid];
        app.setTheme(app.data);

        if (pid) {
            util.req('getPage', 'get', { pid}, function (res) {
                console.log("pid=====>", res);
                if(res.errcode == 0) {
                    // 请求成功，且有数据的情况下
                    self.setData({
                        page: res.items || ""
                    })
                } else {
                    wx.showToast({
                        title: '获取列表失败',
                    })
                }
            });
        } else{
            util.req('getArticle', 'get', { aid }, function (res) {
                console.log("aid=====>", res);
                if (res.errcode == 0) {
                    // 请求成功，且有数据的情况下
                    self.setData({
                        page: res.items
                    })
                } else {
                    wx.showToast({
                        title: '获取文章失败',
                    })
                }
            });
        }
    },

    onShareAppMessage: function (res) {
        return {
            title: this.data.article.title,
            path: ''
        }
    },






})