// pages/create/manage.js
const app = getApp();
Page({

  /**
   * Page initial data
   */
  data: {
    start_time: '2020-01-02',
    file_name: '未选择文件',
    file_path: ''
  },

  /**
   * Lifecycle function--Called when page load
   */
  onLoad: function (options) {

  },
  bindPickerChange: function (e) {
    this.setData({
      start_time: e.detail.value
    })    
  },
  bindSelect: function (e) {
    var that = this;
    wx.chooseMessageFile({
      count: 1,
      type: 'file',
      success(res) {
        // tempFilePath可以作为img标签的src属性显示图片
        var temp_file_name = res.tempFiles[0].name;
        if (temp_file_name.search('xlsx') < 0) {
          wx.showToast({ icon: 'none', title: '请选择xlsx后缀的文件' });
          return;
        }
        that.setData({
          file_name: res.tempFiles[0].name,
          file_path: res.tempFiles[0].path
        })
      }
    })
  },
  /**
   * Lifecycle function--Called when page is initially rendered
   */
  onReady: function () {

  },
  add_wrapper: function (res) {
    if (app.globalData.nickname == null) {
      app.get_user_info_from_res(res);
    }
    else if (app.globalData.openid == 'invalid') {
      wx.showToast({ icon: 'none', title: '不具备添加权限' });
    }
    else if (this.data.file_name == '未选择文件') {
      wx.showToast({ icon: 'none', title: '请先选择文件' });
    }
    else {
      wx.uploadFile({
        url: app.ServerUrl + '/add_meta_data.php',
        filePath: this.data.file_path,
        name: 'volunteer',
        formData: {
          'time': this.data.start_time,
          'openid': app.globalData.openid
        },
        success(res) {
          var json = JSON.parse(res.data);
          if (json.err == 5) {
            wx.showToast({ icon: 'none', title: 'format error' })
          }
          else if (json.err == 44) {
            wx.showToast({ icon: 'none', title: '无权限' })
          }
          else {
            wx.showToast({ title: '更新成功' })
          }
        },
        fail(res) {
          wx.showToast({ icon: 'none', title: 'network error' })
        }
      })
    }
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