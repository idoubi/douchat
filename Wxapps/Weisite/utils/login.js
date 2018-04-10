const app = getApp();
const wxFetch = require("wxFetch");

const login = () => {

	return new Promise((resolve, reject) => {
		const self = this;
		let thrid_session = wx.getStorageSync('thrid_session');

		if (thrid_session) {
			if (!thrid_session.match("NEWAUTH")) {
				// 不是新的thrid_session
				console.warn("=====need new thrid_session======");
				self.aomygodLogin();
			} else {
				// 判断thrid_session是否有效
				wxFetch({
					url: self.globalData.globalApiUrl + 'mpmember/checkTokenExist',
					data: {
						thrid_session
					}
				}).then(res => {
					if (res.data.data.is_correct != 1) {
						console.warn("thrid_session失效，需要重新获取");
						wx.clearStorageSync();
						self.aomygodLogin();
					}
				}).catch(err => {
					console.error("======fail updata thrid_session=========")
				});
			}
		} else {
			self.aomygodLogin();
		}

		self.aomygodLogin = () => {
			wx.login({
				success: res => {
					let code = res.code;

					wx.getSetting({
						success(res) {

							if (!res.authSetting.hasOwnProperty('scope.userInfo')) {
								// 首先判断用户是否对这个授权有过操作
								self.getuserInfo(code);
							} else {

								if (!res.authSetting['scope.userInfo']) {
									// 用户之前拒绝授权
									wx.openSetting({
										success: (res) => {

											wx.authorize({
												scope: 'scope.userInfo',
												success(res) {
													console.log("用户再次授权成功");
													self.getuserInfo(code);
												},
												fail() {
													console.log("用户再次授权失败");
													wx.switchTab({
														url: '/pages/group/index/index'
													})
												}
											})
										}
									})
								} else {
									// 已经授权过
									self.getuserInfo(code);
								}
							}
						}
					})

				}
			})
		}

		self.getuserInfo = (code) => {
				
		}
	})
}