// pages/result/visualization.js
const app = getApp()
Page({

  /**
   * 页面的初始数据
   */
  data: {
    bar_img_src: '',
    line_img_src: '',
    semester_name: '',
    semester_list: [1,2],
    semesterIndex: 0
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    if(app.semester_dic == undefined){
      var that = this;
      // request group name list
      wx.request({
        url: app.ServerUrl + '/get_group_list.php?all=1',
        success(res) {
          if (res.data.err != 0)
            wx.showToast({ icon: 'none', title: 'server error' })
          else {
              var group_list_data = res.data.result.group_list;
              app.set_semester_info(group_list_data);
              that.update_data(); 
          }
        }
      })             
    }
    else{
      this.update_data(); 
    }
  },

  bindPickerChange: function (e) {
    var newIndex = e.detail.value;
    var newName = this.data.semester_list[newIndex];
    var semester_id = app.semester_name_to_id[newName];
    this.setData({
      semesterIndex: newIndex,
      semester_name: newName,
      line_img_src: app.ServerUrl + '/plot.php?type=line&semester='+semester_id
    })
  },

  update_data: function(){
    var semester_list = [];
    for (var i in app.semester_dic) {
      semester_list.push(i);
    }
    this.setData({
      bar_img_src: app.ServerUrl + '/plot.php?type=bar',
      line_img_src: app.ServerUrl + '/plot.php?type=line',
      semester_name: semester_list[0],
      semester_list
    })    
  },
  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {

  },

  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function () {

  },

  /**
   * 生命周期函数--监听页面卸载
   */
  onUnload: function () {

  },

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function () {

  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function () {

  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function () {

  }
})