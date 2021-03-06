<?php include("connexion.php"); ?>
<?php // include ("restriction.php");?>

<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>ReSoC - Mur</title>
    <meta name="author" content="Julien Falconnet">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>

<body>
    <?php include('header.php'); ?>
    <div id="wrapper">
        <?php
        /**
         * Documentation : https://www.php.net/manual/fr/reserved.variables.get.php
         * ... mais en résumé c'est une manière de passer des informations à la page en ajoutant des choses dans l'url
         */

        $user_Id = intval($_GET['user_id']);
        $userId = $_SESSION['connected_id'];
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
            $laQuestionEnSql = "SELECT * FROM users WHERE id= '$user_Id' ";
            $lesInformations = $mysqli->query($laQuestionEnSql);
            $user = $lesInformations->fetch_assoc();
            //@todo: afficher le résultat de la ligne ci dessous, remplacer XXX par l'alias et effacer la ligne ci-dessous
            //echo "<pre>" . print_r($user, 1) . "</pre>";
            ?>
            <img src="clara.jpg" alt="Portrait de l'utilisatrice" />
            <section>
                <h3>Présentation</h3>
                <p>Sur cette page vous trouverez tous les message de l'utilisatrice : <?php echo $user["alias"]; ?></p>
            </section>


            <!-- BOUTON POUR S'ABONNER ET SE DÉSABONNER -->

            <?php 

            //Requête pour savoir si l'utilisateur connecté follow déjà la page
            $followStatus = "SELECT id FROM followers WHERE followed_user_id ='$user_Id' AND following_user_id='$userId' "; 
            $followStatusResult = $mysqli->query($followStatus);
            $row_followStatus = $followStatusResult->num_rows; //Retourne le nombre de lignes dans le jeu de résultats

            //echo $row_followStatus; 

            if($userId == $user_Id) {
                echo  " ";
            } elseif($row_followStatus == 0){
                echo  '<form action="abonnement.php" method="post">
                <input name="userToFollow" type="hidden" value="' . $user_Id . '"/>
            <input id="followbutton" value="S\'abonner" type="submit">
            </form><br>'; 
            } else {
                echo '<form action="desabonnement.php" method="post">
                <input name="userToUnfollow" type="hidden" value="' . $user_Id . '"/>
            <input id="unfollowbutton" value="Se désabonner" type="submit">
            </form><br>';
            }
                ?>
    


            <div>
                <a href="followers.php?user_id=<?php echo $user_Id ?>">Abonnés</a> - 
                <a href="subscriptions.php?user_id=<?php echo $user_Id ?>">Abonnements</a>
            </div><br>



            <!-- FORMULAIRE : POSTER UN MESSAGE SUR SON MUR --> 

            <?php

            // Etape 1 : vérifier si on est en train d'afficher ou de traiter le formulaire
            // si on recoit un champs email rempli il y a une chance que ce soit un traitement
            $enCoursDeTraitement = isset($_POST['message']);
                    if ($enCoursDeTraitement)
                    {
                        // on ne fait ce qui suit que si un formulaire a été soumis.
                        // Etape 2: récupérer ce qu'il y a dans le formulaire @todo: c'est là que votre travaille se situe
                        // observez le résultat de cette ligne de débug (vous l'effacerez ensuite)
                        //echo "<pre>" . print_r($_POST, 1) . "</pre>";
                        // et complétez le code ci dessous en remplaçant les ???
                        //$authorId = $_POST['auteur'];
                        $postContent = $_POST['message'];
                        $authorId = $userId; 


                        //Etape 3 : Petite sécurité
                        // pour éviter les injection sql : https://www.w3schools.com/sql/sql_injection.asp
                        $authorId = intval($mysqli->real_escape_string($authorId));
                        $postContent = $mysqli->real_escape_string($postContent);
                        //Etape 4 : construction de la requete
                        $lInstructionSql = "INSERT INTO posts "
                                . "(id, user_id, content, created, parent_id) "
                                . "VALUES (NULL, "
                                . $authorId . ", "
                                . "'" . $postContent . "', "
                                . "NOW(), "
                                . "NULL);"
                                ;
                        //echo $lInstructionSql;
                        // Etape 5 : execution
                        $ok = $mysqli->query($lInstructionSql);
                        if ( ! $ok)
                        {
                            echo "Impossible d'ajouter le message: " . $mysqli->error;
                        } else
                        {
                            echo "Message posté en tant que :" . $userId;
                            
                            //-----------------------GESTION DES TAGS DANS LE MESSAGE POSTÉ
                            $postContentId = $mysqli->insert_id; //On récupère l'id du dernier msg
                             
                            //On récupère les mots commençant par un #
                            preg_match_all("/(#\w+)/u", $postContent, $matches);  
                            //print_r( $matches );

                            foreach( $matches[0] as $hashtag){
                                $hash = ltrim($hashtag, '#'); //retirer le # devant 
                                echo $hash;

                                //Check si le hashtag est dans la BDD
                                $checkTag = "SELECT id FROM tags WHERE label='$hash' ";
                                $checkTagsResult = $mysqli->query($checkTag);
                                $row_checkTag = $checkTagsResult->num_rows; //retourne le nombre de lignes dans un résultat
                                echo 'ceci est le row : ' . $row_checkTag; 

                                //-----> Si # n'est PAS dans BDD : 
                                //insertion du tag dans la table 'tags'
                                if($row_checkTag == 0){
                                    $insertIntoTag = "INSERT INTO tags (id, label) VALUES (NULL, '$hash');"
                                ;
                                $done = $mysqli->query($insertIntoTag);

                                $lastTagId = $mysqli->insert_id; //On récupère l'id du tag qui vient d'être ajouté
                                //insertion de l'id du tag dans la table 'posts_tags'
                                    if ($done) {
                                        $insertIntoPostTag = "INSERT INTO posts_tags "
                                        . "(id, post_id, tag_id) "
                                        . "VALUES (NULL, "
                                        . $postContentId . ", "
                                        . $lastTagId . ");"
                                        ;

                                        $good = $mysqli->query($insertIntoPostTag);
                                    } else {
                                        echo "ERREUR (ajout dans table tag)"; 
                                    }

                                //-----> Si # EST dans BDD : 
                                } else {
                                    $row = $checkTagsResult->fetch_assoc(); 
                                    $rowId = $row['id'];
                                    echo "Ceci est rowId : " . $rowId; 

                                    $insertTag = "INSERT INTO posts_tags "
                                        . "(id, post_id, tag_id) "
                                        . "VALUES (NULL, "
                                        . $postContentId . ", "
                                        . $rowId . ");"
                                        ;

                                        $allgood = $mysqli->query($insertTag);

                                        if(!$allgood){
                                            echo "ERREUR (ajout dans table post_tags) !";
                                        }
                                }

                            }
                            

                        }
                    }
                    ?> 

                    <?php 

                    //echo $userId;
                    //echo $user_Id; 

                    //Formulaire pour poster un message
                    if($userId == $user_Id) {
                        echo '<div>
                    <form action="wall.php?user_id=' . $userId . '" method="post">
                        <input type="hidden" name="???" value="achanger">
                    
                            <label for="message">Message</label><br>
                            <textarea id="areamessage" name="message"></textarea><br>
                    
                        <input id="messagebutton" type="submit">
                    </form>
                        </div>';

                    } else {
                        echo " ";
                    }

                    ?>

        </aside>




                    <!--- AFFICHER LES MESSAGES -->
        <main>
            <?php
            /**
             * Etape 3: récupérer tous les messages de l'utilisatrice
             */
            $laQuestionEnSql = "
                    SELECT posts.content, posts.id as postid, posts.user_id, posts.created, users.alias as author_name, 
                    COUNT(likes.id) as like_number, GROUP_CONCAT(DISTINCT tags.label) AS taglist,
                    GROUP_CONCAT(DISTINCT tags.id) AS tagid
                    FROM posts
                    JOIN users ON  users.id=posts.user_id
                    LEFT JOIN posts_tags ON posts.id = posts_tags.post_id  
                    LEFT JOIN tags       ON posts_tags.tag_id  = tags.id 
                    LEFT JOIN likes      ON likes.post_id  = posts.id 
                    WHERE posts.user_id='$user_Id' 
                    GROUP BY posts.id
                    ORDER BY posts.created DESC  
                    ";
            $lesInformations = $mysqli->query($laQuestionEnSql);
            if (!$lesInformations) {
                echo ("Échec de la requete : " . $mysqli->error);
            }

            /**
             * Etape 4: @todo Parcourir les messsages et remplir correctement le HTML avec les bonnes valeurs php
             */
            while ($post = $lesInformations->fetch_assoc()) {

                //echo "<pre>" . print_r($post, 1) . "</pre>";
            ?>
                <article>
                    <h3>
                        <time datetime='2020-02-01 11:12:13'><?php echo $post['created'] ?></time>
                    </h3>
                    <address><a href="wall.php?user_id=<?php echo $post['user_id'] ?>"><?php echo $post['author_name'] ?></a></address>
                    <div>
                        <p><?php echo $post['content'] ?></p>
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
                                
                            
                                ?>
                        </small>
                        <?php


                        //TAGS 
                        if (empty($post['taglist'])) {
                            echo "<br>";
                        } else {
                            $arrayTags = explode(',', $post['taglist']);

                            foreach ($arrayTags as $tags) {
                                $result = $mysqli->query("
                            SELECT id FROM tags WHERE label='$tags'
                            ");

                                $row = $result->fetch_array(MYSQLI_NUM);

                                //echo "<pre>" . print_r($row, 1) . "</pre>"; 

                                echo '<a href="tags.php?tag_id=' . $row[0] . '">#' . $tags . ' </a>';
                            }
                        }
                        ?>

                    </footer>
                </article>
            <?php } ?>


        </main>
    </div>
</body>

</html>