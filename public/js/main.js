function FindPosition(oElement) {
    if (typeof (oElement.offsetParent) != "undefined") {
        for (var posX = 0, posY = 0; oElement; oElement = oElement.offsetParent) {
            posX += oElement.offsetLeft;
            posY += oElement.offsetTop;
        }
        return [posX, posY];
    }
    else {
        return [oElement.x, oElement.y];
    }
}

function formatParams(params) {
    return "?" + Object
        .keys(params)
        .map(function (key) {
            return key + "=" + encodeURIComponent(params[key])
        })
        .join("&")
}

function requestImage(parameters, callback) {
    console.log("pushing state", { ...parameters });
    window.history.replaceState(null, null, "?" + new URLSearchParams(parameters).toString());

    let xhr = new XMLHttpRequest();

    var url = '/api.php' + formatParams(parameters);
    console.log("url", url);
    xhr.open("GET", url, true);

    // function execute after request is successful
    xhr.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            let rsp = JSON.parse(this.responseText);
            console.log(rsp);

            let images = [];
            let loadCount = 0;
            function onload(e) {
                loadCount++;
                console.log('ok', loadCount);
                if (loadCount == rsp.info.frames) {


                    const totalFrames = rsp.info.frames;
                    const animationDuration = 3000;
                    const timePerFrame = animationDuration / totalFrames;
                    let timeWhenLastUpdate;
                    let timeFromLastUpdate;
                    let frameNumber = 1;

                    function step(startTime) {
                        
                        if (!timeWhenLastUpdate) timeWhenLastUpdate = startTime;

                        timeFromLastUpdate = startTime - timeWhenLastUpdate;

                        if (timeFromLastUpdate > timePerFrame) {
                            console.log("animate");
                            mainImage.src = images[frameNumber - 1].src;
                            timeWhenLastUpdate = startTime;

                            if (frameNumber >= totalFrames) {
                                console.log("end of loop");
                                frameNumber = 1;
                                
                            } else {
                                frameNumber = frameNumber + 1;
                                requestAnimationFrame(step);
                            }
                        }
                        else {
                            requestAnimationFrame(step);
                        }

                        
                    }

                    requestAnimationFrame(step);

                }
            }
            for (let i = 1; i <= rsp.info.frames; i++) {
                var image = document.createElement('img');
                var uri = '/' + rsp.dir + "/" + i + ".png";
                image.onload = onload;
                image.src = uri;
                images.push(image);
            }



            //mainImage.src = '/' + rsp.dir + "/1.png";
            dataset = rsp.info;

            

            parameters.xMin = rsp.info.xMin;
            parameters.xMax = rsp.info.xMax;
            parameters.yMin = rsp.info.yMin;
            parameters.yMax = rsp.info.yMax;
            console.log("Parameters", {...parameters});

            if (callback) {
                callback();
            }
        }
    }
    // Sending our request
    xhr.send();

}


var parameters = {
    width: 300,
    height: 200,
    xMin: -2,
    xMax: 1,
    yMin: -1,
    yMax: 1
}


for (let pair of new URLSearchParams(window.location.search.substring(1)).entries()) {
    parameters[pair[0]] = pair[1];
}

console.log(parameters);


//document.location.search = parameters;


requestImage(parameters);


var selector;

selector = document.createElement("div");

selector.style.position = 'absolute';
selector.style.top = (8 + 10) + 'px';
selector.style.left = (8 + 10) + 'px';
selector.style.width = 100 + 'px';
selector.style.height = Math.round(100 * parameters.height / parameters.width) + 'px';
selector.style.backgroundColor = 'blue';
selector.style.opacity = '0.5';
selector.style.cursor = 'move';
var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;

var clickTime
var imageHistory = [];
var isLoading = false;
selector.ondblclick = function (e) {
    if (isLoading) return false;
    imageHistory.push({ ...parameters });
    backButton.disabled = null;
    var xRatio = parameters.width / mainImage.width;
    var yRatio = parameters.height / mainImage.height;

    var xMin = ((selector.offsetLeft - mainImage.offsetLeft) * xRatio - dataset.centerX) / dataset.xUnit;
    var xMax = xMin + selector.clientWidth * xRatio / dataset.xUnit;

    var yMax = (dataset.centerY - (selector.offsetTop - mainImage.offsetTop) * yRatio) / dataset.yUnit;
    var yMin = yMax - selector.clientHeight * yRatio / dataset.yUnit;
    
    parameters.zoom = xMin + ','
        + xMax + ','
        + yMin + ','
        + yMax

    isLoading = true;
    selector.innerHTML = 'loading...';

    //selector.appendChild(loadingIcon);    
    requestImage(parameters, function () {
        isLoading = false;
        selector.innerHTML = '';
    });
}

selector.onmousedown = function (e) {
    e = e || window.event;
    e.preventDefault();
    pos3 = e.clientX;
    pos4 = e.clientY;
    document.onmouseup = function closeDragElement() {
        // stop moving when mouse button is released:

        var x = selector.offsetLeft - mainImage.offsetLeft;
        var y = selector.offsetTop - mainImage.offsetTop;
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
        var xMax = mainImage.offsetLeft + mainImage.width - selector.clientWidth;
        var x = Math.min(xMax, selector.offsetLeft - pos1);
        x = Math.max(mainImage.offsetLeft, x);
        var yMax = mainImage.offsetTop + mainImage.height - selector.clientHeight;
        var y = Math.min(yMax, selector.offsetTop - pos2);
        y = Math.max(y, mainImage.offsetTop);
        selector.style.top = y + "px";
        selector.style.left = x + "px";
    }

}

var mainImage = document.createElement("img");
mainImage.style.width = '100%';
var controller = document.createElement("div");
var backButton = document.createElement("button");
controller.style.position = "absolute";
controller.style.top = '10px';
controller.style.left = '10px';

backButton.onclick = e => {
    console.log("back");
    var parameters = imageHistory.pop();
    if (imageHistory.length == 0) {
        backButton.disabled = 'disabled';
    }

    console.log("back", parameters);
    requestImage(parameters, function () {

    });
}
backButton.innerHTML = 'back';
if (imageHistory.length == 0) {
    backButton.disabled = 'disabled';
}
controller.appendChild(backButton);

var interface = document.createElement("div");


interface.appendChild(mainImage);
interface.appendChild(controller);
interface.appendChild(selector);

document.body.appendChild(interface);

var dataset;
