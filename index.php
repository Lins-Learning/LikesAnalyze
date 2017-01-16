<!DOCTYPE html>
<html>
<head>
<title>粉絲團貼文按讚分析工具</title>
<meta charset="UTF-8">
<!-- 最新編譯和最佳化的 CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">

<!-- 選擇性佈景主題 -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap-theme.min.css">

<!-- 最新編譯和最佳化的 JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
</head>
<body>
  
<script>
  function statusChangeCallback(response) {
    console.log('statusChangeCallback');
    console.log(response);
    
    if (response.status === 'connected') {
      console.log("成功登入");
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
  
  var Data = new Array(100);
  var part = new Array(100);
  for(var i=0;i<100;i++){
    Data[i] = new Array(6);//分別為該篇貼文的文字、圖片、影片、連結、TAG、按讚數
  }
  
  //處理並顯示粉絲團的PO文資料
  function showPosts() {
    document.getElementById('status').innerHTML = "";
    var result = document.getElementById('YOLO').value;
    console.log(result);
    
    FB.api(result+'/posts?fields=type,picture,message,message_tags,reactions.limit(0).summary(1)&limit=100', function(response) {

      for(var key in response.data){//一筆一筆顯示
          //處理資料到Data        
          if(response.data[key].message != undefined) Data[key][0] = 1;
          else Data[key][0] = 0;
          if(response.data[key].type == 'photo') Data[key][1] = 1;
          else Data[key][1] = 0;
          if(response.data[key].type == 'video') Data[key][2] = 1;
          else Data[key][2] = 0;
          if(response.data[key].type == 'link') Data[key][3] = 1;
          else Data[key][3] = 0;
          if(response.data[key].message_tags != undefined) Data[key][4] = 1;
          else Data[key][4] = 0;
          Data[key][5] = response.data[key].reactions.summary.total_count ;

      
          document.getElementById('status').innerHTML +=response.data[key].message+'<br>';
          document.getElementById('status').innerHTML +='有無文字:'+Data[key][0]+'<br>';
          document.getElementById('status').innerHTML +='有無圖片:'+Data[key][1]+'<br>';
          document.getElementById('status').innerHTML +='有無影片:'+Data[key][2]+'<br>';
          document.getElementById('status').innerHTML +='有無連結:'+Data[key][3]+'<br>';
          document.getElementById('status').innerHTML +='有無TAG:'+Data[key][4]+'<br>';
          document.getElementById('status').innerHTML +='按讚數:'+Data[key][5]+'<br>'+'<br>'+'================================================================================================================='+'<br>';
      }
      count_analyze();
    });
  }
  
  function count_analyze(){//人數資料分析
      var count = new Array(100);
      for(var i=0;i<100;i++)
        count[i] = Data[i][5];
     
      count.sort(function(a, b) {return a - b;});
      
      var c1 =count[75],c2 =count[50],c3 =count[25];
      console.log(c1+" "+c2+" "+c3);
      
      for(var i=0;i<100;i++){
            if(Data[i][5]>=c1) Data[i][5] = 4;
            else if(Data[i][5]>=c2) Data[i][5] = 3;
            else if(Data[i][5]>=c3) Data[i][5] = 2;
            else Data[i][5] = 1;
      }
      for(var i=0;i<100;i++){
           console.log(Data[i][5]);
       }
  }
  
  function distence(a,b){//算距離
      var sum=0;
      for(var i=0;i<6;i++){
        var delta = (a[i]-b[i])*(a[i]-b[i]);
        sum+=delta;
      }
      sum=Math.sqrt(sum);
      return sum;
  }
  function PAM(a,b,c,d){
    
    for(var i=0;i<100;i++){//分群
      var tmp = distence(Data[i],a);
        part[i] = 1;
      if(distence(Data[i],b) < tmp) {
        tmp = distence(Data[i],b);
        part[i] = 2;
      }
      if(distence(Data[i],c) < tmp) {
        tmp = distence(Data[i],c);
        part[i] = 3;
      }
      if(distence(Data[i],d) < tmp) {
        tmp = distence(Data[i],d);
        part[i] = 4;
      }
    }
    
    var new_a,min=1000000;
    for(var i=0;i<100;i++){//換中心
        if(part[i] != 1) continue;
        var sum=0;
        for(var j =0;j<100;j++){
          if(part[j] == 1){
            sum+=distence(Data[j],Data[i]);
          }
        }
        if(sum < min) {min = sum;new_a = Data[i]}
    }
     var new_b;min=1000000;
    for(var i=0;i<100;i++){//換中心
        if(part[i] != 2) continue;
        var sum=0;
        for(var j =0;j<100;j++){
          if(part[j] == 2){
            sum+=distence(Data[j],Data[i]);
          }
        }
        if(sum < min) {min = sum;new_b = Data[i]}
    }
     var new_c;min=1000000;
    for(var i=0;i<100;i++){//換中心
        if(part[i] != 3) continue;
        var sum=0;
        for(var j =0;j<100;j++){
          if(part[j] == 3){
            sum+=distence(Data[j],Data[i]);
          }
        }
        if(sum < min) {min = sum;new_c = Data[i]}
    }
     var new_d;min=1000000;
    for(var i=0;i<100;i++){//換中心
        if(part[i] != 4) continue;
        var sum=0;
        for(var j =0;j<100;j++){
          if(part[j] == 4){
            sum+=distence(Data[j],Data[i]);
          }
        }
        if(sum < min) {min = sum;new_d = Data[i]}
    }
    
    
    var re = false;
    
    if(new_a != a) re = true;
    if(new_b != b) re = true;
    if(new_c != c) re = true;
    if(new_d != d) re = true;
    
    if(re) PAM(new_a,new_b,new_c,new_d);
    
  }
    
  
  function analyze(){
    
    var a = parseInt(100*Math.random()+1);
    var b = parseInt(100*Math.random()+1);
    var c = parseInt(100*Math.random()+1);
    var d = parseInt(100*Math.random()+1);
    
    
    PAM(Data[a],Data[b],Data[c],Data[d]);
    document.getElementById('status').innerHTML ="分析結果：<br> 文字、圖片、影片、連結、TAG資料：0代表沒有該屬性，1代表有，按讚數資料：分為四個百分比區間，4為最多(前25%)，1最少(75%之後)";
    for(var i=1;i<5;i++){
      document.getElementById('status').innerHTML +="<br>第"+i+"分區：<br>文字\t圖片\t影片\t連結\tTAG\t按讚數<br>";
      for(var key=0;key<100;key++)
        if(part[key] == i)
          document.getElementById('status').innerHTML += Data[key][0]+'  '+Data[key][1]+' '+Data[key][2]+'  '+Data[key][3]+'  '+Data[key][4]+'  '+Data[key][5]+'<br>';
      document.getElementById('status').innerHTML +="<br>";
    }
  }
  
  
  
  

  
  
</script>



<fb:login-button scope="public_profile,email" onlogin="checkLoginState();">
</fb:login-button>

<br><br>

<div class="input-group">
  <span class="input-group-addon" >輸入粉絲團編號</span>
  <input type="text" class="form-control" id='YOLO'  value="1021581417961885" >
</div>

  <button type="button" onclick="showPosts()" class="btn btn-primary">顯示貼文</button>
  <button type="button" onclick="analyze()" class="btn btn-primary">分析</button>
<div id="status">
</div>

</body>
</html>