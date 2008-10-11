/* 
 This file was generated by Dashcode.  
 You may edit this file to customize your widget or web page 
 according to the license.txt file included in the project.
 */
 
var database = null;                            // The client-side database
var DB_tableName = "GrinnellPlansTable";       // database name

var requestManager = {
    _validResponse: false,
    _lastMessage: "",
    base_url: "http://www.grinnellplans.com/json_api.php",
    
    setCurrentToken: function(token) {
        this._currentToken = token;
    },
    getCurrentToken: function() {
        return this._currentToken;
    },
    getLastMessage: function() {
        return this._lastMessage;
    },
    
    processResponse: function(jsonResponse) {
        this._validResponse = false;
        this._lastMessage = "";
        
        if (jsonResponse == ""){
            return false;
        }
        
        eval("var response = ("+jsonResponse+")");
        if (response['success']) {
            this._validResponse = true;
        }
        if (response.message) {
            this._lastMessage = response.message;
        }
        if (this._currentToken != response['token']) {
            this._currentToken = response['token'];
            setCookie("token", this._currentToken);
        }
        if (response['autofingerList']) {
            listController.setupAutofingerlist(response['autofingerList']);
            document.getElementById("list").object.reloadData();
        }
        if (response['plandata']) {
            detailController.setPlanData(response['plandata']);
        }

        return this._validResponse;
    },
    
    refreshData: function() {
        document.getElementById('refreshActivityIndicator').style.visibility="visible";
        requestManager.serverRequest('autofingerlist', "", requestManager.refreshDataSuccess);
    },
    
    refreshDataSuccess: function() {
        document.getElementById('refreshActivityIndicator').style.visibility="hidden";
    },
    
    serverRequest: function(task, data, successFunction, failFunction){
        if (!task) {
            return;
        }
        
        // Values you provide
        var feedURL = this.base_url + "?task="+task;  
        var onloadHandler = function() { requestManager._serverRequestHandler(xmlRequest, successFunction, failFunction); };	
        if (!data){
            data = "";
        }
        if (this._currentToken){
            data = data+"&token="+this._currentToken;
        }

        // XMLHttpRequest setup code
        var xmlRequest = new XMLHttpRequest();
        xmlRequest.onload = onloadHandler;
        xmlRequest.open("POST", feedURL);
        xmlRequest.setRequestHeader("Cache-Control", "no-cache");
        xmlRequest.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");  
        xmlRequest.send(data);
    },
    
    _serverRequestHandler: function(xmlRequest, onSuccessFunction, onFailFunction) {
        if (xmlRequest.status == 200) {
            // Parse and interpret results
            // XML results found in xmlRequest.responseXML
            // Text results found in xmlRequest.responseText
            if(requestManager.processResponse(xmlRequest.responseText)) {
                if (onSuccessFunction) {
                    onSuccessFunction();
                }
            }
            else if (onFailFunction) {
                onFailFunction();
            }
        }
        else {
            alert("Error fetching data: HTTP status " + xmlRequest.status);
        }
    }
    
};

var loginController = {

    doLogin: function(event) {
        
        var loginPassword = document.getElementById('loginPassword');
        var loginUsername = document.getElementById('loginUsername');
        var data = "username="+loginUsername.value+"&password="+loginPassword.value;

        requestManager.serverRequest('login', data, loginController.success, loginController.failure);
    },
    
    success: function() {
        
        if (database) {
            database.transaction(function (tx) {
                tx.executeSql("INSERT INTO " + DB_tableName + " (id, key, value) VALUES (?, ?, ?)", [0, 'username', loginUsername.value]);
                tx.executeSql("INSERT INTO " + DB_tableName + " (id, key, value) VALUES (?, ?, ?)", [1, 'password', loginPassword.value]);
            });
        }        
        
        var stackLayout = document.getElementById('stackLayout').object; 
        stackLayout.setCurrentView('listLevel'); 
        
    },
    
    failure: function() {
        alert(requestController.getLastMessage());
        var loginPassword = document.getElementById('loginPassword');
        if (loginPassword) {
            loginPassword.value = "";
        }
    },
};

var listController = {
    // This object acts as a controller for the list UI.
    // It implements the dataSource methods for the list.
    
    numberOfRows: function() {
        // The List calls this dataSource method to find out how many rows should be in the list.
        return autofingerList.length;
    },
    
    prepareRow: function(rowElement, rowIndex, templateElements) {
        // The List calls this dataSource method for every row.  templateElements contains references to all elements inside the template that have an id. We use it to fill in the text of the rowTitle element.
        if (templateElements.rowTitle) {
            //templateElements.rowTitle.innerText = parks[rowIndex].name;
            templateElements.rowTitle.innerText = "Level "+autofingerList[rowIndex].level+" ("+listController.unreadPlanCount(autofingerList[rowIndex])+")";
        }

        // We also assign an onclick handler that will cause the browser to go to the detail page.
        var handler = function() {
            var level = autofingerList[rowIndex];
            listUsernamesController.setLevel(level);
            var browser = document.getElementById('browser').object;
            // The Browser's goForward method is used to make the browser push down to a new level.  Going back to previous levels is handled automatically.
            browser.goForward(document.getElementById('listUsernamesLevel'), "Level "+level.level );
        };
        rowElement.onclick = handler;
    },
    
    markUsername: function(username) {
        for (i=0; i< autofingerList.length; i++) {
            if (autofingerList[i].usernames) {
                for (j=0; j < autofingerList[i].usernames.length; j++) {
                    if (autofingerList[i].usernames[j].username === username) {
                        autofingerList[i].usernames[j].seen = true;
                    }
                }
            }
        }
    },
    
    unreadPlanCount: function(level) {
        var count = 0;
        for (j=0; j < level.usernames.length; j++) {
            if (level.usernames[j].seen === false) {
                count = count + 1;
            }
        }
        return count;
    },
    
    
    setupAutofingerlist: function(response) {
        autofingerList = response;
        var newList;
        for (i=0; i< autofingerList.length; i++) {
            if (autofingerList[i].usernames) {
                newList = new Array(autofingerList[i].usernames.length);
                for (j=0; j < autofingerList[i].usernames.length; j++) {
                    newList[j] = ({
                        username: autofingerList[i].usernames[j],
                        seen: false
                    });
                }
                autofingerList[i].usernames = newList;
            }
        }
    },
};

var listUsernamesController = {

    _usernameList: [],
    listUsernames: document.getElementById('listUsernames'),
    
    setLevel: function(level) {
        this._usernameList = level.usernames;
        document.getElementById("listUsernames").object.reloadData();
        if (this._usernameList.length === 0) {
            listUsernames.style.visibility = "hidden";
        }
        else {
            listUsernames.style.visibility = "visible";
        }
    },
    
    numberOfRows: function() {
        // The List calls this dataSource method to find out how many rows should be in the list.
        return this._usernameList.length
    },
    
    prepareRow: function(rowElement, rowIndex, templateElements) {
        // The List calls this dataSource method for every row.  templateElements contains references to all elements inside the template that have an id. We use it to fill in the text of the rowTitle element.
        if (templateElements.rowUsernameTitle) {
            //templateElements.rowTitle.innerText = parks[rowIndex].name;
            templateElements.rowUsernameTitle.innerText = "["+this._usernameList[rowIndex].username+"]";
            if (this._usernameList[rowIndex].seen === true) {
                templateElements.rowUsernameTitle.style.color="#CCCCCC";
            }
        }

        // We also assign an onclick handler that will cause the browser to go to the detail page.
        var self = this;
        var handler = function() {
            var username = self._usernameList[rowIndex].username;
            detailController.setUser(username);
            var browser = document.getElementById('browser').object;
            // The Browser's goForward method is used to make the browser push down to a new level.  Going back to previous levels is handled automatically.
            browser.goForward(document.getElementById('detailLevel'), "Read Plan" );
            var childrenCount = rowElement.children.length;
            if (childrenCount > 0) {
                for( i = 0; i < childrenCount; i++) {
                    rowElement.children[i].style.color = "#CCCCCC";
                }
            }
            
        };
        rowElement.onclick = handler;
    }
    
}

var detailController = {
    // This object acts as a controller for the detail UI.
    _planData: {},
    
    setPlanData: function(userData) {
        this._planData = userData;
    },
    
    getPlanData: function() {
        return this._planData;
    },
    
    loadingPlan: function(active) {
        if (active === true) {
            activityIndicator.style.visibility = "visible";
        }
        else {
            activityIndicator.style.visibility = "hidden";
        }
    },
    
    detailUsername: document.getElementById('detailUsername'),    
    detailLastLogin: document.getElementById('detailLastLogin'),
    detailLastUpdated: document.getElementById('detailLastUpdated'),
    detailName: document.getElementById('detailName'),
    detailPlanBox: document.getElementById('detailPlanBox'),
    activityIndicator: document.getElementById('activityIndicator'),
    detailLevel: document.getElementById('detailLevel'),
    downloadRemaining: document.getElementById('downloadRemaining'),
    downloadRemainingText: document.getElementById('downloadRemainingText'),
            
    setUser: function(username) {
        detailController.loadingPlan(true);
        detailUsername.innerHTML = "Username: "+username;
        detailLastLogin.innerHTML = "Last Login: ";
        detailLastUpdated.innerHTML = "Last Updated: ";
        detailName.innerHTML = "Name: ";
        detailPlanBox.innerHTML = "";
        downloadRemaining.style.visibility = "hidden";
        
        var data = "username="+username+"&limitsize=1&readlinkreplacement=javascript:detailController.setUser('{username}');";
        requestManager.serverRequest('read', data, detailController.setUserSuccess); 
    },
    
    setUserSuccess: function(){
        var planData = detailController.getPlanData();
        detailLastLogin.innerHTML = "Last Login: " + planData["last_login"];
        detailLastUpdated.innerHTML = "Last Updated: " + planData["last_updated"];
        detailName.innerHTML = "Name: " + planData["pseudo"];
        detailPlanBox.innerHTML = "" + planData["plan"];
        
        listController.markUsername(planData["username"]);
        document.getElementById("list").object.reloadData();
        
        if (planData.remaining) {
            var kb = Math.round(planData.remaining/1024*100)/100
            downloadRemainingText.innerHTML = "Download Remaining " + kb + " kb";
            downloadRemaining.style.visibility = "visible";
        }
        
        detailController.loadingPlan(false);
    },
    
    downloadRemaining: function() {
        var planData = detailController.getPlanData();
        var data = "username="+planData.username+"&partial=1&readlinkreplacement=javascript:detailController.setUser('{username}');";
        requestManager.serverRequest('read', data, detailController.downloadRemainingSuccess); 
    },
    
    downloadRemainingSuccess: function() {
        var planData = detailController.getPlanData();
        detailPlanBox.innerHTML += planData["remainingplan"];        
        detailController.loadingPlan(false);
    }
    
};

//
// Function: load()
// Called by HTML body element's onload event when the web application is ready to start
//
function load()
{
    dashcode.setupParts();
    initDB();
    if (database) {
        database.transaction(function(tx) {
            tx.executeSql("SELECT key, value FROM " + DB_tableName, [],
            function(tx, result) {
                var loginUsername = document.getElementById('loginUsername');
                var loginPassword = document.getElementById('loginPassword');
                for (var i = 0; i < result.rows.length; ++i) {
                    var row = result.rows.item(i);
                    var key = row['key'];
                    var value = row['value'];

                    if (key == 'username') {
                        loginUsername.value = value;
                    }
                    if (key == 'password') {
                        loginPassword.value = value;
                    }
                }
            }
            )}
        );
        requestManager.serverRequest('autofingerlist', "", moveToListLevel);
    }
}

function moveToListLevel() {
        var stackLayout = document.getElementById('stackLayout').object;
        var simpleTransition = new Transition(Transition.NONE_TYPE);
        stackLayout.setCurrentViewWithTransition('listLevel', simpleTransition, false);
}

//
// Function: initDB()
// Init and create the local database, if possible
//
function initDB()
{
    try {
        if (window.openDatabase) {
            database = openDatabase("Message", "1.0", "Message Database", 1000);
            if (database) {
                database.transaction(function(tx) {
                    tx.executeSql("SELECT COUNT(*) FROM " + DB_tableName, [],
                    null,
                    function(tx, error) {
                        // Database doesn't exist. Let's create one.
                        tx.executeSql("CREATE TABLE " + DB_tableName +
                        " (id INTEGER PRIMARY KEY," +
                        "  key TEXT," +
                        "  value TEXT)");
                    });
                });
            }
        }
    } catch(e) {
        database = null;
    }
}


var autofingerList = [
    {"level":"1","usernames":[]},
    {"level":"2","usernames":[]},
    {"level":"3","usernames":[]}
];

function setCookie(c_name,value) {
    var exdate=new Date();
    exdate.setDate(exdate.getDate()+100);
    document.cookie=c_name+ "=" +escape(value) + ";expires="+exdate.toGMTString();
}

function getCookie(c_name) {
    if (document.cookie.length>0)
      {
      c_start=document.cookie.indexOf(c_name + "=");
      if (c_start!=-1)
        { 
        c_start=c_start + c_name.length+1; 
        c_end=document.cookie.indexOf(";",c_start);
        if (c_end==-1) c_end=document.cookie.length;
        return unescape(document.cookie.substring(c_start,c_end));
        } 
      }
    return "";
}




