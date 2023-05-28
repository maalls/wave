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

let player = {

    loop: true,
    uri: '',
    frameCount: 0,
    duration: 0,

    timePerFrame: 0,
    timeWhenLastUpdate: 0,
    timeFromLastUpdate: 0,
    frameNumber: 1,

    loadCount: 0,

    images: [],
    replay: false,
    status: 'stopped',

    stepCount: 0,
    onAnimationEndedCallbacks: [],
    onAnimationEnded: function (callback) {

        this.onAnimationEndedCallbacks.push(callback);

    },

    resume: function() {

        
        if(this.status == 'stopped') {
            this.play();
        
        }
        else {
            this.status = 'playing';
            this.stepCount = false;
        }
        console.log("status", this.status, this.stepCount);

    },

    play: function () {

        this.stepCount = false;
        console.log("play", this.uri, this.frameCount);
        this.status = 'playing';
        this.stepCount = false;
        this.images = [];
        let loadCount = 0;


        let that = this;
        for (let i = 1; i <= this.frameCount; i++) {
            var image = document.createElement('img');
            image.addEventListener('load', function (e) {
                console.log("loaded", loadCount, that.frameCount, this);
                loadCount++;

                if (loadCount == that.frameCount) {


                    that.timePerFrame = that.duration / that.frameCount * 1000;

                    console.log('ok', 'time per frame', that.timePerFrame);
                    that.timeWhenLastUpdate = 0;
                    that.timeFromLastUpdate;
                    that.frameNumber = 1;

                    let step = function (startTime) {

                        //console.log("step status", that.status);
                        if (that.stepCount !== false) {
                            //console.log('step', that.stepCount);

                            if (that.stepCount == 0) {
                                requestAnimationFrame(step);
                                return;
                            }
                            that.stepCount--;


                        }
                        else if (that.status == 'paused') {
                            requestAnimationFrame(step);
                            return;
                        }
                        

                        that.timeFromLastUpdate = startTime - that.timeWhenLastUpdate;

                        if (that.timeWhenLastUpdate == 0 || that.timeFromLastUpdate > that.timePerFrame) {
                            console.log("animate", that.timeWhenLastUpdate, that.timeFromLastUpdate);
                            mainImage.src = that.images[that.frameNumber - 1].src;
                            that.timeWhenLastUpdate = startTime;

                            if (that.frameNumber == that.frameCount && !that.loop) {

                                
                                    console.log("end of loop");
                                    //if(t)
                                    /*that.frameNumber = 1;
                                    mainImage.src = that.images[1].src;
                                    */
                                    that.status = 'stopped';
                                    that.onAnimationEndedCallbacks.forEach(e => {
                                        e(that);
                                    });

                              

                            } else {
                                that.frameNumber = that.frameNumber % that.frameCount + 1;
                                requestAnimationFrame(step);
                            }
                        }
                        else {
                            requestAnimationFrame(step);
                        }


                    }
                    requestAnimationFrame(step);

                }
            });
            image.src = this.uri + '/' + i + ".png";
            this.images.push(image);
        }


    }

}


function requestImage(parameters, callback) {
    console.log("pushing state", { ...parameters });
    document.body.classList.add("loading");
    //player.status = 'stop';
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


            player.uri = '/' + rsp.dir;
            player.frameCount = rsp.info.frames;
            player.duration = parameters.duration;
            //player.play();
            mainImage.src = '/' + rsp.dir + "/1.png";
            dataset = rsp.info;
            parameters.xMin = rsp.info.xMin;
            parameters.xMax = rsp.info.xMax;
            parameters.yMin = rsp.info.yMin;
            parameters.yMax = rsp.info.yMax;
            console.log("Parameters", { ...parameters });

            if (callback) {
                callback();
            }
            document.body.classList.remove("loading");
        }
    }
    // Sending our request
    xhr.send();

}

var parameters;

var selector;

var dataset;

var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
var clickTime
var imageHistory = [];
var isLoading = false;

let controller = document.createElement("div");
var mainImage = document.createElement("img");

let aj = new XMLHttpRequest();

aj.open("GET", '/api-config.php', true);

// function execute after request is successful
aj.onreadystatechange = function () {

    if (aj.readyState == 4 && aj.status == 200) {

        let rsp = JSON.parse(this.responseText);

        console.log('rsp', rsp);

        let config = rsp;

        parameters = {};

        let template = config.algoritms[0].parameters;
        for (param in template) {
            parameters[param] = null;
        }
        console.log('u', { ...parameters });
        for (let pair of new URLSearchParams(window.location.search.substring(1)).entries()) {
            parameters[pair[0]] = pair[1];
        }

        if (parameters.class == undefined) {
            config.algoritm = config.algoritms[0];
            parameters = { ...config.algoritm.parameters };
        }
        else {
            console.log("found", parameters.class);
            config.algoritm = config.algoritms.find(e => e.parameters.class == parameters.class);
        }

        console.log('calling', parameters);


        //document.location.search = parameters;


        requestImage(parameters);

        mainImage.style.width = '100%';

        var backButton = document.createElement("button");
        controller.style.position = "absolute";
        controller.style.top = '10px';
        controller.style.left = '10px';
        controller.style.background = '#FFFFFF';

        let algoSelect = document.createElement("select");
        config.algoritms.forEach((a, i) => {
            let algoOption = document.createElement("option");
            algoOption.value = i;
            algoOption.innerHTML = a.name;
            //console.log("c", config, a.class, config.algoritm.parameters.class);
            if (a.parameters.class == config.algoritm.parameters.class) {
                algoOption.selected = 'selected';
            }
            algoSelect.appendChild(algoOption);
        });

        algoSelect.onchange = e => {
            console.log("change", config, config.algoritms[algoSelect.value]);
            config.algoritm = config.algoritms[algoSelect.value];
            parameters = config.algoritm.parameters;
            console.log("parameters", parameters);

            for (let parameter in parameters) {

                let input = controller.getElementsByClassName(parameter);
                //console.log("paRAM", parameter);
                //console.log("input", input);
                if (input.length) {
                    input[0].value = parameters[parameter];
                }
            }


            requestImage(parameters);
        }

        controller.appendChild(algoSelect);

        backButton.onclick = function (e) {
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

        let playerController = document.createElement("div");
        interface.appendChild(playerController);
        interface.appendChild(controller);


        document.body.appendChild(interface);



        let resetButton = createButton("reset", function (e) {

            player.frameNumber = 1;

        });
        let playButton = createButton(player.status == 'playing' ? 'pause' : 'play', function (e) {

            if (player.status == 'playing') {

                player.status = 'paused';
                this.innerHTML = 'resume';

            }
            else {
                console.log("WTF", player);
                this.innerHTML = 'pause';
                player.resume();
                
                
            }
        });

        player.onAnimationEnded(function () {

            playButton.innerHTML = 'start';

        });

        let stepButton = createButton('step', function (e) {
            if (player.status == 'stopped') {
                player.play();
            }
            player.stepCount = 1;
        });

        let backStepButton = createButton('back', function (e) {
            if (player.status == 'stopped') {
                player.play();
            }
            player.frameNumber = Math.max(1, player.frameNumber - 2);
            player.stepCount = 1;
        });

        let loopButton = createButton(player.loop ? 'loop on' : 'loop off', function (e) {
            player.loop = !player.loop;
            this.innerHTML = player.loop ? 'loop on' : 'loop off';
        });

        function createButton(label, onclick) {
            let button = document.createElement('button');
            button.innerHTML = label;
            button.onclick = onclick;
            playerController.appendChild(button);
            return button;
        }



        selector = document.createElement("div");

        selector.style.position = 'absolute';
        selector.style.top = (8 + 30) + 'px';
        selector.style.right = (8 + 30) + 'px';
        selector.style.width = 100 + 'px';
        selector.style.height = Math.round(100 * parameters.height / parameters.width) + 'px';
        selector.style.backgroundColor = 'blue';
        selector.style.opacity = '0.5';
        selector.style.cursor = 'move';

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
        interface.appendChild(selector);

        for (let parameter in parameters) {

            if (['class', 'name'].includes(parameter)) {
                continue;
            }
            createParameterInput(parameter);
        }


    }



}
aj.send();

function createParameterInput(name) {

    let div = createInputWithLabel(name, parameters[name], function (e) {
        console.log('change param', name, this.value, { ...parameters });
        parameters[name] = this.value;
        requestImage(parameters);
    });

    controller.appendChild(div);

    return div;

}
function createInputWithLabel(label, value, onChange) {

    var frameRateDiv = document.createElement("div");

    labelDom = document.createElement("label");
    labelDom.innerHTML = label;
    label.width = '20px';
    input = document.createElement("input");
    input.classList.add(label);
    input.style.width = '20px';
    input.value = value;
    input.onchange = onChange;
    frameRateDiv.appendChild(labelDom);
    frameRateDiv.appendChild(input);

    return frameRateDiv;

}