layui.define(['jquery', 'layer', 'util'], function (exports) {
    var $ = layui.$;
    var layer = layui.layer;
    var util = layui.util;
    exports('emot', {
        config: [
            {'key': ['/::)', '[微笑]'], 'icon': '<i class="qq_face qqface0"></i>'},
            {'key': ['/::~', '[撇嘴]'], 'icon': '<i class="qq_face qqface1"></i>'},
            {'key': ['/::B', '[色]'], 'icon': '<i class="qq_face qqface2"></i>'},
            {'key': ['/::|', '[发呆]'], 'icon': '<i class="qq_face qqface3"></i>'},
            {'key': ['/:8-)', '[得意]'], 'icon': '<i class="qq_face qqface4"></i>'},
            {'key': ['/::<', '[流泪]'], 'icon': '<i class="qq_face qqface5"></i>'},
            {'key': ['/::$', '[害羞]'], 'icon': '<i class="qq_face qqface6"></i>'},

            {'key': ['/::X', '[闭嘴]'], 'icon': '<i class="qq_face qqface7"></i>'},
            {'key': ['/::Z', '[睡]'], 'icon': '<i class="qq_face qqface8"></i>'},
            {'key': ["/::\\'(", '[大哭]'], 'icon': '<i class="qq_face qqface9"></i>'},
            {'key': ['/::-|', '[尴尬]'], 'icon': '<i class="qq_face qqface10"></i>'},
            {'key': ['/::@', '[发怒]'], 'icon': '<i class="qq_face qqface11"></i>'},
            {'key': ['/::P', '[调皮]'], 'icon': '<i class="qq_face qqface12"></i>'},
            {'key': ['/::D', '[呲牙]'], 'icon': '<i class="qq_face qqface13"></i>'},

            {'key': ['/::O', '[惊讶]'], 'icon': '<i class="qq_face qqface14"></i>'},
            {'key': ['/::(', '[难过]'], 'icon': '<i class="qq_face qqface15"></i>'},
            {'key': ['/::+', '[酷]'], 'icon': '<i class="qq_face qqface16"></i>'},
            {'key': ['[囧]', '[冷汗]'], 'icon': '<i class="qq_face qqface17"></i>'},
            {'key': ['/::Q', '[抓狂]'], 'icon': '<i class="qq_face qqface18"></i>'},
            {'key': ['/::T', '[吐]'], 'icon': '<i class="qq_face qqface19"></i>'},
            {'key': ['/:,@P', '[偷笑]'], 'icon': '<i class="qq_face qqface20"></i>'},

            {'key': ['/:,@-D', '[愉快]'], 'icon': '<i class="qq_face qqface21"></i>'},
            {'key': ['/::d', '[白眼]'], 'icon': '<i class="qq_face qqface22"></i>'},
            {'key': ['/:,@o', '[傲慢]'], 'icon': '<i class="qq_face qqface23"></i>'},
            {'key': ['/::g', '[饥饿]'], 'icon': '<i class="qq_face qqface24"></i>'},
            {'key': ['/:|-)', '[困]'], 'icon': '<i class="qq_face qqface25"></i>'},
            {'key': ['/::!', '[惊恐]'], 'icon': '<i class="qq_face qqface26"></i>'},
            {'key': ['/::L', '[流汗]'], 'icon': '<i class="qq_face qqface27"></i>'},
            {'key': ['/::>', '[憨笑]'], 'icon': '<i class="qq_face qqface28"></i>'},

            {'key': ['/::,@', '[悠闲]'], 'icon': '<i class="qq_face qqface29"></i>'},
            {'key': ['/:,@f', '[奋斗]'], 'icon': '<i class="qq_face qqface30"></i>'},
            {'key': ['/::-S', '[咒骂]'], 'icon': '<i class="qq_face qqface31"></i>'},
            {'key': ['/:?', '[疑问]'], 'icon': '<i class="qq_face qqface32"></i>'},
            {'key': ['/:,@x', '[嘘]'], 'icon': '<i class="qq_face qqface33"></i>'},
            {'key': ['/:,@@', '[晕]'], 'icon': '<i class="qq_face qqface34"></i>'},
            {'key': ['/::8', '[疯了]'], 'icon': '<i class="qq_face qqface35"></i>'},
            {'key': ['/:,@!', '[衰]'], 'icon': '<i class="qq_face qqface36"></i>'},

            {'key': ['/:!!!', '[骷髅]'], 'icon': '<i class="qq_face qqface37"></i>'},
            {'key': ['/:xx', '[敲打]'], 'icon': '<i class="qq_face qqface38"></i>'},
            {'key': ['/:bye', '[再见]'], 'icon': '<i class="qq_face qqface39"></i>'},
            {'key': ['/:wipe', '[擦汗]'], 'icon': '<i class="qq_face qqface40"></i>'},
            {'key': ['/:dig', '[抠鼻]'], 'icon': '<i class="qq_face qqface41"></i>'},
            {'key': ['/:handclap', '[鼓掌]'], 'icon': '<i class="qq_face qqface42"></i>'},
            {'key': ['/:&-(', '[糗大了]'], 'icon': '<i class="qq_face qqface43"></i>'},
            {'key': ['/:B-)', '[坏笑]'], 'icon': '<i class="qq_face qqface44"></i>'},

            {'key': ['/:<@', '[左哼哼]'], 'icon': '<i class="qq_face qqface45"></i>'},
            {'key': ['/:@>', '[右哼哼]'], 'icon': '<i class="qq_face qqface46"></i>'},
            {'key': ['/::-O', '[哈欠]'], 'icon': '<i class="qq_face qqface47"></i>'},
            {'key': ['/:>-|', '[鄙视]'], 'icon': '<i class="qq_face qqface48"></i>'},
            {'key': ['/:P-(', '[委屈]'], 'icon': '<i class="qq_face qqface49"></i>'},
            {'key': ["/::\\'|", '[快哭了]'], 'icon': '<i class="qq_face qqface50"></i>'},
            {'key': ['/:X-)', '[阴险]'], 'icon': '<i class="qq_face qqface51"></i>'},
            {'key': ['/::*', '[亲亲]'], 'icon': '<i class="qq_face qqface52"></i>'},

            {'key': ['/:@x', '[吓]'], 'icon': '<i class="qq_face qqface53"></i>'},
            {'key': ['/:8*', '[可怜]'], 'icon': '<i class="qq_face qqface54"></i>'},
            {'key': ['/:pd', '[菜刀]'], 'icon': '<i class="qq_face qqface55"></i>'},
            {'key': ['/:<W>', '[西瓜]'], 'icon': '<i class="qq_face qqface56"></i>'},
            {'key': ['/:beer', '[啤酒]'], 'icon': '<i class="qq_face qqface57"></i>'},
            {'key': ['/:basketb', '[篮球]'], 'icon': '<i class="qq_face qqface58"></i>'},
            {'key': ['/:oo', '[乒乓]'], 'icon': '<i class="qq_face qqface59"></i>'},
            {'key': ['/:coffee', '[咖啡]'], 'icon': '<i class="qq_face qqface60"></i>'},
            {'key': ['/:eat', '[饭]'], 'icon': '<i class="qq_face qqface61"></i>'},
            {'key': ['/:pig', '[猪头]'], 'icon': '<i class="qq_face qqface62"></i>'},
            {'key': ['/:rose', '[玫瑰]'], 'icon': '<i class="qq_face qqface63"></i>'},

            {'key': ['/:fade', '[凋谢]'], 'icon': '<i class="qq_face qqface64"></i>'},
            {'key': ['/:showlove', '[嘴唇]'], 'icon': '<i class="qq_face qqface65"></i>'},
            {'key': ['/:heart', '[爱心]'], 'icon': '<i class="qq_face qqface66"></i>'},
            {'key': ['/:break', '[心碎]'], 'icon': '<i class="qq_face qqface67"></i>'},
            {'key': ['/:cake', '[蛋糕]'], 'icon': '<i class="qq_face qqface68"></i>'},
            {'key': ['/:li', '[闪电]'], 'icon': '<i class="qq_face qqface69"></i>'},
            {'key': ['/:bome', '[炸弹]'], 'icon': '<i class="qq_face qqface70"></i>'},
            {'key': ['/:kn', '[刀]'], 'icon': '<i class="qq_face qqface71"></i>'},
            {'key': ['/:footb', '[足球]'], 'icon': '<i class="qq_face qqface72"></i>'},
            {'key': ['/:ladybug', '[瓢虫]'], 'icon': '<i class="qq_face qqface73"></i>'},

            {'key': ['/:shit', '[便便]'], 'icon': '<i class="qq_face qqface74"></i>'},
            {'key': ['/:moon', '[月亮]'], 'icon': '<i class="qq_face qqface75"></i>'},
            {'key': ['/:sun', '[太阳]'], 'icon': '<i class="qq_face qqface76"></i>'},
            {'key': ['/:gift', '[礼物]'], 'icon': '<i class="qq_face qqface77"></i>'},
            {'key': ['/:hug', '[拥抱]'], 'icon': '<i class="qq_face qqface78"></i>'},
            {'key': ['/:strong', '[强]'], 'icon': '<i class="qq_face qqface79"></i>'},
            {'key': ['/:weak', '[弱]'], 'icon': '<i class="qq_face qqface80"></i>'},
            {'key': ['/:share', '[握手]'], 'icon': '<i class="qq_face qqface81"></i>'},
            {'key': ['/:v', '[胜利]'], 'icon': '<i class="qq_face qqface82"></i>'},
            {'key': ['/:@)', '[抱拳]'], 'icon': '<i class="qq_face qqface83"></i>'},

            {'key': ['/:jj', '[勾引]'], 'icon': '<i class="qq_face qqface84"></i>'},
            {'key': ['/:@@', '[拳头]'], 'icon': '<i class="qq_face qqface85"></i>'},
            {'key': ['/:bad', '[差劲]'], 'icon': '<i class="qq_face qqface86"></i>'},
            {'key': ['/:lvu', '[爱你]'], 'icon': '<i class="qq_face qqface87"></i>'},
            {'key': ['/:no', '[NO]'], 'icon': '<i class="qq_face qqface88"></i>'},
            {'key': ['/:ok', '[OK]'], 'icon': '<i class="qq_face qqface89"></i>'},
            {'key': ['/:love', '[爱情]'], 'icon': '<i class="qq_face qqface90"></i>'},
            {'key': ['/:<L>', '[飞吻]'], 'icon': '<i class="qq_face qqface91"></i>'},
            {'key': ['/:jump', '[跳跳]'], 'icon': '<i class="qq_face qqface92"></i>'},
            {'key': ['/:shake', '[发抖]'], 'icon': '<i class="qq_face qqface93"></i>'},
            {'key': ['/:<O>', '[怄火]'], 'icon': '<i class="qq_face qqface94"></i>'},
            {'key': ['/:circle', '[转圈]'], 'icon': '<i class="qq_face qqface95"></i>'},

            {'key': ['/:kotow', '[磕头]'], 'icon': '<i class="qq_face qqface96"></i>'},
            {'key': ['/:turn', '[回头]'], 'icon': '<i class="qq_face qqface97"></i>'},
            {'key': ['/:skip', '[跳绳]'], 'icon': '<i class="qq_face qqface98"></i>'},
            {'key': ['/:oY', '[投降]'], 'icon': '<i class="qq_face qqface99"></i>'},
            {'key': ['/:#-0', '[激动]'], 'icon': '<i class="qq_face qqface100"></i>'},
            {'key': ['/:hiphot', '[乱舞]'], 'icon': '<i class="qq_face qqface101"></i>'},
            {'key': ['/:kiss', '[献吻]'], 'icon': '<i class="qq_face qqface102"></i>'},
            {'key': ['/:<&', '[左太极]'], 'icon': '<i class="qq_face qqface103"></i>'},
            {'key': ['/:&>', '[右太极]'], 'icon': '<i class="qq_face qqface104"></i>'},


            {'key': [''], 'icon': '<i class="symbol_face symbol0"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol1"></i>'},

            {'key': [''], 'icon': '<i class="symbol_face symbol2"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol3"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol4"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol5"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol6"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol7"></i>'},
            {'key': ['[Hey]'], 'icon': '<i class="symbol_face symbol8"></i>'},

            {'key': ['[Facepalm]'], 'icon': '<i class="symbol_face symbol9"></i>'},
            {'key': ['[Smirk]'], 'icon': '<i class="symbol_face symbol10"></i>'},
            {'key': ['[Smart]'], 'icon': '<i class="symbol_face symbol11"></i>'},
            {'key': ['[Concerned]'], 'icon': '<i class="symbol_face symbol12"></i>'},
            {'key': ['[Yeah!]'], 'icon': '<i class="symbol_face symbol13"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol14"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol15"></i>'},

            {'key': [''], 'icon': '<i class="symbol_face symbol16"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol17"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol18"></i>'},
            {'key': ['[Packet]'], 'icon': '<i class="symbol_face symbol19"></i>'},
            {'key': ['[發]'], 'icon': '<i class="symbol_face symbol20"></i>'},

            {'key': [''], 'icon': '<i class="symbol_face symbol21"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol22"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol23"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol24"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol25"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol26"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol27"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol28"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol29"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol30"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol31"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol32"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol33"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol34"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol35"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol36"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol37"></i>'},

            {'key': [''], 'icon': '<i class="symbol_face symbol38"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol39"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol40"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol41"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol42"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol43"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol43"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol44"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol45"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol46"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol47"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol48"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol49"></i>'},

            {'key': [''], 'icon': '<i class="symbol_face symbol50"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol51"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol52"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol53"></i>'},

            {'key': [''], 'icon': '<i class="symbol_face symbol54"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol55"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol56"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol57"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol58"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol59"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol60"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol61"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol62"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol63"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol64"></i>'},

            {'key': [''], 'icon': '<i class="symbol_face symbol65"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol66"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol67"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol68"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol69"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol70"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol71"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol72"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol73"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol74"></i>'},

            {'key': [''], 'icon': '<i class="symbol_face symbol75"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol76"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol77"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol78"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol79"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol80"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol81"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol82"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol83"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol84"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol85"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol86"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol87"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol88"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol89"></i>'},

            {'key': [''], 'icon': '<i class="symbol_face symbol90"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol91"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol92"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol93"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol94"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol95"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol96"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol97"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol98"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol99"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol100"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol101"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol102"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol103"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol104"></i>'},

            {'key': [''], 'icon': '<i class="symbol_face symbol105"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol106"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol107"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol108"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol109"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol110"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol111"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol112"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol113"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol114"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol115"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol116"></i>'},

            {'key': [''], 'icon': '<i class="symbol_face symbol117"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol118"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol119"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol120"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol121"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol122"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol123"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol124"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol125"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol126"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol127"></i>'},

            {'key': [''], 'icon': '<i class="symbol_face symbol128"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol129"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol130"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol131"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol132"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol133"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol134"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol135"></i>'},

            {'key': [''], 'icon': '<i class="symbol_face symbol136"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol137"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol138"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol139"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol140"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol141"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol142"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol143"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol144"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol145"></i>'},


            {'key': [''], 'icon': '<i class="symbol_face symbol146"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol147"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol148"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol149"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol150"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol151"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol152"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol153"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol154"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol155"></i>'},

            {'key': [''], 'icon': '<i class="symbol_face symbol156"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol157"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol158"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol159"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol160"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol161"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol162"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol163"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol164"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol165"></i>'},

            {'key': [''], 'icon': '<i class="symbol_face symbol166"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol167"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol168"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol169"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol170"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol171"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol172"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol173"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol174"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol175"></i>'},
            {'key': [''], 'icon': '<i class="symbol_face symbol176"></i>'},


            {'key': ['[小狗]'], 'icon': '<i class="symbol_face symbol85"></i>'},

            {'key': ['[Candle]'], 'icon': '[蜡烛]'},

        ],
        parse: function (content) {
            for (var i in this.config) {
                for (var k in this.config[i].key) {
                    while (content.indexOf(this.config[i].key[k]) != -1) {
                        content = content.replace(this.config[i].key[k], this.config[i].icon);
                    }
                }
            }
            return content;
        },
        face: function (e, target, content) {

            if ($(target).attr("build") == undefined) {
                var faceList = ["微笑", "撇嘴", "色", "发呆", "得意", "流泪", "害羞", "闭嘴", "睡", "大哭", "尴尬", "发怒", "调皮", "呲牙", "惊讶", "难过", "酷", "冷汗", "抓狂", "吐", "偷笑", "愉快", "白眼", "傲慢", "饥饿", "困", "惊恐", "流汗", "憨笑", "悠闲", "奋斗", "咒骂", "疑问", "嘘", "晕", "疯了", "衰", "骷髅", "敲打", "再见", "擦汗", "抠鼻", "鼓掌", "糗大了", "坏笑", "左哼哼", "右哼哼", "哈欠", "鄙视", "委屈", "快哭了", "阴险", "亲亲", "吓", "可怜", "菜刀", "西瓜", "啤酒", "篮球", "乒乓", "咖啡", "饭", "猪头", "玫瑰", "凋谢", "嘴唇", "爱心", "心碎", "蛋糕", "闪电", "炸弹", "刀", "足球", "瓢虫", "便便", "月亮", "太阳", "礼物", "拥抱", "强", "弱", "握手", "胜利", "抱拳", "勾引", "拳头", "差劲", "爱你", "NO", "OK", "爱情", "飞吻", "跳跳", "发抖", "怄火", "转圈", "磕头", "回头", "跳绳", "投降", "激动", "乱舞", "献吻", "左太极", "右太极"];
                var x = 0;
                var y = 0;
                for (var i = 0; i < faceList.length; i++) {
                    var div = $("<div class='pic'/>");
                    div.attr("title", faceList[i]);
                    div.click(function (e) {
                        var val = $(content).val() + "[" + $(e.target).attr("title") + "]";
                        $(content).val(val);
                    });
                    var pos = ((x * 28 * -1) - x) + "px " + ((y * 28 * -1) - y) + "px";
                    x++;
                    if ((i + 1) % 15 == 0) {
                        x = 0;
                        y++;
                    }
                    $(div).css("background-position", pos);
                    $(target).append(div);
                }
                $(target).attr("build", 'succeed');
            }

            if ($(target).is(":hidden")) {

                $(target).show();
                $(target).css("opacity", 0);
                var top = ($(e.target).position().top - $(target).height() - 15);
                $(target).css("top", (top + 10) + "px");
                $(target).css("left", ($(e.target).position().left - 5) + "px");
                $(target).animate({top: top, opacity: 1}, 250);

                $(document).click(function (e) {

                    if ($(e.target).hasClass("pic")) return;

                    var top = $(target).position().top;
                    $(target).animate({top: top + 10, opacity: 0}, 250, function () {
                        $(target).hide();
                        $(content).focus();
                    });

                    $(document).unbind('click');
                });

                e.stopPropagation();

            } else {
                if ($(target).position() != undefined) {
                    var top = $(target).position().top;
                    $(target).animate({top: top + 10, opacity: 0}, 250, function () {
                        $(target).hide();
                    });
                }
            }
        },
        symbol: function (e) {
            if ($(".symbol").attr("build") == undefined) {

                var symbolList = {
                    "笑脸": {"type": "symbol", "value": "1f604"},
                    "生病": {"type": "symbol", "value": "1f637"},
                    "破涕为笑": {"type": "symbol", "value": "1f602"},
                    "吐舌": {"type": "symbol", "value": "1f445"},
                    "脸红": {"type": "symbol", "value": "1f633"},
                    "恐惧": {"type": "symbol", "value": "1f631"},
                    "失望": {"type": "symbol", "value": "1f640"},
                    "无语": {"type": "symbol", "value": "1f612"},
                    "嘿哈": {"type": "face", "value": "嘿哈"},
                    "捂脸": {"type": "face", "value": "捂脸"},
                    "奸笑": {"type": "face", "value": "奸笑"},
                    "机智": {"type": "face", "value": "机智"},
                    "皱眉": {"type": "face", "value": "皱眉"},
                    "耶": {"type": "face", "value": "耶"},
                    "鬼魂": {"type": "symbol", "value": "1f47b"},
                    "合十": {"type": "symbol", "value": "1f64f"},
                    "强壮": {"type": "symbol", "value": "1f4aa"},
                    "庆祝": {"type": "symbol", "value": "1f389"},
                    "礼物盒": {"type": "symbol", "value": "1f381"},
                    "红包": {"type": "face", "value": "红包"},
                    "小黄鸡": {"type": "face", "value": "鸡"},
                    "开心": {"type": "symbol", "value": "1f60a"},
                    "大笑": {"type": "symbol", "value": "1f603"},
                    "热情": {"type": "symbol", "value": "263a"},
                    "眨眼": {"type": "symbol", "value": "1f609"},
                    "色": {"type": "symbol", "value": "1f60d"},
                    "接吻": {"type": "symbol", "value": "1f618"},
                    "亲吻": {"type": "symbol", "value": "1f61a"},
                    "露齿笑": {"type": "symbol", "value": "1f63c"},
                    "满意": {"type": "symbol", "value": "1f60c"},
                    "戏弄": {"type": "symbol", "value": "1f61c"},
                    "得意": {"type": "symbol", "value": "1f60f"},
                    "汗": {"type": "symbol", "value": "1f613"},
                    "低落": {"type": "symbol", "value": "1f61e"},
                    "呸": {"type": "symbol", "value": "1f616"},
                    "焦虑": {"type": "symbol", "value": "1f625"},
                    "担心": {"type": "symbol", "value": "1f630"},
                    "震惊": {"type": "symbol", "value": "1f628"},
                    "悔恨": {"type": "symbol", "value": "1f62b"},
                    "眼泪": {"type": "symbol", "value": "1f622"},
                    "哭": {"type": "symbol", "value": "1f62d"},
                    "晕": {"type": "symbol", "value": "1f632"},
                    "心烦": {"type": "symbol", "value": "1f620"},
                    "生气": {"type": "symbol", "value": "1f63e"},
                    "睡觉": {"type": "symbol", "value": "1f62a"},
                    "恶魔": {"type": "symbol", "value": "1f47f"},
                    "外星人": {"type": "symbol", "value": "1f47d"},
                    "心": {"type": "symbol", "value": "2764"},
                    "心碎": {"type": "symbol", "value": "1f494"},
                    "丘比特": {"type": "symbol", "value": "1f498"},
                    "闪烁": {"type": "symbol", "value": "2728"},
                    "星星": {"type": "symbol", "value": "1f31f"},
                    "叹号": {"type": "symbol", "value": "2755"},
                    "问号": {"type": "symbol", "value": "2754"},
                    "睡着": {"type": "symbol", "value": "1f4a4"},
                    "水滴": {"type": "symbol", "value": "1f4a6"},
                    "音乐": {"type": "symbol", "value": "1f3b5"},
                    "火": {"type": "symbol", "value": "1f525"},
                    "便便": {"type": "symbol", "value": "1f4a9"},
                    "强": {"type": "symbol", "value": "1f44d"},
                    "弱": {"type": "symbol", "value": "1f44e"},
                    "拳头": {"type": "symbol", "value": "1f44a"},
                    "胜利": {"type": "symbol", "value": "270c"},
                    "上": {"type": "symbol", "value": "1f446"},
                    "下": {"type": "symbol", "value": "1f447"},
                    "右": {"type": "symbol", "value": "1f449"},
                    "左": {"type": "symbol", "value": "1f448"},
                    "第一": {"type": "symbol", "value": "261d"},
                    "吻": {"type": "symbol", "value": "1f48f"},
                    "热恋": {"type": "symbol", "value": "1f491"},
                    "男孩": {"type": "symbol", "value": "1f466"},
                    "女孩": {"type": "symbol", "value": "1f467"},
                    "女士": {"type": "symbol", "value": "1f469"},
                    "男士": {"type": "symbol", "value": "1f468"},
                    "天使": {"type": "symbol", "value": "1f47c"},
                    "骷髅": {"type": "symbol", "value": "1f480"},
                    "红唇": {"type": "symbol", "value": "1f48b"},
                    "太阳": {"type": "symbol", "value": "2600"},
                    "下雨": {"type": "symbol", "value": "2614"},
                    "多云": {"type": "symbol", "value": "2601"},
                    "雪人": {"type": "symbol", "value": "26c4"},
                    "月亮": {"type": "symbol", "value": "1f319"},
                    "闪电": {"type": "symbol", "value": "26a1"},
                    "海浪": {"type": "symbol", "value": "1f30a"},
                    "猫": {"type": "symbol", "value": "1f431"},
                    "小狗": {"type": "symbol", "value": "1f429"},
                    "老鼠": {"type": "symbol", "value": "1f42d"},
                    "仓鼠": {"type": "symbol", "value": "1f439"},
                    "兔子": {"type": "symbol", "value": "1f430"},
                    "狗": {"type": "symbol", "value": "1f43a"},
                    "青蛙": {"type": "symbol", "value": "1f438"},
                    "老虎": {"type": "symbol", "value": "1f42f"},
                    "考拉": {"type": "symbol", "value": "1f428"},
                    "熊": {"type": "symbol", "value": "1f43b"},
                    "猪": {"type": "symbol", "value": "1f437"},
                    "牛": {"type": "symbol", "value": "1f42e"},
                    "野猪": {"type": "symbol", "value": "1f417"},
                    "猴子": {"type": "symbol", "value": "1f435"},
                    "马": {"type": "symbol", "value": "1f434"},
                    "蛇": {"type": "symbol", "value": "1f40d"},
                    "鸽子": {"type": "symbol", "value": "1f426"},
                    "鸡": {"type": "symbol", "value": "1f414"},
                    "企鹅": {"type": "symbol", "value": "1f427"},
                    "毛虫": {"type": "symbol", "value": "1f41b"},
                    "章鱼": {"type": "symbol", "value": "1f419"},
                    "鱼": {"type": "symbol", "value": "1f420"},
                    "鲸鱼": {"type": "symbol", "value": "1f433"},
                    "海豚": {"type": "symbol", "value": "1f42c"},
                    "玫瑰": {"type": "symbol", "value": "1f339"},
                    "花": {"type": "symbol", "value": "1f33a"},
                    "棕榈树": {"type": "symbol", "value": "1f334"},
                    "仙人掌": {"type": "symbol", "value": "1f335"},
                    "礼盒": {"type": "symbol", "value": "1f49d"},
                    "南瓜灯": {"type": "symbol", "value": "1f383"},
                    "圣诞老人": {"type": "symbol", "value": "1f385"},
                    "圣诞树": {"type": "symbol", "value": "1f384"},
                    "铃": {"type": "symbol", "value": "1f514"},
                    "气球": {"type": "symbol", "value": "1f388"},
                    "CD": {"type": "symbol", "value": "1f4bf"},
                    "相机": {"type": "symbol", "value": "1f4f7"},
                    "录像机": {"type": "symbol", "value": "1f3a5"},
                    "电脑": {"type": "symbol", "value": "1f4bb"},
                    "电视": {"type": "symbol", "value": "1f4fa"},
                    "电话": {"type": "symbol", "value": "1f4de"},
                    "解锁": {"type": "symbol", "value": "1f513"},
                    "锁": {"type": "symbol", "value": "1f512"},
                    "钥匙": {"type": "symbol", "value": "1f511"},
                    "成交": {"type": "symbol", "value": "1f528"},
                    "灯泡": {"type": "symbol", "value": "1f4a1"},
                    "邮箱": {"type": "symbol", "value": "1f4eb"},
                    "浴缸": {"type": "symbol", "value": "1f6c0"},
                    "钱": {"type": "symbol", "value": "1f4b2"},
                    "炸弹": {"type": "symbol", "value": "1f4a3"},
                    "手枪": {"type": "symbol", "value": "1f52b"},
                    "药丸": {"type": "symbol", "value": "1f48a"},
                    "橄榄球": {"type": "symbol", "value": "1f3c8"},
                    "篮球": {"type": "symbol", "value": "1f3c0"},
                    "足球": {"type": "symbol", "value": "26bd"},
                    "棒球": {"type": "symbol", "value": "26be"},
                    "高尔夫": {"type": "symbol", "value": "26f3"},
                    "奖杯": {"type": "symbol", "value": "1f3c6"},
                    "入侵者": {"type": "symbol", "value": "1f47e"},
                    "唱歌": {"type": "symbol", "value": "1f3a4"},
                    "吉他": {"type": "symbol", "value": "1f3b8"},
                    "比基尼": {"type": "symbol", "value": "1f459"},
                    "皇冠": {"type": "symbol", "value": "1f451"},
                    "雨伞": {"type": "symbol", "value": "1f302"},
                    "手提包": {"type": "symbol", "value": "1f45c"},
                    "口红": {"type": "symbol", "value": "1f484"},
                    "戒指": {"type": "symbol", "value": "1f48d"},
                    "钻石": {"type": "symbol", "value": "1f48e"},
                    "咖啡": {"type": "symbol", "value": "2615"},
                    "啤酒": {"type": "symbol", "value": "1f37a"},
                    "干杯": {"type": "symbol", "value": "1f37b"},
                    "鸡尾酒": {"type": "symbol", "value": "1f377"},
                    "汉堡": {"type": "symbol", "value": "1f354"},
                    "薯条": {"type": "symbol", "value": "1f35f"},
                    "意面": {"type": "symbol", "value": "1f35d"},
                    "寿司": {"type": "symbol", "value": "1f363"},
                    "面条": {"type": "symbol", "value": "1f35c"},
                    "煎蛋": {"type": "symbol", "value": "1f373"},
                    "冰激凌": {"type": "symbol", "value": "1f366"},
                    "蛋糕": {"type": "symbol", "value": "1f382"},
                    "苹果": {"type": "symbol", "value": "1f34f"},
                    "飞机": {"type": "symbol", "value": "2708"},
                    "火箭": {"type": "symbol", "value": "1f680"},
                    "自行车": {"type": "symbol", "value": "1f6b2"},
                    "高铁": {"type": "symbol", "value": "1f684"},
                    "警告": {"type": "symbol", "value": "26a0"},
                    "旗": {"type": "symbol", "value": "1f3c1"},
                    "男人": {"type": "symbol", "value": "1f6b9"},
                    "女人": {"type": "symbol", "value": "1f6ba"},
                    "O": {"type": "symbol", "value": "2b55"},
                    "X": {"type": "symbol", "value": "274e"},
                    "版权": {"type": "symbol", "value": "a9"},
                    "注册商标": {"type": "symbol", "value": "ae"},
                    "商标": {"type": "symbol", "value": "2122"}
                };
                var symbolCode = {
                    "1f64f": "",
                    "1f604": "",
                    "1f60a": "",
                    "1f603": "",
                    "263a": "",
                    "1f609": "",
                    "1f60d": "",
                    "1f618": "",
                    "1f61a": "",
                    "1f633": "",
                    "1f63c": "",
                    "1f60c": "",
                    "1f61c": "",
                    "1f445": "",
                    "1f612": "",
                    "1f60f": "",
                    "1f613": "",
                    "1f640": "",
                    "1f61e": "",
                    "1f616": "",
                    "1f625": "",
                    "1f630": "",
                    "1f628": "",
                    "1f62b": "",
                    "1f622": "",
                    "1f62d": "",
                    "1f602": "",
                    "1f632": "",
                    "1f631": "",
                    "1f620": "",
                    "1f63e": "",
                    "1f62a": "",
                    "1f637": "",
                    "1f47f": "",
                    "1f47d": "",
                    2764: "",
                    "1f494": "",
                    "1f498": "",
                    2728: "",
                    "1f31f": "",
                    2755: "",
                    2754: "",
                    "1f4a4": "",
                    "1f4a6": "",
                    "1f3b5": "",
                    "1f525": "",
                    "1f4a9": "",
                    "1f44d": "",
                    "1f44e": "",
                    "1f44a": "",
                    "270c": "",
                    "1f446": "",
                    "1f447": "",
                    "1f449": "",
                    "1f448": "",
                    "261d": "",
                    "1f4aa": "",
                    "1f48f": "",
                    "1f491": "",
                    "1f466": "",
                    "1f467": "",
                    "1f469": "",
                    "1f468": "",
                    "1f47c": "",
                    "1f480": "",
                    "1f48b": "",
                    2600: "",
                    2614: "",
                    2601: "",
                    "26c4": "",
                    "1f319": "",
                    "26a1": "",
                    "1f30a": "",
                    "1f431": "",
                    "1f429": "",
                    "1f42d": "",
                    "1f439": "",
                    "1f430": "",
                    "1f43a": "",
                    "1f438": "",
                    "1f42f": "",
                    "1f428": "",
                    "1f43b": "",
                    "1f437": "",
                    "1f42e": "",
                    "1f417": "",
                    "1f435": "",
                    "1f434": "",
                    "1f40d": "",
                    "1f426": "",
                    "1f414": "",
                    "1f427": "",
                    "1f41b": "",
                    "1f419": "",
                    "1f420": "",
                    "1f433": "",
                    "1f42c": "",
                    "1f339": "",
                    "1f33a": "",
                    "1f334": "",
                    "1f335": "",
                    "1f49d": "",
                    "1f383": "",
                    "1f47b": "",
                    "1f385": "",
                    "1f384": "",
                    "1f381": "",
                    "1f514": "",
                    "1f389": "",
                    "1f388": "",
                    "1f4bf": "",
                    "1f4f7": "",
                    "1f3a5": "",
                    "1f4bb": "",
                    "1f4fa": "",
                    "1f4de": "",
                    "1f513": "",
                    "1f512": "",
                    "1f511": "",
                    "1f528": "",
                    "1f4a1": "",
                    "1f4eb": "",
                    "1f6c0": "",
                    "1f4b2": "",
                    "1f4a3": "",
                    "1f52b": "",
                    "1f48a": "",
                    "1f3c8": "",
                    "1f3c0": "",
                    "26bd": "",
                    "26be": "",
                    "26f3": "",
                    "1f3c6": "",
                    "1f47e": "",
                    "1f3a4": "",
                    "1f3b8": "",
                    "1f459": "",
                    "1f451": "",
                    "1f302": "",
                    "1f45c": "",
                    "1f484": "",
                    "1f48d": "",
                    "1f48e": "",
                    2615: "",
                    "1f37a": "",
                    "1f37b": "",
                    "1f377": "",
                    "1f354": "",
                    "1f35f": "",
                    "1f35d": "",
                    "1f363": "",
                    "1f35c": "",
                    "1f373": "",
                    "1f366": "",
                    "1f382": "",
                    "1f34f": "",
                    2708: "",
                    "1f680": "",
                    "1f6b2": "",
                    "1f684": "",
                    "26a0": "",
                    "1f3c1": "",
                    "1f6b9": "",
                    "1f6ba": "",
                    "2b55": "",
                    "274e": "",
                    a9: "",
                    ae: "",
                    2122: ""
                };

                var i = 0;
                var x = 0;
                var y = 0;

                for (var name in symbolList) {
                    var div = $("<div class='pic'/>");
                    $(div).attr("title", name);
                    $(div).click(function (e) {
                        if (symbolList[$(e.target).attr("title")].type == 'symbol') {
                            var val = $("#content").val() + "" + symbolCode[symbolList[$(e.target).attr("title")].value];
                            $("#content").val(val);
                        } else {
                            var val = $("#content").val() + "[" + symbolList[$(e.target).attr("title")].value + "]";
                            $("#content").val(val);
                        }
                    });
                    var pos = ((x * 31 * -1) - x + 2) + "px " + ((y * 31 * -1) - y + 3) + "px";
                    x++;
                    if ((i + 1) % 15 == 0) {
                        x = 0;
                        y++;
                    }
                    $(div).css("background-position", pos);
                    $(".symbol").append(div);
                    i++;
                }
                $(".symbol").attr("build", "succeed");
            }
            if ($(".symbol").is(":hidden")) {
                $(".symbol").show();
                $(".symbol").css("opacity", 0);
                var top = ($(e.target).position().top - $(".symbol").height() - 10);
                $(".symbol").css("top", (top + 10) + "px");
                $(".symbol").css("left", ($(e.target).position().left - 5) + "px");
                $(".symbol").animate({top: top, opacity: 1}, 250);

                $(document).one("click", function () {
                    var top = $(".symbol").position().top;
                    $(".symbol").animate({top: top + 10, opacity: 0}, 250, function () {
                        $(".symbol").hide();
                    });
                });
                e.stopPropagation();
            } else {
                var top = $(".symbol").position().top;
                $(".symbol").animate({top: top + 10, opacity: 0}, 250, function () {
                    $(".symbol").hide();
                });
            }
        }
    });
});
