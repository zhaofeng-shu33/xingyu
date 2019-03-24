// pages/result/index.js
const app = getApp()
Page({

  /**
   * Page initial data
   */
  data: {
    group_list: ['1', '2'],
    groupIndex: 0,
    group_name: '未选择小组',
    group_data: [],
    week:''
  },
  setweek: function (e) {
    if (parseInt(e.detail.value) == NaN)
      return
    this.setData({
      week: e.detail.value
    });
  },
  search: function(e){
    // client data check
    var error_msg = '';
    if (this.data.group_name == '未选择小组') {
      error_msg = '请选择小组'
    }
    else if (this.data.week == '') {
      error_msg = '请填写日期'
    }
    if (error_msg != '') {
      wx.showToast({
        title: error_msg,
        icon: 'none'
      })
      return
    }    
    // request this group data
    var that = this;
    wx.request({
      url: app.ServerUrl + '/get_all_student.php',
      data: { week: that.data.week, student_group: that.data.group_name },
      success(res) {
        if (res.data.err != 0)
          wx.showToast({ icon: 'none', title: 'server error' })
        else {
          var group_data = []
          if (res.data.result.student_list == undefined){
            that.setData({group_data:[]})
            return
          }
          var student_list = res.data.result.student_list
          for (var i = 0; i < student_list.length; i++) {
            group_data.push({ name: student_list[i][0], school: student_list[i][1]})
          }
          that.setData({ group_data: group_data })
        }
      },
      fail(res) {
        wx.showToast({ icon: 'none', title: 'network error' })
      }
    })
  },
  bindPickerChange: function (e) {
    this.setData({
      groupIndex: e.detail.value,
      group_name: this.data.group_list[e.detail.value][0]
    })
    var group_name = this.data.group_list[e.detail.value][0]

  },    
  /**
   * Lifecycle function--Called when page load
   */
  onLoad: function (options) {
    if(app.group_list){
      this.setData({group_list: app.group_list})
      return
    }
    var that = this;
    // request group name list
    wx.request({
      url: app.ServerUrl + '/get_group_list.php',
      success(res) {
        if (res.data.err != 0)
          wx.showToast({ icon: 'none', title: 'server error' })
        else {
          that.setData({ group_list: res.data.result.group_list })
          app.group_list = res.data.result.group_list
        }
      },
      fail(res) {
        wx.showToast({ icon: 'none', title: 'network error' })
      }
    })
  },

  /**
   * Lifecycle function--Called when page is initially rendered
   */
  onReady: function () {

  },

  /**
   * Lifecycle function--Called when page show
   */
  onShow: function () {

  },

  /**
   * Lifecycle function--Called when page hide
   */
  onHide: function () {

  },

  /**
   * Lifecycle function--Called when page unload
   */
  onUnload: function () {

  },

  /**
   * Page event handler function--Called when user drop down
   */
  onPullDownRefresh: function () {

  },

  /**
   * Called when page reach bottom
   */
  onReachBottom: function () {

  },

  /**
   * Called when user click on the top right corner to share
   */
  onShareAppMessage: function () {

  }
})