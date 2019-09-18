// pages/create/group_change.js
const app = getApp()
Page({
  data: {
    student_name: '',
    group_list: [['1', '2'], ['1', '2']],
    groupIndex: [0, 0],
    semester_dic: [],
    semester_name_to_id: {},
    semester_id: 1,
    group_name: '未选择小组',
    openid:''
  },
  inputTyping: function (e) {
    this.setData({
      student_name: e.detail.value
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
  },
  set_up_picker: function(){
    var semester_dic = {};
    var semester_name_to_id = {};
    var group_list_data = app.group_list;
    for (var i = 0; i < group_list_data.length; i++) {
      var semester_name = group_list_data[i][2];
      if (semester_dic[semester_name] == undefined) {
        semester_dic[semester_name] = [];
        semester_name_to_id[semester_name] = group_list_data[i][3];
      }
      var group_name = group_list_data[i][1];
      semester_dic[semester_name].push(group_name);
    }
    var semester_list = [];
    for (var i in semester_dic) {
      semester_list.push(i);
    }
    var first_semester_group_list = semester_dic[semester_list[0]];
    this.setData({ group_list: [semester_list, first_semester_group_list], semester_dic, semester_name_to_id })    
  },
  onLoad: function (options) {
    if (app.group_list) {
      this.set_up_picker()      
      return
    }    
    var that = this;
    // request group name list
    wx.request({
      url: app.ServerUrl + '/get_group_list.php?all=1',
      success(res) {
        if (res.data.err != 0)
          wx.showToast({ icon: 'none', title: 'server error' })
        else {         
          app.group_list = res.data.result.group_list
          that.set_up_picker()
        }
      },
      fail(res) {
        wx.showToast({ icon: 'none', title: 'network error' })
      }
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
  add_wrapper: function (res) {
    if (app.globalData.nickname == null) {
      app.get_user_info_from_res(res);
    }
    else if (app.globalData.openid == 'invalid') {
      wx.showToast({ icon: 'none', title: '不具备添加权限' });
    }
    else {
      this.modify('add');
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
      this.modify('delete');
    }
  },    
  modify: function (action) {
    // client data check
    var error_msg = '';
    var openid = app.globalData.openid;
    if (this.data.student_name == '') {
      error_msg = '请填写姓名'
    }
    else if (this.data.group_name == '未选择小组') {
      error_msg = '请选择小组'
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
        group_name: that.data.group_name,
        openid :openid
      },
      success(res) {
        if (res.data.err == 3) {
          wx.showToast({ icon: 'none',title: that.data.student_name + '不存在' })
        }
        else if (res.data.err == 44) {
          wx.showToast({ icon: 'none', title: '无权限' })
        }  
        else if(res.data.err == 5){
          wx.showToast({ icon: 'none', title: that.data.student_name + '不属于' + that.data.group_name + '小组'})
        }
        else if (res.data.err != 0)
          wx.showToast({ icon: 'none', title: 'server error' })
        else {
          if (action == 'add'){
            wx.showToast({ title: '添加' + that.data.student_name + '到' + that.data.group_name + '小组' +'成功' })
          }
          else{ //delete
            wx.showToast({ title: '从' + that.data.group_name + '小组' + '删除' + that.data.student_name + ' 成功' })
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