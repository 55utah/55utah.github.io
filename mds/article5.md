常见的loading图标，大概的样子是这样：（录制的gif，比较卡）
![录制的gif比较卡](http://img.blog.csdn.net/20170827002624945?watermark/2/text/aHR0cDovL2Jsb2cuY3Nkbi5uZXQvTWFjaV95ZXJh/font/5a6L5L2T/fontsize/400/fill/I0JBQkFCMA==/dissolve/70/gravity/SouthEast)
看到大牛的面试中被问到这个问题，我就想自己动手实现一下。我们来用20行代码实现一下。
代码如下：

```
<!DOCTYPE html>
<html>
<head>
	<title>加载效果</title>
	<style type="text/css">
		.loading{
			width: 30px;
			height: 30px;
			border-radius: 50%;
			border: 5px solid #BEBEBE;
			border-left: 5px solid #498aca;
			animation: load 1s linear infinite;
			-moz-animation:load 1s linear infinite;
			-webkit-animation: load 1s linear infinite;
			-o-animation:load 1s linear infinite;
		}
		@-webkit-keyframes load
		{
			from{-webkit-transform:rotate(0deg);}
			to{-webkit-transform:rotate(360deg);}
		}
		@-moz-keyframes load
		{
			from{-moz-transform:rotate(0deg);}
			to{-moz-transform:rotate(360deg);}
		}
		@-o-keyframes load
		{
			from{-o-transform:rotate(0deg);}
			to{-o-transform:rotate(360deg);}
		}
	</style>
</head>
<body>
<div class="loading"></div>
</body>
</html>
```
如果只考虑chrome浏览器的话，代码少于20行。
**基本思路**是利用border-radius把div变成圆圈，然后border-left改变部分border颜色，最后使用动画完成。
animation: load 1s linear infinite;
原来animation的循环时间属性有一个infinite值，表示无限循环，今天之前我居然不知道，惭愧。

**后续**-2017/9/03

继续写了两个常见的加载样式，并分析常用的方法以及相关知识。
效果如下gif :
![loading...](http://img.blog.csdn.net/20170903195027437?watermark/2/text/aHR0cDovL2Jsb2cuY3Nkbi5uZXQvTWFjaV95ZXJh/font/5a6L5L2T/fontsize/400/fill/I0JBQkFCMA==/dissolve/70/gravity/SouthEast)

代码如下：

```
<style type="text/css">
    html,body{
    	background-color:#080915;
    }
    .loading1{
    	position: absolute;
    	left:10px;
    	top:10px;
    	width: 50px;
    	height: 50px;
    	background-color: #ed5565;
    }
	.loading{
		width:10px;
		height: 10px;
		display: inline-block;
		margin: 0;
		border-radius: 50%;
		position: absolute;
		background-color: #fff;
		-webkit-animation: load 0.5s cubic-bezier(0.77, 0.47, 0.64, 0.28) alternate infinite; 
		animation: load 0.5s cubic-bezier(0.77, 0.47, 0.64, 0.28) alternate infinite; 
	}
	.b1{animation-delay:-0.5s;left:5px;}
	.b2{animation-delay:-0.2s;left:20px;}
	.b3{animation-delay: 0s;left:35px;}
	@keyframes load{
		from {top:10px;}
		to {top:20px;}
	}
	@-webkit-keyframes load{
		from {top:10px;}
		to {top:20px;}
	}
 	.loading2{
 		position: absolute;
 		top:10px;
 		left:80px;
 		background-color: #ed5565;
 		width: 60px;
 		height:40px;
 		padding-top:10px; 
 	}
 	.loading2 div{
 		width: 4px;
 		height: 10px;
 		background-color: #fff;
 		display: inline-block;
 		margin-left:2px; 
 		animation:load2 0.5s cubic-bezier(0.77, 0.47, 0.64, 0.28) alternate infinite;
 		-webkit-animation:load2 0.5s cubic-bezier(0.77, 0.47, 0.64, 0.28) alternate infinite;
 	}
 	.loading2 div:nth-child(1){
 		animation-delay: -0.5s;
 	}
 	.loading2 div:nth-child(2){
 		animation-delay: -0.4s;
 	}
 	.loading2 div:nth-child(3){
 		animation-delay: -0.3s;
 	}
 	.loading2 div:nth-child(4){
 		animation-delay: -0.2s;
 	}
 	.loading2 div:nth-child(5){
 		animation-delay: -0.1s;
 	}
 	@keyframes load2{
 		from{transform:scaleY(1);}
 		to{transform:scaleY(3);}
 	}
 	@-webkit-keyframes load2{
 		from{transform:scaleY(1);}
 		to{transform:scaleY(3);}
 	}
</style>
</head>
<body>
<div class="loading1">
<div class="loading b1"></div>
<div class="loading b2"></div>
<div class="loading b3"></div>
</div>
<div class="loading2">
	<div></div>
	<div></div>
	<div></div>
	<div></div>
	<div></div>
</div>
</body>
```
上面两个加载的css3效果，一样是css3的动画，关键思路是使用animation-delay达到延迟效果，利用transform:sacle()来达到缩放效果。还有一些效果需要使用css3的线性渐变来做。
**1.animation属性总结：**
*animation	所有动画属性的简写属性，除了 animation-play-state 属性；
animation-name	规定 @keyframes 动画的名称。
animation-duration	规定动画完成一个周期所花费的秒或毫秒。默认是 0。
animation-timing-function	规定动画的速度曲线。默认是 "ease"。	
animation-delay	规定动画何时开始。默认是 0。	
animation-iteration-count	规定动画被播放的次数。默认是 1。	
animation-direction	规定动画是否在下一周期逆向地播放。默认是 "normal"。	
animation-play-state	规定动画是否正在运行或暂停。默认是 "running"。	
animation-fill-mode	规定对象动画时间之外的状态。*

**其中timing-function速度曲线常用linear，cubic-bezier(n,n,n,n)贝塞尔曲线；animation-delay属性在做这个加载效果时很有用；animation-iteration-count有一个infinite值，表示无限循环；animation-direction属性常用值：alternate表示逆向播放；**
**2.关于nth-child()的使用**
```
<div class='ss'>
<div></div>
<div></div>
<div></div>
</div>

.ss div:nth-child(1){
 color:#fff;
}
```

总结： `nth:child()`伪元素本身是写在子类上的，然后对子类写样式；而不是写在父类的；参数接受数字/名称或者公式；
1表示第一个子类；2n表示偶数；2n+1表示奇数；odd表示奇数，even表示偶数；4n+3表示每隔四个数的第三个

**3.网页的加载事件**：
**document.addEventListener('DOMContentLoaded', function () {   });**
DOMContentLoaded顾名思义，就是dom内容加载完毕。从页面空白到展示出页面内容，会触发DOMContentLoaded事件。而这段时间就是HTML文档被加载和解析完成。当我们在浏览器地址输入URL时，浏览器会发送请求到服务器，服务器将请求的HTML文档发送回浏览器，浏览器将文档下载下来后，便开始**从上到下解析**，解析完成之后，会**生成DOM**。如果页面中有css，会根据css的内容形成**CSSOM**，然后**DOM和CSSOM会生成一个渲染树**，最后浏览器会根据渲染树的内容计算出各个节点在页面中的确切大小和位置，并将其绘制在浏览器上。javascript会阻塞dom的解析。当解析过程中遇到`<script>`标签的时候，便会停止解析过程，转而去处理脚本，处理完脚本之后，浏览器便继续解析HTML文档。页面上所有的资源（图片，音频，视频等）被加载以后才会触发load事件，简单来说，页面的**load事件会DOMContentLoaded被触发之后才触发。**
**Jquery中的ready与load:**
在 jQuery 中经常使用的 `$(document).ready(function() { // ...代码... });` 其实监听的就是 DOMContentLoaded 事件，而 `$(document).load(function() { // ...代码... });` 监听的是 load 事件（onload事件）。在用jquery的时候，我们一般都会将函数调用写在ready方法内，就是页面被解析后，我们就可以访问整个页面的所有dom元素，可以缩短页面的可交互时间，提高整个页面的体验。
**4.为什么将css放在头部，将js文件放在尾部会优化性能。**
浏览器生成Dom树的时候是一行一行读HTML代码的，script标签放在最后面就不会影响前面的页面的渲染。浏览器为了更好的用户体验,渲染引擎将尝试尽快在屏幕上显示的内容。它不会等到所有HTML解析之前开始构建和布局渲染树。部分的内容将被解析并显示。也就是说浏览器能够渲染不完整的dom树和cssom，**尽快的减少白屏的时间。**假如我们将js放在header，js将阻塞解析dom，dom的内容会影响到First Paint，导致First Paint延后。所以说我们会将js放在后面，以减少First Paint的时间，但是不会减少DOMContentLoaded被触发的时间。

