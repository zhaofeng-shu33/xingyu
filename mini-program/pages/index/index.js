//index.js
//获取应用实例
const app = getApp()

Page({
  data: {
    group_list: [['1','2'], ['1','2']],
    groupIndex: [0, 0],
    group_name: '未选择小组',
    group_data: [],
    flow_student: [],
    semester_dic: [],
    semester_name_to_id: {},
    semester_id: 1,
    search_student_list: [],
    week:'',
    userInfo: {},
    canIUse: wx.canIUse('button.open-type.getUserInfo'),
    openid : ''
  },

  submit_wrapper: function (res) {
    if (app.globalData.nickname == null) {
      app.get_user_info_from_res(res);
    }
    else if (app.globalData.openid == 'invalid') {
      wx.showToast({ icon: 'none', title: '不具备添加权限' });
    }
    else {
      this.submit();
    }
  },
  append_wrapper: function (res) {
    if (app.globalData.nickname == null) {
      app.get_user_info_from_res(res);
    }
    else if (app.globalData.openid == 'invalid') {
      wx.showToast({ icon: 'none', title: '不具备修改权限' });
    }
    else {
      this.append();
    }
  },
  delete_wrapper: function (res) {
    if (app.globalData.nickname == null) {
      app.get_user_info_from_res(res);
    }
    else if (app.globalData.openid == 'invalid') {
      wx.showToast({ icon: 'none', title: '不具备删除权限' });
    }
    else {
      this.delete();
    }
  },
  submit: function (){
    // client data check

    var userInfo = app.globalData.userInfo
    var openid = app.globalData.openid
    console.log('user',openid)

    
    var error_msg = '';
    if (this.data.group_data.length == 0) {
      error_msg = '请选择小组'
    }
    else if (this.data.week == '') {
      error_msg = '请填写日期'
    }

    // collect selected student
    var submit_student = this.data.flow_student.slice(0)
    var group_data = this.data.group_data;
    for(var i=0; i<group_data.length; i++){
      if(group_data[i].checked)
        submit_student.push(group_data[i].name)
    }
    if (submit_student.length == 0 && error_msg == '') {
      error_msg = '请选择参加活动的同学'
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
      url: app.ServerUrl + '/add_activity.php',
      method: 'POST',
      data:{
        week: parseInt(this.data.week),
        name: this.data.group_name,
        student_list: submit_student,
        semester: parseInt(this.data.semester_id),
        openid:openid
      },
      success(res) {
        if(res.data.err == 5){
          wx.showToast({title:'活动已存在'})
        }
        else if (res.data.err == 44) {
          wx.showToast({ icon: 'none', title: '不具备添加权限' })
        }  
        else if (res.data.err != 0)
          wx.showToast({ icon: 'none', title: 'server error' })
        else{
          wx.showToast({
            title: '提交成功',
          })
          that.setData({flow_student:[]})
        }
      },
      fail(res) {
        wx.showToast({ icon: 'none', title: 'network error' })
      }
    })
  },
  append: function () {
    // client data check
    var error_msg = '';
    var openid = app.globalData.openid;
    if (this.data.group_data.length == 0) {
      error_msg = '请选择小组'
    }
    else if (this.data.week == '') {
      error_msg = '请填写日期'
    }
    // collect selected student
    var submit_student = this.data.flow_student.slice(0)
    var group_data = this.data.group_data;
    for (var i = 0; i < group_data.length; i++) {
      if (group_data[i].checked)
        submit_student.push(group_data[i].name)
    }
    if (submit_student.length == 0 && error_msg == ''){
      error_msg = '请选择要补录的同学'
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
      url: app.ServerUrl + '/append_activity.php',
      method: 'POST',
      data: {
        week: parseInt(this.data.week),
        name: this.data.group_name,
        semester: parseInt(this.data.semester_id),
        student_list: submit_student,
        openid:openid
      },
      success(res) {
        if (res.data.err == 5) {
          wx.showToast({ title: '活动不存在' })
        }
        else if (res.data.err == 44) {
          wx.showToast({ icon: 'none', title: '不具备修改权限' })
        }  
        else if (res.data.err != 0)
          wx.showToast({ icon: 'none', title: 'server error' })
        else {
          wx.showToast({
            title: '补录成功',
          })
          that.setData({ flow_student: [] })
        }
      },
      fail(res) {
        wx.showToast({ icon: 'none', title: 'network error' })
      }
    })
  },  
  delete: function () {
    // client data check
    var error_msg = '';
    var openid = app.globalData.openid;
    if (this.data.group_data.length == 0) {
      error_msg = '请选择小组'
    }
    else if (this.data.week == '') {
      error_msg = '请填写日期'
    }
    // collect selected student
    var submit_student = this.data.flow_student.slice(0)
    var group_data = this.data.group_data;
    for (var i = 0; i < group_data.length; i++) {
      if (group_data[i].checked)
        submit_student.push(group_data[i].name)
    }
    if (submit_student.length == 0 && error_msg == '') {
      error_msg = '请选择要移除的同学'
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
      url: app.ServerUrl + '/remove_activity_student.php',
      method: 'POST',
      data: {
        week: parseInt(this.data.week),
        name: this.data.group_name,
        student_list: submit_student,
        openid:openid
      },
      success(res) {
        if (res.data.err == 5) {
          wx.showToast({ title: '活动不存在' })
        }
        else if (res.data.err == 44) {
          wx.showToast({ icon: 'none', title: '不具备删除权限' })
        }  
        else if (res.data.err != 0)
          wx.showToast({ icon: 'none', title: 'server error' })
        else {
          wx.showToast({
            title: '移除成功',
          })
          that.setData({ flow_student: [] })
        }
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
    var group_index = e.detail.value;
    var semester_name = this.data.group_list[0][group_index[0]];
    var semester_id = this.data.semester_name_to_id[semester_name];
    var group_name = this.data.group_list[1][group_index[1]];
    this.setData({
      groupIndex: group_index,
      semester_id,
      group_name
    })
      // request this group data
      var that = this;
      wx.request({
        url: app.ServerUrl + '/get_fixed_student.php',
        data: {student_group : group_name, semester : semester_id},
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
      url: app.ServerUrl + '/get_group_list.php?all=1',
      success(res){
        if (res.data.err != 0)
          wx.showToast({ icon: 'none', title: 'server error' })
        else{
          var semester_dic = {};
          var semester_name_to_id = {};
          var group_list_data = res.data.result.group_list;
          for (var i = 0; i < group_list_data.length; i++) {
            var semester_name = group_list_data[i][2];
            if (semester_dic[semester_name] == undefined) {
              semester_dic[semester_name] = [];
              semester_name_to_id[semester_name] = group_list_data[i][3];
            }
            var group_name = group_list_data[i][1];
            if (group_name == '流动')
              continue;
            semester_dic[semester_name].push(group_name);
          }
          var semester_list = [];
          for (var i in semester_dic) {
            semester_list.push(i);
          }
          var first_semester_group_list = semester_dic[semester_list[0]];
          that.setData({ group_list: [semester_list, first_semester_group_list], semester_dic, semester_name_to_id })
          app.group_list = res.data.result.group_list
        }
      },
      fail(res){
        wx.showToast({icon: 'none',title:'network error'})
      }
    }),

    console.log('onLoad')
    var that = this
    //调用应用实例的方法获取全局数据
    wx.getUserInfo(function (userInfo) {
      //更新数据
      that.setData({
        userInfo: userInfo
      })

    })
  },
  bindMultiPickerColumnChange: function (e) {
    var data = {
      group_list: this.data.group_list,
      groupIndex: this.data.groupIndex
    }
    data.groupIndex[e.detail.column] = e.detail.value;
    var semester_list = this.data.group_list[0];
    if (e.detail.column == 0) {
      var semester_name = semester_list[e.detail.value];
      var semester_group_list = this.data.semester_dic[semester_name];
      data.group_list = [semester_list, semester_group_list];
      data.groupIndex[1] = 0;
    }
    this.setData(data);
  },  
  addNew: function(){
    // nav to create page
   wx.switchTab({
     url: '/pages/create/index',
   })
  },


})
