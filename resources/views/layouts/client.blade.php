<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=1.0, 
    user-scalable=0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
    
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
    </style>
    @yield('style')
</head>
<body>
    <nav class="topnav">
        <div class="topnav-left">
            @if(Auth::guard('waiter')->check())
            <a class="topnav-brand" href="/waiter">မင်္ဂလာပါ {{Auth()->guard('waiter')->user()
            ->name}}</a>
          
            @endif
            @if(Auth::guard('kitchen')->check())
            <a class="topnav-brand" href="/kitchen">မင်္ဂလာပါ </a>
          
            @endif

            @if(Auth::guard('admin_account')->check())
            <a class="topnav-brand" href="/kitchen">Admin </a>
          
            @endif
        </div>
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
    <main class="main-container">
        @yield('content')
    </main>


    @yield('script')
</body>
</html>