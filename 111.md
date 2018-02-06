1.写在前面
之前的理解是hover伪类是写在某个元素上，鼠标移到上面就可以修改此元素的样式，可不可以改变另一个非hover元素样式呢？在使用less的时候，可以这么写：
```
.nav:hover{
	color:red;
	.block{
		color:blue;
	}
}
```
含义是hover到nav元素时，既改变nav样式又改变另一个任意block元素样式。那么在css里面我们可以这么做：

```
<div class='out'>
	<div class="in">1</div>
</div>
```
```
.out:hover .in{
   color:red;
}
```
和普通后代或者派生选择器一样具有伪类的时候同样可以这么使用，含义是out元素hover时候子类in元素的样式改变。
基于这个思路，普通css选择器方式都可以这么用。

```
.show_up:hover+.show_down{
		background-color: #f00;
	}
//兄弟选择器，同一个父元素下的show_up,show_down,当show_up被hover时show_down的样式改变。
```
2.利用这些做的demo

```
<div class="block">
	<div class="show_up">upBlock</div>
	<div class="show_down">DownBlock</div>
</div>
```

```
.block{
		margin: 10px auto;
		width: 160px;
		height: 60px;
		border:1px solid #ff0;
		text-align: center;
		line-height: 60px;
		position: relative;
		overflow: hidden;
	}
	.show_up,.show_down{
		width: 100%;
		height: 60px;
		transition: all 0.2s ease;
	}
	.show_up{
		position: absolute;
		background-color: #ff0;
		top:-60px;
	}
	.block:hover .show_up{
		top:0px;
	}
```
![这里写图片描述](http://img.blog.csdn.net/20171204152537939?watermark/2/text/aHR0cDovL2Jsb2cuY3Nkbi5uZXQvTWFjaV95ZXJh/font/5a6L5L2T/fontsize/400/fill/I0JBQkFCMA==/dissolve/70/gravity/SouthEast)
3.常见的li的hover特效demo（css+少量js）

```
<div class="sample2">
	<ul>
		<li>1</li>
		<li>2</li>
		<li>3</li>
		<li>4</li>
		<li>5</li>
		<li class="line"></li>
	</ul>
</div>
```

```
.sample2{
		margin: 10px auto;
		/*border:1px solid #ff0;*/
		width: 800px;
		height: 62px;
		padding: 0px;
	}
	.sample2 ul{
		margin: 0px;
		width: 840px;
		display: block;
		padding: 0px;
		position: relative;
	}
	.sample2 ul li{
		width:160px;
		height:60px;
		display: inline-block;
		background-color: #ee0;
		text-align: center;
		line-height: 60px;
		list-style-type: none;
	}
	.line{
		position: absolute;
		top:-4px;
		left:0px;
		height:4px !important;
		background-color: #00f !important;
		transition: all 0.2s ease;
	}
	.sample2 ul li:hover{
		color:#fff;
	}
```

```
var li = document.querySelectorAll('.sample2 ul li');
	// console.log(line);
	var sample2 = document.querySelector('.sample2 ul');
	var line = document.querySelector('.line');
	sample2.onmouseover = function(e){
		// console.log(e.target);
	for(var i=0;i<li.length-1;i++){
		if(li[i]==e.target){
			line.style.left = 165*i+'px';
		}
	  }
    }
```
![这里写图片描述](http://img.blog.csdn.net/20171204152903447?watermark/2/text/aHR0cDovL2Jsb2cuY3Nkbi5uZXQvTWFjaV95ZXJh/font/5a6L5L2T/fontsize/400/fill/I0JBQkFCMA==/dissolve/70/gravity/SouthEast)
二.fixed屏幕居中

```
position: fixed; 
top: 50%;
left: 50%;
transform: translate3d(-50%,-50%,0);
```
以上top,left的50%是相对于左上角的，然后使用translate的-50%，是反向移动自身50%,实现了全屏幕中心。
