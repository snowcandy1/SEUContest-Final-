function add0(m){return m<10?'0'+m:m }
function toDate(ts) {
    var time = new Date(parseInt(ts)*1000);
    var y = time.getFullYear();
    var m = time.getMonth()+1;
    var d = time.getDate();
    var h = time.getHours();
    var mm = time.getMinutes();
    var s = time.getSeconds();
    return y+'-'+add0(m)+'-'+add0(d)+' '+add0(h)+':'+add0(mm)+':'+add0(s);
}

$(document).ready(function() {
    console.log("其实最开始我设计的MySQL是支持多场考试的，不过这个只支持一场考试，所以那个TestSelection我就阉割了=。=");
    $.post("/api/check_status", null,
        function(res){
            if (res.code != 0) {
                alert("Error(" + res.code + "): " + res.message);
                window.location.href="/";
                return;
            }
            var status = res.data.status;
            if (status == 100) {
                $("#time_start").html(toDate(res.data.servertime));
                $("#time_end").html("Not_started");
                $("#now_status").html("Public");
                $("#now_status").css('color', 'green');
                $("#enter").html('<a href="#" onclick="starttest()">进入</a>');
            } else if (status == 200 && res.data.starttime + 1800 > res.data.servertime) {
                $("#time_start").html(toDate(res.data.starttime));
                $("#time_end").html(toDate(res.data.starttime + 1800));
                $("#now_status").html("Running");
                $("#now_status").css('color', 'blue');
            } else if (status == 200) {
                $("#time_start").html(toDate(res.data.starttime));
                $("#time_end").html(toDate(res.data.starttime + 1800));
                $("#now_status").html("Exceeded");
                $("#now_status").css('color', 'red');
                $("#enter").html('<a href="#" onclick="conf()">进入</a>');
            } else {
                $("#time_start").html(toDate(res.data.starttime));
                $("#time_end").html(toDate(res.data.submittime));
                $("#scores").html(res.data.score);
                $("#now_status").html("Ended");
                $("#now_status").css('color', 'red');
                $("#enter").html('<a href="#" onclick="alert(\'已提交，试卷无法修改！\')">进入</a>');
            }
        },"json");
})

function conf() {
    var cf = confirm("您已超时，试卷将无法修改。\n请问是否进行提交，若取消提交，将更换试卷。");
    if (cf) {
        window.location.href = "/stu_submission";
    } else {
        var cf2 = confirm("确定要更换试卷吗？");
        if (cf2) {
            $.post("/api/change_test", null, function (res) {
                if (res.code != 0) {
                    alert("Error(" + res.code + "): " + res.message);
                    window.location.href = "/test";
                    return;
                }
                alert("试卷已更换，请刷新本页面，进入考试。");
            }, "json");
        }
    }
}

function starttest() {
    var cf = confirm("确定要开始考试吗？点下“确定”后开始计时");
    if (cf) {
        window.location.href = "/quiz";
    }
}