<?php
require_once('./inc/function.php');
$task = $_GET['task'] ?? 'report';
$error = $_GET['error'] ?? 0;

$fname = '';
$lname = '';
$roll = '';
$id = '';


if ('logout' == $task) {
    $_SESSION['logedin'] = false;
    $_SESSION['user'] = 'Gust';
    $_SESSION['userRoal'] = null;
    header('location: \\crud\\index.php');
}

if (isset($_POST['submit'])) {
    $fname = filter_input(INPUT_POST, 'fname', FILTER_SANITIZE_STRING);
    $lname = filter_input(INPUT_POST, 'lname', FILTER_SANITIZE_STRING);
    $roll  = filter_input(INPUT_POST, 'roll', FILTER_SANITIZE_STRING);
    $id  = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);

    if ($fname == '' || $lname == '' || $roll == '') {
        header('location: \\crud\\index.php?task=add&error=2');
        return;
    }

    if ($id) {
        $result = editStudent($id, $fname, $lname, $roll);
        if ($result) {
            header('location: \\crud\\index.php?task=report');
        } else {
            $error = 1;
        }
    } else {
        $result = addstudent($fname, $lname, $roll);
        if ($result) {
            header('location: \\crud\\index.php?task=report');
        } else {
            $error = 1;
        }
    }
}
if ('delete' == $task) {
    if (isAdmin()) {
        deleteStudent($_GET['id']);
    } else {
        header('location: \\crud\\index.php');
        return false;
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,300italic,700,700italic">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/milligram/1.4.1/milligram.css">
    <title>PHP CRUD Project</title>
    <style>
        body {
            margin-top: 30px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="column column-60 column-offset-20">
                <h2>Project - CRUD</h2>
                <p>A simple prijece to perform CRUD opretions useing plan text file and PHP</p>
                <?php include_once('./inc/templates/nav.php');

                if ($error == 1) : ?>
                    <blockquote>
                        <p>Duplicate Roll</p>
                    </blockquote>
                <?php elseif ($error == 2) : ?>
                    <blockquote>
                        <p>Please include all fields</p>
                    </blockquote>
                <?php endif ?>


                <?php if ('seed' === $task) :
                    seed(); ?>
                    <p>Seeding Complited</p>
                <?php endif; ?>

                <?php if ('report' === $task) {
                    generateReport();
                } ?>
                <?php if ('add' === $task) :

                    if (!isAdmin() && !isEditor()) {
                        header('location: \\crud\\index.php');
                    } ?>

                    <form action="/crud/index.php?task=add" method="POST">
                        <label for="fname">Fast Name</label>
                        <input type="text" placeholder="Enter student fast name" id="fname" value="<?php echo $fname; ?>" name="fname">
                        <label for="lname">Last Name</label>
                        <input type="text" placeholder="Enter student last name" id="lname" value="<?php echo $lname; ?>" name="lname">
                        <label for="roll">Roll</label>
                        <input type="number" placeholder="Enter student roll" id="roll" value="<?php echo $roll; ?>" name="roll">
                        <button type="submit" class="button" name="submit">Submit</button>
                    </form>
                <?php endif; ?>


                <?php if ('edit' === $task) :

                    if (!isAdmin() && !isEditor()) {
                        header('location: \\crud\\index.php');
                    }

                    $json_data = file_get_contents(DB_NAME);
                    $allstudents = json_decode($json_data, true);

                    $id = $_GET['id'];
                    foreach ($allstudents as $student) :
                        if ($id == $student['id']) : ?>
                            <form method="POST">
                                <input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
                                <label for="fname">Fast Name</label>
                                <input type="text" placeholder="Enter student fast name" value="<?php echo $student['fname'] ?>" id="fname" name="fname">
                                <label for="lname">Last Name</label>
                                <input type="text" placeholder="Enter student last name" value="<?php echo $student['lname'] ?>" id="lname" name="lname">
                                <label for="roll">Roll</label>
                                <input type="number" placeholder="Enter student roll" value="<?php echo $student['roll'] ?>" id="roll" name="roll">
                                <button type="submit" class="button-primary" name="submit">Update</button>
                            </form>
                <?php endif;
                    endforeach;
                endif; ?>
            </div>
        </div>
        <?php if ('login' == $task) : ?>
            <div class="row">
                <div class="column column-60 column-offset-20">
                    <h2>Login your account</h2>
                    <form action="./auth.php" method="POST">
                        <label for="username">User Name 🦸 </label>
                        <input type="text" name="username" id="username">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password">
                        <input type="hidden" name="login">
                        <button type="submit" class="button" name="submit">Login</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
        <?php if ('signup' == $task) : ?>
            <div class="row">
                <div class="column column-60 column-offset-20">
                    <h2>Create new account</h2>
                    <form action="./auth.php" method="POST">
                        <label for="name">Name</label>
                        <input type="text" name="name" id="name">
                        <label for="username">User Name 🦸 </label>
                        <input type="text" name="username" id="username">
                        <label for="email">Emain 📧 </label>
                        <input type="email" name="email" id="email">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password">
                        <label for="password">Confirm Password</label>
                        <input type="password" name="password2" id="password2">
                        <input type="hidden" name="signup">
                        <button type="submit" class="button" name="submit">Sign Up</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>