var optarray = ['0', 'A', 'B', 'C', 'D', 'E', 'F', 'G'];
var MAXN = 200;
var nowProb = 0; var probCnt = 0;
var answer = 0;
var tmpArray = new Array(MAXN);

function formatSeconds(value) {
    var theTime = parseInt(value);// 秒
    var theTime1 = 0;// 分
    var theTime2 = 0;// 小时
// alert(theTime);
    if(theTime > 60) {
        theTime1 = parseInt(theTime/60);
        theTime = parseInt(theTime%60);
        if(theTime1 > 60) {
            theTime2 = parseInt(theTime1/60);
            theTime1 = parseInt(theTime1%60);
        }
    }
    var result = ""+parseInt(theTime)+"";
    if(theTime1 > 0) {
        result = ""+parseInt(theTime1)+":"+result;
    }
    if(theTime2 > 0) {
        result = ""+parseInt(theTime2)+":"+result;
    }
    return result;
}

function quizTime() {
    $("#lastTime").html("考试时间：" + formatSeconds(new Date() / 1000 - ts) + " / 30:00");
}

setInterval("quizTime()", 1000);

function loadAns() {
    answer = getCookie('myAnswer');
    if (answer == 0) {
        answer = new Array(MAXN);
        $.each(tmpArray, function(q, a) {
            if (!q || !a) return;
            $("#P_" + q + "_" + a).css("text-shadow", "0px 0px 2px #00f");
            $("#href" + q).css("text-shadow", "0px 0px 2px #0f0");
            answer[q] = parseInt(a);
        })
        setCookie('myAnswer', JSON.stringify(answer));
        // 如题目超限，可以继续修改
    } else {
        answer = JSON.parse(answer);
        $.each(answer, function(q, a) {
            if (!q || !a) return;
            $("#P_" + q + "_" + a).css("text-shadow", "0px 0px 2px #00f");
            $("#href" + q).css("text-shadow", "0px 0px 2px #0f0");
        })
    }
    if (nowProb > 0) goProb(nowProb);
    tmpArray = answer.slice(0);
    si = clearInterval(si);
    return;
}

var si = setInterval("loadAns()", 1000);

var ts = 0;

$(document).ready(function() {
    $.post("/api/load_test", null,
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
            // 加载试题
            var X='<div id="Problem_0">' +
                '<h1>欢迎进入校史知识竞赛</h1>' +
                '<h4>题目均为单选题、判断题，在考试中，点击选项或用键盘输入，变蓝即为选中，后台将会自动保存。</h4>' +
                '<h4 style="color:red">你可以使用键盘的左右键或点击按钮来切换题目。</h4>' +
                '<h4>如果选项无法变蓝，请点击其他选项，再点回来，如持续出现问题，请联系管理员，祝考试愉快！</h4>' +
                '<button class="ui blue button" onclick="goNext()">开始考试</button>' +
                '</div>';
            var Y = '';
            $.each(res.data, function(ind, val){
                if (ind <= 0) return;
                if (!val.question) return;
               X += '<div hidden id="Problem_' + ind + '">';
               X += '<p style="font-size:170%">' + ind + '. ' + val.question + '</p>';
               if (val.category == 1) {
                   $.each(val.options, function(iid, opt){
                       if (iid <= 0) return;
                       X += '<p id="P_' + ind + '_' + iid + '" style="font-size:120%" onclick="setAnswer(' + ind + ', ' + iid + ')">' + optarray[iid] + '. ' + opt + '</p>';
                   });
               } else {
                   X += '<p id="P_' + ind + '_1" style="font-size:120%" onclick="setAnswer(' + ind + ', 1)">A. 正确</p>';
                   X += '<p id="P_' + ind + '_2" style="font-size:120%" onclick="setAnswer(' + ind + ', 2)">B. 错误</p>';
               }
                X+= '<button class="ui red button" onclick="goLast()">上一题</button>';
                X+= '<button class="ui blue button" onclick="goNext()">下一题</button>';
               X += '</div>';
               Y += '<p><a id="href' + ind + '" href="#Problem' + ind + '" onclick="goProb(' + ind + ')">Problem. ' + ind + '</a></p>'
                probCnt++;
            });
            nowProb = getCookie('nowProb');

            $("#probsheet").html(X);
            $("#lists").html(Y);
        }
    , "json");
    $.post("/api/check_status", null,
        function(res){
            if (res.code == 20000) {
                alert("Error(20000): 运行环境改变，需要重新登录。");
                window.location.href="/";
                return;
            }
            ts = res.data.starttime;
            tmpArray = res.data.answersheet;
        }
    , "json");
});


function goNext() {
    if (nowProb >= probCnt) {
        alert("已经是最后一题了！");
        return;
    }
    $("#Problem_" + nowProb).hide(500);
    nowProb++;
    $("#Problem_" + nowProb).show(500);
}

function goLast() {
    if (nowProb <= 0) {
        return;
    }
    $("#Problem_" + nowProb).hide(500);
    nowProb--;
    $("#Problem_" + nowProb).show(500);
}

function goProb(p) {
    if (p <= 0 || p > probCnt) {
        return;
    }
    $("#Problem_" + nowProb).hide(500);
    nowProb = p;
    $("#Problem_" + nowProb).show(500);
}

document.onkeydown = function(ev) {
    var ev = ev || window.event;
    switch (ev.keyCode) {
        case 37:
        case 38:
            goLast();
            break;
        case 39:
        case 40:
            goNext();
            break;
        case 65: case 66: case 67: case 68:
            setAnswer(nowProb, ev.keyCode - 64);
            break;
    }
};

function setAnswer(q, a, ck = 0) {
    if (a <= 0) return;
    if (answer[q] > 0 && ck == 0) $("#P_" + q + "_" + answer[q]).css("text-shadow", "0px 0px 2px #666");
    $("#P_" + q + "_" + a).css("text-shadow", "0px 0px 2px #00f");
    $("#href" + q).css("text-shadow", "0px 0px 2px #00f");
    if (ck == 0) {
        answer[q] = a;
        setCookie('myAnswer', JSON.stringify(answer));
    }
}

function saveAns() {
    var i = 1;
    for (; i < MAXN; i++) {
        if (tmpArray[i] !== answer[i]) {
            // alert(i);
            $.post("/api/submit_one", {"order": i, "answer": answer[i]},
                function (res) {
                    if (res.code != 0) {
                        alert("作答时发生了错误。\nError(" + res.code + "): " + res.message);
                        window.location.href = "/test";
                        return;
                    }
                }, "json");
            tmpArray[i] = answer[i];
            $("#href" + i).css("text-shadow", "0px 0px 2px #0f0");
        }
    }
    return i;
}
var si2 = setInterval("saveAns()", 8888);

function changeTest() {
    var cf = confirm("你确定要更换一份试卷吗？\n【注意这是更换试卷按钮，不是提交按钮！】\n【注意这是更换试卷按钮，不是提交按钮！】\n【注意这是更换试卷按钮，不是提交按钮！】");
    if (!cf) return;
    $.post("/api/change_test", null, function (res) {
        if (res.code != 0) {
            alert("Error(" + res.code + "): " + res.message);
            return;
        }
        setCookie('myAnswer', '[]');
        alert("试卷已更换，请重新进入考试。");
        window.location.href = "/test";
    }, "json");
}

function postAnswer() {
    var cf = confirm("你确定要提交试卷吗？提交试卷后将不可修改！");
    if (cf) {
        si2 = clearInterval(si2);
        saveAns();
        window.location.href = "/stu_submission";
    }
}

/****
	var scookie = getCookie('myAnswer');
	if (scookie != 0) {
		var anss = JSON.parse(scookie);
	} else 
		var anss = answer;
	anss.forEach(function(elem, s) {
			$("#ans_" + s + "_" + elem).css("text-shadow", "0px 0px 2px #00f");
		})
});

function countAnswer() {
	var cnt = 0;
	answer.forEach(function(elem, s) {
			if (elem == 0) cnt++; 
		})
	return cnt;
}

function setAnswer(q, a) {
	$("#ans_" + q + "_" + a).css("text-shadow", "0px 0px 2px #00f");
	$("#href" + q).css("text-shadow", "0px 0px 2px #00f");
	$("#ans_" + q + "_" + answer[q]).css("text-shadow", "0px 0px 2px #666");
	answer[q] = a;
	setCookie('myAnswer', JSON.stringify(answer));
}

function confirmClear(){
	var con = confirm('是否清空答题数据？');
	if (con) { setCookie('myAnswer', ''); location.reload(); }
}

function saveAns() {
	$.getJSON("/api/saveAnswer.php?seuToken=" + getCookie('seuToken'), function(json){
		alert(json.resp);
	});
}

function post(URL, PARAMS) { 
      var temp = document.createElement("form"); 
      temp.action = URL; 
      temp.method = "post"; 
      temp.style.display = "none"; 
      for (var x in PARAMS) { 
        var opt = document.createElement("textarea"); 
        opt.name = x; 
        opt.value = PARAMS[x]; // alert(opt.name) 
        temp.appendChild(opt); 
        } 
      document.body.appendChild(temp); 
      temp.submit(); return temp; 
    }

function postAnswer(tid) {
	var con = confirm('确认要提交吗？你还有' + countAnswer() + '题未完成。');
	if (con) { 
		post("/ansresult.php", {"tid":tid, "ans":JSON.stringify(answer)});
	}
}
****/

