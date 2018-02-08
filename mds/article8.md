1.思路和想法
-------
   由于前端js无法操作文件，读取文件信息，保存移动文件这些需要交给后端处理，本次实现上传本地图片到服务端指定的本地文件夹，并可以读取所有文件显示，选择删除文件，选择添加图片到页面。
   当然也可以上传到服务器端，不必在本地client文件夹下面保存，然后请求图片，本次只是存在client客户端文件夹下。
   使用的工具有：express,multer(用于文件上传处理，见之前文章)，angularjs  。
 2.详细
-------
服务端：
server/index.js:

```
var express = require('express');
var controller = require('./images.controller');

var multer = require('multer');
var fs = require('fs');
var path = require('path');

//上传的文件保存的位置
var upload = multer({dest:'./client/assets/upload'});

var router = express.Router();

//上传单个图片，获取list,删除单个图片
router.post('/uploadimages', upload.single('fabricImages') ,controller.upload);
router.get('/getimagelist',controller.getimagelist);
//router.get('/:id' ,controller.getimageById);
router.delete('/:id', controller.deleteById);
//'/:id'这种用法指请求带有参数
```
server/images.controller.js

```
'use strict';
var moment = require('moment');
moment.locale('zh-cn');
var fs = require('fs');
var path = require('path');

export function upload(req,res){
   console.log(req.file);
   var filename = req.file.originalname;
   var mime = filename.split('.').pop();
   //使用新文件名 防止图片文件名相同
   fs.renameSync( './client/assets/upload/' + req.file.filename,'./client/assets/upload/'+req.file.filename+'.'+mime);
    res.send('上传成功');
}

//export function getimageById(req,res){
//    res.send('done');
//}

//获取的文件按照修改时间排序 然后返回 参考overflow的代码
export  function getimagelist(req,res){
  var dir = './client/assets/upload/';
     fs.readdir(dir,function(err,files){
     	if(err){console.log(err);}
        else{
          var myfiles = files.map(function(v){
            return {
              name:v,
              time:fs.statSync(dir + v).mtime.getTime()
            };
          })
          .sort(function(a, b) { return a.time - b.time; })
          .map(function(v){return v.name;});
          res.send(myfiles);
        }
     })
}

//注意请求参数带有{id:''} ,
export  function deleteById(req,res){
     //console.log(req.params.id);
     fs.unlink('./client/assets/upload/'+req.params.id,function(err){
     if(err){
      res.send(err);
     }else{
      res.send('done');
     }
     });
}
```
客户端：
index.html:

```
<div>
         <p style="font-size: 10px;">请添加图片：</p>
         <form enctype="multipart/form-data" action="/api/images/uploadimages" method="post" target="hidden-iframe">
           <input type="file" accept="image/png,image/jpg,image/jpeg,image/gif" name="fabricImages">
           <input type="submit" value="提交" ng-click='getImageslistDelay()'>
         </form>
         <iframe name="hidden-iframe"  marginheight='2px' marginwidth='2px' scrolling='no' style="display: block; width: 80px;height: 30px;overflow: hidden; background-color: pink;display: inline-block;" seamless></iframe>
        <button style="width: 50px;height: 25px;display: inline-block;" ng-click='getImageslist()'>刷新</button>
        <button style="width: 50px;height: 25px;display: inline-block;" ng-click='fabricDeleteImage()'>删除</button>
        
           <div style="display: flex;display: -webkit-flex;flex-flow:row wrap;justify-content:flex-start;">
             <div ng-repeat='myimg in myimgs' style="flex:0 1 auto;height:104px;margin-left:4px;">
             <img ng-src="../assets/upload/{{myimg}}" ng-click="fabricChoose($index)"  ng-class="{fabricImg1:$index==fabricIsSelected}"  style="height: 100px;">
             </div>
           </div>
        </div>
        <div class="Img-footer" style="height: 100px; width: 400px; margin: 20px auto">
            <button class="btn btn-primary" type="button" ng-click="ok()">添加</button>
            <button class="btn btn-warning" type="button" ng-click="cancel()">取消</button>
        </div>
      </div>
```
1.上传文件方式：
 注意enctype与target，target指定返回内容到的目标，如果没有这个属性，会默认为本页面，会刷新页面，只显示返回值，使用一个iframe标签是解决办法的一种。
 accept="image/png,image/jpg,image/jpeg,image/gif" 则是限制文件类型，html5的特性
2. 点击选择文件，则会改变样式，增加蓝色border,
3. 使用angularjs的前端框架，显示图片使用ng-repeat, 图片的排布使用flex，

controller.js

```
    $scope.myimgs = [];            
    $scope.getImageslist = function(){
        $http.get('/api/images/getimagelist').success(function(data){
            $scope.myimgs=data.reverse();
         });
     }

     $scope.getImageslist();
     //提交之后，延时刷新
     $scope.getImageslistDelay = function(){
         var t1 = setTimeout(function(){$scope.getImageslist();clearTimeout(t1);},800);
     }
     var ImgSelected = '';
     //改变选中图片颜色样式
     $scope.fabricChoose = function(i){
         ImgSelected = $scope.myimgs[i];
         $scope.fabricIsSelected = i;
     }
  
     $scope.fabricDeleteImage = function(){
         let pos = $scope.fabricIsSelected;
         var filename = $scope.myimgs[pos];
         //请求必须是{params:{"id":filename}}这种形式，对应服务端‘/:id’路由以及req.params.id
         $http.delete('/api/images/'+filename, {params:{"id":filename}}).success(function(data){
            // console.log(data);
         });
         $scope.fabricIsSelected = -1;
         $scope.myimgs.splice(pos,1);
     }
     $scope.ok = function() {     
         let url = '../assets/upload/'+ImgSelected;
         //可以在此把图片url添加到需要的位置，因为是client下面的图片，所以可以通过url访问到。
        console.log(url);
     }
     $scope.cancel = function() {
 
     };
```

3.效果
-------
![这里写图片描述](http://img.blog.csdn.net/20170801111933191?watermark/2/text/aHR0cDovL2Jsb2cuY3Nkbi5uZXQvTWFjaV95ZXJh/font/5a6L5L2T/fontsize/400/fill/I0JBQkFCMA==/dissolve/70/gravity/SouthEast)

![这里写图片描述](http://img.blog.csdn.net/20170801112040428?watermark/2/text/aHR0cDovL2Jsb2cuY3Nkbi5uZXQvTWFjaV95ZXJh/font/5a6L5L2T/fontsize/400/fill/I0JBQkFCMA==/dissolve/70/gravity/SouthEast)

![这里写图片描述](http://img.blog.csdn.net/20170801111948553?watermark/2/text/aHR0cDovL2Jsb2cuY3Nkbi5uZXQvTWFjaV95ZXJh/font/5a6L5L2T/fontsize/400/fill/I0JBQkFCMA==/dissolve/70/gravity/SouthEast)

4.总结
-------
代码很多，是一个项目的一部分，特把核心代码贴出，记录下来，使用了比较多的知识，关键是如何与服务器端做请求，上传文件与获取文件，删除文件这一个流程的实现。谢谢大家。