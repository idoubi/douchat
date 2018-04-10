/*
* 微信小程序wx.request方法使用Promise封装
* editor: lzsheng
* createTime: 2017-12-22
* wxFetch最终return 一个Promise对象
*/

// Promise finally 的实现
Promise.prototype.finally = function (callback) {
  let P = this.constructor;
  return this.then(
    value => P.resolve(callback()).then(() => value),
    reason => P.resolve(callback()).then(() => { throw reason })
  );
};

/**
 * ===wx.request自身参数===
 * params.url: 开发者服务器接口地址
 * params.data: 请求的参数
 * params.header: 设置请求的 header , header 中不能设置 Referer
 * params.method: 默认为 GET，有效值：OPTIONS, GET, HEAD, POST, PUT, DELETE, TRACE, CONNECT
 * params.dataType: 默认为 json。如果设置了 dataType 为 json，则会尝试对响应的数据做一次 JSON.parse
 * params.responseType: 默认为 text。设置响应的数据类型。合法值：text、arraybuffer
 * params.complete: 接口调用结束的回调函数（调用成功、失败都会执行）
 * 
 * 
 * ===以下为自定义参数===
 * params.loading: { mask: boolean,text: string } //mask-是否显示遮罩，text-加载时的文本
 * 
 */

const globalNetworkTimeout = 10000//app.json - networkTimeout - request
let count = 0 //引用计数

//容毁 - 防止未知情况下loading一直不消失
function handleTimer() {
  this.timer = undefined
  this.reset = () => {
    this.clear()
    this.timer = setTimeout(() => {
      wx.showLoading && wx.hideLoading()
    }, globalNetworkTimeout);
  }
  this.clear = () => {
    this.timer && clearTimeout(this.timer)
  }
}
const loadTimer = new handleTimer()

const wxFetch = params => {
  // console.log(params)

  //配置
  let config = {
    header: {
      'content-type': 'application/json'
    },
    data: {
      _t: new Date().getTime()
    }
  }
  config = Object.assign({}, config, params)

  //是否显示loading
  if (config.loading) {
    loadTimer.reset()
    count++
    const { mask, text } = config.loading
    wx.showLoading({
      title: text || '加载中',
      mask: mask || false,
    })
  }

  //默认错误返回
  const errRes = {
    _errCode: -9999,
    _errMsg: "服务器异常",
    _isTimeout: false,
  }

  const resPromise = new Promise((resolve, reject) => {
    //res.statusCode!=200(本地服务器的返回码)或者wx.request跑到fail回调(微信请求失败)，都认为是请求失败
    //只有wx.request跑到success回调&&res.statusCode=200，才认为是请求成功
    config.success = function (res) {
      // console.log('=====res=====', res)
      if (res.statusCode == 200) {
        resolve(res)
      } else {
        console.warn(`wx.request->success，HTTP请求异常，返回码为：${res.statusCode}`);
        res = Object.assign({}, errRes, res)
        reject(res);//错误处理，返回HTTP状态码
      }
    }
    config.fail = function (res) {
      console.warn("wx.request->fail", res);
      res = Object.assign({}, errRes, res)
      if (res.errMsg && res.errMsg.indexOf('timeout') > 0) {
        res._isTimeout = true
        res._errMsg = "抱歉,网络异常"
      }

      wx.showToast({
        icon: 'loading',
        // image: '/assets/images/group-active.png',
        title: res._errMsg,
        duration: 2000
      })

      reject(res);
    }

    // console.log(config)

    //使用wx.request发起请求
    wx.request(config);

  });

  //无论是resolve还是reject都执行finally
  if (config.loading) {
    resPromise.finally(() => {
      count--
      if (count === 0 && wx.showLoading) {
        wx.hideLoading()
        // loadTimer.clear()
      }
    })
  }

  return resPromise

}



module.exports = wxFetch


/**

//最基本的使用格式
wxFetch({
    url: 'xxx.api',
    data: {
        key: value
    },
    loading: true
}).then(res => {
    console.log('-----res----', res)
}, err => {
    console.log('-----err----', err)
}).catch(err => {
    console.warn('-----catchErr----', err)
})

 */