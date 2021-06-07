(() => {
    let currentPage = 1;
    let filters = null;
    let pages = null;

    function checkRange(element) {
        const filterMinElm = document.querySelector(`[name=filter_min_${element}]`);
        const filterMaxElm = document.querySelector(`[name=filter_max_${element}]`);

        filterMinElm.min = window.lookup_cars_ranges[element].min;
        filterMaxElm.max = window.lookup_cars_ranges[element].max;

        if (Number(filterMinElm.value) > Number(filterMaxElm.value)) {
            if (Number(filterMinElm.value) > Number(filterMaxElm.max)) {
                filterMinElm.value = filterMinElm.min;
                filterMaxElm.value = filterMaxElm.max;
            } else {
                filterMaxElm.value = filterMinElm.value;
            }
        }
    }

    function checkReservationDate() {
        const filterPickupDateElm = document.querySelector('[name=filter_pickup_date]');
        const filterReturnDateElm = document.querySelector('[name=filter_return_date]');

        if (filterPickupDateElm === null && filterReturnDateElm === null) {
            return;
        }

        if (filterPickupDateElm.valueAsDate === null) {
            filterPickupDateElm.valueAsDate = new Date();
        }
        if (filterReturnDateElm.valueAsDate === null) {
            filterReturnDateElm.valueAsDate = new Date();
        }

        filterPickupDateElm.min = new Date().toISOString().split("T")[0];
        filterReturnDateElm.min = filterPickupDateElm.min;

        if (filterPickupDateElm.valueAsDate > filterReturnDateElm.valueAsDate) {
            filterReturnDateElm.valueAsDate = filterPickupDateElm.valueAsDate;
        }

        const days = (filterReturnDateElm.valueAsDate - filterPickupDateElm.valueAsDate) / (1000 * 3600 * 24) + 1;
        document.querySelector('#reservation-period-in-days').innerText = ` (${days} days)`;
    }

    function refreshPaginationState() {
        const paginationElement = document.querySelector('#pagination');
        const items = Array.from(paginationElement.children);
        items.forEach((li) => {
            const href = li.children[0];
            const pageLink = href.dataset.pageLink;

            if (pageLink == '-') {
                if (currentPage == 1) {
                    li.classList.add('disabled');
                } else {
                    li.classList.remove('disabled');
                }
            } else if (pageLink == '+') {
                if (currentPage == pages.total || pages.total == 0) {
                    li.classList.add('disabled');
                } else {
                    li.classList.remove('disabled');
                }
            } else {
                if (currentPage == Number(pageLink)) {
                    li.classList.add('active');
                } else {
                    li.classList.remove('active');
                }
            }
        });

    }

    function onPageClick() {
        const pageLink = this.dataset.pageLink;

        if (pageLink == '-') {
            currentPage--;
        } else if (pageLink == '+') {
            currentPage++;
        } else {
            currentPage = Number(pageLink);
        }

        fetchResults();
        refreshPaginationState();
    }

    function generatePaginationItem(pageNo, text) {
        const previousPageListItem = document.createElement('li');
        previousPageListItem.classList.add('page-item');

        const previousPageLink = document.createElement('a');
        previousPageLink.classList.add('page-link');
        previousPageLink.setAttribute('data-page-link', pageNo);
        previousPageLink.innerText = text;
        previousPageLink.addEventListener('click', onPageClick)

        previousPageListItem.appendChild(previousPageLink);

        return previousPageListItem;
    }

    function setupPagination() {
        const paginationElement = document.querySelector('#pagination');
        while (paginationElement.firstChild) {
            paginationElement.firstChild.remove();
        }

        const prevCtrl = generatePaginationItem('-', 'Previous');
        prevCtrl.classList.add('disabled');
        paginationElement.appendChild(prevCtrl);

        for (let page = 1; page <= pages.total; page++) {
            const ctrl = generatePaginationItem(`${page}`, `${page}`);

            paginationElement.appendChild(ctrl);
        }

        const nextCtrl = generatePaginationItem('+', 'Next');
        paginationElement.appendChild(nextCtrl);

        refreshPaginationState();
    }

    function fetchResults() {
        const data = { ...filters, currentPage };
        const resultsFeedbackElement = document.querySelector("#results-feedback");
        const resultsElement = document.querySelector("#results");
        const loadingSpinner = document.querySelector("#loading-spinner");
        const resultMessage = document.querySelector("#result-message");
        console.log(data);

        resultsElement.classList.add('d-none');
        resultsFeedbackElement.classList.remove('d-none');

        loadingSpinner.classList.remove('d-none'); // Show the loading spinner

        resultMessage.classList.add('d-none'); // Hide the result message
        resultMessage.innerText = ''; // Reset the result message

        fetch(window.lookup_api, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
            .then(rawResponse => {
                if (rawResponse.status >= 400 && rawResponse.status < 600) { // 4xx and 5xx status
                    throw rawResponse; // throw the response for more handling
                }

                return rawResponse.json(); // Parse the response
            })
            .then(response => { // when parsed response is ready
                if (response.matching_results > 0) {
                    resultsElement.innerHTML = response.content;

                    pages = response.pages; // set the pages obj
                    setupPagination(); // setup pagination for the given results set

                    resultsElement.classList.remove('d-none'); // Show the results section
                    resultsFeedbackElement.classList.add('d-none'); // Hide the feedback section (fully)
                } else {
                    resultMessage.classList.remove('d-none'); // Show the result message
                    resultMessage.innerText = `No matching results, try to change the search filters?`;

                }
            })
            .catch(err => {
                resultMessage.classList.remove('d-none'); // Show the result message
                resultMessage.innerText = `Something went wrong (${err.statusText ?? err ?? '-'})`; // Set the result message (either the statusText or the fatal error or just dash (-))
            })
            .finally(() => { // After everything
                loadingSpinner.classList.add('d-none'); // Hide the loading spinner
            });
    }

    function onFilterChange(event) {
        const filterFormElement = document.querySelector('[name=filter_form]');

        const filtersValid = filterFormElement.checkValidity();
        if (!filtersValid) {
            filterFormElement.reportValidity();
            return;
        }

        checkReservationDate();
        checkRange('price');
        checkRange('year');
        checkRange('seats');

        // filtersValid = filterFormElement.checkValidity();
        // if (!filtersValid) {
        //     filterFormElement.reportValidity();
        //     return;
        // }

        const filterElements = document.querySelectorAll('[data-trigger-filter=true]');

        currentPage = 1;
        filters = {};

        filterElements.forEach(element => {
            const name = element.name;
            let value = element.value;

            if (element.nodeName === 'SELECT') {
                value = [...element.options].filter((x) => x.selected).map((x) => x.value);
            }

            filters[name] = value;
        });

        fetchResults();
    }

    function onReserveButtonClick(carId) {
        const params = new URLSearchParams({
            p: 'place-reservation',
            carId,
            pickupDate: filters.filter_pickup_date,
            returnDate: filters.filter_return_date,
        });

        window.location.href = `?${params.toString()}`;
    }

    document.addEventListener('DOMContentLoaded', (event) => {
        checkReservationDate();

        // Source: https://codeburst.io/throttling-and-debouncing-in-javascript-646d076d0a44
        function debounced(delay, fn) {
            let timerId;
            return function (...args) {
                if (timerId) {
                    clearTimeout(timerId);
                }
                timerId = setTimeout(() => {
                    fn(...args);
                    timerId = null;
                }, delay);
            }
        }

        const filterElements = document.querySelectorAll('[data-trigger-filter=true]');
        filterElements.forEach(element => {
            const dHandler = debounced(300, onFilterChange);
            element.addEventListener('change', dHandler);
        });

        document.addEventListener('click', (event) => {
            if (event.target) {
                if (event.target.classList.contains('reserve-car-btn')) {
                    const carId = event.target.dataset['carId'];

                    if (carId) {
                        onReserveButtonClick(carId);
                    }
                }

                if (event.target.classList.contains('select-all-btn')) {
                    const linkFor = event.target.dataset['linkFor'];

                    console.log(event.target.dataset);

                    if (linkFor) {
                        const targetElement = document.getElementById(linkFor);
                        if (targetElement) {
                            const options = Array.from(targetElement.children);

                            options.forEach((option) => {
                                option.selected = true;
                            });

                            targetElement.dispatchEvent(new Event('change'));
                        }
                    }
                }
            }
        });

        onFilterChange(null);
    }, false);
})();
