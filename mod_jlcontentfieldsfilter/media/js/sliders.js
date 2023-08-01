document.addEventListener("DOMContentLoaded", function () {

    let range = document.querySelectorAll('.jlmf-range');
    let links = document.querySelector('.jlmf-link');
    let sliders = [];

    for (let i=0;i<range.length;i++) {
        let container = range[i].closest('.range-sliders'),
        inputMin = container.querySelector(".input-min"),
        inputMax = container.querySelector(".input-max"),
        min = parseInt(range[i].getAttribute('data-min')),
        max = parseInt(range[i].getAttribute('data-max')),
        from = parseInt(range[i].getAttribute('data-from')),
        to = parseInt(range[i].getAttribute('data-to'));

        if(Number.isNaN(min) || Number.isNaN(max) || Number.isNaN(from) || Number.isNaN(to)) {
            continue;
        }

        if(min !== max) {

            sliders[i] = noUiSlider.create(range[i], {
                start: [from, to],
                connect: true,
                step: 1,
                range: {
                    'min': min,
                    'max': max
                }
            });

            sliders[i].on('slide', function (values, handle, unencoded, tap, positions) {
                inputMin.value = Math.round(values[0]);
                inputMax.value = Math.round(values[1]);
            });

            inputMin.addEventListener('change', function () {
                let currentLeft = parseInt(this.value),
                currentRight = parseInt(inputMax.value);

                currentLeft = isNaN(currentLeft) ? min : currentLeft;

                if(currentLeft > currentRight) {
                    currentLeft = currentRight;
                }

                if(currentLeft < min) {
                    currentLeft = min;
                }

                this.value = currentLeft;
                sliders[i].updateOptions({
                    start: [currentLeft, currentRight]
                });

            });

            inputMax.addEventListener('change', function () {
                let currentLeft = parseInt(inputMin.value),
                currentRight = parseInt(this.value);

                currentRight = isNaN(currentRight) ? max : currentRight;

                if(currentLeft > currentRight) {
                    currentRight = currentLeft;
                }

                if(currentRight > max) {
                    currentRight = max;
                }

                this.value = currentRight;
                sliders[i].updateOptions({
                    start: [currentLeft, currentRight]
                });
            });

        }

    }

    links.addEventListener("click", function () {
        for (let i=0;i<range.length;i++) {
            let container = range[i].closest('.range-sliders'),
            inputMin = container.querySelector(".input-min"),
            inputMax = container.querySelector(".input-max"),
            min = parseInt(range[i].getAttribute('data-min')),
            max = parseInt(range[i].getAttribute('data-max'));

            if(!isNaN(min) && !isNaN(max)) {
                inputMin.value = min;
                inputMax.value = max;

                sliders[i].updateOptions({
                    start: [min, max]
                });
            }

        }
    });

});
