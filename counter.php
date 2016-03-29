<!DOCTYPE html>
<html>
    <head>
        <!--Import Google Icon Font-->
        <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
      <!--Import materialize.css-->
        <link type="text/css" rel="stylesheet" href="libs/materialize/css/materialize.min.css"  media="screen,projection"/>
        <link type="text/css" rel="stylesheet" href="css/style.css" />
        <link type="text/css" rel="stylesheet" href="css/odometer-theme-minimal.css" />

      <!--Let browser know website is optimized for mobile-->
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    </head>
    <body>
        <!--Import jQuery before materialize.js-->
        <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
        <script type="text/javascript" src="libs/materialize/js/materialize.min.js"></script>
        <script type="text/javascript" src="libs/jquery-barcode.min.js"></script>
        <script type="text/javascript" src="libs/odometer.min.js"></script>
        <nav>
            <div class="nav-wrapper indigo">
                <span class="brand-logo" style="padding-left: 20px;">Schlopolis</span>
                <ul class="right hide-on-med-and-down">
                    <li id="timehour" style="font-size: 40px;" class="odometer"></li>
                    <li style="font-size: 40px;"> : </li>
                    <li id="timemin" style="font-size: 40px;" class="odometer"></li>
                    <li style="font-size: 40px;"> : </li>
                    <li id="timesec" style="font-size: 40px;" class="odometer"></li>
                </ul>
            </div>
        </nav>
        <script>
        window.odometerOptions = {
            format: '(.ddd)'
        };
        </script>
        <main style="padding:0;">
            <div class="container">
                <div class="row center">
                        <h1 class="center">Aktuell sind</h1>
                        <div id="count" class="odometer" style="text-align: right; font-size: 35vh;"></div>
                    <h1 class="center"><?php if($_GET['type'] == "all") echo "Personen"; elseif($_GET['type'] == "visit") echo "Besucher"; else echo "Bürger";?> im Staat</h1>
                </div>
            </div>
        </main>
    </body>
    <style>
        .odometer .odometer-inside .odometer-digit:first-child,
        .odometer .odometer-inside .odometer-formatting-mark:nth-child(2) {
            display: none
        }

    </style>
    <script>
        $(document).ready(function() {
          $(".dropdown-button").dropdown();

          // Initialize collapse button
          $(".button-collapse").sideNav();
          // Initialize collapsible (uncomment the line below if you use the dropdown variation)
          //$('.collapsible').collapsible();

          $('.odometer').html(123) // with jQuery
            refresh();
        });
                var request = false;

        function refresh() {
            // Request erzeugen
            if (window.XMLHttpRequest) {
                request = new XMLHttpRequest(); // Mozilla, Safari, Opera
            } else if (window.ActiveXObject) {
                try {
                    request = new ActiveXObject('Msxml2.XMLHTTP'); // IE 5
                } catch (e) {
                    try {
                        request = new ActiveXObject('Microsoft.XMLHTTP'); // IE 6
                    } catch (e) {}
                }
            }

            // überprüfen, ob Request erzeugt wurde
            if (!request) {
                alert("Kann keine XMLHTTP-Instanz erzeugen");
                return false;
            } else {
                var url = "citizen.php?action=counter&type=<?php echo $_GET['type'];?>";
                // Request öffnen
                request.open('post', url, true);
                // Request senden
                request.send(null);
                // Request auswerten
                request.onreadystatechange = interpretRequest;
            }
            window.setTimeout("refresh()", 500)
        }

        // Request auswerten
        function interpretRequest() {
            switch (request.readyState) {
                // wenn der readyState 4 und der request.status 200 ist, dann ist alles korrekt gelaufen
                case 4:
                    if (request.status != 200) {
                    } else {
                        var content = request.responseText;
                        // den Inhalt des Requests in das <div> schreiben
                        var d = new Date();
                        $('#count').html(1000+content)
                        $('#timehour').html(100+d.getHours())
                        $('#timemin').html(100+d.getMinutes())
                        $('#timesec').html(100+d.getSeconds())
                        console.log(content);
                    }
                    break;
                default:
                    break;
            }
        }
    </script>
</html>