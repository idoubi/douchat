// components/tabBar/index.js
var util = require("../../utils/util.js");
var app = getApp();

Component({
    /**
     * 组件的属性列表
     */
    properties: {
        selectedIcon: {
            type: String,
            value: "0"
        }
    },
    /**
     * 组件的初始数据
     */
    data: {
        tabBarList: {}
    },
    ready: function () {
        const self = this;
        let [selectedIcon, list] = [wx.getStorageSync('selectedIcon')];
        if (Object.keys(app.data.tabbar).length > 0 && app.data.tabbar.items) {
            list = app.data.tabbar;

            list.items.forEach((item, index) => {
                if ((selectedIcon != 0 && index == selectedIcon) || (selectedIcon == 0 && index == 0)) {
                    list.items[index].selected = true;
                } else {
                    list.items[index].selected = false;
                }
            });

            self.setData({
                tabBarList: list
            })
        } else {
            util.req('getSettings', 'get', {}, function (res) {
                if (res && res.errcode == 0) {
                    list = res.items.tabbar;

                    list.items.forEach((item, index) => {
                        if ((selectedIcon != 0 && index == selectedIcon) || (selectedIcon == 0 && index == 0)) {
                            list.items[index].selected = true;
                        } else {
                            list.items[index].selected = false;
                        }
                    });

                    self.setData({
                        tabBarList: list
                    });
                    wx.setStorageSync('selectedIcon', '0');
                }
            });
        }

    },
    /**
     * 组件的方法列表
     */
    methods: {
        switchTab: function (e) {
            const self = this;
            console.log(self.data.tabBarList);
            let [index = "0", link = "", type = "", list = self.data.tabBarList] = [e.currentTarget.dataset.index, e.currentTarget.dataset.link, e.currentTarget.dataset.link];
            let items = list.items;
            items.forEach((item, i) => {
                if (i == index) {
                    items[i].selected = true;
                    wx.setStorageSync('selectedIcon', index);
                } else {
                    items[i].selected = false;
                }
            });
            list.items = items;
            console.log('list=====>', list);
            wx.redirectTo({
                url: list.items[index].url,
            })

            // self.setData({
            //     tabBarList: list
            // })
        }
    }
})
