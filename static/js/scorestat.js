var arr = [];

function showDetails(ii) {
    $('.ui.modal').modal('show');
    $("#utitle").html(dep[ii]);
    $("#ucon").html(details[ii]);
}

function exportcsv() {
    window.location.href = "/export.php?key=" + encodeURIComponent(JSON.stringify(arr));
}

$(document).ready(function() {
    $.post("/api/statistics", null,
        function(res){
            if (res.code == 20000) {
                alert("Error(20000): 运行环境改变，需要重新登录。");
                window.location.href = "/";
                return;
            } else if (res.code != 0) {
                alert("Error(" + res.code + "): " + res.message);
                window.location.href = "/test";
                return;
            }
            var D1 = '<div class="table-responsive">' +
                '<table class="table table-condensed">' +
                '<thead>' +
                '<tr><td></td><td>学院</td><td>人数</td><td>完成人数</td><td>平均分</td><td>平均用时</td><td>最高分</td><td>最低分</td><td>最高用时</td><td>最低用时</td></tr>' +
                '</thead><tbody>' ;
            var ii = 0;
                $.each(res.data, function(key, val) {
                    if (key == 'all') key = '<font color="#00F">共计</font>';
                    if (val.finished <= 0) return;
                    D1 += '<tr><td>' + (++ii) + '</td><td onclick="showDetails(' + ii + ')">' + key + '</td><td>' + val.count + '</td><td>' + val.finished + '</td><td>' + val.averagescore + '</td><td>' + val.averagetime + '</td><td>' + val.maxscore + '</td><td>' + val.minscore + '</td><td>' + val.maxtime + '</td><td>' + val.mintime + '</td></tr>';
                    var DT = "<table class=\"table table-condensed\"><tr><th>分数</th><th>人数</th></tr>";
                    if (val.scoresection) {
                        $.each(
                            val.scoresection, function (k1, v1) {
                                DT += "<tr><td>" + k1 + "</td><td>" + v1 + "</td></tr>";
                            }
                        );
                    }
                    DT += '</table>';
                    dep[ii] = key;
                    details[ii] = DT;
                });
                D1 += '</tbody></table>' +
                '</div>';
            $('#scorestats').html(D1);
        }
        , "json");
    $.post("/api/score_list", null,
        function(res){
            if (res.code == 20000) {
                alert("Error(20000): 运行环境改变，需要重新登录。");
                window.location.href = "/";
                return;
            } else if (res.code != 0) {
                alert("Error(" + res.code + "): " + res.message);
                window.location.href = "/test";
                return;
            }
            var ii = 0;
            var D1 = '<div class="table-responsive">' +
                '<table class="table table-condensed">' +
                '<thead>' +
                '<tr><td></td><td>学号</td><td>姓名</td><td>得分</td><td>时间</td><td>状态</td></tr>' +
                '</thead><tbody>' ;
            $.each(res.data, function(key, val) {
                if(val.score) {
                    D1 += '<tr><td>' + (++ii) + '</td><td>' + val.studentid + '</td><td>' + val.name + '</td><td>' + val.score + '</td><td>' + val.time + '</td><td style="color:#0F0">' + val.status + '</td></tr>';
                    arr.push([val.studentid, val.name, val.score, val.status]);
                } else {
                    D1 += '<tr class="unfinished"><td>' + (++ii) + '</td><td>' + val.studentid + '</td><td>' + val.name + '</td><td></td><td></td><td style="color:#F00">' + val.status + '</td></tr>';
                }
            });
            D1 += '</tbody></table></div>';
            $('#scorelistsin').html(D1);
        }
        , "json");
});