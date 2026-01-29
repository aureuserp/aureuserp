<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>AureusERP API Documentation</title>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset("/vendor/scribe/css/theme-default.style.css") }}" media="screen">
    <link rel="stylesheet" href="{{ asset("/vendor/scribe/css/theme-default.print.css") }}" media="print">

    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.10/lodash.min.js"></script>

    <link rel="stylesheet"
          href="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/styles/obsidian.min.css">
    <script src="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/highlight.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jets/0.14.1/jets.min.js"></script>

    <style id="language-style">
        /* starts out as display none and is replaced with js later  */
                    body .content .bash-example code { display: none; }
                    body .content .javascript-example code { display: none; }
            </style>

    <script>
        var tryItOutBaseUrl = "http://127.0.0.1:8000";
        var useCsrf = Boolean();
        var csrfUrl = "/sanctum/csrf-cookie";
    </script>
    <script src="{{ asset("/vendor/scribe/js/tryitout-5.6.0.js") }}"></script>

    <script src="{{ asset("/vendor/scribe/js/theme-default-5.6.0.js") }}"></script>

</head>

<body data-languages="[&quot;bash&quot;,&quot;javascript&quot;]">

<a href="#" id="nav-button">
    <span>
        MENU
        <img src="{{ asset("/vendor/scribe/images/navbar.png") }}" alt="navbar-image"/>
    </span>
</a>
<div class="tocify-wrapper">
    
            <div class="lang-selector">
                                            <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                            <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                    </div>
    
    <div class="search">
        <input type="text" class="search" id="input-search" placeholder="Search">
    </div>

    <div id="toc">
                    <ul id="tocify-header-introduction" class="tocify-header">
                <li class="tocify-item level-1" data-unique="introduction">
                    <a href="#introduction">Introduction</a>
                </li>
                            </ul>
                    <ul id="tocify-header-authenticating-requests" class="tocify-header">
                <li class="tocify-item level-1" data-unique="authenticating-requests">
                    <a href="#authenticating-requests">Authenticating requests</a>
                </li>
                            </ul>
                    <ul id="tocify-header-sales-api-management" class="tocify-header">
                <li class="tocify-item level-1" data-unique="sales-api-management">
                    <a href="#sales-api-management">Sales API management</a>
                </li>
                                    <ul id="tocify-subheader-sales-api-management" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="sales-api-management-orders">
                                <a href="#sales-api-management-orders">Orders</a>
                            </li>
                                                            <ul id="tocify-subheader-sales-api-management-orders" class="tocify-subheader">
                                                                            <li class="tocify-item level-3" data-unique="sales-api-management-GETapi-v1-admin-sales-orders">
                                            <a href="#sales-api-management-GETapi-v1-admin-sales-orders">List orders</a>
                                        </li>
                                                                            <li class="tocify-item level-3" data-unique="sales-api-management-POSTapi-v1-admin-sales-orders">
                                            <a href="#sales-api-management-POSTapi-v1-admin-sales-orders">Store a newly created resource in storage.</a>
                                        </li>
                                                                            <li class="tocify-item level-3" data-unique="sales-api-management-GETapi-v1-admin-sales-orders--id-">
                                            <a href="#sales-api-management-GETapi-v1-admin-sales-orders--id-">Show order</a>
                                        </li>
                                                                            <li class="tocify-item level-3" data-unique="sales-api-management-PUTapi-v1-admin-sales-orders--id-">
                                            <a href="#sales-api-management-PUTapi-v1-admin-sales-orders--id-">Update the specified resource in storage.</a>
                                        </li>
                                                                            <li class="tocify-item level-3" data-unique="sales-api-management-DELETEapi-v1-admin-sales-orders--id-">
                                            <a href="#sales-api-management-DELETEapi-v1-admin-sales-orders--id-">Remove the specified resource from storage.</a>
                                        </li>
                                                                    </ul>
                                                                        </ul>
                            </ul>
            </div>

    <ul class="toc-footer" id="toc-footer">
                    <li style="padding-bottom: 5px;"><a href="{{ route("scribe.postman") }}">View Postman collection</a></li>
                            <li style="padding-bottom: 5px;"><a href="{{ route("scribe.openapi") }}">View OpenAPI spec</a></li>
                <li><a href="http://github.com/knuckleswtf/scribe">Documentation powered by Scribe ✍</a></li>
    </ul>

    <ul class="toc-footer" id="last-updated">
        <li>Last updated: January 29, 2026</li>
    </ul>
</div>

<div class="page-wrapper">
    <div class="dark-box"></div>
    <div class="content">
        <h1 id="introduction">Introduction</h1>
<aside>
    <strong>Base URL</strong>: <code>http://127.0.0.1:8000</code>
</aside>
<pre><code>This documentation aims to provide all the information you need to work with our API.

&lt;aside&gt;As you scroll, you'll see code examples for working with the API in different programming languages in the dark area to the right (or as part of the content on mobile).
You can switch the language used with the tabs at the top right (or from the nav menu at the top left on mobile).&lt;/aside&gt;</code></pre>

        <h1 id="authenticating-requests">Authenticating requests</h1>
<p>This API is not authenticated.</p>

        <h1 id="sales-api-management">Sales API management</h1>

    

                        <h2 id="sales-api-management-orders">Orders</h2>
                                        <p>
                    <p>Do stuff with orders</p>
                </p>
                                        <h2 id="sales-api-management-GETapi-v1-admin-sales-orders">List orders</h2>

<p>
</p>

<p>Retrieve a paginated list of orders with filtering and sorting</p>

<span id="example-requests-GETapi-v1-admin-sales-orders">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://127.0.0.1:8000/api/v1/admin/sales/orders?include=partner%2Clines&amp;sort=-created_at&amp;page=1" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://127.0.0.1:8000/api/v1/admin/sales/orders"
);

const params = {
    "include": "partner,lines",
    "sort": "-created_at",
    "page": "1",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-admin-sales-orders">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [
        {
            &quot;id&quot;: 1,
            &quot;name&quot;: &quot;SO/1&quot;,
            &quot;state&quot;: &quot;sale&quot;,
            &quot;invoice_status&quot;: &quot;invoiced&quot;,
            &quot;partner&quot;: {
                &quot;id&quot;: 33,
                &quot;name&quot;: &quot;Lyle Holman&quot;
            },
            &quot;partner_invoice&quot;: {
                &quot;id&quot;: 33,
                &quot;name&quot;: &quot;Lyle Holman&quot;
            },
            &quot;partner_shipping&quot;: {
                &quot;id&quot;: 33,
                &quot;name&quot;: &quot;Lyle Holman&quot;
            },
            &quot;client_order_ref&quot;: null,
            &quot;origin&quot;: null,
            &quot;reference&quot;: null,
            &quot;date_order&quot;: &quot;2026-01-07&quot;,
            &quot;validity_date&quot;: &quot;2026-02-06&quot;,
            &quot;commitment_date&quot;: null,
            &quot;user&quot;: {
                &quot;id&quot;: 1,
                &quot;name&quot;: &quot;Admin&quot;
            },
            &quot;team&quot;: {
                &quot;id&quot;: null,
                &quot;name&quot;: null
            },
            &quot;company&quot;: {
                &quot;id&quot;: 1,
                &quot;name&quot;: &quot;TechNova Solutions Pvt. Ltd.&quot;
            },
            &quot;currency&quot;: {
                &quot;id&quot;: 1,
                &quot;code&quot;: &quot;USD&quot;,
                &quot;symbol&quot;: &quot;$&quot;
            },
            &quot;currency_rate&quot;: 0,
            &quot;amount_untaxed&quot;: 2440,
            &quot;amount_tax&quot;: 366,
            &quot;amount_total&quot;: 2806,
            &quot;payment_term&quot;: {
                &quot;id&quot;: 2,
                &quot;name&quot;: &quot;15 Days&quot;
            },
            &quot;fiscal_position&quot;: {
                &quot;id&quot;: null,
                &quot;name&quot;: null
            },
            &quot;journal&quot;: {
                &quot;id&quot;: null,
                &quot;name&quot;: null
            },
            &quot;locked&quot;: true,
            &quot;require_signature&quot;: false,
            &quot;require_payment&quot;: false,
            &quot;signed_by&quot;: null,
            &quot;signed_on&quot;: null,
            &quot;prepayment_percent&quot;: 0,
            &quot;campaign&quot;: {
                &quot;id&quot;: null,
                &quot;name&quot;: null
            },
            &quot;utm_source&quot;: {
                &quot;id&quot;: null,
                &quot;name&quot;: null
            },
            &quot;medium&quot;: {
                &quot;id&quot;: null,
                &quot;name&quot;: null
            },
            &quot;warehouse&quot;: {
                &quot;id&quot;: 5,
                &quot;name&quot;: &quot;Central Open Warehouse&quot;
            },
            &quot;note&quot;: &quot;&lt;p&gt;&lt;/p&gt;&quot;,
            &quot;access_token&quot;: null,
            &quot;created_at&quot;: &quot;2026-01-07T11:12:56+00:00&quot;,
            &quot;updated_at&quot;: &quot;2026-01-08T12:59:31+00:00&quot;,
            &quot;deleted_at&quot;: null
        }
    ],
    &quot;links&quot;: {
        &quot;first&quot;: &quot;http://127.0.0.1:8000/api/v1/admin/sales/orders?page=1&quot;,
        &quot;last&quot;: &quot;http://127.0.0.1:8000/api/v1/admin/sales/orders?page=1&quot;,
        &quot;prev&quot;: null,
        &quot;next&quot;: null
    },
    &quot;meta&quot;: {
        &quot;current_page&quot;: 1,
        &quot;from&quot;: 1,
        &quot;last_page&quot;: 1,
        &quot;links&quot;: [
            {
                &quot;url&quot;: null,
                &quot;label&quot;: &quot;&amp;laquo; Previous&quot;,
                &quot;active&quot;: false
            },
            {
                &quot;url&quot;: &quot;http://127.0.0.1:8000/api/v1/admin/sales/orders?page=1&quot;,
                &quot;label&quot;: &quot;1&quot;,
                &quot;active&quot;: true
            },
            {
                &quot;url&quot;: null,
                &quot;label&quot;: &quot;Next &amp;raquo;&quot;,
                &quot;active&quot;: false
            }
        ],
        &quot;path&quot;: &quot;http://127.0.0.1:8000/api/v1/admin/sales/orders&quot;,
        &quot;per_page&quot;: 15,
        &quot;to&quot;: 1,
        &quot;total&quot;: 1
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-admin-sales-orders" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-admin-sales-orders"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-admin-sales-orders"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-admin-sales-orders" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-admin-sales-orders">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-admin-sales-orders" data-method="GET"
      data-path="api/v1/admin/sales/orders"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-admin-sales-orders', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-admin-sales-orders"
                    onclick="tryItOut('GETapi-v1-admin-sales-orders');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-admin-sales-orders"
                    onclick="cancelTryOut('GETapi-v1-admin-sales-orders');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-admin-sales-orders"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/admin/sales/orders</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-admin-sales-orders"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-admin-sales-orders"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>include</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="include"                data-endpoint="GETapi-v1-admin-sales-orders"
               value="partner,lines"
               data-component="query">
    <br>
<p>Comma-separated list of relationships to include in the response. Example: <code>partner,lines</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>partner</code></li> <li><code>lines</code></li></ul>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>filter[id]</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="filter[id]"                data-endpoint="GETapi-v1-admin-sales-orders"
               value=""
               data-component="query">
    <br>
<p>Comma-separated list of IDs to filter by</p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>filter[state]</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="filter[state]"                data-endpoint="GETapi-v1-admin-sales-orders"
               value=""
               data-component="query">
    <br>
<p>Filter by state</p>
Must be one of:
<ul style="list-style-type: square;"><li><code>draft</code></li> <li><code>sent</code></li> <li><code>sale</code></li> <li><code>cancel</code></li></ul>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>filter[partner_id]</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="filter[partner_id]"                data-endpoint="GETapi-v1-admin-sales-orders"
               value=""
               data-component="query">
    <br>
<p>Comma-separated list of partner IDs to filter by</p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>sort</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="sort"                data-endpoint="GETapi-v1-admin-sales-orders"
               value="-created_at"
               data-component="query">
    <br>
<p>Sort field Example: <code>-created_at</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="page"                data-endpoint="GETapi-v1-admin-sales-orders"
               value="1"
               data-component="query">
    <br>
<p>Page number Example: <code>1</code></p>
            </div>
                </form>

                    <h2 id="sales-api-management-POSTapi-v1-admin-sales-orders">Store a newly created resource in storage.</h2>

<p>
</p>



<span id="example-requests-POSTapi-v1-admin-sales-orders">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://127.0.0.1:8000/api/v1/admin/sales/orders" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"name\": \"Honda\",
    \"model\": \"2020\",
    \"cars\": [
        {
            \"name\": \"Carpenter\",
            \"year\": 2019
        },
        {
            \"name\": \"Electric\",
            \"year\": 2020
        }
    ]
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://127.0.0.1:8000/api/v1/admin/sales/orders"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name": "Honda",
    "model": "2020",
    "cars": [
        {
            "name": "Carpenter",
            "year": 2019
        },
        {
            "name": "Electric",
            "year": 2020
        }
    ]
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-v1-admin-sales-orders">
</span>
<span id="execution-results-POSTapi-v1-admin-sales-orders" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-v1-admin-sales-orders"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-v1-admin-sales-orders"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-v1-admin-sales-orders" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-v1-admin-sales-orders">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-v1-admin-sales-orders" data-method="POST"
      data-path="api/v1/admin/sales/orders"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-v1-admin-sales-orders', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-v1-admin-sales-orders"
                    onclick="tryItOut('POSTapi-v1-admin-sales-orders');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-v1-admin-sales-orders"
                    onclick="cancelTryOut('POSTapi-v1-admin-sales-orders');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-v1-admin-sales-orders"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/v1/admin/sales/orders</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-v1-admin-sales-orders"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-v1-admin-sales-orders"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="POSTapi-v1-admin-sales-orders"
               value="Honda"
               data-component="body">
    <br>
<p>Name of the car. Example: <code>Honda</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>model</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="model"                data-endpoint="POSTapi-v1-admin-sales-orders"
               value="2020"
               data-component="body">
    <br>
<p>Model of the car. Example: <code>2020</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>cars</code></b>&nbsp;&nbsp;
<small>object[]</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
<br>
<p>List of car details.</p>
            </summary>
                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="cars.0.name"                data-endpoint="POSTapi-v1-admin-sales-orders"
               value="consequatur"
               data-component="body">
    <br>
<p>Name of the car. Example: <code>consequatur</code></p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>year</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="cars.0.year"                data-endpoint="POSTapi-v1-admin-sales-orders"
               value="1997"
               data-component="body">
    <br>
<p>Example: <code>1997</code></p>
                    </div>
                                    </details>
        </div>
        </form>

                    <h2 id="sales-api-management-GETapi-v1-admin-sales-orders--id-">Show order</h2>

<p>
</p>

<p>Retrieve a specific order by its ID</p>

<span id="example-requests-GETapi-v1-admin-sales-orders--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://127.0.0.1:8000/api/v1/admin/sales/orders/1?include=partner%2Clines" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://127.0.0.1:8000/api/v1/admin/sales/orders/1"
);

const params = {
    "include": "partner,lines",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-admin-sales-orders--id-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;id&quot;: 1,
    &quot;name&quot;: &quot;SO/1&quot;,
    &quot;state&quot;: &quot;sale&quot;,
    &quot;invoice_status&quot;: &quot;invoiced&quot;,
    &quot;partner&quot;: {
        &quot;id&quot;: 33,
        &quot;name&quot;: &quot;Lyle Holman&quot;
    },
    &quot;partner_invoice&quot;: {
        &quot;id&quot;: 33,
        &quot;name&quot;: &quot;Lyle Holman&quot;
    },
    &quot;partner_shipping&quot;: {
        &quot;id&quot;: 33,
        &quot;name&quot;: &quot;Lyle Holman&quot;
    },
    &quot;client_order_ref&quot;: null,
    &quot;origin&quot;: null,
    &quot;reference&quot;: null,
    &quot;date_order&quot;: &quot;2026-01-07&quot;,
    &quot;validity_date&quot;: &quot;2026-02-06&quot;,
    &quot;commitment_date&quot;: null,
    &quot;user&quot;: {
        &quot;id&quot;: 1,
        &quot;name&quot;: &quot;Admin&quot;
    },
    &quot;team&quot;: {
        &quot;id&quot;: null,
        &quot;name&quot;: null
    },
    &quot;company&quot;: {
        &quot;id&quot;: 1,
        &quot;name&quot;: &quot;TechNova Solutions Pvt. Ltd.&quot;
    },
    &quot;currency&quot;: {
        &quot;id&quot;: 1,
        &quot;code&quot;: &quot;USD&quot;,
        &quot;symbol&quot;: &quot;$&quot;
    },
    &quot;currency_rate&quot;: 0,
    &quot;amount_untaxed&quot;: 2440,
    &quot;amount_tax&quot;: 366,
    &quot;amount_total&quot;: 2806,
    &quot;payment_term&quot;: {
        &quot;id&quot;: 2,
        &quot;name&quot;: &quot;15 Days&quot;
    },
    &quot;fiscal_position&quot;: {
        &quot;id&quot;: null,
        &quot;name&quot;: null
    },
    &quot;journal&quot;: {
        &quot;id&quot;: null,
        &quot;name&quot;: null
    },
    &quot;locked&quot;: true,
    &quot;require_signature&quot;: false,
    &quot;require_payment&quot;: false,
    &quot;signed_by&quot;: null,
    &quot;signed_on&quot;: null,
    &quot;prepayment_percent&quot;: 0,
    &quot;campaign&quot;: {
        &quot;id&quot;: null,
        &quot;name&quot;: null
    },
    &quot;utm_source&quot;: {
        &quot;id&quot;: null,
        &quot;name&quot;: null
    },
    &quot;medium&quot;: {
        &quot;id&quot;: null,
        &quot;name&quot;: null
    },
    &quot;warehouse&quot;: {
        &quot;id&quot;: 5,
        &quot;name&quot;: &quot;Central Open Warehouse&quot;
    },
    &quot;note&quot;: &quot;&lt;p&gt;&lt;/p&gt;&quot;,
    &quot;access_token&quot;: null,
    &quot;created_at&quot;: &quot;2026-01-07T11:12:56+00:00&quot;,
    &quot;updated_at&quot;: &quot;2026-01-08T12:59:31+00:00&quot;,
    &quot;deleted_at&quot;: null
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-admin-sales-orders--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-admin-sales-orders--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-admin-sales-orders--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-admin-sales-orders--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-admin-sales-orders--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-admin-sales-orders--id-" data-method="GET"
      data-path="api/v1/admin/sales/orders/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-admin-sales-orders--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-admin-sales-orders--id-"
                    onclick="tryItOut('GETapi-v1-admin-sales-orders--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-admin-sales-orders--id-"
                    onclick="cancelTryOut('GETapi-v1-admin-sales-orders--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-admin-sales-orders--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/admin/sales/orders/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-admin-sales-orders--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-admin-sales-orders--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="GETapi-v1-admin-sales-orders--id-"
               value="1"
               data-component="url">
    <br>
<p>The order ID Example: <code>1</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>include</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="include"                data-endpoint="GETapi-v1-admin-sales-orders--id-"
               value="partner,lines"
               data-component="query">
    <br>
<p>Comma-separated list of relationships to include in the response. Example: <code>partner,lines</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>partner</code></li> <li><code>lines</code></li></ul>
            </div>
                </form>

                    <h2 id="sales-api-management-PUTapi-v1-admin-sales-orders--id-">Update the specified resource in storage.</h2>

<p>
</p>



<span id="example-requests-PUTapi-v1-admin-sales-orders--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PUT \
    "http://127.0.0.1:8000/api/v1/admin/sales/orders/consequatur" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://127.0.0.1:8000/api/v1/admin/sales/orders/consequatur"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "PUT",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-PUTapi-v1-admin-sales-orders--id-">
</span>
<span id="execution-results-PUTapi-v1-admin-sales-orders--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PUTapi-v1-admin-sales-orders--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PUTapi-v1-admin-sales-orders--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PUTapi-v1-admin-sales-orders--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PUTapi-v1-admin-sales-orders--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PUTapi-v1-admin-sales-orders--id-" data-method="PUT"
      data-path="api/v1/admin/sales/orders/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PUTapi-v1-admin-sales-orders--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PUTapi-v1-admin-sales-orders--id-"
                    onclick="tryItOut('PUTapi-v1-admin-sales-orders--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PUTapi-v1-admin-sales-orders--id-"
                    onclick="cancelTryOut('PUTapi-v1-admin-sales-orders--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PUTapi-v1-admin-sales-orders--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-darkblue">PUT</small>
            <b><code>api/v1/admin/sales/orders/{id}</code></b>
        </p>
            <p>
            <small class="badge badge-purple">PATCH</small>
            <b><code>api/v1/admin/sales/orders/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PUTapi-v1-admin-sales-orders--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PUTapi-v1-admin-sales-orders--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="id"                data-endpoint="PUTapi-v1-admin-sales-orders--id-"
               value="consequatur"
               data-component="url">
    <br>
<p>The ID of the order. Example: <code>consequatur</code></p>
            </div>
                    </form>

                    <h2 id="sales-api-management-DELETEapi-v1-admin-sales-orders--id-">Remove the specified resource from storage.</h2>

<p>
</p>



<span id="example-requests-DELETEapi-v1-admin-sales-orders--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "http://127.0.0.1:8000/api/v1/admin/sales/orders/consequatur" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://127.0.0.1:8000/api/v1/admin/sales/orders/consequatur"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-DELETEapi-v1-admin-sales-orders--id-">
</span>
<span id="execution-results-DELETEapi-v1-admin-sales-orders--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-v1-admin-sales-orders--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-v1-admin-sales-orders--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-v1-admin-sales-orders--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-v1-admin-sales-orders--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-v1-admin-sales-orders--id-" data-method="DELETE"
      data-path="api/v1/admin/sales/orders/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-v1-admin-sales-orders--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-v1-admin-sales-orders--id-"
                    onclick="tryItOut('DELETEapi-v1-admin-sales-orders--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-v1-admin-sales-orders--id-"
                    onclick="cancelTryOut('DELETEapi-v1-admin-sales-orders--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-v1-admin-sales-orders--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/v1/admin/sales/orders/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-v1-admin-sales-orders--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-v1-admin-sales-orders--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="id"                data-endpoint="DELETEapi-v1-admin-sales-orders--id-"
               value="consequatur"
               data-component="url">
    <br>
<p>The ID of the order. Example: <code>consequatur</code></p>
            </div>
                    </form>

            

        
    </div>
    <div class="dark-box">
                    <div class="lang-selector">
                                                        <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                                        <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                            </div>
            </div>
</div>
</body>
</html>
