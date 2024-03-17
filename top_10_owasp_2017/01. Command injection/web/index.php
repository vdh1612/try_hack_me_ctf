<html lang="en">

<head>
    <title>!!WIP!! - Directory Search</title>
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
                <?php

                if (isset($_GET["username"])) {
                    $username = $_GET["username"];
                    
                    $command = "awk -F: '{print $1}' /etc/passwd | grep $username";

                    $returned_user = exec($command);
                    if ($returned_user == "") {
                        $result = "<div class='alert alert-danger' role='alert'>
                        <strong>Error!</strong> User <b>$username</b> was not found on the <b>system</b>
                        </div>";
                    } else {
                        $result = "<div class='alert alert-success' role='alert'>
                        <strong>Success!</strong> User <b>$username</b> was found on the <b>system</b>
                        </div>";
                    }

                    echo $result;
                    
                    }

                ?>
                    <h4 class="display-4">Directory Search</h4>
                    <div class="form-group">
                        <input type="text" name="username" class="form-control" id="username"
                            placeholder="Search user..." required> 
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Submit</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="js/bootstrap.min.js"></script>
</body>

</html>
