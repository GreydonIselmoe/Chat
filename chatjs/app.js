$("document").ready(function() {
function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
    }
    return "";
}

var messagebox = document.getElementById("composeTxt");

var chatcontainer = document.getElementById("chatcontainer");
var conn;
var unique = getCookie('unique_D_');
var username;


 conn = new Connection(unique, "message_box", "creditsdice.com:3002");
$("#composeTxt").keypress(function(e) {
    if (e.which != 13 || conn == undefined)
        return;

    e.preventDefault();

    if (messagebox.value == "")
        return;

    conn.sendMsg(messagebox.value);

    messagebox.value = "";
    
    
});


$cont = $('#message_box');
	$cont[0].scrollTop = $cont[0].scrollHeight;

	$('#composeTxt').keyup(function(e) {
    		if (e.keyCode == 13) {
        
       		 $cont[0].scrollTop = $cont[0].scrollHeight;
      
    	}
	})
.focus();






});