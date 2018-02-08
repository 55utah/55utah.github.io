1.介绍
做了一个关于文件上传和下载的demo ，选择了Multer 作为中间件进行数据处理。关于multer请参考中文翻译文档 https://github.com/expressjs/multer/blob/master/doc/README-zh-cn.md  或者官方文档
2. upload 文件上传
html form标签内设置enctype="multipart/form-data"是必须的，这样才可以上传文件，方式为post ，在服务端使用multer时，基本与官网相同，引用中间件multer，app.post('\upload',upload.single('name'),function(){}); 指定单文件上传，参数是html的input里面的name ,这样就可以上传成功，但是问题是multer不会管你的后缀，上传到upload文件夹的文件有一个乱序名称但是没有后缀，所以我增加了这个功能。
req.file是文件信息，取得原名称，经过数组以及堆栈等处理得到后缀'.jpg' ， 用fs.renameSync（）方法重命名文件，加上本来的后缀。这样真正的上传成功。唯一的缺点是没有实现改变文件名字，使用官网方式出现了错误，所以没有深究。。
3.download 文件下载
网上基本上是express对res.download的封装以及fs方法为主，注意要把文件夹设定为静态文件。出现的问题是点击之后就会在新页面显示要下载的文件，无论是图片还是音乐。。这让我很困扰，但是找了很久也没有很好的解决。用IE访问结果是会出现下载还是查看的选项。。很迷。总之就是这样了，一晚上的努力，我很满足。。嘻嘻

//服务端
//index.js
```nodejs
var fs = require('fs')
var express = require('express')
var multer = require('multer')
const path = require('path');

 var app = express();
 var upload = multer({dest:'upload/'});

//多文件上传 （限定上传文件个数）（没有修改后缀）
app.post('/upload-multi',upload.array('myfile',2),function(req,res,next){
	res.send("2 done");
})
//单文件上传获取信息
app.post('/upload-single',upload.single('myfile'),function(req,res,next){
	var file=req.file;
	// console.log("名称：%s",file.originalname);
	// console.log("mime：%s",file.mimetype);
//以下代码得到文件后缀
	name=file.originalname;
	nameArray=name.split('');
	var nameMime=[];
	l=nameArray.pop();
	nameMime.unshift(l);
	while(nameArray.length!=0&&l!='.'){
    l=nameArray.pop();
    nameMime.unshift(l);
	}
//Mime是文件的后缀
    Mime=nameMime.join('');
    console.log(Mime);
	res.send("done");
//重命名文件 加上文件后缀
    fs.renameSync('./upload/'+file.filename,'./upload/'+file.filename+Mime);

})

//文件下载尝试（chrome会直接在页面上展示。.最后也没有解决）
//设置download文件夹为静态 才能下载
 app.use('/download', express.static(path.join(__dirname, 'download')));
// app.get('/download',function(req,res){
//     var path='./download/aa.mp3';
//     res.download(path,'aa.mp3');
// });
app.get('/download', function(req, res){
  var file = __dirname + '/download/aa.mp3';
  res.download(file); 
});
app.get('/',function(req,res,next){
	res.sendFile(__dirname+"/index.html");
})

app.listen(3000);
```


//客户端
//index.html
```
<!DOCTYPE html>
<html>
<head>
	<title>上传文件</title>
	<meta charset="utf-8">
</head>
<body>
<form enctype="multipart/form-data" action="/upload-single" method="post">
<input type="file" name="myfile"></input>
<input type="submit" value="提交"></input>
</form>

<form enctype="multipart/form-data" action="/upload-multi" method="post">
<input type="file" name="myfile"></input>
<input type="file" name="myfile"></input>
<input type="submit" value="提交"></input>
</form>
<a href="download/aa.mp3">下载文件</a>
</body>
</html>
```
3.后续（20177月21日更新）
如果按照上面方法做，会有提交刷新页面的问题；如何做到提交文件不影响页面呢？ 
我遇到这个问题查了很多资料 找到一个简单的处理办法：

```
html:
<form enctype="multipart/form-data" action="/api/images/uploadimages" method="post" target="hidden-iframe">
           <input type="file"  accept="image/png,image/jpg,image/jpeg" name="fabricImages">
           <input type="submit"  value="提交">
         </form>
         <iframe name="hidden-iframe" style="display: block; width: 150px;height: 50px;overflow: hidden; background-color: pink"></iframe>
```
这样在服务端使用multer接受并储存文件，然后res.send('上传成功')；这个返回文字就会在iframe中出现，页面始终不会刷新。还可以检测返回值。

form表单有一个隐藏的属性，target 是可以指向iframe达到不刷新页面功能的。
input 的 accept 属性限制提交文件的类型 只能是普通图片格式。

4.优化 
关于修改文件后缀，我之前的代码过于繁琐了，后来进行了优化，
代码如下：
   var filename = req.file.originalname;
   var mime = filename.split('.').pop();
   //使用新文件名 防止图片文件名相同
   fs.renameSync( './client/assets/upload/' +   req.file.filename,'./client/assets/upload/'+req.file.filename+'.'+mime);
   获取源文件名的后缀，然后添加到新文件名的后边即可。
 5.后续（20170809）
 当不使用form标签提交的时候，只使用input type = file.

```
<label class="custom-file-upload">
<input type="file" 
        accept="image/png,image/jpg,image/jpeg,image/gif" 
        name="myupload" id="uploadInput" style="display: none;">
 </label>
 <label class="custom-file-submit">
 <button ng-click="uploadImage()" style="display: none;"></button>
 </label> 
```

```
 $scope.uploadImage =function(){
    console.log('upload');
     var formData = new FormData();
     var myfile = document.getElementById('uploadInput').files[0];
     formData.append('fabricImage', myfile);
     $http.post('/api/images/uploadimages', formData, {
         transformRequest: angular.identity,
         headers: {'Content-Type': undefined}
     }).then(function(result){
         console.log('succeed');                
     },function(err){
         console.log(err)
     });
     
 }
```

然后在server端 ，使用 app.post('/upload-single',upload.single('myupload').... 
然后你就错了！！ 会报错，找了很久在外网找到了原因，这时候你需要的是与formData.append('fabricImage', myfile); 中的name保持一致，这种方式上传，文件的name会变成formdata的指定的name,（不是文件名，相当于上传时的一个id） 所以服务端是app.post('/upload-single',upload.single('fabricImage').... 

另外，这么修改之后，就可以做成不需要点击提交了，直接选择文件，onchange="" 这样就可以上传文件。

```
<input type="file" 
        accept="image/png,image/jpg,image/jpeg,image/gif" 
        name="myupload" id="uploadInput" style="display: none;" onchange="uploadImage()">
```

 希望可以解决大家在做文件上传的时候遇到的问题。