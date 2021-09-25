
// scrolling progress bar
if (!!document.getElementById('pageProgressBarProgress')) {
    window.addEventListener('scroll', function () {
        var winScroll = document.body.scrollTop || document.documentElement.scrollTop;
        var height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        var scrolled = (winScroll / height) * 100;
        document.getElementById("pageProgressBarProgress").style.width = scrolled + "%";
    })
}


// xhr Search
if (!!document.getElementById('search-form-1')) {
    let input = document.getElementById('search-form-1'),
        debounceTimer = null,
        lastSearchedValue = null,
        xhr = null;

    function handleXhrResponse() {
        console.log(this.responseText);
    }

    function doXhrSearch() {
        let searchTerm = input.value.trim()
        if (searchTerm === lastSearchedValue || debounceTimer !== null) {
            return;
        }
        if (xhr !== null) {
            xhr.abort();
        }
        // TODO handle cases of "" value
        xhr = new XMLHttpRequest();
        xhr.addEventListener('load', handleXhrResponse);
        xhr.open("GET", `/wp-json/wp/v2/search?search=${searchTerm}`);
        lastSearchedValue = searchTerm;
        xhr.send();
    }

    input.addEventListener('keyup', function() {
        if (debounceTimer === null) {
            debounceTimer = setTimeout(function() { debounceTimer = null; doXhrSearch() }, 700);
            doXhrSearch();
        }
    })
}