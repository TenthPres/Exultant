
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
if (!!document.getElementById('search-input')) {
    let input = document.getElementById('search-input'),
        ul = document.getElementById('search-results-list'),
        statusSpan = document.getElementById('search-results-status'),
        statusLi = statusSpan.parentElement,
        debounceTimer = null,
        lastSearchedValue = "",
        xhr = null;

    document.addEventListener('keyup', function(event) {
        if (event.key === "/" &&
            document.activeElement?.tagName.toString().toUpperCase() !== "INPUT" &&
            document.activeElement?.tagName.toString().toUpperCase() !== "TEXTAREA") {
            input.focus();
            event.preventDefault();
        }
    });
    input.addEventListener("blur", SearchResult.prototype.clearActiveRes);
    input.addEventListener("focus", doXhrSearch);

    function SearchResult(data, ul) {
        this.a = document.createElement('a');
        let li = document.createElement('li'),
            exc = document.createElement('div'),
            a = this.a,
            res = this;
        a.className = "";
        a.href = data.url;
        a.innerHTML = data.title;
        li.appendChild(a);
        if (data.excerpt?.trim().length > 0) {
            exc.innerHTML = data.excerpt;
            li.appendChild(exc);
        }
        ul.appendChild(li);

        a.addEventListener('mouseover', () => res.setActiveRes());
        a.addEventListener('mouseout', SearchResult.prototype.clearActiveRes);

        SearchResult.prototype.results.push(this);

        this.setActiveRes = function() {
            SearchResult.prototype.clearActiveRes();
            this.a.classList.add('selected');
            SearchResult.prototype.selectedResult = this;
        }
    }
    SearchResult.prototype.clearActiveRes = function() {
        if (SearchResult.prototype.selectedResult) {
            SearchResult.prototype.selectedResult.a.classList.remove('selected');
            SearchResult.prototype.selectedResult = null;
        }
    }
    SearchResult.prototype.advanceSelectedResult = function(steps) {
        let i = 0;
        if (SearchResult.prototype.results.length < 1) {
            SearchResult.prototype.clearActiveRes();
            return;
        }
        if (SearchResult.prototype.selectedResult) {
            i = SearchResult.prototype.results.indexOf(SearchResult.prototype.selectedResult);
        } else if (steps > 0) {
            i--;
        }
        i += steps + SearchResult.prototype.results.length; // adding the length prevents negative indexes
        i %= SearchResult.prototype.results.length;
        SearchResult.prototype.results[i].setActiveRes();
    }
    SearchResult.prototype.selectedResult = null;
    SearchResult.prototype.results = [];

    function clearSearchResults() {
        while (ul.lastChild !== statusLi) {
            ul.removeChild(ul.lastChild)
        }
        statusSpan.innerText = "Start Typing..." // TODO i18n
        statusLi.style.display = "";
    }

    function handleXhrResponse() {
        let data = JSON.parse(this.responseText);

        clearSearchResults();

        if (data.length === 0 && lastSearchedValue.trim().length > 0) {
            statusSpan.innerText = "No Results" // TODO i18n
        } else {
            statusLi.style.display = "none";
        }

        SearchResult.prototype.results = [];

        // add new results
        for (di in data) {
            new SearchResult(data[di], ul)
        }
    }

    function doXhrSearch() {
        let searchTerm = input.value.trim()
        if (searchTerm.length === 0) {
            clearSearchResults();
            lastSearchedValue = "";
            return
        }
        if (searchTerm === lastSearchedValue || debounceTimer !== null) {
            return;
        }
        statusSpan.innerText = "Loading..." // TODO i18n
        statusLi.style.display = "";
        if (xhr !== null) {
            xhr.abort();
        }
        xhr = new XMLHttpRequest();
        xhr.addEventListener('load', handleXhrResponse);
        xhr.open("GET", `/wp-json/wp/v2/search?search=${searchTerm}`);
        lastSearchedValue = searchTerm;
        xhr.send();
    }

    function searchInputEvent() {
        if (debounceTimer === null) {
            debounceTimer = setTimeout(function() { debounceTimer = null; doXhrSearch() }, 700);
            doXhrSearch();
        }
    }

    input.addEventListener('input', searchInputEvent);
    input.addEventListener('keyup', function(event) {
        searchInputEvent();

        switch(event.key) {
            case "ArrowDown":
                SearchResult.prototype.advanceSelectedResult(1);
                event.preventDefault();
                break;

            case "ArrowUp":
                SearchResult.prototype.advanceSelectedResult(-1);
                event.preventDefault();
                break;

            case "Escape":
                document.body.focus();
                input.value = "";
                doXhrSearch(); // cancels existing XHRs.
                clearSearchResults();
                lastSearchedValue = "";
                event.preventDefault();
                break;

            case "Enter":
                if (SearchResult.prototype.selectedResult) {
                    window.location = SearchResult.prototype.selectedResult.a.href;
                    event.preventDefault();
                    return;
                }
                break;
        }
    })
}