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

    function handleResponse(response) {
        console.log(response);
        const resultsElement = document.querySelector('#results');
        resultsElement.innerHTML = response.content;

        pages = response.pages;
        setupPagination();
    }

    function fetchResults() {
        const data = { ...filters, currentPage };
        console.log(data);

        fetch('lookup-cars-api.php', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
            .then(rawResponse => rawResponse.json())
            .then(response => handleResponse(response));
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
