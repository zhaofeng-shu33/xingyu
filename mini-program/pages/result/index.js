// pages/result/index.js
const app = getApp()
Page({

  /**
   * Page initial data
   */
  data: {
    group_list: [['1', '2'],['1','2']],
    groupIndex: [0, 0],
    group_name: '未选择小组',
    group_data: [],
    semester_dic: [],
    week:'',
    semester_id: 1,
    semester_name_to_id: {}
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
      data: { week: that.data.week, student_group: that.data.group_name, semester: that.data.semester_id },
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
            group_data.push({ name: student_list[i][0], school: app.SchoolReverseMapping[student_list[i][1]]})
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
    var group_index = e.detail.value;
    var semester_name = this.data.group_list[0][group_index[0]];
    this.setData({
      groupIndex: group_index,
      group_name: this.data.group_list[1][group_index[1]],
      semester_id: this.data.semester_name_to_id[semester_name]
    })
  },    
  bindMultiPickerColumnChange: function(e){
    var data = {
      group_list: this.data.group_list,
      groupIndex: this.data.groupIndex
    }
    data.groupIndex[e.detail.column] = e.detail.value;
    var semester_list = this.data.group_list[0];
    if(e.detail.column == 0){
      var semester_name = semester_list[e.detail.value];
      var semester_group_list = this.data.semester_dic[semester_name];
      data.group_list = [semester_list, semester_group_list];
      data.groupIndex[1] = 0;
    }
    this.setData(data);
  },
  onLoad: function (options) {
    var that = this;
    // request group name list
    wx.request({
      url: app.ServerUrl + '/get_group_list.php?all=1',
      success(res) {
        if (res.data.err != 0)
          wx.showToast({ icon: 'none', title: 'server error' })
        else {
          var semester_dic = {};
          var semester_name_to_id = {};
          var group_list_data = res.data.result.group_list;
          for(var i=0; i<group_list_data.length; i++){
            var semester_name = group_list_data[i][2];
            if(semester_dic[semester_name] == undefined){
              semester_dic[semester_name] = [];
              semester_name_to_id[semester_name] = group_list_data[i][3];
            }
            var group_name = group_list_data[i][1];
            if (group_name == '流动')
              continue;
            semester_dic[semester_name].push(group_name);
          }
          var semester_list = [];
          for(var i in semester_dic){
            semester_list.push(i);
          }
          var first_semester_group_list = semester_dic[semester_list[0]];
          that.setData({ group_list: [semester_list, first_semester_group_list], semester_dic, semester_name_to_id })
        }
      },
      fail(res) {
        wx.showToast({ icon: 'none', title: 'network error' })
      }
    })
  },

})