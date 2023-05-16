<?php

?>
<!DOCTYPE html>
<html>
<body>
<style>
    img {
        width: 100%;
    }
    </style>
<div id="display"><img id="myImgId"
    
    src="/data/mandelbrot.png" />
</div>
<p>X:<span id="x"></span></p>
<p>Y:<span id="y"></span></p>
<p id="debug"></p>
<script type="text/javascript">


function FindPosition(oElement)
{
  if(typeof( oElement.offsetParent ) != "undefined")
  {
    for(var posX = 0, posY = 0; oElement; oElement = oElement.offsetParent)
    {
      posX += oElement.offsetLeft;
      posY += oElement.offsetTop;
    }
      return [ posX, posY ];
    }
    else
    {
      return [ oElement.x, oElement.y ];
    }
}
var selector
function GetCoordinates(e)
{
  var PosX = 0;
  var PosY = 0;
  var ImgPos;
  ImgPos = FindPosition(myImg);
  if (!e) var e = window.event;
  if (e.pageX || e.pageY)
  {
    PosX = e.pageX;
    PosY = e.pageY;
  }
  else if (e.clientX || e.clientY)
    {
      PosX = e.clientX + document.body.scrollLeft
        + document.documentElement.scrollLeft;
      PosY = e.clientY + document.body.scrollTop
        + document.documentElement.scrollTop;
    }
  PosX = PosX - ImgPos[0];
  PosY = PosY - ImgPos[1];

  var xRatio = width /myImg.width;
  var yRatio =height /myImg.height;

  

  document.getElementById("x").innerHTML = (PosX * xRatio - dataset.centerX) / dataset.xUnit;
  document.getElementById("y").innerHTML = (dataset.centerY - PosY * yRatio) / dataset.yUnit;
}   

//-->
</script>
<script>

function formatParams( params ){
  return "?" + Object
        .keys(params)
        .map(function(key){
          return key+"="+encodeURIComponent(params[key])
        })
        .join("&")
}


    let xhr = new XMLHttpRequest();

    let parameters = {
      width: 300,
      height: 200,
      xMin: -2,
      xMax: 1,
      yMin: -1,
      yMax: 1
    }

    var url = '/generate.php' + formatParams(parameters);
    xhr.open("GET", url, true);
 
    // function execute after request is successful
    xhr.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            let rsp =JSON.parse(this.responseText);
            console.log(rsp);
            myImg.src = '/data/' + rsp.file;
            dataset = rsp.info;

            var img = new Image();
            img.onload = function(){
                height = img.height;
                width = img.width;
                console.log("dim", height, width, myImg.width, myImg.height);

                
                selector = document.createElement("div");
  
                selector.style.position = 'absolute';
                selector.style.top = (8 + 10) + 'px';
                selector.style.left = (8 + 10) + 'px';
                selector.style.width = 100 + 'px';
                selector.style.height = Math.round(100 *height / width) + 'px';
                selector.style.backgroundColor = 'blue';
                selector.style.opacity = '0.5';
                selector.style.cursor = 'move';
                var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;

                var clickTime
                selector.ondblclick = function(e) {

                  var xRatio  = width / myImg.width;
                  var yRatio = height / myImg.height;

                  var xMin = ((selector.offsetLeft - myImg.offsetLeft) * xRatio - dataset.centerX) / dataset.xUnit;
                  var xMax = xMin + selector.clientWidth * xRatio / dataset.xUnit;

                  var yMax = (dataset.centerY - (selector.offsetTop - myImg.offsetTop) * yRatio) / dataset.yUnit;
                  var yMin = yMax - selector.clientHeight * yRatio / dataset.yUnit;
                  console.log("position", xMin, xMax, yMin, yMax);
                }

                selector.onmousedown = function(e) {
                    e = e || window.event;
                    e.preventDefault();
                    pos3 = e.clientX;
                    pos4 = e.clientY;
                    document.onmouseup = function closeDragElement() {
                    // stop moving when mouse button is released:

                    var x = selector.offsetLeft -myImg.offsetLeft;
                    var y = selector.offsetTop -myImg.offsetTop;
                    //console.log("position", x, y);
                    document.onmouseup = null;
                    document.onmousemove = null;
                }

                    // call a function whenever the cursor moves:
                    document.onmousemove = function elementDrag(e) {
                        e = e || window.event;
                        e.preventDefault();
                        // calculate the new cursor position:
                        pos1 = pos3 - e.clientX;
                        pos2 = pos4 - e.clientY;
                        
                       
                        pos3 = e.clientX;
                        pos4 = e.clientY;
                        // set the element's new position:
                        var xMax = myImg.offsetLeft + myImg.width - selector.clientWidth;
                        var x = Math.min(xMax, selector.offsetLeft - pos1);
                        x = Math.max(myImg.offsetLeft, x);
                        var yMax =myImg.offsetTop +myImg.height - selector.clientHeight;
                        var y = Math.min(yMax, selector.offsetTop - pos2);
                        y = Math.max(y, myImg.offsetTop);
                        console.log(x,y, xMax, yMax, selector.offsetTop - pos2);
                        selector.style.top = y + "px";
                        selector.style.left = x + "px";
                    }

                }

                document.getElementById("display").prepend(selector);

            // code here to use the dimensions
            }
            img.src = myImg.src;
        }
    }
    // Sending our request
    xhr.send();

    var myImg = document.getElementById("myImgId");

    
    var height;
    var width;

    


    var dataset;
    myImg.onmousedown = GetCoordinates;

    var debug = document.getElementById("debug");

    //debug.innerHTML = "center (" + dataset.centerX + "," + dataset.centerY + ")";

    </script>
</body>
</html>