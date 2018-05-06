var apiBase = {
    domain: '',
    mpid: 0,
    addon: '',
    version: '',
    ak: '',
    sk: ''
};

// 初始化
function init() {
    var ext = require('../ext.js')
    if (ext && ext.apiType == 1) {  // 手动接入的方式
        apiBase = ext.apiBase;
    }
}

// 发起请求
function request(options) {
    var url = options.url || '';    // 请求地址
    if (url.indexOf('http') != 0) {     // 通过相对地址发起请求
        url = apiBase.domain + '/addon/' + apiBase.addon + '/api/' + url + '/mpid/' + apiBase.mpid;
    }
    var data = options.data || {};          // 请求数据
    var header = options.header || {};      // 请求头
    if (!header['content-type']) {
        header['content-type'] = 'application/x-www-form-urlencoded';
    }
    if (!header['ak']) {
        header['ak'] = apiBase.ak;
    }
    if (!header['sk']) {
        header['sk'] = apiBase.sk;
    }
    if (!header['version']) {
        header['version'] = apiBase.version;
    }
    if (!header['User-Token']) {
        header['User-Token'] = wx.getStorageSync('userToken');
    }
    var method = (options.method || 'get').toUpperCase();   // 请求方式
    var dataType = options.dataType || 'json';              // 请求数据的格式
    var responseType = options.responseType || 'text';      // 响应数据格式
    wx.request({
        url: url,
        method: method,
        data: data,
        header: header,
        dataType: dataType,
        responseType: responseType,
        success: function(res) {
            if (options.success && typeof options.success == 'function') {
                options.success(res.data);
            }
        },
        fail: function(res) {
            if (options.fail && typeof options.fail == 'function') {
                options.fail(res.data);
            }
        },
        complete: function(res) {
            if (options.complete && typeof options.complete == 'function') {
                options.complete(res.data);
            }
        }
    });
}

// 获取配置
function getSettings(cb, refresh) {
    var settings = wx.getStorageSync('settings');
    if (!settings || refresh == true) {
        request({
            url: 'getSettings',
            method: 'get',
            success: function (res) {
                if (res && res.errcode == 0 && res.items) {
                    wx.setStorageSync('settings', res.items);
                    if (typeof cb == 'function') {
                        cb(res.items);
                    }
                }
            }
        });
    } else {
        if (typeof cb == 'function') {
            cb(settings);
        }
    }
}

// 获取用户信息
function getUserInfo(cb, refresh) {
    if (refresh == true) {
        login(cb)
    } else {
        var userInfo = wx.getStorageSync('userInfo');
        if (typeof cb == 'function') {
            cb(userInfo);
        }
    }
}

// 登录检测
function checkLogin(options) {
    wx.checkSession({
       success: function() {
           var userInfo = wx.getStorageSync('userInfo');
           var userToken = wx.getStorageSync('userToken');
           if (!userInfo || !userToken) {
               if (options && typeof options.fail == 'function') {
                   options.fail();
               }
           } else {
               request({
                   url: 'isLogin',
                   method: 'post',
                   header: {
                       'User-Token': userToken
                   },
                   success: function (res) {
                       if (res && res.errcode == 0) {  // 登录有效
                           if (options && typeof options.success == 'function') {
                               options.success();
                           }
                       } else {
                           if (options && typeof options.fail == 'function') {
                               options.fail();
                           }
                       }
                   },
                   fail: function () {
                       if (options && typeof options.fail == 'function') {
                           options.fail();
                       }
                   }
               });
           }
       },
       fail: function() {
           if (options && typeof options.fail == 'function') {
               options.fail();
           }
       } 
    });
}

// 用户登录
function login(cb) {
    wx.login({
       success: function(res) {     // 本地登录成功
            wx.getUserInfo({    // 获取用户信息
                success: function(ret) {
                    request({       // 远程登录
                        url: 'login',
                        method: 'post',
                        data: {
                            code: res.code,
                            encryptedData: ret.encryptedData,
                            iv: ret.iv
                        },
                        success: function(data) {
                            if (data.errcode == 0 && data.items) { // 登录成功
                                // 缓存用户信息和登录态sk
                                wx.setStorageSync('userInfo', data.items.user_info);
                                wx.setStorageSync('userToken', data.items.user_token);
                                if (typeof cb == 'function') {
                                    cb(data.items.user_info);
                                }
                            } else {
                                loginFail();
                            }
                        },
                        fail: function() {
                            loginFail();
                        }
                    });
                },
                fail: function() {
                    loginFail();
                }
            });
       },
       fail: function() {   // 登录失败
           loginFail();
       }
    });
}

// 登录失败
function loginFail() {
    wx.showModal({
        content: '登录失败，请允许获取用户信息,如不显示请删除小程序重新进入',
        showCancel: false
    });
}

function showTip(sms, icon, fun, t) {
    if (!t) {
        t = 1000;
    }
    wx.showToast({
        title: sms,
        icon: icon,
        duration: t,
        success: fun
    })
}

function showModal(c, t, fun) {
    if (!t)
        t = '提示'
    wx.showModal({
        title: t,
        content: c,
        showCancel: false,
        success: fun
    })
}

function formatTime(date) {
    var year = date.getFullYear();
    var month = date.getMonth() + 1;
    var day = date.getDate();
    var hour = date.getHours();
    var minute = date.getMinutes();
    var second = date.getSeconds();

    return [year, month, day].map(formatNumber).join('/') + ' ' + [hour, minute, second].map(formatNumber).join(':');
}

function formatNumber(n) {
    n = n.toString();
    return n[1] ? n : '0' + n
}

module.exports = {
    formatTime: formatTime,
    init: init,
    checkLogin: checkLogin,
    login: login,
    getSettings: getSettings,
    getUserInfo: getUserInfo,
    request: request,
    showTip: showTip,
    showModal: showModal
};
