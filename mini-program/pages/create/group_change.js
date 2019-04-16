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
  add: function(){
    this.modify('add');
  },
  delete: function(){
    this.modify('delete');
  },
  modify: function (action) {
    // client data check
    var error_msg = '';
    if (this.data.student_name == '') {
      error_msg = '请填写姓名'
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
      url: app.ServerUrl + '/modify_student_group.php?action=' + action,
      method: 'POST',
      data: {
        student_name: that.data.student_name,
        group_id: parseInt(that.data.groupIndex) + 1
      },
      success(res) {
        if (res.data.err == 3) {
          wx.showToast({ icon: 'none',title: that.data.student_name + '不存在' })
        }
        else if(res.data.err == 5){
          wx.showToast({ icon: 'none', title: that.data.student_name + '不属于' + that.data.groupName + '小组'})
        }
        else if (res.data.err != 0)
          wx.showToast({ icon: 'none', title: 'server error' })
        else {
          if (action == 'add'){
            wx.showToast({ title: '添加' + that.data.student_name + '到' + that.data.groupName + '小组' +'成功' })
          }
          else{ //delete
            wx.showToast({ title: '从' + that.data.groupName + '小组' + '删除' + that.data.student_name + ' 成功' })
          }
          that.setData({ student_name: '' })
        }
      },
      fail(res) {
        wx.showToast({ icon: 'none', title: 'network error' })
      }
    })
  }  
 
})