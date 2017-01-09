<!DOCTYPE html>
<html>
<head>
<title>粉絲團貼文按讚分析工具</title>
<meta charset="UTF-8">
</head>
<body>
<script>
  function statusChangeCallback(response) {
    console.log('statusChangeCallback');
    console.log(response);
    
    if (response.status === 'connected') {
      testAPI();
    } else if (response.status === 'not_authorized') {
      document.getElementById('status').innerHTML = 'Please log ' +
        'into this app.';
    } else {
    
      document.getElementById('status').innerHTML = 'Please log ' +
        'into Facebook.';
    }
  }

  window.fbAsyncInit = function() {
  FB.init({
    appId      : '1147891325329747',
    cookie     : true,  
    xfbml      : true,  
    version    : 'v2.8' 
  });


  FB.getLoginStatus(function(response) {
    statusChangeCallback(response);
  });

  };

  (function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));
  
  //以上為facebook api的初始化和login的function設定

  //顯示粉絲團的PO文
  function testAPI() {
    console.log('Welcome!  Fetching your information.... ');
    FB.api('/441374949280067/posts', function(response) {

      for(var key in response.data){
      document.getElementById('status').innerHTML +=
          response.data[key].message + '<br>'+ response.data[key].id +'<br><br>';
      }
    });
  }
</script>



<fb:login-button scope="public_profile,email" onlogin="checkLoginState();">
</fb:login-button>

<div id="status">
</div>

</body>
</html>