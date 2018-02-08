git地址：https://github.com/Macixsq1/github404.git
开始学前端的时候，发现了github404的页面非常有趣，当时觉得很神奇，一直不明白怎么实现的呀，最近又看到，随便想了一下，发现其实并不复杂，动手写了一个demo.
使用angularjs搭建了初步页面，实际上核心使用了原生js的操作，效果不错。
核心代码如下：
con1.js
```
angular.module('myapp',[])
.controller("con1",function($scope,$document){
    function $(id){
    	return document.querySelector(id);
    }
    function set(elem,top,left,posX,posY,bool){
    	if(bool){
	    	elem.style.top = posY+top +'px';
	    	elem.style.left = posX+left +'px';
    	}else{
    		elem.style.top = top - posY +'px';
	    	elem.style.left = left - posX +'px';
    	}
    	
    }
	let bgdiv = $('#bg-div'),
	    img0 = $('#img0'),
	    img1 = $('#img1'),
	    img2 = $('#img2'),
	    img3 = $('#img3'),
	    img4 = $('#img4'),
	    img5 = $('#img5');
	function main(posX,posY){
	   bgdiv.style.height = 100 - posY + 'px';
	   set(img0,67,62,posX,posY,true);
	   set(img1,90,350,posX,posY,true);
	   set(img2,145,427,posX,posY,true);
	   set(img3,292,366,posX,posY,true);
	   set(img4,127,800,posX,posY,false);
	   set(img5,82,492,posX,posY,false);
	}
	main(0,0);
	$document.on('mousemove',function(e){
	   let posY = e.clientY/40;
	   let posX = e.clientX/40;
	   main(posX,posY);
	});

})
```
index.html

```
<!DOCTYPE html>
<html ng-app="myapp">
<head>
	<title></title>
<link rel="stylesheet" type="text/css" href="./index.css">
</head>
<body ng-controller="con1">
	<div class="container">
		<div class="bg-div" id="bg-div"></div>
		<div class="in-container">
			<img class="myimg" style="z-index: 6" id="img0" src="./image/404.png">
			<img class="myimg" style="z-index: 5" id="img1" src="./image/guy.png">
			<img class="myimg" style="z-index: 4" id="img2" src="./image/ship.png">
			<img class="myimg" style="z-index: 3" id="img3" src="./image/shadow.png">
			<img class="myimg" style="z-index: 2" id="img4" src="./image/building.png">
			<img class="myimg" style="z-index: 1" id="img5" src="./image/build2.png">		
		</div>
	</div>
</body>
<script type="text/javascript" src="./angular.min.js"></script>
<script type="text/javascript" src="./con1.js"></script>
</html>
```
index.css

```
html,body{
	padding: 0;
	margin: 0;
}
.container{
	width: 100%;
	height: 400px;
	background-color:#FFDAB9;
}
.bg-div{
	width: 100%;
	height: 100px;
	background-color:#2763a0;
	position: absolute;
	top: 0px;
	left: 0px;
}
.in-container{
	margin: 0px auto;
	width: 940px;
	height: 400px;
	position: relative;
}
.myimg{
	position: absolute;
}

```
最后效果截图：
![](http://img.blog.csdn.net/20170827234313881?watermark/2/text/aHR0cDovL2Jsb2cuY3Nkbi5uZXQvTWFjaV95ZXJh/font/5a6L5L2T/fontsize/400/fill/I0JBQkFCMA==/dissolve/70/gravity/SouthEast)
会随着页面鼠标移动，而且移动的方式有差别，会体现出层次感。
分析：核心的方法是使用document.querySelector获取dom，然后dom.style.top这样的方式操作style，监听鼠标移动，修改top,left属性。当然前提是需要position:absolute;
细节是前面几个图片和后边两张图片改变方式不同，一个是随着鼠标右移，一个是左移，一个上移，另一个会下移，这样就会显示出远近的错觉。
