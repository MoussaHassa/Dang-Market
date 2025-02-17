<?php
    session_start();
    require_once 'config.php';
    // tester si l'utilisateur est deja connecter
    if(isset($_SESSION["loggedin_user"]) && $_SESSION["loggedin_user"]==true && $_SESSION["username_user"]==$_SESSION["getusername_user"] && $_SESSION["id_user"]==$_SESSION["getid_user"])
    {
        header("Location: ../index.php");
        exit;
    }
    $username=$password= "";
    $username_err=$password_err=$login_err= "";
    if($_SERVER["REQUEST_METHOD"] == "POST")
    {   // tester si l' nom d'utilisateur est defini
        
        if(empty(securiser($_POST["username_login"])))
        {
            echo "Entrer votre nom d'utilisateur";
        }else{
            //securiser le champ nom d'utilisateur
            $username=securiser($_POST["username_login"]);
        }
        // tester si le mot de passe est definie
        if(empty(securiser($_POST["password_login"])))
        {
            $password_err="Entrer votre mot de passe s'il vous plait ";
        }else{
            $password=securiser($_POST["password_login"]);
        }
        if(empty($username_err) && empty($password_err))
        {
            $sql="SELECT id,username,password FROM users WHERE username =:username";
            
            if($requete=$bdd->prepare($sql))
            {
                //parametre requete preparee
                $requete->bindParam(":username",$param_username,PDO::PARAM_STR);
                //valeur parametre
                $param_username=securiser($_POST["username_login"]);
                if($requete->execute())
                {
                    if($requete->rowcount()==1)
                    {
                        if($info_user=$requete->fetch())
                        {
                            $id=$info_user["id"];
                            $username=$info_user["username"];
                            $hashed_password=$info_user["password"];
                            if(password_verify($password,$hashed_password))
                            {
                                // mot de passe correct
                                // stockage des données dans les variables sessions
                                $_SESSION["loggedin_user"]=true;
                                $_SESSION["getid_user"]=$id;
                                $_SESSION["id_user"]=$id;
                                $_SESSION["username_user"]=$username;
                                $_SESSION["getusername_user"]=$username;
                                // redirection vers acceuil admin
                                header("Location: ../index.php");
                            }else{
                                header("Location: loginSignUp.php");
                            }
                        }else{
                            header("Location: loginSignUp.php");
                        }  
                    }else{
                        header("Location: loginSignUp.php");
                    }    
                }else{
                    echo "Oops ! une erreur est survenue ! veillez réessayer plus tard";
                    header("Location: loginSignUp.php");
                }
                    // fermeture requete
                    unset($requete);
            }
            }
            //déconnexion à la bdd
            unset($bdd);
    }  
?>