function userLogin(uname, upass) {
    $.post("/api/login", { "account": uname, "password": upass },
        function(data){
            alert("(" + data.code + ")" + data.message);
            if (data.code == 0) {
                if (data.data.auth == "student") {
                    window.location.href="/test";
                } else if (data.data.auth == "admin") {
                    window.location.href="/panel";
                } else {
                    window.location.href="/stats";
                }

            } else if (data.code == 10000) {
                window.location.href="/userappeal";
            } else {
                window.location.href="/";
            }
        },"json");
}

function userAppeal() {
    var username = document.getElementById('uname').value;
    var usercon = document.getElementById('umess').value;
    $.post("/api/appeal", { "account": username, "content": usercon },
        function(data){
            alert("(" + data.code + ")" + data.message);
            alert("你的申诉回执编号为：" + data.data);
            $("#showresu1t").html("你的申诉回执编号：" + data.data + "，查询时请务必记住。");
        },"json");
}
function queryAppeal() {
    var username = document.getElementById('unames').value;
    var num = document.getElementById('unum').value;
    $.post("/api/query_appeal", { "account": username, "number": num },
        function(res){
            if (res.code != 0) {
                alert(res.message);
                return;
            }
            var resp;
            resp = "回执编号：" + res.data.id + "<br>";
            resp += "申诉账号：" + res.data.account + "<br>";
            resp += "申诉历史：";
            res.data.logs.forEach(function(val){
               resp += "<br>" + val;
            });
            $("#showresult").html(resp);
        },"json");
}