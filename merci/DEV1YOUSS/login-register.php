<?php require_once '_tools.php';

?>
<?php IF (ISSET($_SESSION['email'])) {

    header('location:index.php');

} else { ?>
    <?php
    IF (ISSET($_POST['register'])) {
        IF (!empty($_POST['firstname']) && !empty($_POST['lastname']) && !empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['password_confirm']) && !empty($_POST['bio'])) {

            //VARIABLE
            $firstname = $_POST['firstname'];
            $lastname = $_POST['lastname'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $pass_confirm = $_POST['password_confirm'];
            $bio = $_POST['bio'];
            $is_admin = 0;

            //TEST SI PASSWORD = PASSWORD_CONFIRM


            IF ($password !== $pass_confirm) {

                header('Location:login-register.php?message=password');

            } ELSE {

                //TEST SI EMAIL UTILISE    EXISTE DEJA

                $query = "SELECT  *  FROM  user WHERE  email=:email";
                $Stmt1 = $db->prepare($query);
                $Stmt1->bindParam(':email', $email, PDO::PARAM_STR);
                $Stmt1->execute() or die(Print_r($db->errorInfo()));
                $Count1 = $Stmt1->rowCount();
                IF ($Count1 > 0) {
                    header('Location: login-register.php?message=email');

                } ELSE {

                    //CRYPTAGE DU PASSWORD
                    $passwords = sha1($password);

                    // ENVOIE DE LA REQUETTE

                    $requete = $db->prepare("INSERT INTO user (firstname,lastname,email,password,biographie,is_admin) VALUES(?,?,?,?,?,?)");
                    $requete->execute(array($firstname, $lastname, $email, $passwords, $bio, $is_admin));

                    header('Location: login-register.php?message=registersucces');
                }
            }
        }
    }

    IF (ISSET($_POST['login'])) {
        $email = $_POST['email'];
        $password = sha1($_POST['password']);


        $REQUETE = "SELECT  email,password  From  user Where  email=:email  AND  password=:password";

        $Stmt = $db->prepare($REQUETE);
        $Stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $Stmt->bindParam(':password', $password, PDO::PARAM_STR);
        $Stmt->execute() or die(Print_r($db->errorInfo()));
        $Compte = $Stmt->rowCount();// on va cherche ࡣompter les nombres des utilisateurs inscrits
        IF ($Compte > 0) { //si le nombre est superieur ࡺero on affiche la page correspondant ࡬'utilisateur


            $_SESSION['id'] = 1;
            $_SESSION['email'] = $email;

            header('location:index.php?page=dashboard');

        } ELSE {
            header('Location:login-register.php?message=loginerror');
        }


        $Stmt->CloseCursor();
    }
    ?>

    <!DOCTYPE html>
    <html>
<head>


    <title>Login - Mon premier blog !</title>

    <meta charset="utf-8">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.3.1/jquery.fancybox.min.css"/>
    <link rel="stylesheet" href="css/main.css">
<!--    <link rel="stylesheet" href="../css/main.css"> -->

</head>

<body class="article-body">
<div class="container-fluid">

    <?php require 'partials/header.php'; ?>

    <div class="row my-3 article-content">


        <?php require 'partials/nav.php'; ?>
        <main class="col-9">

            <?php
            if (isset($_GET['message'])) {
                $_GET['message'];
                IF ($_GET['message'] == 'password') {
                    echo '<p class="alert">Le deux mots de passe ne correspond pas</p>';
                }
            }

            ?>

            <?php
            if (isset($_GET['message'])) {
                $_GET['message'];
                IF ($_GET['message'] == 'email') {
//                    echo '<p style="color:red;text-align:center;font-size:16px">L"adresse e-mail existe déjà , veuillez vous connectez.</p>';
                    echo '<p class="alert">L"adresse e-mail existe déjà , veuillez vous connectez.</p>';
                }
            }

            ?>


            <?php
            if (isset($_GET['message'])) {
                $_GET['message'];
                IF ($_GET['message'] == 'registersucces') {
//                    echo '<p class="alert" style="color:red;text-align:center;font-size:16px"> Inscription réussie , veuillez vous connectez. </p>';
                    echo '<p class="success"> Inscription réussie , veuillez vous connectez. </p>';
                }
            }

            ?>

            <?php
            if (isset($_GET['message'])) {
                $_GET['message'];
                IF ($_GET['message'] == 'loginerror') {
                    echo '<p class="alert">Mot de passe ou adresse email incorrecte, veuillez réessayer !!! </p>';
                }
            }

            ?>


            <ul class="nav nav-tabs justify-content-center nav-fill" role="tablist">
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#login" role="tab" aria-expanded="false">Connexion</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#register" role="tab" aria-expanded="true">Inscription</a>
                </li>
            </ul>

            <div class="tab-content">

                <div class="tab-pane container-fluid" id="login" role="tabpanel" aria-expanded="false">

                    <form action="login-register.php" method="post" class="p-4 row flex-column">

                        <h4 class="pb-4 col-sm-8 offset-sm-2">Connexion</h4>


                        <div class="form-group col-sm-8 offset-sm-2">
                            <label for="email">Email</label>
                            <input class="form-control" value="" type="email" placeholder="Email" name="email"
                                   id="email" required>
                        </div>

                        <div class="form-group col-sm-8 offset-sm-2">
                            <label for="password">Mot de passe</label>
                            <input class="form-control" value="" type="password" placeholder="Mot de passe"
                                   name="password" id="password" required>
                        </div>

                        <div class="text-right col-sm-8 offset-sm-2">
                            <input class="btn btn-success" type="submit" name="login" value="Valider" required>
                        </div>

                    </form>

                </div>

                <div class="tab-pane container-fluid active" id="register" role="tabpanel" aria-expanded="true">
                    <form action="login-register.php" method="post" class="p-4 row flex-column">

                        <h4 class="pb-4 col-sm-8 offset-sm-2">Inscription</h4>


                        <div class="form-group col-sm-8 offset-sm-2">
                            <label for="firstname">Prénom <b class="text-danger">*</b></label>
                            <input class="form-control" value="" type="text" placeholder="Prénom" name="firstname"
                                   id="firstname" required>
                        </div>
                        <div class="form-group col-sm-8 offset-sm-2">
                            <label for="lastname">Nom de famille</label>
                            <input class="form-control" value="" type="text" placeholder="Nom de famille"
                                   name="lastname" id="lastname" required>
                        </div>
                        <div class="form-group col-sm-8 offset-sm-2">
                            <label for="email">Email <b class="text-danger">*</b></label>
                            <input class="form-control" value="" type="email" placeholder="Email" name="email"
                                   id="email" required>
                        </div>
                        <div class="form-group col-sm-8 offset-sm-2">
                            <label for="password">Mot de passe <b class="text-danger">*</b></label>
                            <input class="form-control" value="" type="password" placeholder="Mot de passe"
                                   name="password" id="password" required>
                        </div>
                        <div class="form-group col-sm-8 offset-sm-2">
                            <label for="password_confirm">Confirmation du mot de passe <b
                                        class="text-danger">*</b></label>
                            <input class="form-control" value="" type="password"
                                   placeholder="Confirmation du mot de passe" name="password_confirm"
                                   id="password_confirm" required>
                        </div>
                        <div class="form-group col-sm-8 offset-sm-2">
                            <label for="bio">Biographie</label>
                            <textarea class="form-control" name="bio" id="bio" placeholder="Ta vie Ton oeuvre..."
                                      required></textarea>
                        </div>

                        <div class="text-right col-sm-8 offset-sm-2">
                            <p class="text-danger">* champs requis</p>
                            <input class="btn btn-success" type="submit" name="register" value="Valider">
                        </div>

                    </form>

                </div>
            </div>
        </main>

    </div>

    <?php require 'partials/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.1.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.3.1/jquery.fancybox.min.js"></script>

    <script src="js/main.js"></script>

</div>


</body>
<?php } ?>