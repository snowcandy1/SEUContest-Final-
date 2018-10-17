function getResult(num) {
	$.getJSON("/api/studentRes.php?seuToken=" + getCookie('seuToken') + "&major=" + num, function(json){
	  
	});
}