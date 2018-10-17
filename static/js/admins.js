var arrInfo = [];
var arrModal = [];

function showInfo(a) {
    alert(arrInfo[a]);
}

function showModal(a) {
    $('.ui.modal').modal('show');
    $("#ucon").html(arrModal[a]);
}

function dealAppeal(a) {
    $.post("/api/deal_appeal", {"number":a},
        function(res) {
            if (res.code == 20000) {
                alert("Error(20000): 运行环境改变，需要重新登录。");
                window.location.href = "/";
                return;
            } else if (res.code != 0) {
                alert("Error(" + res.code + "): " + res.message);
                return;
            } else {
                alert("处理申诉成功~");
            }

        }
        , "json");
    $.post("/api/finish_appeal", {"result":a},
        function(res) {
            if (res.code == 20000) {
                alert("Error(20000): 运行环境改变，需要重新登录。");
                window.location.href = "/";
                return;
            } else if (res.code != 0) {
                alert("Error(" + res.code + "): " + res.message);
                return;
            } else {

            }

        }
        , "json");
}

$(document).ready(function() {
    $.post("/api/get_appeals", null,
        function(res) {
            if (res.code == 20000) {
                alert("Error(20000): 运行环境改变，需要重新登录。");
                window.location.href = "/";
                return;
            } else if (res.code != 0) {
                alert("Error(" + res.code + "): " + res.message);
                return;
            }
            arrInfo = [];
            var resS = "<table class=\"table table-condensed\">" +
                "<tr><td>ID</td><td>账户</td><td>信息</td><td></td><td></td></tr>";
            $.each(res.data, function(ind, val) {
                resS += "<tr>";
                resS += "<td>" + val.id + "</td>";
                resS += "<td>" + val.account + "</td>";
                resS += "<td>" + val.message + "</td>";
                // resS += "<td><a href='#' onclick='showInfo(" + ind + ");'>信息</a></td>";
                resS += "<td><a href='#' onclick='showModal(" + ind + ");'>日志</a></td>";
                resS += "<td><a href='#' onclick='dealAppeal(" + val.id + ");'>处理</a></td>";
                arrInfo.push(val.extra);
                var t = "";
                if (val.logs) {
                    $.each(val.logs, function (ind, val) {
                        t += val + "<br>";
                    });
                }
                arrModal.push(t);
                resS += "</tr>";
            });
            resS += "</table>";
            $("#dealappeal").html(resS);
        }
    , "json");
});


function createacc() {
    var accname = $("#account").val();
    var accpass = $("#pass").val();
    $.post("/api/create_account", {"name":accname, "newpassword":accpass },
        function(res) {
            if (res.code == 20000) {
                alert("Error(20000): 运行环境改变，需要重新登录。");
                window.location.href = "/";
                return;
            } else if (res.code != 0) {
                alert("Error(" + res.code + "): " + res.message);
                return;
            }
            alert(res.message);
        }, "json");
}