

var Connection = (function() {

    function Connection(uniquehash, chatWindowId, url) {
        this.uniquehash = uniquehash;
        this.chatwindow = document.getElementById(chatWindowId);

        this.open = false;

        this.socket = new WebSocket("ws://" + url);
        this.setupConnectionEvents();
    }

    Connection.prototype = {
        
        getHistory: function() {
            
             this.socket.send(JSON.stringify({
                action: 'getHistory'
            }));
            
        },
        
        updateUsername: function() {
            this.socket.send(JSON.stringify({
                action: 'setname',
                uniquehash: this.uniquehash
            }));
            console.log("Inside updateUsername... | uniquehash= "+this.uniquehash);
        },

        addChatMessage: function(name,id, msg,htime) {
            	
    var date =new Date();
			  var hours =date.getHours();
			  if (hours <10) {
			    hours= "0"+hours;
			  }
			  var minutes =date.getMinutes();
			  if (minutes <10) {
			    minutes= "0"+minutes;
			  }
			  var seconds =date.getSeconds();
			  if (seconds <10) {
			      seconds= "0"+seconds;
			  }

			 var time = hours+":"+minutes+":"+seconds;

            this.chatwindow.innerHTML += "<div style=\"display:table;padding:2px;height:30px;\"><span style=\"display:table-cell;vertical-align:middle;font-size:11px;padding-right:10px;\">"+(htime || time)+" </span><span style=\"display:table-cell;vertical-align:middle;font-size:11px;padding-right:5px;\" onclick=\"javascript:addpmid(this.innerHTML);\">("+id+")</span><span onclick=\"javascript:addalias(this.innerHTML);\" class=\"user_name\" style=\"display:table-cell;vertical-align:middle;color:;padding-right:5px;\">"+name+":</span>  <span  class=\"user_message\" style=\"display:table-cell;background-color:rgba(0,0,0,0.6);vertical-align:middle;padding:6px;\">"+msg+"</span></div>";
            console.log("Message Added. name= "+name);
        },

        addSystemMessage: function(msg) {
            this.chatwindow.innerHTML += '<p><b>' + msg + '</b></p>';
        },

        setupConnectionEvents: function() {
            var self = this;

            self.socket.onopen = function(evt) { self.connectionOpen(evt); };
            self.socket.onmessage = function(evt) { self.connectionMessage(evt); };
            self.socket.onclose = function(evt) { self.connectionClose(evt); };
        },

        connectionOpen: function(evt) {
            this.open = true;
            this.addSystemMessage("Connected");
            console.log("Connected");
            this.getHistory();
            this.updateUsername();
             console.log("Username update sent");
        },

        connectionMessage: function(evt) {
                 console.log("Connection message, if no message follows, the socket is not open");
            if (!this.open){
            return;}
            console.log("Socket is open.");
            var data = JSON.parse(evt.data);
            if (data.action == 'setname') {
                if (data.success){
                    
                         console.log("Username updated. Name = "+data.username);
                }else{
                    this.addSystemMessage("Username " + data.username + " has been taken.");
                }
            } else if (data.action === 'message' && data.msg !== "") {
                     console.log("Message received. name= "+data.username+" msg= "+data.msg);
                this.addChatMessage(data.username,data.id, data.msg);
                
                $cont = $('#message_box');
                $cont[0].scrollTop = $cont[0].scrollHeight;
                     console.log("Message added to chat window.");
            }  else if (data.action === 'historymessage') {
                     console.log("History received, name= "+data.username+" msg= "+data.msg);
                this.addChatMessage(data.username,data.id, data.msg,data.time);
                     console.log("History added to chat window.");
            }
        },

        connectionClose: function(evt) {
            this.open = false;
            this.addSystemMessage("Disconnected");
             console.log("Disconnected.");
        },

        sendMsg: function(message) {
            if (this.open) {
                this.socket.send(JSON.stringify({
                    action: 'message',
                    msg: message
                }));

                //this.addChatMessage(data.username, message);
            } else {
                this.addSystemMessage("You are not connected to the server.");
            }
        }
    };

    return Connection;

})();
