<?php
define('DB_NAME', getcwd() . "\\data\\db.json");

session_name('login_session');
session_start();
$error = false;
$message = '';

function seed()
{
    $data = array(
        array(
            "id" => 1,
            "fname" => "Tanin",
            "lname" => "Ahmed",
            "roll" => 1
        ),
        array(
            "id" => 2,
            "fname" => "Jui",
            "lname" => "Akond",
            "roll" => 2
        ),
        array(
            "id" => 3,
            "fname" => "Tanvir",
            "lname" => "Ahmed",
            "roll" => 3
        ),
        array(
            "id" => 4,
            "fname" => "Ranu",
            "lname" => "Akond",
            "roll" => 4
        )
    );

    $json_data = json_encode($data);
    file_put_contents(DB_NAME, $json_data);
}

function generateReport()
{
    $json_data = file_get_contents(DB_NAME);
    $students = json_decode($json_data, true);

    if ($students) : ?>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Roll</th>
                    <?php if (isAdmin() || isEditor()) : ?>
                        <th>Action</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($students as $student) { ?>
                    <tr>
                        <td><?php printf('%s %s', $student['fname'], $student['lname']); ?></td>
                        <td><?php printf('%s', $student['roll']) ?></td>
                        <?php if (isAdmin()) : ?>
                            <td style="width: 25%;"><?php printf('<a href="/crud/index.php?task=edit&id=%1$s">Edit</a> | <a href="/crud/index.php?task=delete&id=%1$s">Delete</a>', $student['id']); ?></td>
                        <?php elseif (isEditor()) : ?>
                            <td style="width: 25%;"><?php printf('<a href="/crud/index.php?task=edit&id=%s">Edit</a>', $student['id']); ?></td>
                        <?php endif; ?>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php
    else : ?>
        <p>No result found</p>
<?php endif;
}

function addstudent($fname, $lname, $roll)
{
    $json_data = file_get_contents(DB_NAME);
    $allstudents = json_decode($json_data, true);

    $ids = [];

    foreach ($allstudents as $student) {
        array_push($ids, $student['id']);
        if ($roll == $student['roll']) {
            return false;
        }
    }

    $newstudent = array(
        'id' => max($ids)  + 1,
        'fname' => $fname,
        'lname' => $lname,
        'roll'  => $roll
    );
    array_push($allstudents, $newstudent);
    $json_data = json_encode($allstudents);
    file_put_contents(DB_NAME, $json_data);
    return true;
}

function deleteStudent($id)
{
    $json_data = file_get_contents(DB_NAME);
    $allstudents = json_decode($json_data, true);

    foreach ($allstudents as $key => $student) {
        if ($id == $student['id']) {
            unset($allstudents[$key]);
        }
    }

    $json_data = json_encode($allstudents);
    file_put_contents(DB_NAME, $json_data);
    header('location: \\crud\\index.php');
}



function editStudent($id, $fname, $lname, $roll)
{
    $json_data = file_get_contents(DB_NAME);
    $allstudents = json_decode($json_data, true);
    $updateStudentList = [];



    foreach ($allstudents as $student) {
        if ($roll == $student['roll'] && $id != $student['id']) {
            return false;
        }

        if ($id == $student['id']) {
            $student['fname'] = $fname;
            $student['lname'] = $lname;
            $student['roll'] = $roll;
        }
        array_push($updateStudentList, $student);
    }

    $json_data = json_encode($updateStudentList);
    file_put_contents(DB_NAME, $json_data);
    return true;
}

function isAdmin()
{
    return ('admin' == $_SESSION['userRoal']);
}
function isEditor()
{
    return ('editor' == $_SESSION['userRoal']);
}
function isSubscriber()
{
    return ('subscriber' == $_SESSION['userRoal']);
}
