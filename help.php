<!DOCTYPE html>
<head>
    <title>help</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

    <!-- jQuery and JS bundle w/ Popper.js -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>

    <style>
        .logout{
            float: right;
            height: 30px;
            line-height: 1px;
        }
        .navbar{
            background-color: #B0DE6B;
            color: #fff;
            height: 40px;
            line-height: 10px;
            padding: 0px;
        }
        .active{
            color: darkslategrey;
        }
        .logo{
            width: 30px;
            height: 30px;
            
        }
        a{
            color:#fff
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row navbar">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-8">
                        <nav class="nav">
                            <a  href="flashball.php"> <img src="ball.png" alt="home" class="logo"></a>
                            <a class="nav-link" href="flashball.php">Session</a>
                            <a class="nav-link" href="graph.php">Graph</a>
                            <a class="nav-link active" href="help.php">Help</a>
                        </nav>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-outline-light logout" onclick="location.href='index.html'">logout</button>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div>
        <h3>嘿 BallBall</h3>
    </div>
    
</body>
</html>