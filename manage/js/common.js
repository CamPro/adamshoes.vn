/* $Id : common.js 4824 2007-01-31 08:23:56Z paulgao $ */
var NEW_ORDER_INTERVAL = 30000;

/*
var timeout = setTimeout("location.reload(true);",35000); //6m la chay lai
function resetTimeout() {
    clearTimeout(timeout);
    timeout = setTimeout("location.reload(true);", 35000);
}
*/
/* *
 * Check order and comment
 */
//CheckOrder();

function startCheckOrder() {
    // body...
}
function CheckOrder()
{
    try
    {
      console.log('Chay qua day');
      Ajax.call('index.php?is_ajax=1&act=check_order','', checkOrderResponse, 'GET', 'JSON');
    }
    catch (e) { }
}

function checkOrderResponse(result)
{
    //console.log(result);

  if (result.error != 0 || (result.new_order == 0 && result.new_comment == 0))
  {
    return;
  }
  try
  {
    var title = 'Thông báo cho Admin';
    var body = 'Có '+result.new_order+' đơn hàng mới và '+result.new_comment+' bình luận mới';
    notify(title, body);
  }
  catch (e) { }
}

// Notifycation
function notify(title, body,timeout) {
  timeout = (timeout) ? timeout : 10000; //5s de hien thi thong bao
  Notification.requestPermission(function () {
    var nf = new Notification(title, {
      body: body,
      icon: "http://bachkhoashop.com/images/LogoITMart.png"
    });
    nf.onshow = function () {
      setTimeout(function () {
        nf.close()
      }, timeout)
    };
  });
}

function createCookie(name,value,minute) {
    if (minute) {
        var date = new Date();
        date.setTime(date.getTime()+ minute);
        var expires = "; expires="+date.toGMTString();
    }
    else var expires = "";
    document.cookie = name+"="+value+expires+"; path=/adPanel";
}

function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

function eraseCookie(name) {
    createCookie(name,"",-1);
}

/**
 * 确认后跳转到指定的URL
 */
function confirm_redirect(msg, url)
{
  if (confirm(msg))
  {
    location.href=url;
  }
}
/* *
 * 设置页面宽度
 */
function set_size(w)
{
  var y_width = document.body.clientWidth
  var s_width = screen.width
  var agent   = navigator.userAgent.toLowerCase();
  if (y_width < w)
  {
    if (agent.indexOf("msie") != - 1)
    {
      document.body.style.width = w + "px";
    }
    else
    {
      document.getElementById("bd").style.width = (w - 10) + 'px';
    }
  }
}
/* *
 * 显示隐藏图片
 * @param   id  div的id
 * @param   show | hide
 */
function showImg(id, act)
{
  if (act == 'show')
  {
    document.getElementById(id).style.visibility = 'visible';
  }
  else
  {
    document.getElementById(id).style.visibility = 'hidden';
  }
}

var listZone = new Object();
/* *
 * 显示正在载入
 */
listZone.showLoader = function()
{
  listZone.toggleLoader(true);
}
listZone.hideLoader = function()
{
  listZone.toggleLoader(false);
}
listZone.toggleLoader = function(disp)
{
  document.getElementsByTagName('body').item(0).style.cursor = (disp) ? "wait" : 'auto';
  try
  {
    var doc = top.frames['header-frame'].document;
    var loader = doc.getElementById("load-div");
    if (typeof loader == 'object') loader.style.display = disp ? "block" : "none";
  }
  catch (ex) { }
}
function $import(path,type,title){
  var s,i;
  if(type == "js"){
    var ss = document.getElementsByTagName("script");
    for(i =0;i < ss.length; i++)
    {
      if(ss[i].src && ss[i].src.indexOf(path) != -1)return ss[i];
    }
    s      = document.createElement("script");
    s.type = "text/javascript";
    s.src  =path;
  }
  else if(type == "css")
  {
    var ls = document.getElementsByTagName("link");
    for(i = 0; i < ls.length; i++)
    {
      if(ls[i].href && ls[i].href.indexOf(path)!=-1)return ls[i];
    }
    s          = document.createElement("link");
    s.rel      = "alternate stylesheet";
    s.type     = "text/css";
    s.href     = path;
    s.title    = title;
    s.disabled = false;
  }
  else return;
  var head = document.getElementsByTagName("head")[0];
  head.appendChild(s);
  return s;
}
/**
 * 返回随机数字符串
 *
 * @param : prefix  前缀字符
 *
 * @return : string
 */
function rand_str(prefix)
{
  var dd = new Date();
  var tt = dd.getTime();
  tt = prefix + tt;
  var rand = Math.random();
  rand = Math.floor(rand * 100);
  return (tt + rand);
}