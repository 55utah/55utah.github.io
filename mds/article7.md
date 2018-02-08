写在前面
---
   突然对js文件的下载产生了兴趣，研究了一下，以后肯定用得到，参考的文章中，最全面的是这个：（https://segmentfault.com/a/1190000006600936，作者写的很好）。
  上边这个文章还包括上传文件，拖动上传，大文件处理，以及多张对象类型的区别。
普通的下载方式
---
 普通的下载思路是使用a标签，如下：
```
<a href="http://xxx.main.js" download="main.js">下载</a>
```
使用a标签的特性，对链接的文本进行下载。另外还可以设置为点击图片下载，方式为
```
<a href="http://xxx.main.jpg" download="main.js"><img src="a.jpg"></a>
```
这样的方式下载比较局限性。

更好的方式
-----
   参考其他文章，根据saveAs.js这些库得到的技巧是写一个函数，这个函数动态创建一个blob对象，再使用URL.createObjectURL()方法把blob对象转为一个可与url绑定的内容，MDN上面这样解释：静态方法会创建一个DOMString，它的 URL 表示参数中的对象。这个 URL 的生命周期和创建它的窗口中的 document 绑定。这个新的URL 对象表示着指定的 File 对象或者 Blob 对象。
    将这个url赋给href，download设置下载的命名；注意文件的格式由blob设定：
    `var aBlob = new Blob( array, options );`
下载的demo代码如下：
```
 <a id="down" href="" onclick="down('122.txt','我是保存的内容?-1~')">111</a>
 
function down(name,content){
		//var aBlob = new Blob( array, options );
		var a = document.querySelector('#down');
		var blob = new Blob([content],{type:'text/plain'});
		a.href = window.URL.createObjectURL(blob);
		a.download = name;
	}
```
实现了将文本下载为一个txt文件并保存。

写在最后
----
  使用blob格式保存文件时候，因为blob格式是原始二进制格式，所以像是exe,mp3,img这种非文本都可以保存成功。网上一些js保存table到本地excel文件，其实没什么必要效果也不好，建议后端处理，nodejs保存文件为excel肯定比前端好做的多。

