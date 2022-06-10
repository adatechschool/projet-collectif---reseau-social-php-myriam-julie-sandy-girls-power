<?php 
include("connexion.php");
include('connexionbdd.php'); 

echo $_POST['postUnliked']; 
echo $_SESSION['connected_id']; 
//echo "<pre>" . print_r($_POST, 1) . "</pre>";
            $clickUnlike = isset($_POST['postUnliked']);
                    if ($clickUnlike)
                    {
                        // on ne fait ce qui suit que si un formulaire a été soumis.
                        // Etape 2: récupérer ce qu'il y a dans le formulaire @todo: c'est là que votre travaille se situe
                        // observez le résultat de cette ligne de débug (vous l'effacerez ensuite)
                        //echo "<pre>" . print_r($_POST, 1) . "</pre>";
                    
                        
                        $postUnliked = $_POST['postUnliked'];
                        $connectedUser = $_SESSION['connected_id']; 


                        //Etape 3 : Petite sécurité
                        // pour éviter les injection sql : https://www.w3schools.com/sql/sql_injection.asp
                        $postUnliked = intval($mysqli->real_escape_string($postUnliked));
                        $connectedUser = $mysqli->real_escape_string($connectedUser);
                        //Etape 4 : construction de la requete
                        $lInstructionSql = "DELETE FROM likes WHERE `user_id` ='$connectedUser' AND post_id='$postUnliked' ";
                        //echo $lInstructionSql;
                        // Etape 5 : execution
                        $ok = $mysqli->query($lInstructionSql);
                        if ( ! $ok)
                        {
                            echo "Impossible de unliker" . $mysqli->error;
                        } else
                        {
                            echo "Vous avez unliké le post numéro :" . $postUnliked;
                        }
                    }
                
                    header('location:' .$_SERVER[HTTP_REFERER]. '?user_id=' . $connectedUser .'');
                    ?> 