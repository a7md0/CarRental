/**
 * sends a request to the specified url from a form. this will change the window location.
 * @param {string} path the path to send the post request to
 * @param {object} params the parameters to add to the url
 * @param {string} [method=post] the method to use on the form
 *
 * @source https://stackoverflow.com/a/133997/1738413
 */

function post(path, params, method = 'post') {

    // The rest of this code assumes you are not using a library.
    // It can be made less verbose if you use one.
    const form = document.createElement('form');
    form.method = method;
    form.action = path;

    for (const key in params) {
        if (params.hasOwnProperty(key)) {
            const hiddenField = document.createElement('input');
            hiddenField.type = 'hidden';
            hiddenField.name = key;
            hiddenField.value = params[key];

            form.appendChild(hiddenField);
        }
    }

    document.body.appendChild(form);
    form.submit();
}

function submit_get(params) {
    const url = new URL(document.location);

    const entries = url.searchParams.entries();
    const currentParams = paramsToObject(entries);

    post(url.pathname, {...currentParams, ...params}, 'get');
}

function paramsToObject(entries) { // https://stackoverflow.com/a/52539264/1738413
    const result = {}
    for (const [key, value] of entries) { // each 'entry' is a [key, value] tupple
        result[key] = value;
    }
    return result;
}
