var pmode=0; var pwind;
var currimg=0;
 function playlist() {
   if (pmode==0 || pwind.closed==true) {
     pwind=window.open("","mp3_playlist","resizable,width=460,height=400,scrollbars=no,menubar=no,hotkeys=no");
     pmode++;
     //setTimeout('pwind.focus();',250);
   }
 }

function rightString(str, sstr) {
   if (str.indexOf(sstr) == -1) {
      return "";
   } else {
      return (str.substring(str.indexOf(sstr)+sstr.length, str.length));
   }
}

function addall() {
  var bigurl='',url;
  playlist();	
  for (i=0;i<100;i++) {
      idname="add" + i;
      k = document.getElementById(idname);
      if (!k) { continue; }
      url=rightString(k.getAttribute("href"),"path=");
      bigurl=bigurl+url+'@@';
  }
  //bigurl='\''+bigurl+'\'';
  document.addallform.ppath.value=bigurl;
  document.addallform.submit();
}

function previmage() {
    if (currimg>0)
        showimage(currimg-1);
}

function nextimage() {
    if (currimg<(Pictures.length-1))
        showimage(++currimg);
}

function showimage(imgno) {
  x2 = document.getElementById('photoimg');
  x2.src=Pictures[imgno];
  currimg=imgno
}

