function changePsWd() {
	var k1 = document.getElementById("oldpass").value;
    var k2 = document.getElementById("newpass").value;
    var k3 = document.getElementById("newpass2").value;
	if (k2 !== k3) {
		alert("Error(1): 两次密码不一样。");
        document.getElementById("newpass").value = "";
        document.getElementById("newpass2").value = "";
        return;
	}
	$.post("/api/change_my_password", { "prevpass": k1, "newpass": k2 },
		function (res) {
			if (res.code !== 0) {
                alert("Error(" + res.code + "): " + res.message);
                return;
			}
            alert(res.message);
		}
		, "json");
}