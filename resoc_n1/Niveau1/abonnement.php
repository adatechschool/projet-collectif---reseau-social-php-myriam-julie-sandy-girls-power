<?php 
include("connexion.php");
include('connexionbdd.php'); 

echo $_POST['userToFollow']; 
echo $_SESSION['connected_id']; 
            $abonnementEnCours = isset($_POST['userToFollow']);
                    if ($abonnementEnCours)
                    {
                        // on ne fait ce qui suit que si un formulaire a été soumis.
                        // Etape 2: récupérer ce qu'il y a dans le formulaire @todo: c'est là que votre travaille se situe
                        // observez le résultat de cette ligne de débug (vous l'effacerez ensuite)
                        //echo "<pre>" . print_r($_POST, 1) . "</pre>";
                        // et complétez le code ci dessous en remplaçant les ???
                        //$authorId = $_POST['auteur'];
                        $userToFollow = $_POST['userToFollow'];
                        $connectedUser = $_SESSION['connected_id']; 


                        //Etape 3 : Petite sécurité
                        // pour éviter les injection sql : https://www.w3schools.com/sql/sql_injection.asp
                        $userToFollow = intval($mysqli->real_escape_string($userToFollow));
                        $connectedUser = $mysqli->real_escape_string($connectedUser);
                        //Etape 4 : construction de la requete
                        $lInstructionSql = "INSERT INTO followers "
                                . "(id, followed_user_id, following_user_id) "
                                . "VALUES (NULL, "
                                . $userToFollow . ", "
                                . $connectedUser . ");"
                                ;
                        //echo $lInstructionSql;
                        // Etape 5 : execution
                        $ok = $mysqli->query($lInstructionSql);
                        if ( ! $ok)
                        {
                            echo "Impossible de s'abonner: " . $mysqli->error;
                        } else
                        {
                            echo "Vous suivez :" . $userId;
                        }
                    }
                    
                    header("location:wall.php?user_id=$userToFollow");
                    ?> 