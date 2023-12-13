<html lang="en" class="light" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@2.51.3/dist/full.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <link href="https://cdn.jsdelivr.net/gh/hung1001/font-awesome-pro@4cac1a6/css/all.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/7.3.67/css/materialdesignicons.min.css" integrity="sha512-nRzny9w0V2Y1/APe+iEhKAwGAc+K8QYCw4vJek3zXhdn92HtKt226zHs9id8eUq+uYJKaH2gPyuLcaG/dE5c7A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link href='https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.16.0/codemirror.css' rel='stylesheet'>
    <!-- The link above loaded the core css -->
    <script src='https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.16.0/codemirror.js'></script>
    <!-- The script above loaded the core editor -->

    <script src='https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.16.0/mode/javascript/javascript.js'></script>

    {{--    <script src="https://codemirror.net/5/lib/codemirror.js"></script>--}}
    {{--    <link rel="stylesheet" href="https://codemirror.net/5/doc/docs.css" />--}}
    <link rel="stylesheet" href="https://codemirror.net/5/theme/material.css" />
    {{--    <script src="https://codemirror.net/5/mode/javascript/javascript.js"></script>--}}
    <script src="https://codemirror.net/5/addon/selection/active-line.js"></script>
    <script src="https://codemirror.net/5/addon/edit/matchbrackets.js"></script>
    <title>Calegplus API</title>
    <link rel="shortcut icon" href="{{ asset('/image/logo/logo-calegplus.png') }}" />

    <meta name="title" content="{{ env('APP_NAME') }}">
    <meta name="description" content="Documentation for AWokey developers. ">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ env('APP_URL') }}">
    <meta property="og:title" content="{{ env('APP_NAME') }}">
    <meta property="og:description" content="Documentation for AWokey developers. ">
    <!--    <meta property="og:image" content="{{ asset('/img/ihome-seo-img.png') }}">-->

    <style>
        html {
            scroll-behavior: smooth;
        }

        li {
            font-size: 14px;
            margin-left: 10px;
            list-style-type: disc;
        }
    </style>
</head>

<body class="block bg-base-300" hidden>
<div id="app" class="flex container mx-auto flex-col">
    <div class="container mx-auto px-4 py-2">
        <div class="card w-full bg-base-100 shadow-xl mt-5">
            <div class="card-body pt-3 pb-3 px-6">
                <div class="container">
                    <div class="grid grid-cols-12">
                        <div class="col-span-12 lg:col-span-6 flex items-center my-5 lg:my-0">
                            <!-- Logo -->
                            <div class="flex-grow flex items-center">
                                <img src="/image/logo/logo-calegplus.png" alt="ihome-logo" style="height: 27px" class="logo-light hidden" />
                                <img src="/image/logo/logo-calegplus-light.png" alt="ihome-logo" style="height: 27px" class="logo-dark hidden" />
                                <div class="mt-1 ml-2 font-bold">CalegPlus</div>
                            </div>

                            <!-- Theme Toggle For Mobile View -->
                            <div class="dropdown dropdown-end block lg:hidden flex-grow-1">
                                <label tabindex="0" class="btn btn-sm btn-ghost text-lg text-gray-600 dark:text-gray-300">
                                    <i class="mdi icon-theme-light mdi-weather-sunny"></i>
                                    <i class="mdi icon-theme-dark hidden mdi-weather-night"></i>
                                    <i class="mdi icon-theme-system hidden mdi-monitor"></i>
                                </label>
                                <ul tabindex="0" class="dropdown-content menu p-2 bg-gray-200 dark:bg-gray-700 shadow-lg rounded-box w-52">
                                    <li><a onclick="onToggleTheme('light')">
                                            <i class="mdi mdi-weather-sunny"></i> Light</a>
                                    </li>
                                    <li><a onclick="onToggleTheme('dark')">
                                            <i class="mdi mdi-weather-night"></i> Dark</a>
                                    </li>
                                    <li><a onclick="onToggleTheme('system')">
                                            <span class="mdi mdi-monitor"></span> System</a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Navbar Menu -->
                        <div class="col-span-12 lg:col-span-6 flex lg:justify-end items-center">
                            <div class="flex items-center flex-wrap gap-2">
                                <div
                                    onclick="onOpenModal('signin-modal')"
                                    class="btn btn-xs lg:btn-sm border-0 bg-blue-500 hover:bg-blue-600 text-white"
                                >
                                    Login
                                </div>
                                <div
                                    onclick="onOpenModal('settoken-modal')"
                                    class="btn btn-xs lg:btn-sm border-0 bg-orange-500 hover:bg-orange-600 text-white"
                                >
                                    Set Token
                                </div>
                                <button
                                    type="button"
                                    class="btn btn-xs lg:btn-sm border-0 bg-red-500 hover:bg-red-600 text-white"
                                    onclick="onReset()"
                                >
                                    Reset Token
                                </button>
                                <div
                                    onclick="onOpenModal('config-modal')"
                                    class="btn btn-xs lg:btn-sm border-0 bg-gray-500 hover:bg-gray-600 text-white"
                                >
                                    Config
                                </div>
                                <div class="dropdown dropdown-hover dropdown-end">
                                    <label tabindex="0" class="btn btn-sm btn-ghost text-lg text-gray-600 dark:text-gray-300">
                                        <i class="mdi icon-theme-light mdi-weather-sunny"></i>
                                        <i class="mdi icon-theme-dark hidden mdi-weather-night"></i>
                                        <i class="mdi icon-theme-system hidden mdi-monitor"></i>
                                    </label>
                                    <ul tabindex="0" class="dropdown-content menu p-2 bg-gray-200 dark:bg-gray-700 shadow-lg rounded-box w-52">
                                        <li><a onclick="onToggleTheme('light')">
                                                <i class="mdi mdi-weather-sunny"></i> Light</a>
                                        </li>
                                        <li><a onclick="onToggleTheme('dark')">
                                                <i class="mdi mdi-weather-night"></i> Dark</a>
                                        </li>
                                        <li><a onclick="onToggleTheme('system')">
                                                <span class="mdi mdi-monitor"></span> System</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-12 mt-5">
            <!-- API Documentation -->
            <div class="col-span-12 mb-2">
                <div class="collapse collapse-arrow border border-base-300 bg-base-100 shadow-xl rounded-box">
                    <input type="checkbox" checked/>
                    <div class="collapse-title text-xl font-medium dark:text-gray-300">
                        Query Params
                    </div>
                    <div class="collapse-content">
                        <div class="container">
                            @foreach($metaData['params'] as $key => $keterangan)
                            <div class="w-full border-b @if($key === 'paginate') border-t @endif border-r border-l dark:text-gray-300 items-center px-5">
                                <div class="grid grid-cols-12 py-3 lg:py-0">
                                    <div class="col-span-12 lg:col-span-6 flex">
                                        <b>{{$key}}</b><b class="ml-1 flex lg:hidden">:</b>
                                    </div>
                                    <div class="col-span-12 lg:col-span-6 flex">
                                        <span>{{$keterangan}}</span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- API List -->
            @foreach($metaData['endpoints'] as $index => $endpoint)
            <div class="col-span-12 mb-2 transition-all duration-200 rounded-box" id="{{ $endpoint['model'] }}" onclick="onCollapse(`{{$endpoint['model']}}`)">
                <div class="collapse-model-{{$endpoint['model']}} collapse collapse-arrow border border-base-300 bg-base-100 shadow-xl rounded-box">
                    <input type="checkbox" />
                    <div class="collapse-title text-xl font-medium dark:text-gray-300">
                        {{ $endpoint['model'] }}
                    </div>
                    <div class="collapse-content">
                        @if($endpoint['description'])
                        <div class="mb-5 dark:text-gray-300">
                            {{ $endpoint['description'] }}
                        </div>
                        @endif
                        @foreach($endpoint['items'] as $index => $item)
                        @if(!isset($item['hide']) || (isset($item['hide']) && !$item['hide']))
                        <div class="grid grid-cols-12">
                            <div class="col-span-2 lg:col-span-1 border-b @if(!$index) border-t @endif border-l p-1 select-all hover:bg-opacity-80
                                    @if($item['type'] == 'GET')
                                        bg-blue-400 text-white
                                    @elseif($item['type'] == 'GET_ID')
                                        bg-blue-600 text-white
                                    @elseif($item['type'] == 'POST')
                                        bg-emerald-500 text-white
                                    @elseif($item['type'] == 'DELETE')
                                        bg-red-600 text-white
                                    @elseif($item['type'] == 'PUT')
                                        bg-yellow-600 text-white
                                    @else
                                        bg-gray-200
                                    @endif">
                                {{ $item['type'] }}
                            </div>
                            <div class="col-span-10 lg:col-span-11 border-b @if(!$index) border-t @endif border-r border-l flex items-center pl-4">
                                <span class="select-all dark:text-gray-300">
                                    {{ $item['path'] }}
                                </span>
                                @php
                                $validation = $item['validation'] ?? null;
                                @endphp
                                @if(in_array($item['type'],['GET']))
                                <span
                                    onclick='onAction("try", "{{$item["path"]}}", "GET", "", {!! json_encode($item["available_scopes"] ?? []) !!}, {!! json_encode($item["notes"] ?? []) !!})'
                                    class="ml-4 bg-blue-600 px-2 py-0.5 rounded text-white text-xs cursor-pointer"
                                >
                                    Try
                                </span>
                                @elseif(in_array($item['type'],['GET_ID']))
                                <span
                                    onclick='onAction("try", "{{$item["path"]}}", "GET_ID", "", {!! json_encode($item["available_scopes"] ?? []) !!}, {!! json_encode($item["notes"] ?? []) !!})'
                                    class="ml-4 bg-blue-600 px-2 py-0.5 rounded text-white text-xs cursor-pointer"
                                >
                                    Try
                                </span>
                                @elseif(in_array($item['type'],['DELETE']))
                                <span
                                    onclick='onAction("try", "{{$item["path"]}}", "DELETE", "", {!! json_encode($item["available_scopes"] ?? []) !!}, {!! json_encode($item["notes"] ?? []) !!})'
                                    class="ml-4 bg-blue-600 px-2 py-0.5 rounded text-white text-xs cursor-pointer"
                                >
                                    Try
                                </span>
                                @elseif(in_array($item['type'],['PUT']))
                                <span
                                    onclick='onAction("try", "{{$item["path"]}}", "PUT", {{$validation}}, {!! json_encode($item["available_scopes"] ?? []) !!}, {!! json_encode($item["notes"] ?? []) !!})'
                                    class="ml-4 bg-blue-600 px-2 py-0.5 rounded text-white text-xs cursor-pointer"
                                >
                                    Try
                                </span>
                                @elseif(in_array($item['type'],['POST']))
                                <span
                                    onclick='onAction("try", "{{$item["path"]}}", "POST", {{$validation}}, {!! json_encode($item["available_scopes"] ?? []) !!}, {!! json_encode($item["notes"] ?? []) !!})'
                                    class="ml-4 bg-blue-600 px-2 py-0.5 rounded text-white text-xs cursor-pointer"
                                >
                                    Try
                                </span>
                                @endif
                                @if(isset($item['last_updated']))
                                @php
                                $start = \Carbon\Carbon::parse('2023-06-25 00:00:00');
                                $end =  \Carbon\Carbon::parse(now());
                                $days = $end->diffInDays($start);
                                @endphp
                                @if($days < 7)
                                <span class="bg-emerald-500 px-2 py-0.5 rounded text-white text-xs ml-2"><i class="fad fa-chevron-double-up"></i> Updated: {{ Carbon\Carbon::parse($item['last_updated'])->format('d/m/Y') }}</span>
                                @endif
                                @endif
                                @if(isset($item['note']))
                                <span class="bg-gray-400 px-2 py-0.5 rounded text-white text-xs ml-2"><b>Note:</b> {{ $item['note'] }}</span>
                                @endif
                                @if(isset($item['labels']) && count($item['labels']))
                                @foreach($item['labels'] as $label)
                                <span class="px-2 py-0.5 rounded {{$label['bg-color'] ?? 'bg-red-400'}} {{$label['text-color'] ?? 'text-white'}} text-xs ml-2">
                                    <i class="fas fa-tag"></i> {{ $label['text'] }}
                                </span>
                                @endforeach
                                @endif
                                @if(isset($item['screenshot']) && count($item['screenshot']) && $item['screenshot']['url'])
                                <span
                                    onclick="onShowScreenshot(`{{$item['screenshot']['url']}}`, `{{$item['screenshot']['figma_url']}}`)"
                                    class="bg-blue-600 px-2 py-0.5 rounded text-white text-xs cursor-pointer ml-2"
                                >
                                    <i class="fas fa-camera"></i> Screenshot
                                </span>
                                @endif
                            </div>
                        </div>
                        @endif
                        @endforeach
                        <div class="grid grid-cols-12 mt-3">
                            <div class="col-span-12 flex justify-end">
                                <div onclick="clipboard(`{{$endpoint['model']}}`)" class="text-gray-400 cursor-pointer">Copy to clipboard <i class="fas fa-copy"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="modal signin-modal">
    <div class="modal-box bg-gray-50 dark:bg-gray-700" style="min-width: 700px">
        <label onclick="onCloseModal('signin-modal')" class="btn btn-sm btn-circle absolute right-2 top-2">✕</label>
        <h3 class="font-bold text-lg">Signin</h3>

        <div class="tabs mt-4">
            <a class="tab tab-bordered login-tab-bo tab-active" onclick="onClickTabSigninType('bo')">
                <span class="mdi mdi-monitor mr-1"></span> Backoffice
            </a>
            <a class="tab tab-bordered login-tab-mb" onclick="onClickTabSigninType('mb')">
                <span class="mdi mdi-cellphone mr-1"></span> Mobile
            </a>
        </div>

        <form onsubmit="return onSignin('bo')" class="grid grid-cols-12 gap-2 pt-5 pb-0 form-login-bo">
            <div class="col-span-6">
                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text">Identity</span>
                    </label>
                    <input type="text" id="identity" placeholder="Username or Email" class="input input-bordered w-full" />
                </div>
            </div>
            <div class="col-span-6">
                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text">Password</span>
                    </label>
                    <input type="password" id="password" placeholder="*****" class="input input-bordered w-full" />
                </div>
            </div>
            <div class="col-span-12 flex justify-end mt-5">
                <button
                    type="submit"
                    class="btn btn-sm bg-blue-500 hover:bg-blue-600 text-white border-0 btn-signin normal-case"
                >
                    Signin <span class="mdi mdi-login ml-2"></span>
                </button>
            </div>
        </form>
        <form onsubmit="return onSignin('mb')" class="grid grid-cols-12 gap-2 pt-5 pb-0 form-login-mb hidden">
            <div class="col-span-12 form-login-mb-email">
                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text">Email</span>
                    </label>
                    <input type="email" id="email" placeholder="Email" class="input input-bordered w-full" />
                </div>
            </div>
            <div class="col-span-12 form-login-mb-otp_code hidden">
                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text">OTP Code</span>
                    </label>
                    <input type="text" id="otp_code" placeholder="OTP Code" class="input input-bordered w-full" />
                </div>
            </div>
            <div class="col-span-12 flex justify-end mt-5">
                <button
                    type="submit"
                    class="btn btn-sm bg-blue-500 hover:bg-blue-600 text-white border-0 btn-signin normal-case"
                >
                    Signin <span class="mdi mdi-login ml-2"></span>
                </button>
            </div>
        </form>
    </div>
</div>

<div class="modal settoken-modal">
    <div class="modal-box bg-gray-50 dark:bg-gray-700">
        <label onclick="onCloseModal('settoken-modal')" class="btn btn-sm btn-circle absolute right-2 top-2">✕</label>
        <h3 class="font-bold text-lg">Set Token</h3>
        <form onsubmit="return onSaveToken(event)" class="grid grid-cols-12 gap-2 pt-5 pb-0">
            <div class="col-span-12">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Bearer Token</span>
                    </label>
                    <textarea
                        id="inputtoken"
                        class="textarea textarea-bordered h-24"
                        onchange="onChangeToken()"
                        onkeyup="onChangeToken()"
                        onkeydown="onChangeToken()"
                    ></textarea>
                </div>
            </div>
            <div class="modal-action col-span-12 flex justify-end mt-5">
                <button
                    type="submit"
                    class="btn btn-settoken btn-sm bg-blue-500 hover:bg-blue-600 text-white border-0 normal-case"
                >
                    Save
                </button>
            </div>
        </form>
    </div>
</div>

<div class="modal config-modal">
    <div class="modal-box bg-gray-50 dark:bg-gray-700">
        <label onclick="onCloseModal('config-modal')" class="btn btn-sm btn-circle absolute right-2 top-2">✕</label>
        <h3 class="font-bold text-lg">Configs</h3>
        <form onsubmit="return onSaveConfig(event)" class="grid grid-cols-12 gap-2 pt-5 pb-0">
            <div class="col-span-12">
                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text">HOST</span>
                    </label>
                    <input
                        type="text"
                        id="inputhost"
                        placeholder="http://example.com"
                        class="input input-bordered w-full" />
                </div>
            </div>
            <div class="col-span-12 flex justify-end mt-5">
                <button
                    type="submit"
                    class="btn btn-sm bg-blue-500 hover:bg-blue-600 text-white border-0 normal-case"
                >
                    Save
                </button>
            </div>
        </form>
    </div>
</div>

<div class="modal test-modal">
    <div class="modal-box bg-gray-50 dark:bg-gray-700 w-11/12 max-w-4xl">
        <label onclick="onCloseModal('test-modal')" class="btn btn-sm btn-circle absolute right-2 top-2">✕</label>
        <div class="tabs">
            <a class="tab tab-bordered test-modal-tab-request tab-active dark:text-gray-300" onclick="onChangeTab('test-modal', 'request')">Request</a>
            <a class="tab tab-bordered test-modal-tab-response dark:text-gray-300" onclick="onChangeTab('test-modal', 'response')">Response</a>
            <a class="tab tab-bordered test-modal-tab-validation hidden dark:text-gray-300" onclick="onChangeTab('test-modal', 'validation')">Validation</a>
        </div>
        <div class="test-modal-body-request grid grid-cols-12 gap-2 pt-5 pb-0">
            <div class="col-span-12 flex">
                <select
                    id="test-regmethod"
                    class="select select-bordered w-full max-w-xs flex-grow-1 mr-2" style="max-width: 100px"
                    onchange="onMethodChange()"
                >
                    <option>GET</option>
                    <option>POST</option>
                    <option>PUT</option>
                    <option>DELETE</option>
                </select>
                <input
                    type="text"
                    id="test-regurl"
                    placeholder="Enter request url"
                    class="input input-bordered w-full flex-grow" />
            </div>
            <div class="col-span-12 pl-4 available_scopes_list hidden">
                <h2>Available Scopes</h2>
                <ul class="available_scopes">
                </ul>
                <small class="font-medium">Contoh: https://example.com?scopes=scope1,scope2,...</small>
                <br /><br />
            </div>
            <div class="col-span-12 pl-4 available_notes_list hidden">
                <h2>Note</h2>
                <ul class="available_notes">
                </ul>
            </div>
            <div class="col-span-12 test-withbody hidden">
                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text">Body</span>
                    </label>
                    <textarea id="test-regbody" class="textarea textarea-bordered w-full" autocomplete="off"></textarea>
                </div>
            </div>
            <div class="col-span-12 flex justify-end mt-5">
                <button
                    type="button"
                    onclick="onSendRequest()"
                    class="btn btn-sendreq btn-sm bg-blue-500 hover:bg-blue-600 text-white border-0"
                >
                    Send <i class="fas btn-sendreq-unspinner ml-2 fa-paper-plane"></i><i class="fad btn-sendreq-spinner"></i>
                </button>
            </div>
        </div>
        <div class="test-modal-body-response grid grid-cols-12 pt-5 hidden">
            <div class="col-span-12 test-result">
                <div style="max-height: 500px; overflow-x: hidden; overflow-y: auto">
                    <pre id="json-display-ouput-request"></pre>
                </div>
            </div>
        </div>
        <div class="test-modal-body-validation grid grid-cols-12 pt-5 hidden" style="min-height: 327px">
            <div class="col-span-12">
                <div style="max-height: 350px; overflow-x: hidden; overflow-y: auto">
                    <pre id="json-display-ouput-validation"></pre>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal ss-modal">
    <div class="modal-box bg-gray-50 dark:bg-gray-700 w-11/12 max-w-4xl">
        <label onclick="onCloseModal('ss-modal')" class="btn btn-sm btn-circle absolute right-2 top-2">✕</label>
        <h3 class="text-lg font-bold">Screenshot</h3>
        <div id="img-ss-container" class="flex justify-center"></div>
        <div id="img-figma-container" class="flex justify-center"></div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script type="text/javascript" src="{{asset('postman/dist/jquery.json-editor.min.js')}}"></script>
<script>
    tailwind.config.darkMode = 'class'

    if(window.location.hash) {
        setTimeout(() => {
            $(window.location.hash).addClass('border border-2 border-blue-400')
        }, 500)
    }

    let host = '{{ env('APP_URL') }}'
    let method = ''
    let validation = null
    let theme = 'light'
    let editor = null
    let jdor = $('#json-display-ouput-request')
    let heightResponse = 500
    let heightBody = 350

    function onToggleTheme(mode = '') {
        const el = $(`html[data-theme]`)
        const eli = $('i[class*=icon-theme-]')
        eli.removeClass('hidden')
        eli.addClass('hidden')
        $(`.icon-theme-${mode}`).removeClass('hidden')

        el.attr('data-theme', '')
        el.removeClass('light')
        el.removeClass('dark')
        el.removeClass('system')
        el.addClass(mode)
        el.attr('data-theme', mode)

        $('.logo-light').addClass('hidden')
        $('.logo-dark').addClass('hidden')
        $('.logo-' + mode).removeClass('hidden')

        localStorage.setItem('_theme', mode)
    }

    if (!localStorage.getItem('_theme')) {
        localStorage.setItem('_theme', theme)
    } else {
        theme = localStorage.getItem('_theme')
        onToggleTheme(theme)
    }

    if (!localStorage.getItem('config_host')) {
        localStorage.setItem('config_host', host)
    } else {
        host = localStorage.getItem('config_host')
        $('#inputhost').val(host)
    }
    const notyf = new Notyf({
        position: {x:'right',y:'top'},
        types: [
            {
                type: 'success',
                className: 'bg-success',
                icon: {
                    className: 'notyf__icon--success text-success',
                    tagName: 'i'
                }
            },
            {
                type: 'error',
                className: 'notyf__toast--error bg-error',
                icon: {
                    className: 'notyf__icon--error text-error',
                    tagName: 'i'
                }
            },
            {
                type: 'warning',
                className: 'bg-warning',
                icon: false
            }
        ]
    });

    function clipboard(id) {
        const url = `{{ env('APP_URL') }}/api/v1#${id}`
        navigator.clipboard.writeText(url)
        notyf.success('Has been successfully copied to the clipboard');
    }

    let currentCollapseActive = ''
    function onCollapse(model = '') {
        currentCollapseActive = `collapse-model-${model}`
    }

    let currentModalActive = ''
    function onOpenModal(id = '', with_jdor = true) {
        currentModalActive = id

        $(`.${id}`).addClass('modal-open')
        if (with_jdor) {
            let editor = new JsonEditor('#json-display-ouput-request');
            jdor.show();
            jdor.addClass('overflow-y-auto');
            jdor.addClass(`min-h-[${heightResponse}px]`);
            if (id === 'settoken-modal') {
                onChangeToken()
            }
        }
    }

    function onShowScreenshot(url = '', figma_url = '')
    {
        $('.ss-modal').addClass('modal-open')
        $("#img-ss-container").empty();
        $("#img-figma-container").empty();
        setTimeout(function() {
            var imgElement = $("<img>");
            imgElement.attr("src", url);
            imgElement.attr("class", "mb-3")
            imgElement.css("height", "500px");

            $("#img-ss-container").append(imgElement);

            if (figma_url) {
                var imgElement2 = $("<a>");
                imgElement2.attr("href", figma_url);
                imgElement2.attr("target", '_blank');
                imgElement2.attr("class", 'text-blue-600 hover:text-blue-800 text-sm');
                imgElement2.text(">> Click here to show Figma <<");
                $("#img-figma-container").append(imgElement2);
            }
        }, 100)
    }

    function onCloseModal(id = '', with_jdor = true) {
        currentModalActive = ''

        if (id === 'signin-modal') {
            $('.form-login-mb-email').removeClass('hidden')
            $('.form-login-mb-otp_code').addClass('hidden')
        }

        $(`.${id}`).removeClass('modal-open')
        if (with_jdor) {
            onChangeTab('test-modal', 'request')
            if (editor) {
                editor = editor.toTextArea()
                editor = null
            }
            jdor.hide();
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.keyCode === 27 && currentModalActive){
            onCloseModal(currentModalActive)
        }
    });

    function validationToTemplate(validation) {
        let template = {}
        for (const key in validation) {
            const val = validation[key]
            if (!Array.isArray(val) && typeof val === "object") {
                template[key.replace('.*', '')] = [validationToTemplate(val)]
            } else {
                template[key] = null
            }
        }
        return template
    }

    function onAction(type = '', url = '', set_method = '', set_validation = null, available_scopes = [], notes = []) {
        if (type === 'try') {
            $('#test-regurl').val(host + '/' + url)
            if (['POST', 'PUT'].includes(set_method)) {
                $('.test-withbody').removeClass('hidden')
                $('.test-modal-tab-validation').removeClass('hidden')
            } else {
                $('.test-modal-tab-validation').addClass('hidden')
                $('.test-withbody').addClass('hidden')
            }

            editor = CodeMirror.fromTextArea(document.getElementById("test-regbody"), {
                matchBrackets: true,
                autoCloseBrackets: true,
                mode: "application/ld+json",
                lineWrapping: true,
                theme: 'material',
                lineNumbers: false
            });
            if (set_validation) {
                let template = validationToTemplate(set_validation)
                if (template) {
                    editor.setValue(JSON.stringify(template, null, '\t'));
                } else {
                    $('.test-modal-tab-validation').addClass('hidden')
                    editor.setValue("")
                }
            } else {
                $('.test-modal-tab-validation').addClass('hidden')
                editor.setValue("")
            }
            editor.setSize("100%", heightBody);

            method = set_method
            $('#test-regmethod').val(method)
            if (set_validation) {
                validation = set_validation
            } else {
                validation = null
            }

            // Jika availableScopes adalah array
            if (Array.isArray(available_scopes)) {
                if (available_scopes.length) {
                    $('.available_scopes_list').removeClass('hidden')
                } else {
                    $('.available_scopes_list').addClass('hidden')
                }
                // Dapatkan elemen ul.available_scopes dengan jQuery
                var $ul = $('ul.available_scopes');

                // Bersihkan elemen ul.available_scopes
                $ul.empty();

                // Loop melalui array availableScopes dan tambahkan setiap elemen sebagai li
                available_scopes.forEach(function(scope) {
                    $ul.append('<li>' + scope + '</li>');
                });
            }

            // Jika availableScopes adalah array
            if (Array.isArray(notes)) {
                if (notes.length) {
                    $('.available_notes_list').removeClass('hidden')
                } else {
                    $('.available_notes_list').addClass('hidden')
                }
                // Dapatkan elemen ul.available_notes dengan jQuery
                var $ul = $('ul.available_notes');

                // Bersihkan elemen ul.available_notes
                $ul.empty();

                // Loop melalui array availableScopes dan tambahkan setiap elemen sebagai li
                notes.forEach(function(scope) {
                    $ul.append('<li>' + scope + '</li>');
                });
            }

            onOpenModal('test-modal')
        }
    }

    function onMethodChange() {
        const currentMethod = $('#test-regmethod').val()
        if (['POST', 'PUT'].includes(currentMethod)) {
            $('.test-withbody').removeClass('hidden')
        } else {
            $('.test-withbody').addClass('hidden')
        }
        method = currentMethod
    }

    function onChangeTab(element = '', id = '') {
        $(`a[class*=${element}-tab]`).removeClass('tab-active')
        $(`a[class*=${element}-tab-${id}]`).addClass('tab-active')
        $(`div[class*=${element}-body]`).addClass('hidden')
        $(`div[class*=${element}-body-${id}]`).removeClass('hidden')

        if (id === 'validation') {
            let json = validation
            let editor = new JsonEditor(`#json-display-ouput-${id}`, json);
            editor.load(json);
            $(`pre[id*=json-display-ouput-${id}]`).show()
        }
    }

    function onSendRequest() {
        let url = $('#test-regurl').val()

        if (
            url.indexOf('signin') > -1 ||
            url.indexOf('signup') > -1 ||
            url.indexOf('forgot-password') > -1
        ) {
            $('.btn-sendreq').prop('disabled', true)
            $('.btn-sendreq-unspinner').removeClass('ml-2 fa-paper-plane')
            $('.btn-sendreq-spinner').addClass('ml-2 fa-spin fa-spinner-third')
            const body = editor.getValue()
            let data = ['POST', 'PUT'].includes(method) ? (body ? JSON.parse(body) : {}) : undefined;
            let configs = {
                method,
                url,
                data,
                success: (json) => {
                    $('.btn-sendreq').prop('disabled', false)
                    $('.btn-sendreq-unspinner').addClass('ml-2 fa-paper-plane')
                    $('.btn-sendreq-spinner').removeClass('ml-2 fa-spin fa-spinner-third')
                    let editor = new JsonEditor('#json-display-ouput-request', json);
                    editor.load(json);
                    jdor.show();
                    onChangeTab('test-modal', 'response')
                },
                error: (err) => {
                    $('.btn-sendreq').prop('disabled', false)
                    $('.btn-sendreq-unspinner').addClass('ml-2 fa-paper-plane')
                    $('.btn-sendreq-spinner').removeClass('ml-2 fa-spin fa-spinner-third')
                    if (err.responseJSON) {
                        let editor = new JsonEditor('#json-display-ouput-request', err.responseJSON);
                        editor.load(err.responseJSON);
                        jdor.show();
                        onChangeTab('test-modal', 'response')
                    }
                }
            }

            $.ajax(configs)
        } else {
            const token = localStorage.getItem('_token')
            $('.btn-sendreq').prop('disabled', true)
            $('.btn-sendreq-unspinner').removeClass('ml-2 fa-paper-plane')
            $('.btn-sendreq-spinner').addClass('ml-2 fa-spin fa-spinner-third')

            let data = undefined
            if (['POST', 'PUT'].includes(method)) {
                const body = editor.getValue()
                if (body) {
                    data = JSON.parse(body)
                }
            }

            let configs = {
                method,
                url,
                data,
                headers: {},
                success: (json) => {
                    $('.btn-sendreq').prop('disabled', false)
                    $('.btn-sendreq-unspinner').addClass('ml-2 fa-paper-plane')
                    $('.btn-sendreq-spinner').removeClass('ml-2 fa-spin fa-spinner-third')
                    let editor = new JsonEditor('#json-display-ouput-request', json);
                    editor.load(json);
                    jdor.show();
                    jdor.addClass('overflow-y-auto');
                    onChangeTab('test-modal', 'response')
                },
                error: (err) => {
                    $('.btn-sendreq').prop('disabled', false)
                    $('.btn-sendreq-unspinner').addClass('ml-2 fa-paper-plane')
                    $('.btn-sendreq-spinner').removeClass('ml-2 fa-spin fa-spinner-third')
                    if (err.responseJSON) {
                        let editor = new JsonEditor('#json-display-ouput-request', err.responseJSON);
                        editor.load(err.responseJSON);
                        jdor.show();
                        onChangeTab('test-modal', 'response')
                    }
                }
            }

            if (token) {
                configs.headers.Authorization = 'Bearer ' + token
            }

            $.ajax(configs)
        }
    }

    function onReset() {
        localStorage.removeItem('_token')
        notyf.success('Token has been removed!');
    }

    function onSaveToken() {
        const token = $('#inputtoken').val()
        localStorage.setItem('_token', token || '')
        onCloseModal('settoken-modal')
        notyf.success('Token has been saved!');
        return false
    }

    function onChangeToken() {
        const token = $('#inputtoken').val()
        const storage = localStorage.getItem('_token')
        if (token) {
            $('.btn-settoken').prop('disabled', false)
        } else {
            if (!storage) {
                $('.btn-settoken').prop('disabled', true)
            } else {
                $('.btn-settoken').prop('disabled', false)
            }
        }
    }

    function onSaveConfig() {
        const inputhost = $('#inputhost').val()
        localStorage.setItem('config_host', inputhost)
        host = inputhost
        onCloseModal('config-modal')
        notyf.success('Config has been saved!');
        return false
    }

    let login_mb_email_has_valid = false
    function onSignin(mode) {
        $('.btn-signin').prop('disabled', true)
        $('.btn-signin-text').addClass('fa-spin fa-spinner-third')
        if (mode === 'bo') {
            // backoffice
            const identity = $('#identity').val()
            const password = $('#password').val()
            $.ajax({
                method: 'POST',
                url: '/api/signin',
                data: {
                    identity,
                    password
                },
                success: (res) => {
                    const { token } = res.data
                    localStorage.setItem('_token', token)
                    $('.btn-signin').prop('disabled', false)
                    $('.btn-signin-text').removeClass('fa-spin fa-spinner-third')
                    notyf.success('Login has been successfully!');
                    onCloseModal('signin-modal')
                },
                error: (err) => {
                    $('.btn-signin').prop('disabled', false)
                    $('.btn-signin-text').removeClass('fa-spin fa-spinner-third')
                    if (err.status === 403) {
                        notyf.error('Identity or password is invalid!');
                    } else {
                        notyf.error('Something went wrong!');
                    }
                }
            })
            return false
        } else {
            // mobile
            if (!login_mb_email_has_valid) {
                const email = $('#email').val()
                $.ajax({
                    method: 'POST',
                    url: '/api/signin-mobile',
                    data: {
                        email
                    },
                    success: (res) => {
                        $('.btn-signin').prop('disabled', false)
                        $('.btn-signin-text').removeClass('fa-spin fa-spinner-third')
                        $('.form-login-mb-email').addClass('hidden')
                        $('.form-login-mb-otp_code').removeClass('hidden')
                        login_mb_email_has_valid = true
                        notyf.success(res.message);
                        // onCloseModal('signin-modal')
                    },
                    error: (err) => {
                        $('.btn-signin').prop('disabled', false)
                        $('.btn-signin-text').removeClass('fa-spin fa-spinner-third')
                        if (err.status === 403) {
                            notyf.error('Email is invalid!');
                        } else {
                            notyf.error('Something went wrong!');
                        }
                    }
                })
            } else {
                const otp_code = $('#otp_code').val()
                $.ajax({
                    method: 'GET',
                    url: '/api/otp-verification/' + otp_code,
                    success: (res) => {
                        const { token } = res.data
                        localStorage.setItem('_token', token)
                        $('.btn-signin').prop('disabled', false)
                        $('.btn-signin-text').removeClass('fa-spin fa-spinner-third')
                        $('.form-login-mb-email').removeClass('hidden')
                        $('.form-login-mb-otp_code').addClass('hidden')
                        login_mb_email_has_valid = false
                        notyf.success(res.message);
                    },
                    error: (err) => {
                        $('.btn-signin').prop('disabled', false)
                        $('.btn-signin-text').removeClass('fa-spin fa-spinner-third')
                        notyf.error(err.message);
                    }
                })
            }
            return false
        }
    }

    function onClickTabSigninType(mode) {
        $('.login-tab-bo').removeClass('tab-active')
        $('.login-tab-mb').removeClass('tab-active')
        $('.form-login-bo').addClass('hidden')
        $('.form-login-mb').addClass('hidden')


        $(`.login-tab-${mode}`).addClass('tab-active')
        $(`.form-login-${mode}`).removeClass('hidden')
    }
</script>
</body>

</html>
