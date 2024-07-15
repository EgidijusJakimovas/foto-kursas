<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
    <title>Redirecting...</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: aliceblue;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            text-align: center;
        }
        .info {
            margin-top: 10px;
        }
        .info a {
            color: blue !important;
        }
        .info a:hover {
            color: blue !important;
        }
    </style>
    <script type="text/javascript">
        setTimeout(function() {
            //TODO Nukreipsite i kalkuliatoriaus aplikacija, kur bus vietoj pirkimo migtuko bus uzkrautas youtube video
            window.location.href = "https://heroku-calculator-a6b0384b6190.herokuapp.com/courses";
        }, 10000); // 10000 milliseconds = 10 seconds
    </script>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Payment Successful</h1>
        </div>
        <div class="info">
            <p>Thank you for your purchase! We have received your order and are processing it. Please check your email for payment confirmation and further instructions. You will be redirected to our video in 10 seconds.</p>
        </div>
    </div>
</body>
</html>
