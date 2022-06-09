<?php 
include("connexion.php");
include('connexionbdd.php'); 

//echo $_POST['userToFollow']; 
//echo $_SESSION['connected_id']; 
            $desabonnementEnCours = isset($_SESSION['connected_id']);
                    if ($desabonnementEnCours)
                    {
                        // on ne fait ce qui suit que si un formulaire a été soumis.
                        // Etape 2: récupérer ce qu'il y a dans le formulaire @todo: c'est là que votre travaille se situe
                        $userToUnfollow = $_POST['userToUnfollow'];
                        $connectedUser = $_SESSION['connected_id']; 


                        //Etape 3 : Petite sécurité
                        // pour éviter les injection sql : https://www.w3schools.com/sql/sql_injection.asp
                        $userToUnfollow = intval($mysqli->real_escape_string($userToUnfollow));
                        $connectedUser = $mysqli->real_escape_string($connectedUser);

                        //Etape 4 : construction de la requete
                        $lInstructionSql = "DELETE FROM followers WHERE followed_user_id ='$userToUnfollow' AND following_user_id='$connectedUser' ";

                        // Etape 5 : execution
                        $ok = $mysqli->query($lInstructionSql);
                        if ( ! $ok)
                        {
                            echo "Impossible de se désabonner: " . $mysqli->error;
                        } else
                        {
                            echo "Vous vous êtes bien désabonné de :" . $userToUnfollow;
                        }
                    }
                    
                    header("location:wall.php?user_id=$userToUnfollow");
