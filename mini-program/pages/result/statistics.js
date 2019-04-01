// pages/result/statistics.js
const app = getApp()

Page({
  data: {
     statistics: []
  },
  onLoad: function (options) {
    var that = this;
    // request group name list
    wx.request({
      url: app.ServerUrl + '/get_statistics.php',
      success(res) {
        if (res.data.err != 0)
          wx.showToast({ icon: 'none', title: 'server error' })
        else {
          var statistic_list = res.data.result;
          var total_student = 0;
          var total_cnt = 0;
          for (var i = 0; i < statistic_list.length; i++) {
            statistic_list[i]['school'] = app.SchoolReverseMapping[statistic_list[i]['school']];
            total_student += parseInt(statistic_list[i]['total_student']); 
            total_cnt += parseInt(statistic_list[i]['total_count']);      
          }
          for (var i = 0; i < statistic_list.length; i++) {
            var stu_num_percent = parseInt(statistic_list[i]['total_student'])/total_student;
            statistic_list[i]['total_student'] += '(' + Math.round(stu_num_percent * 100).toString() + '%)'
            var stu_cnt_percent = parseInt(statistic_list[i]['total_count']) / total_cnt;
            statistic_list[i]['total_count'] += '(' + Math.round(stu_cnt_percent * 100).toString() + '%)'
          }

          statistic_list.push({'school':'总计', 'total_student': total_student, 'total_count': total_cnt})
          that.setData({ statistics: statistic_list })
        }
      },
      fail(res) {
        wx.showToast({ icon: 'none', title: 'network error' })
      }
    })
  }
})