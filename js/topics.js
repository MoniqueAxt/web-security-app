/**
 * topics.js
 * Javascript specific to the page forumtopics.php
 *
 * @author  Monique Axt <>
 * @note    Projekt, Mjukvarusäkerhet
 */

let MAX_TOPIC_CHARS = 3000;

/** Hides or shows different forms (search/topic list or Create New Topic) depending
 * on which is currently hidden or visible */
function toggleNewTopicForm(){
    if (byId('forumTopics').style.display === "none") {

        // make the Submit New Topic section visible
        byId('forumTopics').style.display = "block";
        byId('submitNewTopicForm').style.display = "none";
        byId('createNewTopicBtn').value = "Create New Topic";
    }

    else {  // make search/topic-list sections visible
        byId('forumTopics').style.display = "none";
        byId('submitNewTopicForm').setAttribute('style', 'display: block');
        byId('createNewTopicBtn').value = "Back to topics";
    }
}

/** Clear the "Create new topic" form and reset the visual elements */
function resetSubmitTopicForm(){
    byId('submitNewTopicForm').reset();
    byId('messageToUser').style.display = "none";
    byId('charCount').innerText = "0";
    byId('charCount').style.color = "black";
    byId('submitTopicBtn').disabled = false;
}

/******************************************************************************
 * SUBMIT NEW TOPIC functions
 ******************************************************************************/
function processSubmitNewTopic() {
    if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
        xhr.removeEventListener('readystatechange', processSubmitNewTopic, false);

        let serverResponse = "";

        try {
            serverResponse = JSON.parse(this.responseText);
        } catch (e) {
            outputMessageToUser('Sorry, couldn\'t submit topic! Please try again later.', 'red');
        }

        // the topic was submitted successfully
        if (serverResponse.hasOwnProperty('successfulAddTopic') && serverResponse.successfulAddTopic === true) {
            if (serverResponse.hasOwnProperty('topicURL')) {
                window.location.replace(serverResponse.topicURL);
            }
        } else {
            outputServerErrorMessage(serverResponse);
        }
    }
}

function submitNewTopic() {
    let topicTitle = byId('submitTopicTitle').value;
    let topicContent = byId('submitTopicText').value;

    if (topicTitle !== '' && topicContent !== '') {
        const data = JSON.stringify({'topicTitle' : topicTitle, 'topicContent' : topicContent});

        xhr.addEventListener('readystatechange', processSubmitNewTopic, false);
        xhr.open('POST', '../src/submitTopic.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send(data);
    }
    else if (characterLimitReached(byId('submitTopicText'), 3000)){
        outputMessageToUser('Topic content can not exceed ' + MAX_TOPIC_CHARS + ' characters.', 'red');
    }
    else {  // title or text is empty
        if (topicTitle === '') {
            blinkRed('submitTopicTitle');
        }
        if (topicContent === '') {
            blinkRed('submitTopicText');
        }

        outputMessageToUser("Topic must have title and text.", 'red');
    }
}

/*******************************************************************************
 * Main function
 ******************************************************************************/
function main() {

    // submit a new topic section
    if (byId('createNewTopicBtn')) {
        byId('createNewTopicBtn').addEventListener('click', function (event) {
            event.preventDefault();
            toggleNewTopicForm();
        });
    }
    if (byId('submitTopicBtn')) {
        byId('submitTopicBtn').addEventListener('click', submitNewTopic, false);
    }

    if (byId('submitTopicText')) {
        byId('submitTopicText').addEventListener('keyup', function () {

           byId('submitTopicBtn').disabled = !!characterLimitReached(byId('submitTopicText'), MAX_TOPIC_CHARS);
        }, false);
    }

    // reset the topic-submit form
    if (byId('resetSubmitTopicBtn')) {
        byId('resetSubmitTopicBtn').addEventListener('click', resetSubmitTopicForm, false);
    }
}   // end main

window.addEventListener("load", main, false); // Connect the main function to window load event







