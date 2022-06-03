<?php include("connexion.php"); ?>

<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>ReSoC - Flux</title>         
        <meta name="author" content="Julien Falconnet">
        <link rel="stylesheet" href="style.css"/>
    </head>
    <body>
    <?php include('header.php'); ?>

        <div id="wrapper">
            <?php
            /**
             * Cette page est TRES similaire à wall.php. 
             * Vous avez sensiblement à y faire la meme chose.
             * Il y a un seul point qui change c'est la requete sql.
             */
            /**
             * Etape 1: Le mur concerne un utilisateur en particulier
             */
            $userId = intval($_GET['user_id']);
            ?>
            <?php
            /**
             * Etape 2: se connecter à la base de donnée
             */
            include('connexionbdd.php'); 
            ?>

            <aside>
                <?php
                /**
                 * Etape 3: récupérer le nom de l'utilisateur
                 */
                $laQuestionEnSql = "SELECT * FROM `users` WHERE id= '$userId' ";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                $user = $lesInformations->fetch_assoc();
                //@todo: afficher le résultat de la ligne ci dessous, remplacer XXX par l'alias et effacer la ligne ci-dessous
                //echo "<pre>" . print_r($user, 1) . "</pre>";
                ?>
                <img src="user.jpg" alt="Portrait de l'utilisatrice"/>
                <section>
                    <h3>Présentation</h3>
                    <p>Sur cette page vous trouverez tous les message des utilisatrices
                        auxquel est abonnée l'utilisatrice <?php echo $user['alias']?>.
    
                    </p>

                </section>
            </aside>
            <main>
                <?php
                /**
                 * Etape 3: récupérer tous les messages des abonnements
                 */
                $laQuestionEnSql = "
                    SELECT posts.content,
                    posts.created,
                    posts.user_id,
                    users.alias as author_name,  
                    count(likes.id) as like_number,  
                    GROUP_CONCAT(DISTINCT tags.label) AS taglist, 
                    GROUP_CONCAT(DISTINCT tags.id) AS tagid
                    FROM followers 
                    JOIN users ON users.id=followers.followed_user_id
                    JOIN posts ON posts.user_id=users.id
                    LEFT JOIN posts_tags ON posts.id = posts_tags.post_id  
                    LEFT JOIN tags       ON posts_tags.tag_id  = tags.id 
                    LEFT JOIN likes      ON likes.post_id  = posts.id 
                    WHERE followers.following_user_id='$userId' 
                    GROUP BY posts.id
                    ORDER BY posts.created DESC  
                    ";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                if ( ! $lesInformations)
                {
                    echo("Échec de la requete : " . $mysqli->error);
                }

                /**
                 * Etape 4: @todo Parcourir les messsages et remplir correctement le HTML avec les bonnes valeurs php
                 * A vous de retrouver comment faire la boucle while de parcours...
                 */

                while ($post = $lesInformations->fetch_assoc())
                {

                    //echo "<pre>" . print_r($post, 1) . "</pre>";

                ?>                
                <article>
                    <h3>
                        <time datetime='2020-02-01 11:12:13' ><?php echo $post['created']?></time>
                    </h3>
                    <address><a href="wall.php?user_id=<?php echo $post['user_id'] ?>"><?php echo $post['author_name']?></a></address>
                    <div>
                        <p><?php echo $post['content']?></p>
                    </div>                                            
                    <footer>
                        <small>♥ <?php echo $post['like_number']?></small>

                        <?php 

                        //A REVOIR (effacer ligne pour avoir taglist dans la query??) 
                        if(empty($post['taglist'])){
                            echo "<br>"; 
                        } else {
                        $arrayTags = explode(',', $post['taglist']);
        
                           foreach($arrayTags as $tags) {
                            $result = $mysqli->query("
                            SELECT id FROM tags WHERE label='$tags'
                            ");  

                            $row = $result->fetch_array(MYSQLI_NUM);

                            //echo "<pre>" . print_r($row, 1) . "</pre>";

                            echo '<a href="tags.php?tag_id='.$row[0][0].'">#' . $tags . ' </a>';
                            } 
                        }

                        /*
                        OLD VERSION 1 : for + query 
                        for($i=1; $i < count($arrayTags); $i++) {
                            $tag_label = $arrayTags[$i];
                            $result = $mysqli->query("
                            SELECT id FROM tags WHERE label='$tag_label'
                            ");  

                            $row = $result->fetch_array(MYSQLI_NUM);

                           // echo "<pre>" . print_r($row, 1) . "</pre>";

                            echo '<a href="tags.php?tag_id='.$row[0].'">#' . $arrayTags[$i] . ' </a>';
                        }
                        */

                        /*
                        OLD VERSION 2 without query not working on multiple tags
                        foreach($arrayTags as $tags) {
                        echo '<a href="tags.php?tag_id='.$post['tagid'].'">#' . $tags . ' </a>';
                        }
                        */

                        ?>
                        
                    </footer>
                </article>
                <?php
                }
                ?>


            </main>
        </div>
    </body>
</html>
