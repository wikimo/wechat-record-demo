<?php
require_once "jssdk.php";
require_once "config.php";

$jssdk = new JSSDK($wechat_config['appId'], $wechat_config['appSecret']);
$signPackage = $jssdk->GetSignPackage();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <title>Wechat Record</title>
  <style type="text/css">
  body{
    font-size: 22px;
  }
  </style>
</head>
<body>
<span class="desc">start to record</span><br>
<button class="btn btn_primary" id="startRecord">startRecord</button><br>
<span class="desc">stop to record</span><br>
<button class="btn btn_primary" id="stopRecord">stopRecord</button><br>
<span class="desc">play the voice(there's a bug, you can't quit when playing voice)</span><br>
<button class="btn btn_primary" id="playVoice">playVoice</button><br>
<span class="desc">pause the voice</span><br>
<button class="btn btn_primary" id="pauseVoice">pauseVoice</button><br>
<span class="desc">stop the voice</span><br>
<button class="btn btn_primary" id="stopVoice">stopVoice</button><br>
<span class="desc">upload the record</span><br>
<button class="btn btn_primary" id="uploadVoice">uploadVoice</button><br>
</body>

<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script type="text/javascript" src="http://code.jquery.com/jquery-1.12.0.min.js"></script>
<script>
  wx.config({
    debug: true,
    appId: '<?php echo $signPackage["appId"];?>',
    timestamp: <?php echo $signPackage["timestamp"];?>,
    nonceStr: '<?php echo $signPackage["nonceStr"];?>',
    signature: '<?php echo $signPackage["signature"];?>',
    jsApiList: [
      'startRecord',
      'stopRecord',
      'onVoiceRecordEnd',
      'playVoice',
      'pauseVoice',
      'stopVoice',
      'onVoicePlayEnd',
      'uploadVoice',
      'downloadVoice'
    ]
  });

  wx.ready(function () {
    var voice = {
      localId: '',
      serverId: ''
    };

    // API
    // start to record
    document.querySelector('#startRecord').onclick = function () {
      wx.startRecord({
        cancel: function () {
          alert('user refuse to record');
        }
      });
    };

    // stop to record
    document.querySelector('#stopRecord').onclick = function () {
      wx.stopRecord({
        success: function (res) {
          voice.localId = res.localId;
        },
        fail: function (res) {
          alert(JSON.stringify(res));
        }
      });
    };

    // 4.4 record stop for 60s
    wx.onVoiceRecordEnd({
      complete: function (res) {
        voice.localId = res.localId;
        alert('record has spend 60s');
      }
    });

    // 4.5 play voice
    document.querySelector('#playVoice').onclick = function () {
      if (voice.localId == '') {
        alert('use startRecord first');
        return;
      }
      wx.playVoice({
        localId: voice.localId
      });
    };

    // 4.6 pause the voice
    document.querySelector('#pauseVoice').onclick = function () {
      wx.pauseVoice({
        localId: voice.localId
      });
    };

    // 4.7 stop the voice
    document.querySelector('#stopVoice').onclick = function () {
      wx.stopVoice({
        localId: voice.localId
      });
    };

    // 4.8 voice over
    wx.onVoicePlayEnd({
      complete: function (res) {
        alert('record（' + res.localId + '）play over');
      }
    });

    // 4.8 upload
    document.querySelector('#uploadVoice').onclick = function () {
      if (voice.localId == '') {
        alert('use startRecord first');
        return;
      }
      wx.uploadVoice({
        localId: voice.localId,
        success: function (res) {
          alert('upload，serverId: ' + res.serverId);
          voice.serverId = res.serverId;
          $.post('server.php', { serverId: res.serverId }, function(data) {
            alert(data);
          });
        }
      });
    };
  });
</script>
</html>