<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    @laravelPWA
    <style>
        body {
            background-color: rgb(253, 249, 253);            
        }
        .tiles {
            width: 90vw;
            height: 90vh;
            display: flex;
            flex-direction: column;
        }
        .tiles-item {
            height: 100%;
            width: 100%;
            text-align: center;
            background-color: rgb(138, 217, 236);
            margin-bottom: 1rem;    
            text-decoration: none;
        }
        .tiles-item:hover {
            background-color: aquamarine;
        }
        .tiles-item > span {
            font-size: 5rem;
            font-family: Impact, Haettenschweiler, 'Arial Narrow Bold', sans-serif;
        }
    </style>
</head>
<body>
    <div class="tiles">
        <a class="tiles-item" href="/admin">            
            <span>Admin</span>
        </a>
        <a class="tiles-item" href="/waiter">
            <span>Waiter</span>
        </a>
        <a class="tiles-item" href="/kitchen">            
            <span>Kitchen</span>
        </a>
    </div>
</body>
</html>
