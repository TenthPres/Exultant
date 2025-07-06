(function() {

    const {__, _x, _n, _nx, sprintf} = wp.i18n;

// scrolling progress bar
    if (!!document.getElementById('pageProgressBarProgress')) {
        window.addEventListener('scroll', function () {
            const winScroll = document.body.scrollTop || document.documentElement.scrollTop,
                height = document.documentElement.scrollHeight - document.documentElement.clientHeight,
                scrolled = (winScroll / height) * 100;
            document.getElementById("pageProgressBarProgress").style.width = scrolled + "%";
        })
    }


// xhr Search
    function initSearch() {
        const input = document.getElementById('search-input');
        const ul = document.getElementById('search-results-list');
        const statusSpan = document.getElementById('search-results-status');
        const statusLi = statusSpan.parentElement;
        let debounceTimer = null;
        let lastSearchedValue = "";
        let xhr = null;

        if (input) {
            document.addEventListener('keyup', (event) => {
                if (event.key === "/" &&
                    !["INPUT", "TEXTAREA"].includes(document.activeElement?.tagName.toUpperCase())) {
                    input.focus();
                    event.preventDefault();
                }
            });

            class SearchResult {
                constructor(data, ul) {
                    this.a = document.createElement('a');
                    const li = document.createElement('li');
                    const exc = document.createElement('div');
                    this.a.href = data.url;
                    this.a.innerHTML = data.title;
                    li.appendChild(this.a);
                    if (data.excerpt?.trim().length > 0) {
                        exc.innerHTML = data.excerpt;
                        li.appendChild(exc);
                    }
                    ul.appendChild(li);

                    this.a.addEventListener('mouseover', () => this.setActiveRes());
                    this.a.addEventListener('mouseout', SearchResult.clearActiveRes);

                    SearchResult.results.push(this);
                }

                setActiveRes() {
                    SearchResult.clearActiveRes();
                    this.a.classList.add('selected');
                    SearchResult.selectedResult = this;
                }

                static clearActiveRes() {
                    if (SearchResult.selectedResult) {
                        SearchResult.selectedResult.a.classList.remove('selected');
                        SearchResult.selectedResult = null;
                    }
                }

                static advanceSelectedResult(steps) {
                    if (SearchResult.results.length < 1) {
                        SearchResult.clearActiveRes();
                        return;
                    }
                    let i = SearchResult.selectedResult ? SearchResult.results.indexOf(SearchResult.selectedResult) : -1;
                    i = (i + steps + SearchResult.results.length) % SearchResult.results.length;
                    SearchResult.results[i].setActiveRes();
                }
            }

            input.addEventListener("blur", SearchResult.clearActiveRes);
            input.addEventListener("focus", doXhrSearch);

            SearchResult.selectedResult = null;
            SearchResult.results = [];

            function clearSearchResults() {
                while (ul.lastChild !== statusLi) {
                    ul.removeChild(ul.lastChild);
                }
                statusSpan.innerText = __("Start Typing...", "Exultant");
                statusLi.style.display = "";
            }

            function handleXhrResponse() {
                const data = JSON.parse(this.responseText);

                // if results are for an older query, do nothing.
                if (lastSearchedValue !== this.query) {
                    return;
                }

                clearSearchResults();

                if (data.length === 0 && lastSearchedValue.trim().length > 0) {
                    statusSpan.innerText = __("No Results", "Exultant");
                } else {
                    statusLi.style.display = "none";
                }

                SearchResult.results = [];

                data.forEach(item => new SearchResult(item, ul));
            }

            function doXhrSearch() {
                const searchTerm = input.value.trim();
                if (searchTerm.length === 0) {
                    clearSearchResults();
                    lastSearchedValue = "";
                    return;
                }
                if (searchTerm === lastSearchedValue || debounceTimer !== null) {
                    return;
                }
                statusSpan.innerText = __("Loading...", "Exultant");
                statusLi.style.display = "";
                if (xhr !== null) {
                    xhr.removeEventListener('load', handleXhrResponse);
                    xhr.abort();
                }
                xhr = new XMLHttpRequest();
                xhr.query = searchTerm;
                xhr.addEventListener('load', handleXhrResponse);
                xhr.open("GET", `/wp-json/wp/v2/search?search=${searchTerm}&locale=${tpvm.locale}`);
                lastSearchedValue = searchTerm;
                xhr.send();
            }

            function searchInputEvent() {
                if (debounceTimer === null) {
                    debounceTimer = setTimeout(() => {
                        debounceTimer = null;
                        doXhrSearch();
                    }, 700);
                    doXhrSearch();
                }
            }

            input.addEventListener('input', searchInputEvent);
            input.addEventListener('keydown', (event) => {
                searchInputEvent();

                switch (event.key) {
                    case "ArrowDown":
                        SearchResult.advanceSelectedResult(1);
                        event.preventDefault();
                        break;

                    case "ArrowUp":
                        SearchResult.advanceSelectedResult(-1);
                        event.preventDefault();
                        break;

                    case "Escape":
                        if (input.value === "") {
                            event.preventDefault();
                            document.body.focus();
                            input.blur();
                            break;
                        } else {
                            input.value = "";
                            doXhrSearch(); // cancels existing XHRs.
                            clearSearchResults();
                            lastSearchedValue = "";
                            event.preventDefault();
                            break;
                        }

                    case "Enter":
                        if (SearchResult.selectedResult) {
                            window.location = SearchResult.selectedResult.a.href;
                        }
                        event.preventDefault();
                        break;
                }
            });
        }
    }

    initSearch();

})();