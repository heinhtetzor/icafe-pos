<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        body {
            background-color: orchid;            
        }
        .tiles {
            color: black;
            display: flex;
            width: 100%;
            height: 100%;
            justify-content: center;
            align-content: center;
        }
        .tiles-item {
            border: 1px solid hotpink;
            border-radius: 10px;
            width: 200px;
            height: 200px;
            background-color: black;
            text-align: center;
            cursor: pointer;
        }
        .tiles-item > a {
            color: hotpink;
            padding-top: 40%;display: block;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="tiles">
        <div class="tiles-item">
            <a href="/waiter">Waiter</a>
        </div>
        <div class="tiles-item">            
            <a href="/admin">Admin</a>
        </div>
        <div class="tiles-item">            
            <a href="/kitchen">Kitchen</a>
        </div>
    </div>
</body>
</html>