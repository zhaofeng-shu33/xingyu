//app.js
let config = require('./config');
App({
  ServerUrl: 'https://www.leidenschaft.cn/xingyu',
  SchoolMapping: { '深大':'szu', '南科大':'sust', '哈工大':'hit', '北大':'pku', '清华':'thu'},
  SchoolReverseMapping: { 'szu':'深大', 'sust':'南科大', 'hit':'哈工大', 'pku':'北大', 'thu':'清华' },


  onLaunch: function () {
    wx.login({
      success: res => {
        // 发送 res.code 到后台换取 openId, sessionKey, unionId
      }
    })


    wx.login({
      success: res => {
        // 发送 res.code 到后台换取 openId, sessionKey, unionId
        console.log('res', res);

        wx.getSetting({
          success: res => {
            if (res.authSetting['scope.userInfo']) {
              // 已经授权，可以直接调用 getUserInfo 获取头像昵称，不会弹框
              wx.getUserInfo({
                success: res => {
                  // 可以将 res 发送给后台解码出 unionId
                  this.globalData.userInfo = res.userInfo
                  // 由于 getUserInfo 是网络请求，可能会在 Page.onLoad 之后才返回
                  // 所以此处加入 callback 以防止这种情况
                  if (this.userInfoReadyCallback) {
                    this.userInfoReadyCallback(res)
                  }
                }
              })
            }
          }
        })
        wx.request({
          url: config.service.host + "/openid.php?code=" + res.code,
          header: {
            "Content-Type": "application/x-www-form-urlencoded"
          },
          method: 'GET',
          success: action => {
            // 返回成功后action.openId可以获取到这个用户的openId
            // 如果openId为空，表示后台不能获取到openId，前端应以匿名调查的方式进行处理
            console.log('act', action);//openid = .data.result.openid
            this.globalData.action = action.data;
            this.globalData.openid = action.data.result.openid;
            console.log('openid', action.data.result.openid)

            wx.getUserInfo({
              success: res => {
                // 可以将 res 发送给后台解码出 unionId
                console.log('user', res)
                this.globalData.userInfo = res.userInfo
                // 由于 getUserInfo 是网络请求，可能会在 Page.onLoad 之后才返回
                // 所以此处加入 callback 以防止这种情况
                if (this.userInfoReadyCallback) {
                  this.userInfoReadyCallback(res)
                }

                console.log('usrname', res.userInfo.nickName)
                wx.request({

                  url: config.service.host + "/openid.php",
                  method: 'POST',

                  data: {

                    nickname: res.userInfo.nickName,
                    openid: action.data.result.openid,

                  },
                  header: {
                    'Accept': 'application/json; charset=utf8mb4_bin'
                    //"Content-Type": "application/x-www-form-urlencoded"
                  },

                  //login: false,
                  success: function (res) {
                    //that.setData({
                    //requestResult: JSON.stringify(result.data)
                    //});

                    console.log("success", res.data)
                  },
                  fail(error) {
                    console.log('request fail', error);
                  }
                })
              },
              fail:res=>{
                this.globalData.openid = 'invalid'

              }

            })



          }
        });



      
      },
      fail: res => {
        this.globalData.openid = 'invalid'

      }

      

    })
      
  },



  globalData: {
    userInfo: null,
    openid: null
  }



})