//app.js
let config = require('./config');
App({
  ServerUrl: 'https://www.leidenschaft.cn/xingyu',
  SchoolMapping: { '深大':'szu', '南科大':'sust', '哈工大':'hit', '北大':'pku', '清华':'thu'},
  SchoolReverseMapping: { 'szu':'深大', 'sust':'南科大', 'hit':'哈工大', 'pku':'北大', 'thu':'清华' },
  get_user_info_from_res: function(user_info_auth_result){
    var userInfoData = user_info_auth_result.detail.userInfo;
    if (userInfoData) {
      this.associate_nickname_with_openid_on_server(userInfoData.nickName);
      return;
    }
    wx.showToast({ title: '未授权无法操作' })
  },
  //! associate user nickname with openid on the server
  associate_nickname_with_openid_on_server: function(nickname){
    if(this.globalData.nickname != null){
      return;
    }
    if (this.globalData.openid == null){
      this.get_openid_of_anonymous_user();
    }
    var that = this;
    wx.request({
      url: config.service.host + "/openid.php",
      method: 'POST',
      data: {
        nickname: nickname,
        openid: that.globalData.openid,
      },
      header: {
        "Content-Type": "application/json"
      },
      success: function (res) {
        console.log(res);
        if(res.data.err == 0){
          that.globalData.nickname = nickname; 
          wx.showToast({ title: '授权成功，请再次点击完成操作' })         
        }
        else if(res.data.err == 5){
          that.globalData.openid = 'invalid';
          wx.showToast({ title: '非小组长无法授权该操作' })         
        }
        else{
          wx.showToast({ title: 'server error' });
        }
      },
      fail(error) {
        wx.showToast({ title: 'network error' })
      }
    })
  },
  get_openid_of_anonymous_user: function(){
    wx.login({
      success: res => {
        // 发送 res.code 到后台换取 openId, sessionKey, unionId
        console.log('res', res);

        wx.request({
          url: config.service.host + "/openid.php?code=" + res.code,
          header: {
            "Content-Type": "application/x-www-form-urlencoded"
          },
          method: 'GET',
          success: res => {
            if (res.data.err == 0){
              this.globalData.openid = res.data.result.openid;
            }
            else{
              wx.showToast({ title: 'server error' })
            }
          },
          fail: function () {
            wx.showToast({ title: 'network error' })
          }
        });
      },
      fail: res => {
        wx.showToast({ title: '微信服务器错误' })
      }
    })
  },
  //! set the initial openid of anonymous user
  onLaunch: function () {
    if(this.globalData.openid != null){
      return;
    }
    this.get_openid_of_anonymous_user();      
  },
  globalData: {
    nickname: null,
    openid: null
  }

})