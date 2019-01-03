layui.define(['jquery', 'layer', 'doyo'], function (exports) {
    var $ = layui.$;
    var layer = layui.layer;
    var doyo = layui.doyo;

    exports('client', {
        listen: {
            method: [],
            add: function (tags, callback) {
                for (var k in this.method) {
                    if (this.method[k].tags == tags) {
                        return;
                    }
                }
                this.method.push({'tags': tags, 'callback': callback});
            },
            remove: function (tags) {
                for (var k in this.method) {
                    if (this.method[k].tags == tags) {
                        this.method.splice(k, 1);
                    }
                }
            }
        },
        logout: false,
        ws: null,
        url: null,
        connect: function (host, port, passport_id, token, callback) {
            var $this = this;

            if (this.ws != null && this.ws.readyState == 1) {
                console.log('web socket already connected..');
                var data = {};
                data.op = 10001;
                callback(data);
                return;
            }

            var device = layui.device();

            this.url = "wss://" + host + ":" + port + "/?passport_id=" + passport_id + "&token=" + token + "&os=" + device.os + "&android=" + device.android + "&ios=" + device.ios + "&weixin=" + device.weixin + "&device=browser";

            console.log(this.url);

            this.ws = new WebSocket(this.url);

            this.ws.onopen = function () {
                console.log("open");
            };

            this.ws.onmessage = function (evt) {
                var ret = JSON.parse(evt.data);
                console.log(ret);

                if (ret.code == 0) {
                    callback(ret);

                    for (var i in $this.listen.method) {
                        $this.listen.method[i].callback(ret);
                    }
                } else if (ret.code == -9001) {

                    window.onbeforeunload = null;
                    layui.sessionData('status', {key: 'message', value: ret.data.message});
                    doyo.logout();

                } else if (ret.code == -9002) {
                    $this.logout = true;

                    // layer.msg(ret.data.message, {
                    //     time: 3000
                    // });

                    callback(ret);

                    setTimeout(function () {
                        $this.logout = false;
                    }, 1000);

                } else {
                    layer.msg(ret.data.message);
                }
            };

            this.ws.onclose = function (evt) {
                console.log('web socket closed');
                if (!$this.logout) {
                    window.onbeforeunload = null;
                    var status = layui.sessionData('status');
                    if (!status.message) {
                        doyo.logout();
                        layui.sessionData('status', {key: 'message', value: '网络断开，请尝试重新登录。'});
                    }
                }
            };

            this.ws.onerror = function (evt) {

            };
        },
        send: function (op, param) {
            var data = {};
            data.op = op;
            data.param = param;
            if (this.ws) {
                if (this.ws.readyState == 1) {
                    console.log("send: " + JSON.stringify(data));
                    this.ws.send(JSON.stringify(data));
                } else {

                }
            } else {

            }
        }
    });
});
