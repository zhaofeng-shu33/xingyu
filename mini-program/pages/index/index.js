//index.js
//获取应用实例
const app = getApp()

Page({
  data: {
    group_list: ['1', '2'],
    groupIndex: 0,
    group_name: '未选择小组',
    group_data: [],
    flow_student: [],
    search_student_list: [],
    week:''
  },
  submit: function(){
    // client data check
    var error_msg = '';
    if (this.data.group_data.length == 0) {
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
    // collect selected student
    var submit_student = this.data.flow_student.slice(0)
    var group_data = this.data.group_data;
    for(var i=0; i<group_data.length; i++){
      if(group_data[i].checked)
        submit_student.push(group_data[i].name)
    }
    wx.request({
      url: app.ServerUrl + '/add_activity.php',
      method: 'POST',
      data:{
        week: parseInt(this.data.week),
        name: this.data.group_name,
        student_list: submit_student
      },
      success(res) {
        if (res.data.err != 0)
          wx.showToast({ icon: 'none', title: 'server error' })
        else
          wx.showToast({
            title: '提交成功',
          })
      },
      fail(res) {
        wx.showToast({ icon: 'none', title: 'network error' })
      }
    })
  },
  add: function(e){
    var student_name = e.target.id;
    if(this.data.flow_student.indexOf(student_name) == -1){
      var flow_student = this.data.flow_student
      flow_student.push(student_name)
      this.setData({ flow_student: flow_student, inputVal: ''});
      wx.showToast({
        title: '已添加'+ student_name,
      })      
    }
  },
  setweek: function(e){
    if(parseInt(e.detail.value) == NaN)
      return
    this.setData({
      week: e.detail.value
    });    
  },
  bindPickerChange: function (e) {
    this.setData({
      groupIndex: e.detail.value,
      group_name: this.data.group_list[e.detail.value][0]
    })
    var group_name = this.data.group_list[e.detail.value][0]
      // request this group data
      var that = this;
      wx.request({
        url: app.ServerUrl + '/get_fixed_student.php',
        data: {student_group : group_name},
        success(res){
          if(res.data.err != 0)
            wx.showToast({ icon: 'none', title: 'server error' })
          else{
            var group_data = []
            var student_list = res.data.result.student_list
            for (var i = 0; i < student_list.length; i++){
              group_data.push({ name: student_list[i][0], school: app.SchoolReverseMapping[student_list[i][1]], value: i})
            }
            that.setData({ group_data: group_data})
          }
        },
        fail(res){
          wx.showToast({ icon: 'none', title: 'network error' })
        }
      })
  },  
  checkboxChange: function(e){
    var checkboxItems = this.data.group_data, values = e.detail.value;
    for (var i = 0, lenI = checkboxItems.length; i < lenI; ++i) {
      checkboxItems[i].checked = false;

      for (var j = 0, lenJ = values.length; j < lenJ; ++j) {
        if (checkboxItems[i].value == values[j]) {
          checkboxItems[i].checked = true;
          break;
        }
      }
    }

    this.setData({
      group_data: checkboxItems
    });
  },
  showInput: function () {
    this.setData({
      inputShowed: true
    });
  },
  hideInput: function () {
    this.setData({
      inputVal: "",
      inputShowed: false
    });
  },
  clearInput: function () {
    this.setData({
      inputVal: ""
    });
  },
  inputTyping: function (e) {
    this.setData({
      inputVal: e.detail.value
    });
    // search the backend database, ignore errors in this case
    var that = this;
    wx.request({
      url: app.ServerUrl + '/get_student_list.php',
      data: { student_name_prefix: e.detail.value},
      success(res) {
        if (res.data.err == 0){
          var search_student_list = []
          var student_list = res.data.result.student_list
          for (var i = 0; i < student_list.length; i++) {
            search_student_list.push({ name: student_list[i][0], school: app.SchoolReverseMapping[student_list[i][1]]})
          }
          that.setData({ search_student_list: search_student_list })
        }
      }
    })  
  },  
  onLoad: function () {
    var that = this;
    // request group name list
    wx.request({
      url: app.ServerUrl + '/get_group_list.php',
      success(res){
        if (res.data.err != 0)
          wx.showToast({ icon: 'none', title: 'server error' })
        else{
          that.setData({ group_list: res.data.result.group_list})
          app.group_list = res.data.result.group_list
        }
      },
      fail(res){
        wx.showToast({icon: 'none',title:'network error'})
      }
    })
  },
  addNew: function(){
    // nav to create page
   wx.switchTab({
     url: '/pages/create/index',
   })
  }
})