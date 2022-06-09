<header>
            <a href='admin.php' id="fondimage"><img src="ada.png" alt="Logo de notre réseau social"/></a>
            <nav id="menu">
                <a href="news.php">Actualités</a>
                <a href="wall.php?user_id=<?php echo $_SESSION['connected_id'] ?>">Mur</a>
                <a href="feed.php?user_id=<?php echo $_SESSION['connected_id'] ?>">Flux</a>
                <a href="tags.php?tag_id=1">Mots-clés</a>
            </nav>
            <nav id="user">
                <a href="#">▾ Profil</a>
                <ul>
                    <li><a href="settings.php?user_id=<?php echo $_SESSION['connected_id'] ?>">Paramètres</a></li>
                    <?php 
                    if (!isset($_SESSION['connected_id'])) {
                        echo '<li><a href= "login.php?">Connexion</a></li>';
                    }else {
                        echo '<li><a href= "deconnexion.php">Déconnexion</a></li>';
                    }
                    ?>
                </ul>
            </nav>
        </header>