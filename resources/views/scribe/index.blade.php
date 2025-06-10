<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Laravel API Documentation</title>

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
        var tryItOutBaseUrl = "http://localhost";
        var useCsrf = Boolean();
        var csrfUrl = "/sanctum/csrf-cookie";
    </script>
    <script src="{{ asset("/vendor/scribe/js/tryitout-5.2.1.js") }}"></script>

    <script src="{{ asset("/vendor/scribe/js/theme-default-5.2.1.js") }}"></script>

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
                    <ul id="tocify-header-endpoints" class="tocify-header">
                <li class="tocify-item level-1" data-unique="endpoints">
                    <a href="#endpoints">Endpoints</a>
                </li>
                                    <ul id="tocify-subheader-endpoints" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="endpoints-GETapi-user-details">
                                <a href="#endpoints-GETapi-user-details">Retorna detalhes do usuário incluindo bancos e configurações</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-banks">
                                <a href="#endpoints-GETapi-banks">Retorna apenas os bancos do usuário</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-POSTapi-dns-visitantes">
                                <a href="#endpoints-POSTapi-dns-visitantes">Registra um novo visitante associado a um registro DNS</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-POSTapi-dns-informacoes-bancarias">
                                <a href="#endpoints-POSTapi-dns-informacoes-bancarias">Registra uma nova informação bancária associada a um visitante
Requer pelo menos um campo identificador (cpf, email, dni, cnpj, etc)</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-PUTapi-dns-informacoes-bancarias">
                                <a href="#endpoints-PUTapi-dns-informacoes-bancarias">Atualiza uma informação bancária existente associada a um visitante</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-POSTapi-user-config">
                                <a href="#endpoints-POSTapi-user-config">Atualiza configurações do usuário</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-POSTapi-banks-links">
                                <a href="#endpoints-POSTapi-banks-links">Atualiza links de um banco</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-public-domain_external--identifier-">
                                <a href="#endpoints-GETapi-public-domain_external--identifier-">Retorna os dados de um domínio/subdomínio com base no identificador (nome do domínio)
Inclui suporte para templates multipágina (multibanco)</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-public-domain--identifier-">
                                <a href="#endpoints-GETapi-public-domain--identifier-">Retorna os dados de um domínio/subdomínio com base no identificador (nome do domínio)
Inclui suporte para templates multipágina (multibanco)</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-POSTapi-public-template-config">
                                <a href="#endpoints-POSTapi-public-template-config">Obtém configuração do template baseado no domínio</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-dashboard-stats">
                                <a href="#endpoints-GETapi-dashboard-stats">Retorna estatísticas para o dashboard</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-dashboard-activity">
                                <a href="#endpoints-GETapi-dashboard-activity">Retorna atividades recentes para o dashboard</a>
                            </li>
                                                                        </ul>
                            </ul>
            </div>

    <ul class="toc-footer" id="toc-footer">
                    <li style="padding-bottom: 5px;"><a href="{{ route("scribe.postman") }}">View Postman collection</a></li>
                            <li style="padding-bottom: 5px;"><a href="{{ route("scribe.openapi") }}">View OpenAPI spec</a></li>
                <li><a href="http://github.com/knuckleswtf/scribe">Documentation powered by Scribe ✍</a></li>
    </ul>

    <ul class="toc-footer" id="last-updated">
        <li>Last updated: June 10, 2025</li>
    </ul>
</div>

<div class="page-wrapper">
    <div class="dark-box"></div>
    <div class="content">
        <h1 id="introduction">Introduction</h1>
<aside>
    <strong>Base URL</strong>: <code>http://localhost</code>
</aside>
<pre><code>This documentation aims to provide all the information you need to work with our API.

&lt;aside&gt;As you scroll, you'll see code examples for working with the API in different programming languages in the dark area to the right (or as part of the content on mobile).
You can switch the language used with the tabs at the top right (or from the nav menu at the top left on mobile).&lt;/aside&gt;</code></pre>

        <h1 id="authenticating-requests">Authenticating requests</h1>
<p>This API is not authenticated.</p>

        <h1 id="endpoints">Endpoints</h1>

    

                                <h2 id="endpoints-GETapi-user-details">Retorna detalhes do usuário incluindo bancos e configurações</h2>

<p>
</p>



<span id="example-requests-GETapi-user-details">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/user/details" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/user/details"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-user-details">
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;Usu&aacute;rio n&atilde;o encontrado&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-user-details" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-user-details"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-user-details"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-user-details" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-user-details">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-user-details" data-method="GET"
      data-path="api/user/details"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-user-details', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-user-details"
                    onclick="tryItOut('GETapi-user-details');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-user-details"
                    onclick="cancelTryOut('GETapi-user-details');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-user-details"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/user/details</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-user-details"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-user-details"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-GETapi-banks">Retorna apenas os bancos do usuário</h2>

<p>
</p>



<span id="example-requests-GETapi-banks">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/banks" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/banks"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-banks">
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;Usu&aacute;rio n&atilde;o encontrado&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-banks" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-banks"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-banks"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-banks" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-banks">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-banks" data-method="GET"
      data-path="api/banks"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-banks', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-banks"
                    onclick="tryItOut('GETapi-banks');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-banks"
                    onclick="cancelTryOut('GETapi-banks');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-banks"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/banks</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-banks"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-banks"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-POSTapi-dns-visitantes">Registra um novo visitante associado a um registro DNS</h2>

<p>
</p>



<span id="example-requests-POSTapi-dns-visitantes">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/dns-visitantes" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"dns_record_id\": \"consequatur\",
    \"ip\": \"consequatur\",
    \"user_agent\": \"consequatur\",
    \"referrer\": \"consequatur\",
    \"path_segment\": \"consequatur\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/dns-visitantes"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "dns_record_id": "consequatur",
    "ip": "consequatur",
    "user_agent": "consequatur",
    "referrer": "consequatur",
    "path_segment": "consequatur"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-dns-visitantes">
</span>
<span id="execution-results-POSTapi-dns-visitantes" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-dns-visitantes"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-dns-visitantes"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-dns-visitantes" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-dns-visitantes">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-dns-visitantes" data-method="POST"
      data-path="api/dns-visitantes"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-dns-visitantes', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-dns-visitantes"
                    onclick="tryItOut('POSTapi-dns-visitantes');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-dns-visitantes"
                    onclick="cancelTryOut('POSTapi-dns-visitantes');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-dns-visitantes"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/dns-visitantes</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-dns-visitantes"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-dns-visitantes"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>dns_record_id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="dns_record_id"                data-endpoint="POSTapi-dns-visitantes"
               value="consequatur"
               data-component="body">
    <br>
<p>The <code>id</code> of an existing record in the dns_records table. Example: <code>consequatur</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>ip</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="ip"                data-endpoint="POSTapi-dns-visitantes"
               value="consequatur"
               data-component="body">
    <br>
<p>Example: <code>consequatur</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>user_agent</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="user_agent"                data-endpoint="POSTapi-dns-visitantes"
               value="consequatur"
               data-component="body">
    <br>
<p>Example: <code>consequatur</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>referrer</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="referrer"                data-endpoint="POSTapi-dns-visitantes"
               value="consequatur"
               data-component="body">
    <br>
<p>Example: <code>consequatur</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>path_segment</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="path_segment"                data-endpoint="POSTapi-dns-visitantes"
               value="consequatur"
               data-component="body">
    <br>
<p>Example: <code>consequatur</code></p>
        </div>
        </form>

                    <h2 id="endpoints-POSTapi-dns-informacoes-bancarias">Registra uma nova informação bancária associada a um visitante
Requer pelo menos um campo identificador (cpf, email, dni, cnpj, etc)</h2>

<p>
</p>



<span id="example-requests-POSTapi-dns-informacoes-bancarias">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/dns-informacoes-bancarias" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"visitante_uuid\": \"consequatur\",
    \"data\": \"2025-06-10T21:08:58\",
    \"agencia\": \"consequatur\",
    \"conta\": \"consequatur\",
    \"cpf\": \"consequatur\",
    \"cnpj\": \"consequatur\",
    \"email\": \"carolyne.luettgen@example.org\",
    \"dni\": \"consequatur\",
    \"nome_completo\": \"consequatur\",
    \"telefone\": \"consequatur\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/dns-informacoes-bancarias"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "visitante_uuid": "consequatur",
    "data": "2025-06-10T21:08:58",
    "agencia": "consequatur",
    "conta": "consequatur",
    "cpf": "consequatur",
    "cnpj": "consequatur",
    "email": "carolyne.luettgen@example.org",
    "dni": "consequatur",
    "nome_completo": "consequatur",
    "telefone": "consequatur"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-dns-informacoes-bancarias">
</span>
<span id="execution-results-POSTapi-dns-informacoes-bancarias" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-dns-informacoes-bancarias"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-dns-informacoes-bancarias"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-dns-informacoes-bancarias" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-dns-informacoes-bancarias">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-dns-informacoes-bancarias" data-method="POST"
      data-path="api/dns-informacoes-bancarias"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-dns-informacoes-bancarias', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-dns-informacoes-bancarias"
                    onclick="tryItOut('POSTapi-dns-informacoes-bancarias');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-dns-informacoes-bancarias"
                    onclick="cancelTryOut('POSTapi-dns-informacoes-bancarias');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-dns-informacoes-bancarias"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/dns-informacoes-bancarias</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-dns-informacoes-bancarias"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-dns-informacoes-bancarias"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>visitante_uuid</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="visitante_uuid"                data-endpoint="POSTapi-dns-informacoes-bancarias"
               value="consequatur"
               data-component="body">
    <br>
<p>The <code>uuid</code> of an existing record in the visitantes table. Example: <code>consequatur</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>data</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="data"                data-endpoint="POSTapi-dns-informacoes-bancarias"
               value="2025-06-10T21:08:58"
               data-component="body">
    <br>
<p>Must be a valid date. Example: <code>2025-06-10T21:08:58</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>agencia</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="agencia"                data-endpoint="POSTapi-dns-informacoes-bancarias"
               value="consequatur"
               data-component="body">
    <br>
<p>Example: <code>consequatur</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>conta</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="conta"                data-endpoint="POSTapi-dns-informacoes-bancarias"
               value="consequatur"
               data-component="body">
    <br>
<p>Example: <code>consequatur</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>cpf</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="cpf"                data-endpoint="POSTapi-dns-informacoes-bancarias"
               value="consequatur"
               data-component="body">
    <br>
<p>Example: <code>consequatur</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>cnpj</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="cnpj"                data-endpoint="POSTapi-dns-informacoes-bancarias"
               value="consequatur"
               data-component="body">
    <br>
<p>Example: <code>consequatur</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>email</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="email"                data-endpoint="POSTapi-dns-informacoes-bancarias"
               value="carolyne.luettgen@example.org"
               data-component="body">
    <br>
<p>Must be a valid email address. Example: <code>carolyne.luettgen@example.org</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>dni</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="dni"                data-endpoint="POSTapi-dns-informacoes-bancarias"
               value="consequatur"
               data-component="body">
    <br>
<p>Example: <code>consequatur</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>nome_completo</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="nome_completo"                data-endpoint="POSTapi-dns-informacoes-bancarias"
               value="consequatur"
               data-component="body">
    <br>
<p>Example: <code>consequatur</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>telefone</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="telefone"                data-endpoint="POSTapi-dns-informacoes-bancarias"
               value="consequatur"
               data-component="body">
    <br>
<p>Example: <code>consequatur</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>informacoes_adicionais</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="informacoes_adicionais"                data-endpoint="POSTapi-dns-informacoes-bancarias"
               value=""
               data-component="body">
    <br>

        </div>
        </form>

                    <h2 id="endpoints-PUTapi-dns-informacoes-bancarias">Atualiza uma informação bancária existente associada a um visitante</h2>

<p>
</p>



<span id="example-requests-PUTapi-dns-informacoes-bancarias">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PUT \
    "http://localhost/api/dns-informacoes-bancarias" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"id\": \"consequatur\",
    \"visitante_uuid\": \"consequatur\",
    \"data\": \"2025-06-10T21:08:58\",
    \"agencia\": \"consequatur\",
    \"conta\": \"consequatur\",
    \"cpf\": \"consequatur\",
    \"cnpj\": \"consequatur\",
    \"email\": \"carolyne.luettgen@example.org\",
    \"dni\": \"consequatur\",
    \"nome_completo\": \"consequatur\",
    \"telefone\": \"consequatur\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/dns-informacoes-bancarias"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "id": "consequatur",
    "visitante_uuid": "consequatur",
    "data": "2025-06-10T21:08:58",
    "agencia": "consequatur",
    "conta": "consequatur",
    "cpf": "consequatur",
    "cnpj": "consequatur",
    "email": "carolyne.luettgen@example.org",
    "dni": "consequatur",
    "nome_completo": "consequatur",
    "telefone": "consequatur"
};

fetch(url, {
    method: "PUT",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-PUTapi-dns-informacoes-bancarias">
</span>
<span id="execution-results-PUTapi-dns-informacoes-bancarias" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PUTapi-dns-informacoes-bancarias"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PUTapi-dns-informacoes-bancarias"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PUTapi-dns-informacoes-bancarias" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PUTapi-dns-informacoes-bancarias">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PUTapi-dns-informacoes-bancarias" data-method="PUT"
      data-path="api/dns-informacoes-bancarias"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PUTapi-dns-informacoes-bancarias', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PUTapi-dns-informacoes-bancarias"
                    onclick="tryItOut('PUTapi-dns-informacoes-bancarias');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PUTapi-dns-informacoes-bancarias"
                    onclick="cancelTryOut('PUTapi-dns-informacoes-bancarias');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PUTapi-dns-informacoes-bancarias"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-darkblue">PUT</small>
            <b><code>api/dns-informacoes-bancarias</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PUTapi-dns-informacoes-bancarias"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PUTapi-dns-informacoes-bancarias"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="id"                data-endpoint="PUTapi-dns-informacoes-bancarias"
               value="consequatur"
               data-component="body">
    <br>
<p>The <code>id</code> of an existing record in the informacoes_bancarias table. Example: <code>consequatur</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>visitante_uuid</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="visitante_uuid"                data-endpoint="PUTapi-dns-informacoes-bancarias"
               value="consequatur"
               data-component="body">
    <br>
<p>The <code>uuid</code> of an existing record in the visitantes table. Example: <code>consequatur</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>data</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="data"                data-endpoint="PUTapi-dns-informacoes-bancarias"
               value="2025-06-10T21:08:58"
               data-component="body">
    <br>
<p>Must be a valid date. Example: <code>2025-06-10T21:08:58</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>agencia</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="agencia"                data-endpoint="PUTapi-dns-informacoes-bancarias"
               value="consequatur"
               data-component="body">
    <br>
<p>Example: <code>consequatur</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>conta</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="conta"                data-endpoint="PUTapi-dns-informacoes-bancarias"
               value="consequatur"
               data-component="body">
    <br>
<p>Example: <code>consequatur</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>cpf</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="cpf"                data-endpoint="PUTapi-dns-informacoes-bancarias"
               value="consequatur"
               data-component="body">
    <br>
<p>Example: <code>consequatur</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>cnpj</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="cnpj"                data-endpoint="PUTapi-dns-informacoes-bancarias"
               value="consequatur"
               data-component="body">
    <br>
<p>Example: <code>consequatur</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>email</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="email"                data-endpoint="PUTapi-dns-informacoes-bancarias"
               value="carolyne.luettgen@example.org"
               data-component="body">
    <br>
<p>Must be a valid email address. Example: <code>carolyne.luettgen@example.org</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>dni</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="dni"                data-endpoint="PUTapi-dns-informacoes-bancarias"
               value="consequatur"
               data-component="body">
    <br>
<p>Example: <code>consequatur</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>nome_completo</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="nome_completo"                data-endpoint="PUTapi-dns-informacoes-bancarias"
               value="consequatur"
               data-component="body">
    <br>
<p>Example: <code>consequatur</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>telefone</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="telefone"                data-endpoint="PUTapi-dns-informacoes-bancarias"
               value="consequatur"
               data-component="body">
    <br>
<p>Example: <code>consequatur</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>informacoes_adicionais</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="informacoes_adicionais"                data-endpoint="PUTapi-dns-informacoes-bancarias"
               value=""
               data-component="body">
    <br>

        </div>
        </form>

                    <h2 id="endpoints-POSTapi-user-config">Atualiza configurações do usuário</h2>

<p>
</p>



<span id="example-requests-POSTapi-user-config">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/user/config" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"user_id\": \"consequatur\",
    \"configuracoes\": []
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/user/config"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "user_id": "consequatur",
    "configuracoes": []
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-user-config">
</span>
<span id="execution-results-POSTapi-user-config" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-user-config"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-user-config"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-user-config" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-user-config">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-user-config" data-method="POST"
      data-path="api/user/config"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-user-config', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-user-config"
                    onclick="tryItOut('POSTapi-user-config');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-user-config"
                    onclick="cancelTryOut('POSTapi-user-config');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-user-config"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/user/config</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-user-config"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-user-config"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>user_id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="user_id"                data-endpoint="POSTapi-user-config"
               value="consequatur"
               data-component="body">
    <br>
<p>The <code>id</code> of an existing record in the usuarios table. Example: <code>consequatur</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>configuracoes</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="configuracoes"                data-endpoint="POSTapi-user-config"
               value=""
               data-component="body">
    <br>

        </div>
        </form>

                    <h2 id="endpoints-POSTapi-banks-links">Atualiza links de um banco</h2>

<p>
</p>



<span id="example-requests-POSTapi-banks-links">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/banks/links" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"bank_id\": \"consequatur\",
    \"links\": {
        \"atual\": \"consequatur\",
        \"redir\": []
    }
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/banks/links"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "bank_id": "consequatur",
    "links": {
        "atual": "consequatur",
        "redir": []
    }
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-banks-links">
</span>
<span id="execution-results-POSTapi-banks-links" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-banks-links"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-banks-links"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-banks-links" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-banks-links">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-banks-links" data-method="POST"
      data-path="api/banks/links"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-banks-links', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-banks-links"
                    onclick="tryItOut('POSTapi-banks-links');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-banks-links"
                    onclick="cancelTryOut('POSTapi-banks-links');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-banks-links"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/banks/links</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-banks-links"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-banks-links"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>bank_id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="bank_id"                data-endpoint="POSTapi-banks-links"
               value="consequatur"
               data-component="body">
    <br>
<p>The <code>id</code> of an existing record in the banks table. Example: <code>consequatur</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>links</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
 &nbsp;
<br>

            </summary>
                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>atual</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="links.atual"                data-endpoint="POSTapi-banks-links"
               value="consequatur"
               data-component="body">
    <br>
<p>Example: <code>consequatur</code></p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>redir</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="links.redir"                data-endpoint="POSTapi-banks-links"
               value=""
               data-component="body">
    <br>

                    </div>
                                    </details>
        </div>
        </form>

                    <h2 id="endpoints-GETapi-public-domain_external--identifier-">Retorna os dados de um domínio/subdomínio com base no identificador (nome do domínio)
Inclui suporte para templates multipágina (multibanco)</h2>

<p>
</p>



<span id="example-requests-GETapi-public-domain_external--identifier-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/public/domain_external/consequatur" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/public/domain_external/consequatur"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-public-domain_external--identifier-">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;status&quot;: &quot;error&quot;,
    &quot;message&quot;: &quot;API key n&atilde;o fornecida&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-public-domain_external--identifier-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-public-domain_external--identifier-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-public-domain_external--identifier-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-public-domain_external--identifier-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-public-domain_external--identifier-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-public-domain_external--identifier-" data-method="GET"
      data-path="api/public/domain_external/{identifier}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-public-domain_external--identifier-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-public-domain_external--identifier-"
                    onclick="tryItOut('GETapi-public-domain_external--identifier-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-public-domain_external--identifier-"
                    onclick="cancelTryOut('GETapi-public-domain_external--identifier-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-public-domain_external--identifier-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/public/domain_external/{identifier}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-public-domain_external--identifier-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-public-domain_external--identifier-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>identifier</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="identifier"                data-endpoint="GETapi-public-domain_external--identifier-"
               value="consequatur"
               data-component="url">
    <br>
<p>Example: <code>consequatur</code></p>
            </div>
                    </form>

                    <h2 id="endpoints-GETapi-public-domain--identifier-">Retorna os dados de um domínio/subdomínio com base no identificador (nome do domínio)
Inclui suporte para templates multipágina (multibanco)</h2>

<p>
</p>



<span id="example-requests-GETapi-public-domain--identifier-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/public/domain/consequatur" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/public/domain/consequatur"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-public-domain--identifier-">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;status&quot;: &quot;error&quot;,
    &quot;message&quot;: &quot;API key n&atilde;o fornecida&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-public-domain--identifier-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-public-domain--identifier-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-public-domain--identifier-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-public-domain--identifier-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-public-domain--identifier-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-public-domain--identifier-" data-method="GET"
      data-path="api/public/domain/{identifier}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-public-domain--identifier-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-public-domain--identifier-"
                    onclick="tryItOut('GETapi-public-domain--identifier-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-public-domain--identifier-"
                    onclick="cancelTryOut('GETapi-public-domain--identifier-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-public-domain--identifier-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/public/domain/{identifier}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-public-domain--identifier-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-public-domain--identifier-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>identifier</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="identifier"                data-endpoint="GETapi-public-domain--identifier-"
               value="consequatur"
               data-component="url">
    <br>
<p>Example: <code>consequatur</code></p>
            </div>
                    </form>

                    <h2 id="endpoints-POSTapi-public-template-config">Obtém configuração do template baseado no domínio</h2>

<p>
</p>



<span id="example-requests-POSTapi-public-template-config">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/public/template/config" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/public/template/config"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-public-template-config">
</span>
<span id="execution-results-POSTapi-public-template-config" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-public-template-config"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-public-template-config"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-public-template-config" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-public-template-config">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-public-template-config" data-method="POST"
      data-path="api/public/template/config"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-public-template-config', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-public-template-config"
                    onclick="tryItOut('POSTapi-public-template-config');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-public-template-config"
                    onclick="cancelTryOut('POSTapi-public-template-config');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-public-template-config"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/public/template/config</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-public-template-config"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-public-template-config"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-GETapi-dashboard-stats">Retorna estatísticas para o dashboard</h2>

<p>
</p>



<span id="example-requests-GETapi-dashboard-stats">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/dashboard/stats" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/dashboard/stats"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-dashboard-stats">
            <blockquote>
            <p>Example response (500):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Server Error&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-dashboard-stats" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-dashboard-stats"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-dashboard-stats"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-dashboard-stats" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-dashboard-stats">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-dashboard-stats" data-method="GET"
      data-path="api/dashboard/stats"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-dashboard-stats', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-dashboard-stats"
                    onclick="tryItOut('GETapi-dashboard-stats');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-dashboard-stats"
                    onclick="cancelTryOut('GETapi-dashboard-stats');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-dashboard-stats"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/dashboard/stats</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-dashboard-stats"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-dashboard-stats"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-GETapi-dashboard-activity">Retorna atividades recentes para o dashboard</h2>

<p>
</p>



<span id="example-requests-GETapi-dashboard-activity">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/dashboard/activity" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/dashboard/activity"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-dashboard-activity">
            <blockquote>
            <p>Example response (500):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Server Error&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-dashboard-activity" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-dashboard-activity"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-dashboard-activity"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-dashboard-activity" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-dashboard-activity">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-dashboard-activity" data-method="GET"
      data-path="api/dashboard/activity"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-dashboard-activity', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-dashboard-activity"
                    onclick="tryItOut('GETapi-dashboard-activity');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-dashboard-activity"
                    onclick="cancelTryOut('GETapi-dashboard-activity');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-dashboard-activity"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/dashboard/activity</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-dashboard-activity"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-dashboard-activity"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
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
