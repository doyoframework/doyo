layui.define(['jquery', 'layer', 'util'], function (exports) {
    var $ = layui.$;
    var layer = layui.layer;
    var util = layui.util;
    exports('doyo', {
        customer_id: 0,
        post: function (url, args, callback) {
            if (layui.cache.post[url]) {
                var cache = layui.sessionData(url);
                if (cache.html) {
                    console.log('cache ..' + url);
                    console.log(cache.html);
                    callback(cache.html);
                    return;
                }
            }

            $.post(url, args, function (res) {
                if (res.code == 0) {
                    layui.sessionData(url, {
                        'key': 'html',
                        'value': res
                    });
                    callback(res);
                } else {
                    layer.msg(res.data.message, function () {

                    });
                }
            }, 'json');
        },
        page: function (template, callback) {
            if (layui.cache.template) {
                var cache = layui.sessionData(template);
                if (cache.html) {
                    $('#main-body').html(cache.html);
                    callback();
                    return;
                }
            }

            var args = {};
            args.template = template;
            $.post('/index/page/' + template, args, function (html) {
                layui.sessionData(template, {
                    'key': 'html',
                    'value': html
                });
                $('#main-body').html(html);
                callback();
            }, 'html');
        },
        redirect: function (url, callback) {
            if (callback) {
                var args = {};
                args.template = url;
                $.post('/index/page/', args, function (html) {
                    $('#main-content').html(html);
                    callback();
                }, 'html');
            } else {
                window.location = url;
            }
        },
        dialog: function (title, template, args, area, callback) {
            args.template = template;
            $.post('/index/page/', args, function (html) {
                var options = {
                    anim: 0,
                    type: 1,
                    title: title,
                    content: html,
                    area: area,
                    btnAlign: 'c',
                    btn: [],
                    success: function (layero, index) {
                        callback(layero, index);
                    }
                }

                if (area.type != undefined) {
                    options = area;
                    options.title = title;
                    options.content = html;
                    options.success = function (layero, index) {
                        callback(layero, index);
                    }
                }

                layer.open(options);
            }, 'html');
        },
        submit: function (data, callback) {

            if (data.form.enctype == 'multipart/form-data') {
                var formData = new FormData();
                for (var k in data.field) {
                    formData.append(k, data.field[k]);
                }
                for (var i in data.form) {
                    if (typeof data.form[i] == 'object') {
                        try {
                            if (data.form[i].tagName == 'INPUT') {
                                if (data.form[i].type == 'file') {
                                    formData.set(data.form[i].name, data.form[i].files[0]);
                                }
                            }
                        } catch (e) {
                        }
                    }
                }
                $.ajax({
                    url: data.form.action,
                    type: 'POST',
                    cache: false,
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: "json",
                    beforeSend: function () {

                    },
                    success: function (res, status, xhr) {
                        if (res.code == 0) {
                            callback(res);
                        } else {
                            layer.msg(res.data.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        layer.msg('error');
                    }
                });

            } else {
                $.post(data.form.action, data.field, function (res) {
                    if (res.code == 0) {
                        callback(res);
                    } else {
                        layer.msg(res.data.message);
                    }
                }, 'json');
            }
        },
        in_array: function (val, ary) {
            for (var k in ary) {
                if (ary[k] == val) {
                    return true;
                }
            }
            return false;
        },
        logout: function () {
            var $this = this;
            this.post('/passport/logout/', {}, function () {
                $this.redirect('/');
            });
        },
        second: function (sec) {
            sec = Math.ceil(sec);
            var minute = Math.floor(sec / 60);
            var second = sec % 60;
            return util.digit(minute, 2) + ":" + util.digit(second, 2);
        },
        chat: function (customer_id, robot_id) {
            this.page('customer/chat.html', function () {
                choose_firend(customer_id, robot_id);
            });
        },
        wechat: function (wechat_user_id, wechat_id) {
            this.page('control/wechat.html', function () {
                choose_firend(wechat_user_id, wechat_id);
            });
        },
        tencent_map: function (x, y, elem) {
            var center = new qq.maps.LatLng(x, y);
            var map = new qq.maps.Map(elem, {
                disableDefaultUI: true,
                center: center,
                zoom: 16
            });
            var marker = new qq.maps.Marker({
                map: map,
                position: map.getCenter()
            });
        },
        refresh_dot: function (menu) {
            var dot = 0;
            $('#' + menu).find('.layui-badge').each(function (index, elem) {
                dot += parseInt($(elem).html());
            });
            $('#' + menu + '-dot').html('');
            $('#' + menu + '-dot').hide();
            if (dot <= 0) {

            } else {
                $('#' + menu + '-dot').show();
            }
        },
        download: function (url) {
            $("<iframe style='display: none;' src='" + url + "'></iframe>").prependTo('body');
        },
        week: function (v) {
            var str = '';
            if (v == 1) {
                str = '一';
            } else if (v == 2) {
                str = '二';
            } else if (v == 3) {
                str = '三';
            } else if (v == 4) {
                str = '四';
            } else if (v == 5) {
                str = '五';
            } else if (v == 6) {
                str = '六';
            } else if (v == 7) {
                str = '日';
            }
            return str;
        },
        timeframe: function (v) {
            var str = '';
            if (v == 1) {
                str = '上午';
            } else if (v == 2) {
                str = '下午';
            } else if (v == 3) {
                str = '晚上';
            }
            return str;
        },
        notification: function (title, content, iconUrl) {

            if (window.webkitNotifications) {
                //chrome老版本
                if (window.webkitNotifications.checkPermission() == 0) {
                    var notif = window.webkitNotifications.createNotification(iconUrl, title, content);
                    notif.display = function () {
                    }
                    notif.onerror = function () {
                    }
                    notif.onclose = function () {
                    }
                    notif.onclick = function () {
                        this.cancel();
                    }
                    notif.replaceId = 'Meteoric';
                    notif.show();
                } else {
                    window.webkitNotifications.requestPermission($jy.notify);
                }
            } else if ("Notification" in window) {
                // 判断是否有权限
                if (Notification.permission === "granted") {
                    var notification = new Notification(title, {
                        "icon": iconUrl,
                        "body": content,
                    });
                } else if (Notification.permission !== 'denied') {  //如果没权限，则请求权限
                    Notification.requestPermission(function (permission) {
                        // Whatever the user answers, we make sure we store the
                        // information
                        if (!('permission' in Notification)) {
                            Notification.permission = permission;
                        }
                        //如果接受请求
                        if (permission === "granted") {
                            var notification = new Notification(title, {
                                "icon": iconUrl,
                                "body": content,
                            });
                        }
                    });
                }
            }
        }
    });
});