<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    
    <link rel="stylesheet" href="/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/chartcss/dist/charts.min.css">
    <script defer src="/bootstrap/js/bootstrap.min.js"></script>
    
    <style>
        .topnav {
            position: fixed;
            top: 0;
            width: 100vw;
            height: 50px;
            border-bottom: 1px solid #b3b3b3;
            background-color: #fff;
            display: flex;
            justify-content: space-between;
            z-index: 999;
            padding: 0px 1rem;
        }   
        .topnav > * {
            display: flex;
            align-items: center;
        }
        .topnav-item a {
            text-decoration: none;
            font-weight: 900;
            border-right: 1px solid #ececec;
            padding: 0px 8px;   
        }
        .topnav-brand {
            font-size: 2rem;
            font-weight: 900;
            text-decoration: none;
        }
        .logout-button {
            font-size: 2rem;
            font-weight: 900;
            text-decoration: none;
            margin-top: 100px;
        }
        
        .container-fluid {
            margin-top: 60px;
        }


        /* for menu groups */
        .menu-image {
        height: 4rem;
    }
    .inline-form > * {
        display: inline-block;
    }
    .grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        grid-gap: 1rem;
    }
    .grid-item {
        border: 1px solid rgb(148, 148, 148);
        border-radius: 8px;
        width: 150px;
        height: 150px;
        cursor: pointer;
        position: relative;
    }
    .grid-item img {
        position: absolute;
        height: 100%;
        width: 100%;
        object-fit: cover;
        border: 4px solid blueviolet;
    }
    .grid-item .menu-text {
        width: 96% ;
        font-size: 1rem;
        position: absolute;
        font-weight: 900;
        left: 4px;
        text-align: center;
        bottom: 10px;
        background-color: #fff;
        color: blueviolet;
        z-index: 1000;
    }

    /* for sidebar */
    .sidebar {
        min-height: 100vh;
        overflow-y: scroll;
        background-color: rgb(220, 235, 255); 
    }
    .list {        
        padding-left: 0;
    }
    .list-item {
        width: 100%;
        height: 2rem;
        border-bottom: 1px solid #cfcccc;
        list-style: none;
        cursor: pointer;
    }
    .list-item-link {
        display: block;
        width: 100%;
        height: 100%;
    }
    .list-item:hover {
        background-color: rgb(174, 195, 255);
    }
    .selected-list-item {
        font-weight: 900;
        background-color: rgb(174, 195, 255);        
    }
    .list-item-add-new {        
        margin-top: 5px;
        padding: 8px;   
        width: 100%;
    }
    .list-item-add-new .form-control {
        width: 70%;
        display: inline;
    }
    .content {
         display: flex;
         
    }
    .main {
        flex: 1;
    }
    </style>
    @yield('css')
</head>
<body>
    @if(Auth::guard('admin_account')->check())
    <nav class="topnav">
        <div class="topnav-left">
            <a class="topnav-brand" href="/admin">
                <img src="/logo.png" width="40" height="40" alt="logo">
                Admin
            </a>
        </div>
        
        <div class="topnav-center">
            <span class="clock" style="padding:8px;background-color: black; color:rgb(158, 245, 158)">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </span>
        </div>

        <div class="topnav-right">
            @if(Auth::guard('admin_account')->check())
            <div class="topnav-item">                
                <span class="badge rounded-pill bg-success"> Logged in as {{Auth()->guard('admin_account')->user()->username}}</span>
            </div>
            @endif            

            @if(Auth::guard('admin_account')->check())
            <div class="topnav-item">
                <a class="logout-button" href="#" onclick="event.preventDefault();document.querySelector('#logout-form').submit();">
                    ⎋
                </a>
                <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
            @endif
            
        </div>
    </nav>
    @endif
    <div class="container-fluid">
        @yield('content')
    </div>
    <script>
        if ('serviceWorker' in navigator ) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/service-worker.js').then(function(registration) {
                // Registration was successful
                console.log('ServiceWorker registration successful with scope: ', registration.scope);
                }, function(err) {
                // registration failed :(
                console.log('ServiceWorker registration failed: ', err);
                });
            });
        }    

        time();
        setInterval(time, 1000);
        function time() {
            const now = new Date();
            let hour = now.getHours();
            
            let amPm = hour >= 12 ? "PM" : "AM";
            hour = hour > 12 ? hour - 12 : hour; 
            hour = hour < 10 ? "0" + hour : hour;
            let minute = now.getMinutes();
            minute = minute < 10 ? "0" + minute : minute;
            let second = now.getSeconds();
            second = second < 10 ? "0" + second : second;

            const clockHtml = document.querySelector('.clock');
            clockHtml.innerHTML = `
                ${hour} : ${minute} : ${second} ${amPm}
            `;

        }
    </script>
    @yield('js')
</body>
</html>