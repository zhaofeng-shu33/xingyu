// pages/result/activity_detail.js
const app = getApp()
Page({

  /**
   * 页面的初始数据
   */
  data: {
    activity_name: '',
    activity_location: '',
    activity_time: '',
    student_list: []
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var name = options.activity_name;
    var location = options.activity_location;
    var time = options.activity_time;

    if (name != undefined && location != undefined && time != undefined){
      // request all student joined this activity
      var that = this;
      wx.request({
        url: app.ServerUrl + '/get_all_student.php',
        data: { name: name, location: location,time: time },
        success(res) {
          if (res.data.err != 0){
            wx.showToast({ icon: 'none', title: 'server error' })
            that.setData({ activity_name: name, activity_location: location, activity_time: time })
          }
          else {
            var group_data = []
            var student_list = res.data.result.student_list
            if(student_list != undefined){
              for (var i = 0; i < student_list.length; i++) {
                group_data.push({ name: student_list[i][0], school: app.SchoolReverseMapping[student_list[i][1]] })
              }
            }
            that.setData({ student_list: group_data, 
              activity_name: name, activity_location: location, activity_time: time })
          }
        },
        fail(res) {
          wx.showToast({ icon: 'none', title: 'network error' })
          that.setData({ activity_name: name, activity_location: location, activity_time: time})
        }
      })      
    }
  }
})