/**
 * common.js

 * @author  Monique Axt <>
 * @note    Projekt, Mjukvarusäkerhet
 */

let xhr;                // Variabel att lagra XMLHttpRequestobjektet

/*******************************************************************************
 * Util functions
 ******************************************************************************/
function byId(id) {
    return document.getElementById(id);
}

function outputMessageToUser(message, msgColor){
    byId('messageToUser').style.color = msgColor;
    byId('messageToUser').innerText = message;
    byId('messageToUser').style.display = "block";
}

function characterLimitReached(textBox, maxChars) {
    let limitReached = true;

    let textEntered = textBox.value;
    let charCount = textEntered.length;

    byId('charCount').innerText = charCount + "/" + maxChars;

    if (charCount > maxChars) {
        byId('charCount').style.color = 'red';
        limitReached = true;

    } else {
        byId('charCount').style.color = 'black';
        limitReached = false;
    }
    return limitReached;
}

function blinkRed(elementID) {
    const e = document.getElementById(elementID);

    setTimeout(function() {
        e.style.border = (e.style.border === '' ? 'solid red 1px' : '');
        setTimeout(function() {
            e.style.border = (e.style.border === '' ? 'solid red 1px' : '');
        }, 1000);
    }, 5);
}

/** Checks if the server response sent an error message back, if so
 * the message is printed to output to inform the user.
 * */
function outputServerErrorMessage(serverResponse) {
    if (serverResponse.hasOwnProperty('errorMessage')) {

        if(serverResponse.errorMessage !== "none") {
            outputMessageToUser(serverResponse.errorMessage, 'red');
        }
    } else {
        // if server-response didn't return this property
        outputMessageToUser('Sorry, invalid input. Please check and try again.', 'red');
    }
}

/** Updates the login-form's size and the content-section's size
 * within their flex boxes, depending on whether the user is logged-in or not */
function adjustLoginFormSize() {
    if (byId('login') || byId('logout')) {

        let aside = document.getElementsByTagName('aside')[0];
        //let section = document.getElementsByTagName('section')[0];

        //if login is visible (user is logged-in)
        if (byId('logout').style.display === "none") {
            aside.style.flex = "0 5 30%";
            // section.style.flex = "0 5 70%";
        }
        // if logout is visible (user is NOT logged-in)
        if (byId('login').style.display === "none") {
            aside.style.flex = "0 5 10%";
            // section.style.flex = "0 5 80%";
        }
    }
}

/** (Ajax) Prints the navigation links for the website depending on if the user is
 * logged-in or not. An array with the links is received from the server. */
function printLinks(serverResponse) {
    const nav = byId("nav_links");
    while (nav.firstChild) {
        nav.removeChild(nav.firstChild);
    }

    if (serverResponse.hasOwnProperty('linkarray')) {
        for (let key in serverResponse.linkarray) {
            const a = document.createElement("a");
            a.setAttribute("href", serverResponse.linkarray[key]);
            a.innerHTML = key.toUpperCase();

            if (key === "HOME") {
                a.setAttribute('class', 'active');
            }
            // add links to the navigation-links container
            nav.appendChild(a);
        }
    }
} //end printLinks()

/** Updates the login-form's size and visibility and prints nav links to pages
 * that a logged-in user can access.
 * */
function updateUILoggedin(serverResponse) {
    byId('logout').style.display = "block";
    byId('login').style.display = "none";

    // message to indicate successful log-in
    outputMessageToUser("Successfully logged in! ", 'green');

    //display username of user logged-in
    if(serverResponse.hasOwnProperty('username')) {
        byId('loggedInAsUser').innerText = "Logged in as:\n" + serverResponse.username;
    }
    adjustLoginFormSize();
    printLinks(serverResponse);
}

/******************************************************************************
 * CREATE ACCOUNT FUNCTIONS
 ******************************************************************************/
/** Process Create Account */
function processCreateAccount() {
    if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
        xhr.removeEventListener('readystatechange', processCreateAccount, false);

        let serverResponse = "";

        try {
            serverResponse = JSON.parse(this.responseText);
        } catch (e) {
            outputMessageToUser('Sorry, couldn\'t create account! Please try again later.', 'red');
        }

        // new account was created successfully
        if (serverResponse.hasOwnProperty('successfulAddUser') && serverResponse.successfulAddUser === true) {

            byId('loginForm').reset();
            let username = (serverResponse.hasOwnProperty('username') ? serverResponse.username : "you");
            outputMessageToUser("Account created for " + username + "! You can now log in.", 'green');

            // new account NOT created
        } else {
            outputServerErrorMessage(serverResponse);
        }
    } else if (xhr.status === 500) {
        window.location.replace("../errors/custom_50x.html");
    }
}

function createAccount(){
    let username = byId('uname').value;
    let password = byId('psw').value;
    const data = JSON.stringify({"newUsername": username, "newPassword": password});

    if (byId('uname').value !== "" && byId('psw').value !== "") {
        xhr.addEventListener('readystatechange', processCreateAccount, false);
        xhr.open('POST', '../src/createAccount.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send(data);

    } else if (!username || !password) {
        outputMessageToUser("Username / password cannot be empty.", 'red');
    }
}

/*******************************************************************************
 * LOGIN FUNCTIONS
 ******************************************************************************/
/** Function processLogin */
function processLogin() {
    if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
        xhr.removeEventListener('readystatechange', processLogin, false);

        let serverResponse = "";

        try {
            serverResponse = JSON.parse(this.responseText);
        } catch (e) {
            byId('loginForm').reset();
            outputMessageToUser('Sorry, couldn\'t log in! Please try again later.', 'red');
        }

        // successful login: valid server response received
        if (serverResponse.hasOwnProperty('loggedin') && serverResponse.loggedin === true)  {
            updateUILoggedin(serverResponse);
        } else {
            byId('loginForm').reset();
            outputServerErrorMessage(serverResponse);
        }
    }
} //end processLogin()

function doLogin() {
    let username = byId('uname').value;
    let password = byId('psw').value;
    let token = byId('token').value;

    if (byId('uname').value !== "" && byId('psw').value !== "") {
        xhr.addEventListener('readystatechange', processLogin, false);
        xhr.open('POST', '../src/login.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        const data = JSON.stringify({"username": username, "password": password, "token": token});
        xhr.send(data);

    } else if (!username || !password) {
        outputMessageToUser("Username / password cannot be empty.", 'red');
    }
} //end doLogin()

/*******************************************************************************
 * LOGOUT functions
 ******************************************************************************/
function processLogout() {
    if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
        xhr.removeEventListener('readystatechange', processLogout, false);
        const serverResponse = JSON.parse(this.responseText);

        if (!window.location.pathname.endsWith('index.php')) {
            window.location.replace("index.php");
        }

        if (window.location.pathname.endsWith('index.php')) {
            if(serverResponse.hasOwnProperty('msg')) {
                outputMessageToUser(serverResponse.msg, 'red');
            }
        }
        window.location.replace("index.php");
    }
} //end processLogout()

function doLogout() {
    xhr.addEventListener('readystatechange', processLogout, false);
    xhr.open('POST', '../src/logout.php', true);
    xhr.send(null);
} //end doLogout()

/*******************************************************************************
 * Main function
 ******************************************************************************/
function main() {
    // login and logout functions
    byId("loginButton").addEventListener('click', doLogin, false);
    byId("logoutButton").addEventListener('click', doLogout, false);
    if (byId('createAccountButton')) {
        byId("createAccountButton").addEventListener('click', createAccount, false);
    }

    // Displays a button that jumps back to the top of the page if the user has scrolled down
    window.onscroll = function() {
        if (byId('navTopBtn'))
        {
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                byId('navTopBtn').style.display = "block";
            } else {
                byId('navTopBtn').style.display = "none";
            }
        }
    };
    if (byId('navTopBtn')) {
        // scroll to the top when this button is clicked
        byId('navTopBtn').addEventListener('click', function () {
            document.body.scrollTop = 0;
            document.documentElement.scrollTop = 0;
        });
    }

    // if user is logged in, make the log-in section smaller
    adjustLoginFormSize();

    /****************************************************************************
     Code for XMLHttpRequest
     *****************************************************************************/
// Stöd för IE7+, Firefox, Chrome, Opera, Safari
    try {
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xhr = new XMLHttpRequest();
        } else if (window.ActiveXObject) {
            // code for IE6, IE5
            xhr = new ActiveXObject("Microsoft.XMLHTTP");
        } else {
            throw new Error('Cannot create XMLHttpRequest object');
        }

    } catch (e) {
        alert('"XMLHttpRequest failed!' + e.message);
    }
} //end main()

window.addEventListener("load", main, false); // Connect the main function to window load event













