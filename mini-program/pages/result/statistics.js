// pages/result/statistics.js
const app = getApp()

Page({
  data: {
     statistics: [],
     current_show: '学校',
     institution_list: [],
     school_list: []
  },

  change_current_show: function(event) {
    // 学校 <-> 机构
    if(this.data.current_show == '学校')
      this.setData({current_show: '机构', statistics: this.data.institution_list})
    else
      this.setData({ current_show: '学校', statistics: this.data.school_list})
  },

  get_excel: function(event){
  	var school_chinese_name = event.currentTarget.dataset.school;
    var school_name;
    if (this.data.current_show = '机构')
      school_name = school_chinese_name;
    else{
      school_name = app.SchoolMapping[school_chinese_name];
    }

	  wx.downloadFile({
	  	  url: app.ServerUrl + '/download_summary.php?student_school=' + school_name,
		  success(res){
		  	  if(res.statusCode == 200){
				const filePath = res.tempFilePath;
				wx.openDocument({
					filePath,
					fileType: 'xlsx',
				success(res) {					
				},
				fail(res) {
					wx.showToast({
					title: 'error',
					icon: 'none'
				  })
				},

				})
			  }
		  },
		  fail(res) {
				wx.showToast({
				title: res.errMsg + school_name,
				icon: 'none'
				})
		  },
	  })
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
          for(var i = 0; i < statistic_list.length; i++) {
            statistic_list[i]['school'] = app.SchoolReverseMapping[statistic_list[i]['school']];
            total_student += parseInt(statistic_list[i]['total_student']); 
            total_cnt += parseInt(statistic_list[i]['total_count']);      
          }
          if (total_cnt > 0){
            for (var i = 0; i < statistic_list.length; i++) {
              var stu_num_percent = parseInt(statistic_list[i]['total_student'])/total_student;
              statistic_list[i]['total_student'] += '(' + Math.round(stu_num_percent * 100).toString() + '%)'
              var stu_cnt_percent = parseInt(statistic_list[i]['total_count']) / total_cnt;
              statistic_list[i]['total_count'] += '(' + Math.round(stu_cnt_percent * 100).toString() + '%)'
            }
          }
          var institution_list_received = res.data.orgs;
          var institution_list = new Array();
          for (var i = 0; i < institution_list_received.length; i++){
            var institution = institution_list_received[i];
            var institution_student_int = parseInt(institution['total_student']);
            var institution_student = institution['total_student'];
            var institution_student_cnt = parseInt(institution['total_count']);
            var institution_cnt = institution['total_count'];
            var institution_name = institution['name'];
            if (total_cnt > 0) {
              institution_student += '(' + Math.round(institution_student_int / total_student * 100).toString() + '%)'
              institution_cnt += '(' + Math.round(institution_student_cnt / total_cnt * 100).toString() + '%)'
            }
            institution_list.push({ 'school': institution_name, 'total_student': institution_student, 'total_count': institution_cnt })
          }
          statistic_list.push({ 'school': '总计', 'total_student': total_student, 'total_count': total_cnt });                   
          institution_list.push({ 'school': '总计', 'total_student': total_student, 'total_count': total_cnt });
          that.setData({ statistics: statistic_list, institution_list, school_list: statistic_list })
        }
      },
      fail(res) {
        wx.showToast({ icon: 'none', title: 'network error' })
      }
    })
  }
})