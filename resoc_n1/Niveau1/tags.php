<?php include("connexion.php"); ?>

<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>ReSoC - Les message par mot-clé</title> 
        <meta name="author" content="Julien Falconnet">
        <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    </head>
    <body>
    <?php include('header.php'); ?>
        <div id="wrapper">
            <?php
            /**
             * mais elle porte sur les mots-clés (tags)
             */
            /**
             * Etape 1: Le mur concerne un mot-clé en particulier
             */
            $tagId = intval($_GET['tag_id']);
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
                 * Etape 3: récupérer le nom du mot-clé
                 */
                $laQuestionEnSql = "SELECT * FROM tags WHERE id= '$tagId' ";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                $tag = $lesInformations->fetch_assoc();
                //@todo: afficher le résultat de la ligne ci dessous, remplacer XXX par le label et effacer la ligne ci-dessous
                //echo "<pre>" . print_r($tag, 1) . "</pre>";
                ?>
                <img src="clara.jpg" alt="Portrait de l'utilisatrice"/>
                <section>
                    <h3>Présentation</h3>
                    <p>Sur cette page vous trouverez les derniers messages comportant
                        le mot-clé <?php echo $tag['label']?>.
                        
                    </p>

                </section>
            </aside>
            <main>
                <?php
                /**
                 * Etape 3: récupérer tous les messages avec un mot clé donné
                 */
                $laQuestionEnSql = "
                    SELECT posts.content,
                    posts.created,
                    posts.user_id,
                    posts.id as postid, 
                    users.alias as author_name,  
                    count(likes.id) as like_number,  
                    GROUP_CONCAT(DISTINCT tags.label) AS taglist 
                    FROM posts_tags as filter 
                    JOIN posts ON posts.id=filter.post_id
                    JOIN users ON users.id=posts.user_id
                    LEFT JOIN posts_tags ON posts.id = posts_tags.post_id  
                    LEFT JOIN tags       ON posts_tags.tag_id  = tags.id 
                    LEFT JOIN likes      ON likes.post_id  = posts.id 
                    WHERE filter.tag_id = '$tagId' 
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
                            <small>
                            
                            <?php 

                            // -------> BOUTON LIKE 
                            $userId = $_SESSION['connected_id']; 
                            $postLiked = $post['postid']; 


                            //Requête pour savoir si l'utilisateur like déjà le post 
                            $likeStatus = "SELECT id FROM likes WHERE `user_id` ='$userId' AND post_id='$postLiked' "; 
                            $likeStatusResult = $mysqli->query($likeStatus);
                            $row_likeStatus = $likeStatusResult->num_rows; //Retourne le nombre de lignes dans le jeu de résultats
                            //echo $row; 
                            //echo $row_likeStatus; 

                            if($row_likeStatus == 0){
                                echo '<form action="like.php" method="post" id="likePost">
                                <input name="postLiked" type="hidden" value="' . $post["postid"] . '" />
                                <button id="heart" type="submit">♥ ' . $post["like_number"] . '</button>
                            </form>';
                            } else {
                                echo '<form action="unlike.php" method="post" id="likePost">
                                <input name="postUnliked" type="hidden" value="' . $post["postid"] . '" />
                                <button id="heart" type="submit"><span id="heartColor">♥</span> ' . $post["like_number"] . '</button>
                                </form>';
                            }
                            ?></small>


                            <?php 

                            //A REVOIR (effacer ligne pour avoir taglist dans la query??) 
                            $arrayTags = explode(',', $post['taglist']);
    
                            foreach($arrayTags as $tags) {
                                $result = $mysqli->query("
                                SELECT id FROM tags WHERE label='$tags'
                                ");  
    
                                $row = $result->fetch_array(MYSQLI_NUM);
    
                                //echo "<pre>" . print_r($row, 1) . "</pre>";
    
                                echo '<a href="tags.php?tag_id='.$row[0].'">#' . $tags . ' </a>';
                                }
                        ?>

                        
                        </footer>
                    </article>
                <?php } ?>


            </main>
        </div>
    </body>
</html>