// pages/result/special_activity.js
const app = getApp()
Page({

  /**
   * 页面的初始数据
   */
  data: {
    special_activity_list: []
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    if (app.special_activity_list != null){
      this.setData({ special_activity_list: app.special_activity_list })
    }
    var that = this;
    // request group name list
    wx.request({
      url: app.ServerUrl + '/get_special_activity.php',
      success(res) {
        if (res.data.err != 0)
          wx.showToast({ icon: 'none', title: 'server error' })
        else {
          var activity_list = res.data.result.special_activity_list;
          var special_activity_list = []
          for(var i=0; i<activity_list.length; i++){
              special_activity_list.push({name: activity_list[i][0], location: activity_list[i][1], time: activity_list[i][2]})
          }
          that.setData({ special_activity_list: special_activity_list })
          app.special_activity_list = special_activity_list
        }
      },
      fail(res) {
        wx.showToast({ icon: 'none', title: 'network error' })
      }
    })
  },

 
})