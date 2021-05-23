<!-- wRunner CSS -->
<link rel="stylesheet" href="assets/css/wrunner-default-theme.css">
<!-- vanillaSelectBox CSS -->
<link rel="stylesheet" href="assets/css/vanillaSelectBox.css">


<!-- wRunner Vanilla JS -->
<script src="assets/js/wrunner-native.js"></script>
<!-- vanillaSelectBox Vanilla JS -->
<script src="assets/js/vanillaSelectBox.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', (event) => {
        const sliderSetting = {
            roots: document.querySelector('#filter-years'),
            type: 'range',
            limits: {
                minLimit: 2016,
                maxLimit: 2021
            },
            rangeValue: {
                minValue: 2016,
                maxValue: 2021,
            },
            step: 1,
        };

        const seatsSetting = {
            roots: document.querySelector('#filter-seats'),
            type: 'range',
            limits: {
                minLimit: 2,
                maxLimit: 9
            },
            rangeValue: {
                minValue: 2,
                maxValue: 9,
            },
            step: 1,
        };

        let yearsSlider = wRunner(sliderSetting);
        let seatsSlider = wRunner(seatsSetting);

        let filterBrandsSelect = new vanillaSelectBox("#filter-brands", {
            placeHolder: "Filter by brand"
        });

        let filterTypesSelect = new vanillaSelectBox("#filter-types", {
            placeHolder: "Filter by type"
        });

    }, false);
</script>
