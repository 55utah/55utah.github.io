1.写在前面
------
根据最近的学习，写了一个demo,
可以通过navigator.mediaDevices.getUserMedia()方法调用电脑摄像头，并实现了录制音频，录制视频，对摄像头的内容进行截图下载；通过AudioContext使用web audio api处理音频信息，实现滤波以及简单变声处理。录制使用的api是MediaRecorder。整个demo没有使用其他的库，全部是原生api. 本项目代码我会上传。

2.实现过程
------
**(1)调用摄像头**
pc端大部分浏览器支持getUserMedia()方法，安卓端除了chrome for android支持的很少；同时，要注意，在本地调用这个方法被禁止，只能在在线网页使用，我的测试使用了nodejs方法，写一个简单的后台，构建一个http服务器，localhost方式访问。使用navigator.mediaDevices.getUserMedia()获取视频，返回值是一个promise类型；调用代码如下：
```
var video = document.querySelector('video');
var option = {audio:false,video:{width:640,height:480}};
var media = navigator.mediaDevices.getUserMedia(option);
  media.then(show).catch((error)=>{console.log(error)});
  function show(mediaStream){
  	video.src = window.URL.createObjectURL(mediaStream);
  	//将meida流转为url
  	video.onloadedmetadata = function(e) {
  		video.play();
  };
```
这段代码是调用核心，会请求用户是否打开摄像头。方法参数是设置，具体参见MDN网站接口指导。其中，URL.createObjectURL(blob)这个方法会把blob格式数据转为url，然后通过提供给audio,video等标签就可以显示，这个方法在我们的应用中很常用。
**(2)视频特效**
实现了简单的特效，grayscale/sepia/blur 分别是灰色画面/黄色画面特效/模糊效果等等，核心是使用css的filter去完成，`<filter属性不止可以使用在图片上，也可以用在video标签上！>` filter特效有很多.参考MDN.

```
css:
video{
      background: rgba(255,255,255,0.5);
    }
    .grayscale{
      filter: grayscale(1);
    }
    .sepia{
      filter:sepia(1);
    }
    .blur{
      filter: blur(3px);
    }
//js:
video.onclick = ()=>{
      count++;
      switch(count){
        case 1:video.className = 'grayscale';break;
        case 2:video.className = 'sepia';break;
        case 3:video.className = 'blur';break;
        case 4:video.className = '';count=0;break;
      }
    }
```
然后设定每次点击让video具有上面其中一个className，就可以具有不同特效了，很简单。同时要明白这只是css特效，视频本身并没有改变。截图等得到的画面不会有这些css特效，如果需要视频本身就有特效，可以被截图捕捉，可以使用canvas播放视频并处理：（我的简单尝试）
```
  // 	var canvas = document.querySelector('canvas');
  // 	var ctx = canvas.getContext('2d');
  // 	setInterval(function(){
  // 		ctx.drawImage(video,0,0,640,480);
  // 		var imgdata = ctx.getImageData(0,0,640,480);
  // 		//反相处理
  // 		for(var i = 0; i < imgdata.height; ++i){
  //     for(var j = 0; j < imgdata.width; ++j){
  //       var x = i*4*imgdata.width + 4*j, 
  //        //每个像素需要占用4位数据，分别是r,g,b,alpha透明通道
  //       r = imgdata.data[x],
  //       g = imgdata.data[x+1],
  //       b = imgdata.data[x+2];
  //       imgdata.data[x+3] = 150; 
  //       //图片反相：
  //       imgdata.data[x] = 255-r;
  //       imgdata.data[x+1] = 255-g;
  //       imgdata.data[x+2] = 255-b; 
  //     }
  //   }
  // 		ctx.putImageData(imgdata,0,0);
  // 	},100);
  // }
  //以上代码实现了反转效果，视频变成负片样式，显示在canvas上，但是canvas显示视频自然性能比<video>差很多。
```

**(3)截图+下载**
截图的思路是构建一个canvas标签，使用drawImage()把视频一帧绘制在canvas上，再使用canvas.toBlob()方法得到blob数据，这时就可以显示在页面或者保存了(这里是用了URL.createObjectURL(blob))，下载的思路是构建一个a标签，href赋值上面的url，download设置名称，然后a.click()就ok了。`类似于<a href="xxx" download="aaa">点我下载</a>`
```
function shoot(){
      var canvas = document.createElement('canvas');
      canvas.width = 640;
      canvas.height = 480;
      var ctx = canvas.getContext('2d');
      ctx.drawImage(video,0,0);
      canvas.toBlob((myblob)=>{
        download(myblob);
      });
  }
  function download(blob){
     var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = Math.random().toString(36).substr(2,14);
        a.click();
  }
```
Math.random().toString(36).substr(2,14) 这句代码可以得到14位随机字符串，这样就可以让文件名是一个随机字符串而不会重复。
**(4)使用web audio api---AudioContext （音频可视化+滤波+变声）**
这个api很强大，使用也是相当复杂，方法极多。建议先去查一下详细介绍，这里我只谈关键点。
![这里写图片描述](http://img.blog.csdn.net/20171105221600080?watermark/2/text/aHR0cDovL2Jsb2cuY3Nkbi5uZXQvTWFjaV95ZXJh/font/5a6L5L2T/fontsize/400/fill/I0JBQkFCMA==/dissolve/70/gravity/SouthEast)
这里是音频可视化部分，可以实时随着音调变化数字大小以及黄色条带的长度，实现一个简单的音频可视化，如果需要做华丽的可视化也就不难了。
```
html:
<p id="volume"></p>
<div class="box"></div>
<br>
高通滤波:<input type="range" min='20' max='15000' step='40' value='20' id="high">
<p id='level'>0</p>
<p>只有高于此频率的才能通过，滤掉低频率内容</p>
js:
  var audio = document.querySelector('audio'); 
   var audioctx = new (window.AudioContext || window.webkitAudioContext);
   var filter_high = audioctx.createBiquadFilter();
   var analyser = audioctx.createAnalyser();
   var dataArray = new Uint8Array(analyser.frequencyBinCount);
   var media1 = navigator.mediaDevices.getUserMedia({audio:true});
   media1.then(getdata).catch((error)=>{alert(error)});
   function getdata(mediaStream){
    var mic = audioctx.createMediaStreamSource(mediaStream);
    audio.src = window.URL.createObjectURL(mediaStream);
    mic.connect(analyser);
    filter_high.type = 'highpass';
    filter_high.frequency.value = 20;
    analyser.connect(filter_high);
    //filter_high.connect(audioctx.destination);
    //analyser.connect(audioctx.destination);
   }
   elVolume = document.getElementById('volume');
   var box = document.querySelector('.box');
  function draw(){
    var x = Math.floor(getByteFrequencyDataAverage());
    elVolume.innerHTML = x;
    box.style.width = 2+x*3+'px';
  };
  setInterval(draw,100);

   function getByteFrequencyDataAverage(){
    analyser.getByteFrequencyData(dataArray);
    return dataArray.reduce(function(previous, current) {
        return previous + current;
    }) / analyser.frequencyBinCount;
    };
    //以下是简单的高频滤波部分
    var high = document.querySelector('#high');
    high.onchange = function(){
      filter_high.frequency.value = high.value;
      document.querySelector('#level').innerText = high.value;
    }
```
首先创建AudioContext，创建图形化需要的Analyser,滤波需要的BiquadFilter; 通过audioctx.createMediaStreamSource(mediaStream) 把麦克风的声音处理一下，connect连接到analyser,
 filter_high.type = 'highpass'; filter_high.frequency.value = 20;设定滤波器是高通滤波器，界限为20HZ，高于20HZ才能输出。
 var dataArray = new Uint8Array(analyser.frequencyBinCount);这句是创建一个无符号数组，用于analyser.getByteFrequencyData(dataArray);方法获取analyser得到的音调信息，并存在dataArray里面，通过求一个实际段内音调的均值得到此瞬间的音调是多少，然后draw()运行，改变
 `<p id="volume"></p>
<div class="box"></div>` 
的表现，实现音频可视化，具体参见MDN；
那么变声怎么做呢，使用ScriptProcessor这个node，就可以对音频内容做处理然后再输出，也就能让我们去做变声处理了。
![这里写图片描述](http://img.blog.csdn.net/20171105223234105?watermark/2/text/aHR0cDovL2Jsb2cuY3Nkbi5uZXQvTWFjaV95ZXJh/font/5a6L5L2T/fontsize/400/fill/I0JBQkFCMA==/dissolve/70/gravity/SouthEast)

```
	  //通过ScriptProcessorNode处理音频
      //变声音  播放速度减小 声音变粗 速度增大 声音变细
      //反应在代码就是采样点减少。。
      var style = document.querySelector('#audio_style');
      style.onchange = function(){
        audio_style = parseInt(style.value);
      }
      var audio_style = 0;
  var scriptNode = audioctx.createScriptProcessor(4096, 1, 1);
      scriptNode.onaudioprocess = function(audioProcessingEvent) {
      var inputBuffer = audioProcessingEvent.inputBuffer;
      var outputBuffer = audioProcessingEvent.outputBuffer;
     for (var channel = 0; channel < outputBuffer.numberOfChannels; channel++) {
        var inputData = inputBuffer.getChannelData(channel);
        var outputData = outputBuffer.getChannelData(channel);
        // Loop through the 4096 samples
        for (var sample = 0; sample < inputBuffer.length; sample++) {
          // outputData[sample] = inputData[sample]*1; 
          //这里做处理即可 
          switch(audio_style){
            case 0:outputData[sample] = inputData[sample];break;
            case 1:if(sample%2==0){
                       outputData[sample] = inputData[sample/2];}
                   else{
                       outputData[sample] = inputData[(sample-1)/2];
                    };break;
          }
        }
      }
    }
    filter_high.connect(scriptNode);
    scriptNode.connect(audioctx.destination);
```
这里的style就是html上的一个select，我设定了两个value，一个正常一个是声音加粗变声特效，实现方式就是
`if(sample%2==0){outputData[sample]=inputData[sample/2];}
else{outputData[sample] = inputData[(sample-1)/2];`
只把每个采样区间的前一半数据输出，这样就可以基本实现采样速度减小一半，播放起来的效果是就是声音很粗，很低沉。我只简单做了一个声音变粗的处理，还没有想出复杂的变声技巧，但是肯定是在上面这个地方做处理的，按照一定的算法修改输出buffer数据，知道了核心处理方法剩下的就是算法层面了。
```
filter_high.connect(scriptNode);
    scriptNode.connect(audioctx.destination);
```
这两句代码意思是把之前那个滤波器连接到处理模块再把处理模块连接到输出设备上。我们就可以听到处理后的声音了。
(5)录制视频+录制音频
录制视频与音频的核心是MediaRecorder这样一个api，可以把stream流转为blob，然后根据我们的设定保存，使用的方法有start,stop等方法，ondataavailable等事件。先上代码：
![这里写图片描述](http://img.blog.csdn.net/20171105224423560?watermark/2/text/aHR0cDovL2Jsb2cuY3Nkbi5uZXQvTWFjaV95ZXJh/font/5a6L5L2T/fontsize/400/fill/I0JBQkFCMA==/dissolve/70/gravity/SouthEast)
录制音频：
```
<button id="record_audio">录音频</button>
<button id="stop_audio">stop</button>
<audio controls="controls" id="record_play"></audio>
js:
 var record_audio = document.querySelector('#record_audio');
    var stop_audio = document.querySelector('#stop_audio');
    var record_play = document.querySelector('#record_play');
    var audio_stream;
     var media1 = navigator.mediaDevices.getUserMedia({audio:true});
    media1.then((stream)=>{
      audio_stream = stream;
    }).catch((error)=>{alert(error)});
    record_audio.onclick = function(){
         record_audio.disabled = 'true';
         var mediaRecorder = new MediaRecorder(audio_stream);
         mediaRecorder.ondataavailable = function(e) {
           // blob_audio = new Blob([e.data], {type:e.data.type});
           blob_audio = new Blob([e.data], {type:'audio/mp3'});
           //可以存为mp3格式但是本地无法播放，只有网页可以播放
        }
         mediaRecorder.start();
        mediaRecorder.onstop = function(){
           record_play.src = URL.createObjectURL(blob_audio);
           record_play.play();
           //download(blob_audio);
        }
        stop_audio.onclick = function(){
          record_audio.disabled = '';
          mediaRecorder.stop();
        }
      }
```
录制过程就是new MediaRecorder(audio_stream)创建一个mediaRecorder , ondataavailable方法下，将e.data数据转为blob格式：new Blob([e.data], {type:'audio/mp3'}); 其实这里可以使用原本格式，e,data.type,看了一下返回的是video/webm, 很迷，改成mp3之后，在网页端依旧可以播放，但是保存到本地就无法播放（未解决），然后的流程和上面知识点差不多。
录制视频：视频录制与音频录制差别不大，

```
<button id="record">录视频</button>
<button id="stop">stop</button>
js:
  var record = document.querySelector('#record');
  var stop_record = document.querySelector('#stop');
  var video2 = document.querySelector('#video2');
  var video_stream;
  //var media = navigator.mediaDevices.getUserMedia(option);
  //这个media就是上面那个
      media.then((stream)=>{
        video_stream = stream;
      }).catch((error)=>{alert(error)});
      record.onclick = function(){
         record.disabled = 'true';
         var mediaRecorder = new MediaRecorder(video_stream);
         mediaRecorder.ondataavailable = function(e) {
            myblob = new Blob([e.data], {type:e.data.type});
        }
         mediaRecorder.start();
        mediaRecorder.onstop = function(){
          //download(myblob);
          //播放器不能播放webm格式视频，只能网页播放
          //可能需要使用库转码
           video2.src = URL.createObjectURL(myblob);
           video2.play();
        }
        stop_record.onclick = function(){
          record.disabled = '';
          mediaRecorder.stop();
        }
      }
```
这里的视频就是video/webm格式，还不能转为其他格式，否则无法播放，webm格式我的本地播放器不支持，所以就没有下载，直接在网页端播放了，网上查到貌似不能音频视频一起保存；如果需要其他格式的视频，可以使用库转码，或者参考网上js转码过程。

3.总结
----
本次demo我研究了一两天，也迈过了很多坑，找到了合适的实现方式，目前pc端支持情况还不错，之前找这方面的东西，找了很久都没有找到很全面的，所以我自己实现了一遍，虽然很多地方还没有完美解决，但是核心的问题都一一解决了。继续努力，希望帮到大家。