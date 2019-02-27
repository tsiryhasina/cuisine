<?php
//connexion bdd
try{
$bdd = New PDO('mysql:host=localhost;dbname=cuisine;charset=utf8', 'root','',array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
}
catch(Exception $e)
{
        die('Erreur : '.$e->getMessage());
}
$req=$bdd->query('SELECT * FROM roles');
//traitement formulaire
if(isset($_POST['form_ajout_utilisateurs']))
    {
        //stock mes valeurs des $_POST
        $nom = htmlspecialchars($_POST['ajout_nom']);
        $prenom = htmlspecialchars($_POST['ajout_prenom']);
        $mail = htmlspecialchars($_POST['ajout_mail']);
        $telephone = htmlspecialchars($_POST['ajout_telephone']);
        $specialites = htmlspecialchars($_POST['ajout_specialites']);
        //$roles = htmlspecialchars($_POST['id']);
        
        //Vérifier existence et si non vide
        if (isset($nom) AND !empty($nom) AND isset($prenom) AND !empty($prenom)
            AND isset($mail) AND !empty($mail) AND isset($specialites) AND !empty($specialites)
            AND isset($telephone) AND !empty($telephone))                                    
        {          
            if(filter_var($mail, FILTER_VALIDATE_EMAIL))
            {
                $reqmail = $bdd ->prepare("SELECT * FROM utilisateur WHERE mail = ?");
                $reqmail->execute(array($mail));
                $mailexist = $reqmail->rowCount();
                if($mailexist == 0)
                {
                    //prepare insert into pour envoyer des données dans la BDD
                    $utilisateurs = $bdd -> prepare('INSERT INTO utilisateurs (nom, prenom, mail, specialites, telephone) VALUES (?,?,?,?,?)');
                    $utilisateurs ->execute(array($nom, $prenom, $mail, $specialites, $telephone));
                    $message ='Utilisateur bien enregistrer!';  
                    
                    $lastid = $bdd -> lastInsertId();
                    $lastid = $bdd -> prepare('INSERT INTO roles_utilisateurs (id_roles, id_utilisateurs) VALUES (?,?)');
                    $lastid -> execute(array( htmlspecialchars($roles['id'], $lastid)));
                }
                else
                {
                    $message = "le mail existe déja !"; 
                }
            }
            else
            {
                $message = "votre mail n'est pas valide ! ";
            }
        }
        else
        {
             $message ='Saisi incorrect.';
        }
    }   
    else
        {
            $message = 'Tous les champs doivent être complétés.';
        }
    
?>

<html>
<head>
<title>utilisateurs</title>	
<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
</head>
<body>
	<form action="afficher_utilisateurs.php" style="align:center" method="POST">
        <div class="form-group">
            <input class="" type="text" name="ajout_nom" placeholder="saisir votre nom">
        </div>
        <div class="form-group">
            <input   type="text" name="ajout_prenom" placeholder="saisir votre prenom">
        </div>
        <div class="form-group">
            <input type="mail" name="ajout_mail" placeholder="saisir votre mail">
        </div>
        <div class="form-group">
            <input type="text" name="ajout_specialites" placeholder="saisir specialites">
        </div>
        <div class="form-group">
        <input type="tel" name="ajout_telephone" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}-[0-9]{2}" placeholder="0692-54-58-96" required>
        </div>
        <div class="form-group">
        <select name="roles">
         <?php
        /*affichage des données dans la table roles */
           
            while ($roles=$req->fetch()){
               ?><option value="<?php echo $roles['id']; ?>"><?php echo $roles['label']; ?></option>
        <?php
            }


        ?> 
        
        </select>
        </div>
        <button type="submit" class="btn btn-primary mb-2" name="form_ajout_utilisateurs" value="ajouter un utilisateur">ajouter</button>
      
	</form>
        <?php
        if(isset($message)){
        echo $message;
        }
        ?>
    

</body>
</html>