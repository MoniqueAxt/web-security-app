/**
 * posts.js
 * Javascript specific to the page topic.php
 * Each topic can have 0+ post-messages in response.
 *
 * @author  Monique Axt <>
 * @note    Projekt, Mjukvarusäkerhet
 */

/** Updates the number representing the total votes on a certain posts */
function updateUIVoteCount(serverResponse){
    if (serverResponse.hasOwnProperty('postID') && serverResponse.hasOwnProperty('sumVote')) {
        let id = 'voteCount_' + serverResponse.postID;

        if (byId(id)) {
            byId(id).innerText = "(" + serverResponse.sumVote + ")";
        }
    }
}

/******************************************************************************
 * Register Post Vote
 ******************************************************************************/
/** Processes the response from the server; see registerVotePost() */
function processRegisterPostVote() {
    if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
        xhr.removeEventListener('readystatechange', processRegisterPostVote, false);
        const serverResponse = JSON.parse(this.responseText);

        let hasVoteData = (serverResponse.hasOwnProperty('vote') && serverResponse.hasOwnProperty('postID')
            && serverResponse.hasOwnProperty('sumVote'));

        let hasErrorMessage = serverResponse.hasOwnProperty('errorMessage');

        if (hasErrorMessage){
            if (serverResponse['errorMessage'] !== "none" ) {
                alert(serverResponse['errorMessage']);
            }
            else if (hasVoteData) {
                toggleVoteArrowImages(serverResponse);
                updateUIVoteCount(serverResponse);
            }
        }
    }
}

function registerPostVote() {
    // get the parent element (fieldset) representing the whole post
    let fieldset = this.closest('fieldset');         //this.parentElement;
    // each fieldset-element's class is the post's ID
    let postID = fieldset.className;
    //log if it was an upvote or downvote
    let vote = null;

    if (this.getAttribute('alt') === "up") {
        vote = 1;
    }
    else if (this.getAttribute('alt') === "down") {
        vote = -1;
    }
    //post_id, username, vote
    const data = JSON.stringify({"postID" : postID, "vote" : vote});
    xhr.addEventListener('readystatechange', processRegisterPostVote, false);
    xhr.open('POST', '../src/registervote.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send(data);
}

/** Changes the arrow-images representing an up- or downvote on a post
 *  when the logged-in user votes on a post*/
function toggleVoteArrowImages(serverResponse) {
    if (serverResponse.hasOwnProperty('postID')) {
        let postID = serverResponse.postID;
        let vote = serverResponse.vote;

        // change upvote image
        const upSrc = '/img/up_green.png';
        const downScr = '/img/down_red.png';
        const upVote = byId('up_' + postID);
        const downVote = byId('down_' + postID);

        if (vote === 1) {
            upVote.setAttribute('src', upSrc);
            downVote.setAttribute('src', '/img/down_grey.png');
            upVote.removeEventListener('click', registerPostVote, false);
            downVote.addEventListener('click', registerPostVote, false);
        }

        //change downvote image
        else if (vote === -1) {
            upVote.setAttribute('src', '/img/up_grey.png');
            downVote.setAttribute('src', downScr);
            upVote.addEventListener('click', registerPostVote, false);
            downVote.removeEventListener('click', registerPostVote, false);
        }
    }
}


/** Visually highlights the post that is anchored */
function highlightAnchor(){
    let anchorLocation = window.location.hash;
    let hash = anchorLocation.split('#').pop();

    // item to be highlighted is a topic
    if (hash === "topic") {
        if (byId('topicHeading')) {
            byId('topicHeading').style.boxShadow = "0 0 20px #667ffa";
        }
    } // item to be highlighted is a post
    else if (byId(hash)) {
        byId(hash).style.boxShadow = "0 0 20px #667ffa";
    }
}

/******************************************************************************
 * Delete Post
 ******************************************************************************/
function processDeletePost(){
    if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
        xhr.removeEventListener('readystatechange', processDeletePost, false);

        const serverResponse = JSON.parse(this.responseText);

        if (serverResponse.hasOwnProperty('errorMessage')){
            // post wasn't deleted from the DB
            if (serverResponse.errorMessage !== "none") {
                alert(serverResponse.errorMessage);
                location.reload();

            } else if (serverResponse.hasOwnProperty('postID')) {
                // a valid postID was not returned by the server
                if (serverResponse.postID === null) {
                    location.reload();
                    // postID of the post that was deleted from the DB
                } else {
                    let deletedPostNode = byId(serverResponse.postID);
                    // delete the post from UI (the fieldset element that contains the post data)
                    while (deletedPostNode.firstChild) {
                        deletedPostNode.removeChild(deletedPostNode.lastChild);
                    }
                    deletedPostNode.remove();
                }
            }
        }
    }
}

function deletePost(){
    let answer = window.confirm("Are you sure you want to delete this post?");

    if (answer) {
        let fieldset = this.parentElement;
        let postID = fieldset.id;

        //username, content, timestamp, votecount
        let userElement = fieldset.firstChild;
        let username = userElement.innerText;
        let timestamp = byId('timestamp_' + postID).innerText;
        let content = byId('content_' + postID).textContent;  //innerText trims trailing white-spaces

        let voteCount = byId('voteCount_' + postID).innerText;

        const data = JSON.stringify({
            'username': username,
            'timestamp': timestamp,
            'content': content,
            'voteCount': voteCount,
            'postID': postID
        });

        xhr.addEventListener('readystatechange', processDeletePost, false);
        xhr.open('POST', '../src/deletePost.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send(data);
    }
    else {
        // do nothing
    }
}

/******************************************************************************
 * Submit New Post
 ******************************************************************************/
function processSubmitPost() {
    if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
        xhr.removeEventListener('readystatechange', processSubmitPost, false);

        let serverResponse = "";

        try {
            serverResponse = JSON.parse(this.responseText);
        } catch (e) {
            outputMessageToUser('Sorry, couldn\'t submit post! Please try again later.', 'red');
        }

        // the post was submitted successfully
        if (serverResponse.hasOwnProperty('successfulAddPost') && serverResponse.successfulAddPost === true) {

            // output the post without requiring a reload
            if (serverResponse.hasOwnProperty('username')
                && serverResponse.hasOwnProperty('timestamp')
                && serverResponse.hasOwnProperty('postContent')
                && serverResponse.hasOwnProperty('topicID')
                && serverResponse.hasOwnProperty('postID')) {

                let textbox = byId('submitPostText');
                textbox.value = "";
                byId('charCount').innerText = "0";


                const topicSection = byId('topicContent');
                const fieldset = document.createElement('fieldset');
                fieldset.setAttribute("class", serverResponse.postID);
                fieldset.setAttribute("name", serverResponse.postID);
                fieldset.setAttribute('id', serverResponse.postID);

                const username = document.createElement('legend');
                username.innerText = serverResponse.username;
                username.setAttribute('class', 'postUser');

                const timestamp = document.createElement('p');
                timestamp.setAttribute("class", "postTS");
                timestamp.setAttribute("id", "timestamp_" + serverResponse.postID);
                timestamp.innerText = serverResponse.timestamp;

                const postContent = document.createElement('p');
                postContent.innerText = serverResponse.postContent;

                // if user posts again without refreshing the page, remove previous post's box-shadow
                let posts = topicSection.getElementsByTagName('fieldset');
                if (posts.length > 0) {
                    posts[posts.length - 1].style.boxShadow = "none";
                }

                fieldset.appendChild(username);
                fieldset.appendChild(timestamp);
                fieldset.appendChild(postContent);
                topicSection.appendChild(fieldset);

                // add box-shadow to the last post posted
                fieldset.style.boxShadow = "0 0 20px #667ffa";
            }
        } else {
            outputServerErrorMessage(serverResponse);
        }
    }
}

function submitPost() {
    if (byId('submitPostText')) {
        let postContent = byId('submitPostText').value; //includes trailing white-space

        if (characterLimitReached(byId('submitPostText'), 1500)) {
            blinkRed('submitPostText');
            outputMessageToUser('Post cannot be longer than 1500 characters.', 'red');

        } else if (postContent !== '') {
            const data = JSON.stringify({'postContent': postContent});

            xhr.addEventListener('readystatechange', processSubmitPost, false);
            xhr.open('POST', '../src/submitPost.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.send(data);
        } else {
            blinkRed('submitPostText');
            outputMessageToUser("Post text is empty.", 'red');
        }
    }
}


/*******************************************************************************
 * Main function
 ******************************************************************************/
function main() {
    // voting on posts
    let votingButtons = document.getElementsByClassName('voting');

    if (votingButtons.length > 0) {
        for (let i = 0; i < votingButtons.length; i++){
            votingButtons[i].addEventListener('click', registerPostVote, false);
        }
    }

    // submit new post
    byId('submitPostBtn').addEventListener('click', submitPost, false);

    // delete a post
    if (document.getElementsByClassName('deleteBtn')) {
        let deleteBtnArray = document.getElementsByClassName('deleteBtn');

        for (let i = 0; i < deleteBtnArray.length; i++) {
            deleteBtnArray[i].addEventListener('click', deletePost, false);
        }
    }

    // show a character limit when typing a new post
    if (byId('submitPostText')) {
        byId('submitPostText').addEventListener('keyup', function () {

            byId('submitPostBtn').disabled = !!characterLimitReached(byId('submitPostText'), 1500);
        }, false);
    }

    highlightAnchor();

} //end main()

window.addEventListener("load", main, false); // Connect the main function to window load event



