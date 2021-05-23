(() => {
    let currentPage = null;

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

    function fetchResults(filters) {
        const data = {...filters, currentPage};
        console.log(data);

        fetch('lookup-cars-api.php', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        }).then(rawResponse => rawResponse.json())
            .then(response => {
                console.log(response);
                const resultsElement = document.querySelector('#results');
                resultsElement.innerHTML = response.content;
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
        const data = {};
        filterElements.forEach(element => {
            // data[element.name]
            const name = element.name;
            let value = element.value;

            if (element.nodeName === 'SELECT') {
                value = [...element.options].filter((x) => x.selected).map((x) => x.value);
            }

            data[name] = value;
        });

        fetchResults(data);
    }

    document.addEventListener('DOMContentLoaded', (event) => {
        checkReservationDate();

        const filterElements = document.querySelectorAll('[data-trigger-filter=true]');
        filterElements.forEach(element => {
            element.addEventListener('change', onFilterChange);
        });

        onFilterChange(null);
    }, false);
})();
