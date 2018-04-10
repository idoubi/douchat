//app.js
let util = require('utils/util.js');
util.init();
App({
	data: {
		tabbar: {}
	},
	onLaunch: function () {
		let that = this;
    console.log(util.apiBase)
		// util.req('getSettings', 'get', {}, function (res) {
		// 	if (res && res.errcode == 0 && res.items && res.items.site) {
		// 		wx.setNavigationBarColor({
		// 			frontColor: '#ffffff',
		// 			backgroundColor: res.items.site.theme_color,
		// 		})
		// 		wx.setNavigationBarTitle({
		// 			title: res.items.site.title,
		// 		});
		// 		let basic = {
		// 			frontColor: '#ffffff',
		// 			backgroundColor: res.items.site.theme_color,
		// 			title: res.items.site.title,
		// 			tabbar: res.items.tabbar
		// 		};
		// 		Object.assign(that.data, basic);
		// 		for (let item in basic) {
		// 			wx.setStorageSync(item, basic[item])
		// 		}
		// 	}
		// })
	},
	onShow: function() {
		const self = this;
		self.setTheme(self.data);
	},
	setTheme: function (data) {
		const self = this;
		wx.setNavigationBarColor({
			frontColor: data.frontColor || wx.getStorageSync("frontColor"),
			backgroundColor: data.backgroundColor || wx.getStorageSync("backgroundColor"),
		})
		wx.setNavigationBarTitle({
			title: data.title || wx.getStorageSync("title")
		});
	},
	globalData: {
	}
})