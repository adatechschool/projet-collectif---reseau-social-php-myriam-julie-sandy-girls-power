<?php include("connexion.php"); ?>

<header>
            <a href='admin.php'><img src="resoc.jpg" alt="Logo de notre réseau social"/></a>
            <nav id="menu">
                <a href="news.php">Actualités</a>
                <a href="wall.php?user_id=<?php $_SESSION['connected_id'] ?>">Mur</a>
                <a href="feed.php?user_id=<?php $_SESSION['connected_id'] ?>">Flux</a>
                <a href="tags.php?tag_id=1">Mots-clés</a>
            </nav>
            <nav id="user">
                <a href="#">▾ Profil</a>
                <ul>
                    <li><a href="settings.php?user_id=5">Paramètres</a></li>
                    <li><a href= "login.php?user_id=5">Connexion</a></li>
                </ul>
            </nav>
        </header>