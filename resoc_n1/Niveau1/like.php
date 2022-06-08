<?php 
include("connexion.php");
include('connexionbdd.php'); 

echo $_POST['postLiked']; 
echo $_SESSION['connected_id']; 
//echo "<pre>" . print_r($_POST, 1) . "</pre>";
            $clickLike = isset($_POST['postLiked']);
                    if ($clickLike)
                    {
                        // on ne fait ce qui suit que si un formulaire a été soumis.
                        // Etape 2: récupérer ce qu'il y a dans le formulaire @todo: c'est là que votre travaille se situe
                        // observez le résultat de cette ligne de débug (vous l'effacerez ensuite)
                        //echo "<pre>" . print_r($_POST, 1) . "</pre>";
                    
                        
                        $postLiked = $_POST['postLiked'];
                        $connectedUser = $_SESSION['connected_id']; 


                        //Etape 3 : Petite sécurité
                        // pour éviter les injection sql : https://www.w3schools.com/sql/sql_injection.asp
                        $postLiked = intval($mysqli->real_escape_string($postLiked));
                        $connectedUser = $mysqli->real_escape_string($connectedUser);
                        //Etape 4 : construction de la requete
                        $lInstructionSql = "INSERT INTO likes "
                                . "(id, user_id, post_id) "
                                . "VALUES (NULL, "
                                . $connectedUser . ", "
                                . $postLiked . ");"
                                ;
                        //echo $lInstructionSql;
                        // Etape 5 : execution
                        $ok = $mysqli->query($lInstructionSql);
                        if ( ! $ok)
                        {
                            echo "Impossible de liker" . $mysqli->error;
                        } else
                        {
                            echo "Vous likez :" . $userId;
                        }
                    }
                
                    header("location:news.php?user_id=$connectedUser");
                    ?> 