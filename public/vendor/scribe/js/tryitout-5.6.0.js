window.abortControllers = {};

function cacheAuthValue() {
    // Whenever the auth header is set for one endpoint, cache it for the others
    window.lastAuthValue = '';
    let authInputs = document.querySelectorAll(`.auth-value`)
    authInputs.forEach(el => {
        el.addEventListener('input', (event) => {
            window.lastAuthValue = event.target.value;
            authInputs.forEach(otherInput => {
                if (otherInput === el) return;
                // Don't block the main thread
                setTimeout(() => {
                    otherInput.value = window.lastAuthValue;
                }, 0);
            });
        });
    });
}

window.addEventListener('DOMContentLoaded', cacheAuthValue);

function getCookie(name) {
    if (!document.cookie) {
        return null;
    }

    const cookies = document.cookie.split(';')
        .map(c => c.trim())
        .filter(c => c.startsWith(name + '='));

    if (cookies.length === 0) {
        return null;
    }

    return decodeURIComponent(cookies[0].split('=')[1]);
}

function tryItOut(endpointId) {
    document.querySelector(`#btn-tryout-${endpointId}`).hidden = true;
    document.querySelector(`#btn-canceltryout-${endpointId}`).hidden = false;
    const executeBtn = document.querySelector(`#btn-executetryout-${endpointId}`).hidden = false;
    executeBtn.disabled = false;

    // Show all input fields
    document.querySelectorAll(`input[data-endpoint=${endpointId}],label[data-endpoint=${endpointId}]`)
        .forEach(el => el.style.display = 'block');

    if (document.querySelector(`#form-${endpointId}`).dataset.authed === "1") {
        const authElement = document.querySelector(`#auth-${endpointId}`);
        authElement && (authElement.hidden = false);
    }
    // Expand all nested fields
    document.querySelectorAll(`#form-${endpointId} details`)
        .forEach(el => el.open = true);
}

function cancelTryOut(endpointId) {
    if (window.abortControllers[endpointId]) {
        window.abortControllers[endpointId].abort();
        delete window.abortControllers[endpointId];
    }

    document.querySelector(`#btn-tryout-${endpointId}`).hidden = false;
    const executeBtn = document.querySelector(`#btn-executetryout-${endpointId}`);
    executeBtn.hidden = true;
    executeBtn.textContent = executeBtn.dataset.initialText;
    document.querySelector(`#btn-canceltryout-${endpointId}`).hidden = true;
    // Hide inputs
    document.querySelectorAll(`input[data-endpoint=${endpointId}],label[data-endpoint=${endpointId}]`)
        .forEach(el => el.style.display = 'none');
    document.querySelectorAll(`#form-${endpointId} details`)
        .forEach(el => el.open = false);
    const authElement = document.querySelector(`#auth-${endpointId}`);
    authElement && (authElement.hidden = true);

    document.querySelector('#execution-results-' + endpointId).hidden = true;
    document.querySelector('#execution-error-' + endpointId).hidden = true;

    // Revert to sample code blocks
    document.querySelector('#example-requests-' + endpointId).hidden = false;
    document.querySelector('#example-responses-' + endpointId).hidden = false;
}

function makeAPICall(method, path, body = {}, query = {}, headers = {}, endpointId = null) {
    console.log({endpointId, path, body, query, headers});

    if (!(body instanceof FormData) && typeof body !== "string") {
        body = JSON.stringify(body)
    }

    const url = new URL(window.tryItOutBaseUrl + '/' + path.replace(/^\//, ''));

    // We need this function because if you try to set an array or object directly to a URLSearchParams object,
    // you'll get [object Object] or the array.toString()
    function addItemToSearchParamsObject(key, value, searchParams) {
            if (Array.isArray(value)) {
                value.forEach((v, i) => {
                    // Append {filters: [first, second]} as filters[0]=first&filters[1]second
                    addItemToSearchParamsObject(key + '[' + i + ']', v, searchParams);
                })
            } else if (typeof value === 'object' && value !== null) {
                Object.keys(value).forEach((i) => {
                    // Append {filters: {name: first}} as filters[name]=first
                    addItemToSearchParamsObject(key + '[' + i + ']', value[i], searchParams);
                });
            } else {
                searchParams.append(key, value);
            }
    }

    Object.keys(query)
        .forEach(key => addItemToSearchParamsObject(key, query[key], url.searchParams));

    window.abortControllers[endpointId] = new AbortController();

    return fetch(url, {
        method,
        headers,
        body: method === 'GET' ? undefined : body,
        signal: window.abortControllers[endpointId].signal,
        referrer: window.tryItOutBaseUrl,
        mode: 'cors',
        credentials: 'same-origin',
    })
        .then(response => Promise.all([response.status, response.statusText, response.text(), response.headers]));
}

function hideCodeSamples(endpointId) {
    document.querySelector('#example-requests-' + endpointId).hidden = true;
    document.querySelector('#example-responses-' + endpointId).hidden = true;
}

function handleResponse(endpointId, response, status, headers) {
    hideCodeSamples(endpointId);

    // Hide error views
    document.querySelector('#execution-error-' + endpointId).hidden = true;

    const responseContentEl = document.querySelector('#execution-response-content-' + endpointId);

    // Check if the response contains Laravel's  dd() default dump output
    const isLaravelDump = response.includes('Sfdump');

    // If it's a Laravel dd() dump, use innerHTML to render it safely
    if (isLaravelDump) {
        responseContentEl.innerHTML = response === '' ? responseContentEl.dataset.emptyResponseText : response;
    } else {
        // Otherwise, stick to textContent for regular responses
        responseContentEl.textContent = response === '' ? responseContentEl.dataset.emptyResponseText : response;
    }

    // Prettify it if it's JSON
    let isJson = false;
    try {
        const jsonParsed = JSON.parse(response);
        if (jsonParsed !== null) {
            isJson = true;
            response = JSON.stringify(jsonParsed, null, 4);
            responseContentEl.textContent = response;
        }
    } catch (e) {

    }

    isJson && window.hljs.highlightElement(responseContentEl);
    const statusEl = document.querySelector('#execution-response-status-' + endpointId);
    statusEl.textContent = ` (${status})`;
    document.querySelector('#execution-results-' + endpointId).hidden = false;
    statusEl.scrollIntoView({behavior: "smooth", block: "center"});
}

function handleError(endpointId, err) {
    hideCodeSamples(endpointId);
    // Hide response views
    document.querySelector('#execution-results-' + endpointId).hidden = true;

    // Show error views
    let errorMessage = err.message || err;
    const $errorMessageEl = document.querySelector('#execution-error-message-' + endpointId);
    $errorMessageEl.textContent = errorMessage + $errorMessageEl.textContent;
    const errorEl = document.querySelector('#execution-error-' + endpointId);
    errorEl.hidden = false;
    errorEl.scrollIntoView({behavior: "smooth", block: "center"});

}

async function executeTryOut(endpointId, form) {
    const executeBtn = document.querySelector(`#btn-executetryout-${endpointId}`);
    executeBtn.textContent = executeBtn.dataset.loadingText;
    executeBtn.disabled = true;
    executeBtn.scrollIntoView({behavior: "smooth", block: "center"});

    let body;
    let setter;
    if (form.dataset.hasfiles === "1") {
        body = new FormData();
        setter = (name, value) => body.append(name, value);
    } else if (form.dataset.isarraybody === "1") {
        body = [];
        setter = (name, value) => _.set(body, name, value);
    } else {
        body = {};
        setter = (name, value) => _.set(body, name, value);
    }
    const bodyParameters = form.querySelectorAll('input[data-component=body]');
    bodyParameters.forEach(el => {
        let value = el.value;

        if (el.type === 'number' && typeof value === 'string') {
            value = parseFloat(value);
        }

        if (el.type === 'file' && el.files[0]) {
            setter(el.name, el.files[0]);
            return;
        }

        if (el.type !== 'radio') {
            if (value === "" && el.required === false) {
                // Don't include empty optional values in the request
                return;
            }
            setter(el.name, value);
            return;
        }

        if (el.checked) {
            value = (value === 'false') ? false : true;
            setter(el.name, value);
        }
    });

    const query = {};
    const queryParameters = form.querySelectorAll('input[data-component=query]');
    queryParameters.forEach(el => {
        if (el.type !== 'radio' || (el.type === 'radio' && el.checked)) {
            if (el.value === '') {
                // Don't include empty values in the request
                return;
            }

            _.set(query, el.name, el.value);
        }
    });

    let path = form.dataset.path;
    const urlParameters = form.querySelectorAll('input[data-component=url]');
    urlParameters.forEach(el => (path = path.replace(new RegExp(`\\{${el.name}\\??}`), el.value)));

    const headers = Object.fromEntries(Array.from(form.querySelectorAll('input[data-component=header]'))
        .map(el => [el.name, el.value]));

    // When using FormData, the browser sets the correct content-type + boundary
    let method = form.dataset.method;
    if (body instanceof FormData) {
        delete headers['Content-Type'];

        // When using FormData with PUT or PATCH, use method spoofing so PHP can access the post body
        if (['PUT', 'PATCH'].includes(form.dataset.method)) {
            method = 'POST';
            setter('_method', form.dataset.method);
        }
    }

    let preflightPromise = Promise.resolve();
    if (window.useCsrf && window.csrfUrl) {
        preflightPromise = makeAPICall('GET', window.csrfUrl).then(() => {
            headers['X-XSRF-TOKEN'] = getCookie('XSRF-TOKEN');
        });
    }

    return preflightPromise.then(() => makeAPICall(method, path, body, query, headers, endpointId))
        .then(([responseStatus, statusText, responseContent, responseHeaders]) => {
            handleResponse(endpointId, responseContent, responseStatus, responseHeaders)
        })
        .catch(err => {
            if (err.name === "AbortError") {
                console.log("Request cancelled");
                return;
            }
            console.log("Error while making request: ", err);
            handleError(endpointId, err);
        })
        .finally(() => {
            executeBtn.disabled = false;
            executeBtn.textContent = executeBtn.dataset.initialText;
        });
}
