        <ul class="menu">
            <li style="float:left">
                <p>Welcome back <b><?php echo $_SESSION['user_fullname']." ".$_SESSION['user_surename']; ?></b>!</p>
            </li>
            <li style="float:right">
                <form action="logout.php" method="post">
                    <input type="submit" name="logout" value="Logout" class="custom-btn">
                </form>
            </li>
            <li style="float:right"><a href="#home">Settings</a></li>
        </ul>
