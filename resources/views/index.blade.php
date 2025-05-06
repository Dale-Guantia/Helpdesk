<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>HELPDESK</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <style>
        /* Reset and base */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body, html {
            height: 100%;
            overflow-x: hidden;
            margin: 0;
        }
        body {
            display: flex;
            flex-direction: column;
        }
        .main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #2245e0 0%, #2e97ec 100%);
            padding: 20px;
            color: #fff;
        }
        .container {
            background-color: rgba(255,255,255,0.1);
            padding: 40px 30px;
            border-radius: 12px;
            max-width: 400px;
            width: 100%;
            box-shadow: 0 8px 15px rgba(0,0,0,0.3);
            user-select: none;
            text-align: center
        }
        h1 {
            font-size: 2.8rem;
            margin-bottom: 15px;
            font-weight: 700;
            text-shadow: 1px 1px 6px rgba(0,0,0,0.3);
        }
        p {
            font-size: 1.2rem;
            margin-bottom: 30px;
            line-height: 1.5;
            text-shadow: 1px 1px 4px rgba(0,0,0,0.3);
        }
        a.cta-button {
            background-color: #ff6f61;
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            padding: 15px 30px;
            border-radius: 50px;
            font-size: 1.1rem;
            box-shadow: 0 4px 12px rgba(255,111,97,0.6);
            display: inline-block;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }
        a.cta-button:hover, a.cta-button:focus {
            background-color: #ff3b2e;
            box-shadow: 0 6px 20px rgba(255,59,46,0.8);
            outline: none;
        }
        a.login-button {
            background-color: #ff6f61;
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 50px;
            font-size: .8rem;
            box-shadow: 0 4px 12px rgba(255,111,97,0.6);
            display: inline-block;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }
        a.login-button:hover, a.login-button:focus {
            background-color: #ff3b2e;
            box-shadow: 0 6px 20px rgba(255,59,46,0.8);
            outline: none;
        }
        footer {
            margin-top: 40px;
            font-size: 0.9rem;
            opacity: 0.75;
            text-shadow: none;
        }
        @media (max-width: 480px) {
            h1 {
                font-size: 2rem;
            }
            p {
                font-size: 1rem;
            }
            a.cta-button {
                padding: 12px 25px;
                font-size: 1rem;
            }
            .container {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-light bg-light justify-content-between">
        <a class="navbar-brand" href="#">
        <img src="{{ asset('images/PrimaryLogo.png') }}" width="auto" height="35" class="d-inline-block align-top" alt="">
        </a>
        <a href="{{ route('filament.ticketing.auth.login')}}" class="login-button">Login</a>
    </nav>
    <div class="main">
        <div class="container">
            <h1>Helpdesk System</h1>
            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Dolorem atque maiores quidem vero alias commodi assumenda, ipsa.</p>
            <a href="#get-started" class="cta-button" aria-label="submit-ticket">Submit Ticket</a>&nbsp;&nbsp;
            <a href="#get-started" class="cta-button" aria-label="track-ticket">Track Ticket</a>
            <footer>
                &copy; <?php echo date('Y'); ?> Your Company. All rights reserved.
            </footer>
        </div>
    </div>
</body>
</html>
