/**
 * search.js
 * Javascript specific to the page forumtopics.php
 *
 * @author  Monique Axt <>
 * @note    Projekt, Mjukvarusäkerhet
 */

/**
 * Shortens a provided string for display purposes
 * @param str
 * @returns {string}
 */
function shortenString(str){
    if(str.length >= 50 ) {
        return str.substring(0,50-3)+"...";
    }
}

/**
 * Displays the results of a search received from the server
 * @param serverResponse
 */
function printSearchResults(serverResponse){
    const data = serverResponse.data;
    const searchType = serverResponse.searchType;

    // hide the fieldset containing all the topic titles
    byId('topics_fieldset').style.display = "none";

    //clear previous search results
    const fieldset = byId("results_fieldset");
    while (fieldset.firstChild) {
        fieldset.removeChild(fieldset.firstChild);
    }

    const ul = document.createElement("ul");
    // create fieldset-related elements for all the search results
    const legend = document.createElement("legend");
    legend.style.color = "green";

    // modify legend text depending on type of search
    if (searchType === "keyword") {
        legend.innerHTML = "<b>Search results by keyword</b>";
    }
    else if (searchType === "username"){
        legend.innerHTML = "<b>Search results of user</b>";
    }

    // print each topic as a bullet in a list
    for (let i = 0; i < serverResponse.data.length; i++) {

        // create list elements fo each search "row"
        const li = document.createElement("li");
        const a = document.createElement("a");

        let topicID = data[i]['topic_id'];
        let displayTitle = "";
        let jumpLocation = "";

        //fields returned from DB search: topic_id, display_title, is_post, post_id (see searchmanagement.php)
        // if is_post === null, title is in display_title column (for get-URL param purposes)
        if (data[i]['is_post'] === null) {  // use display-title (search result is a topic)
            displayTitle = data[i]['display_title'];
            jumpLocation = "#topic";

        } else {    // use topic_of_post (is a post)
            displayTitle = data[i]['is_post'];
            jumpLocation = "#" + data[i]['post_id'];
        }
        a.innerText = shortenString(data[i]['display_title']);

        // create links of each search result
        let url = 'topic.php?id=' + topicID + '&topic=' + displayTitle + jumpLocation;
        a.setAttribute("id", "memberLink");
        a.setAttribute("href", url);
        a.setAttribute("target", "_blank");

        li.appendChild(a);
        ul.appendChild(li);
    }
    fieldset.appendChild(legend);
    fieldset.appendChild(ul);
    byId('forumTopics').appendChild(fieldset);

    byId('results_fieldset').style.display = "block";
}

/**
 * Reset the search form's values.
 */
function resetSearchForm(){
    byId('forumTopics').reset();
    byId('results_fieldset').style.display = "none";
    byId('topics_fieldset').style.display = "block";
    byId('messageToUser').style.display = "none";
}

/**
 * Process the response from the server; see: getSearchOptionValue()
 * */
function processSearchOptionValue() {
    if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
        xhr.removeEventListener('readystatechange', processSearchOptionValue, false);

        let serverResponse = "";
        try {
            serverResponse = JSON.parse(this.responseText);
        } catch (e) {
            outputMessageToUser('Sorry, couldn\'t search! Please try again later.', 'red');
        }

        // search results received
        if (serverResponse.hasOwnProperty('data') && serverResponse.data.length > 0 && serverResponse.hasOwnProperty('searchType')) {
            printSearchResults(serverResponse);
        }
        // No results back from search
        else {
            byId('topics_fieldset').style.display = "none";
            byId('results_fieldset').style.display = "none";
            outputServerErrorMessage(serverResponse);
        }
    }
}

/**
 * Get the search key selected by the user (username/keyword) from the UI
 */
function getSearchOptionValue() {
    byId('messageToUser').innerText = "";

    let searchDropDownList = byId('searchDropDownList');
    let searchType = searchDropDownList.options[searchDropDownList.selectedIndex].value;
    let searchText = byId('searchTextBox').value;

    // first option's value in the drop-down list is "unspecified"; used as a placeholder value
    if (searchType === "unspecified"){
        outputMessageToUser("Choose an option from the dropdown menu and enter a search keyword.", 'red');
    }
    else {
        const data = JSON.stringify({"searchType" : searchType, "searchText" : searchText});

        xhr.addEventListener('readystatechange', processSearchOptionValue, false);
        xhr.open('POST', 'src/searchmanagement.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send(data);
    }
} //end printLinks()

/*******************************************************************************
 * Main function
 ******************************************************************************/
function main() {
    // search section
    if (byId('searchBtn') && byId('resetSearchBtn')) {

        byId('searchBtn').addEventListener('click', getSearchOptionValue, false);
        byId('resetSearchBtn').addEventListener('click', resetSearchForm, false);

        // Allow option to trigger the search function by pressing "Enter" (same as clicking the search button)
        byId('searchTextBox').addEventListener('keydown', function (event) {
            if (event.key === "Enter" || event.keyCode === 13 || event.which === 13) {
                event.preventDefault();
                getSearchOptionValue();
                return false;
            }
            return true;
        });
    }
}

window.addEventListener("load", main, false); // Connect the main function to window load event