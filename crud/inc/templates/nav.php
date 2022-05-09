<?php
?>

<nav class="nav">
    <p>
        <a href="/crud/index.php?task=report">All Students</a>
        <?php if (isAdmin() || isEditor()) : ?>
            |
            <a href="/crud/index.php?task=add">Add Student</a>
        <?php endif; ?>
        <?php if (isAdmin()) : ?>
            |
            <a href="/crud/index.php?task=seed">Seeding</a>
        <?php endif; ?>
        <span class="float-right">
            <?php if ($_SESSION['logedin']) : ?>
                <a href="index.php?task=logout">Log Out(<?php echo $_SESSION['user'] . ' | ' . $_SESSION['userRoal'] ?>)</a>
            <?php else : ?>
                <a href="index.php?task=login">Log in</a> |
                <a href="index.php?task=signup">Register</a>
            <?php endif; ?>
        </span>
    </p>
</nav>