// pages/create/index.js
const app = getApp()
Page({

  /**
   * Page initial data
   */
  data: {
    school_list: ['深大','南科大','哈工大', '北大', '清华'],
    schoolIndex: 0,
    student_school: '未选择',
    student_name: ''
  },
  inputTyping: function (e) {
    this.setData({
      student_name: e.detail.value
    });
  },    
  bindPickerChange: function (e) {
    this.setData({
      schoolIndex: e.detail.value,
      student_school: this.data.school_list[e.detail.value]
    })
  },
  /**
   * Lifecycle function--Called when page load
   */
  onLoad: function (options) {

  },
  submit: function (){
    // client data check
    var error_msg = '';
    if(this.data.student_name == ''){
      error_msg = '请填写姓名'
    }
    else if(this.data.school_name == '未选择'){
      error_msg = '请选择学校'
    }
    if(error_msg != ''){
      wx.showToast({
        title: error_msg,
        icon: 'none'
      })
      return
    }
    var that = this;
    wx.request({
      url: app.ServerUrl + '/add_student_flow.php',
      method: 'POST',
      data: { 
        student_name: that.data.student_name, 
        student_school: app.SchoolMapping[that.data.student_school]
      },
      success(res) {
        if (res.data.err != 0)
          wx.showToast({ icon: 'none', title: 'server error' })
        else{
          wx.showToast({title: that.data.student_name + ' 添加成功'})
          that.setData({student_name:''})
        }
      },
      fail(res) {
        wx.showToast({ icon: 'none', title: 'network error' })
      }
    })
  }
})