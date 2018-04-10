var app = getApp()
var util = require('../../utils/util.js');
Page({
    data: {
        theme_color: '#1ba358',
        slider: {},
        category: {},
        nav: {},
        notice: {},
        tabBar_is_show: "0",
        selectedIcon: "0"
    },
    onLoad: function (options) {
        var that = this
        util.checkLogin(app);
        app.setTheme(app.data);
        util.req('getSettings', 'get', {}, function (res) {
            if (res && res.errcode == 0) {
                if (res.items) {
                    var site = res.items.site;
                    var index_show = res.items.index_show;
                    var tabbar = res.items.tabbar;

                    if (site) {
                        that.setData({
                            theme_color: site.theme_color,
                            copyright: site.copyright
                        })
                    }

                    if (index_show) {
                        that.setData({
                            slider: index_show.slider,
                            category: index_show.category,
                            nav: index_show.nav,
                            notice: index_show.notice,
                            tabBar_is_show: tabbar.is_show
                        })
                    }
                }
            }
        });
    },
    onShareAppMessage: function (res) {
        return {
            title: this.data.sysinfo.name,
            path: '/yyf_company/pages/index/index'
        }
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
        var fid = event.currentTarget.dataset.fid;
        var appid = this.data.list[fid].list[sid].appid;
        var pageaddress = this.data.list[fid].list[sid].pageaddress;
        wx.navigateToMiniProgram({
            appId: appid,
            path: pageaddress,
            success(res) {
                console.log('11');
            }
        })
    },
    getCurrentTab: function () {
        const self = this;
    }
})