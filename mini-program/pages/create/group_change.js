// pages/create/group_change.js
const app = getApp()
Page({
  data: {
    student_name: '',
    group_list:[1,2],
    groupIndex:0,
    groupName: '流动'
  },
  inputTyping: function (e) {
    this.setData({
      student_name: e.detail.value
    });
  },
  bindPickerChange: function (e) {
    this.setData({
      groupIndex: e.detail.value,
      groupName: this.data.group_list[e.detail.value]
    })
  },
  onLoad: function (options) {
    if (app.group_list) {
      this.setData({ group_list: app.group_list })
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
  modify: function () {
    // client data check
    var error_msg = '';
    if (this.data.student_name == '') {
      error_msg = '请填写姓名'
    }
    else if (this.data.groupName == '流动') {
      error_msg = '不允许更改到流动组'
    }
    if (error_msg != '') {
      wx.showToast({
        title: error_msg,
        icon: 'none'
      })
      return
    }
    var that = this;
    wx.request({
      url: app.ServerUrl + '/modify_student_group.php',
      method: 'POST',
      data: {
        student_name: that.data.student_name,
        group_id: parseInt(that.data.groupIndex) + 1
      },
      success(res) {
        if (res.data.err == 3) {
          wx.showToast({ title: that.data.student_name + '不存在' })
        }
        else if (res.data.err != 0)
          wx.showToast({ icon: 'none', title: 'server error' })
        else {
          wx.showToast({ title: that.data.student_name + ' 更改成功' })
          that.setData({ student_name: '' })
        }
      },
      fail(res) {
        wx.showToast({ icon: 'none', title: 'network error' })
      }
    })
  }  
 
})