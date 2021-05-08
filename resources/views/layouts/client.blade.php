<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=1.0, 
    user-scalable=0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/icons/font/bootstrap-icons.css">
    <script defer src="/bootstrap/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="/toastify/toastify.css">
    <script defer src="/toastify/toastify.js"></script>
    <title>Waiter View</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Josefin+Sans&display=swap');
        *, ::after, ::before {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        :root {
            --primaryFont: 'Arial', sans-serif;
        }
        main {
            font-family: var(--primaryFont);
        }
        /* navbar style */
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
            padding: 0 9px;
        }
        .topnav-item a {
            text-decoration: none;
            font-weight: 900;
            border-right: 1px solid #ececec;
            padding: 0px 8px;   
        }
        .topnav-brand {
            font-size: 1.4rem;
            font-weight: 900;
            text-decoration: none;
        }
        .logout-button {
            font-size: 2rem;
            font-weight: 900;
            text-decoration: none;
            margin-top: 100px;
        }
        /* boostrap override */
        .card, .btn {
            border-radius: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        @media(max-width: 580px) {
            .topnav-center {
                display: none;
            }
        }
    </style>
    @yield('style')
</head>
<body>
    @if (Auth::guard('waiter')->check() ||
         Auth::guard('admin_account')->check() ||
         Auth::guard('kitchen')->check())
    <nav class="topnav">
        <div class="topnav-left">
            @if(Auth::guard('waiter')->check())
            <a class="topnav-brand" href="/waiter">
            <img src="/logo.png" width="40" height="40" alt="logo">
            {{Auth()->guard('waiter')->user()
            ->name}}</a>
          
          @endif
          @if(Auth::guard('kitchen')->check())
            <a class="topnav-brand" href="/kitchen">
                <img src="/logo.png" width="40" height="40" alt="logo">
                မင်္ဂလာပါ </a>
            
            @endif
            
            @if(Auth::guard('admin_account')->check())
            <a class="topnav-brand" href="/admin">
                <img src="/logo.png" width="40" height="40" alt="logo">                
                Admin </a>
          
            @endif
        </div>
        @if(!Auth::guard('waiter')->check())
        <div class="topnav-center">
            <span class="clock" style="padding:8px;background-color: black; color:rgb(158, 245, 158)">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </span>
        </div>
        @endif
        <div class="topnav-right">
            @if(Auth::guard('waiter')->check())
            <div class="topnav-item">
                <span id="counter" class="badge bg-primary">
                    0
                </span>
            </div>
            {{-- <div class="topnav-item">                
                <span class="badge rounded-pill bg-success"> Logged in as {{Auth()->guard('waiter')->user()->name}}</span>
            </div> --}}
            <div class="topnav-item">
                <a class="logout-button" href="#" onclick="event.preventDefault();document.querySelector('#logout-form').submit();">
                    ⎋
                </a>
                <form id="logout-form" action="{{ route('waiter.logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
            @endif
            @if(Auth::guard('kitchen')->check())
            
            {{-- <div class="topnav-item">                
                <span class="badge rounded-pill bg-success"> Logged in as {{Auth()->guard('kitchen')->user()->name}}</span>
            </div> --}}
            <div class="topnav-item">
                <a class="logout-button" href="#" onclick="event.preventDefault();document.querySelector('#logout-form').submit();">
                    ⎋
                </a>
                <form id="logout-form" action="{{ route('kitchen.logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
            @endif

            @if(Auth::guard('admin_account')->check())
            
            {{-- <div class="topnav-item">                
                <span class="badge rounded-pill bg-success"> Logged in as {{Auth()->guard('admin_account')->user()->name}}</span>
            </div> --}}
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
    <main class="main-container">
        @yield('content')
    </main>

    @if(!Auth::guard('waiter')->check())
    <script>
        time();
        setInterval(time, 1000);
        function time() {
            const now = new Date();
            let hour = now.getHours();
            
            let amPm = hour >= 12 ? "PM" : "AM";
            hour = hour >= 12 ? hour - 12 : hour; 
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
    @endif
    @yield('script')
</body>
</html>