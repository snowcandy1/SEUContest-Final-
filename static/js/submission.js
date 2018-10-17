$(document).ready(function() {
    $.post("/api/finish_test", null,
        function(res) {
            if (res.code == 20000) {
                alert("Error(20000): 运行环境改变，需要重新登录。");
                window.location.href = "/";
                return;
            } else if (res.code != 0) {
                alert("Error(" + res.code + "): " + res.message);
            }
            $("#res").html(res.message);
        }, "json");
    $.post("/api/check_status", null,
        function(res) {
            if (res.code == 20000) {
                alert("Error(20000): 运行环境改变，需要重新登录。");
                window.location.href = "/";
                return;
            } else if (res.code != 0) {
                alert("Error(" + res.code + "): " + res.message);
                window.location.href = "/test";
                return;
            }
            alert("您的分数为 " + res.data.score + " 分");
            $("#resscore").html(res.data.score);
        }, "json");

});