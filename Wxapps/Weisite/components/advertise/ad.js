// components/advertise/ad.js
Component({
  /**
   * 组件的属性列表
   */
  properties: {
      adInfo: {
          type: Object,
          value: {},
          observer(newVal, oldVal) {
              console.log('new', newVal);
              console.log('old', oldVal);
          }
      }
  },

  /**
   * 组件的初始数据
   */
  data: {
      
  },

  /**
   * 组件的方法列表
   */
  methods: {
      turnDetails(e) {
          console.log(e);
          const self = this;
          const caseid = e.currentTarget.dataset.caseid;

          if (caseid && caseid != "undefined") {
              wx.navigateTo({
                  url: '/pages/caseDetails/details?caseid=' + caseid,
              })
          }
      }
  }
})
