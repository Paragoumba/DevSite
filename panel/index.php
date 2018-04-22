<!DOCTYPE html>
<?php $pseudo = "pseudo";
$passwd = "password";

$edit = 0;
$delete = 1;

function getCredentials(){

    $fileContent = file_get_contents("../creds.para");
    $fileContent = explode("\n", $fileContent);

    $creds['server'] = substr($fileContent[0], 0, strlen($fileContent[0]) - 1);
    $creds['username'] = substr($fileContent[1], 0, strlen($fileContent[1]) - 1);
    $creds['password'] = substr($fileContent[2], 0, strlen($fileContent[2]) - 1);
    $creds['dbName'] = substr($fileContent[3], 0, strlen($fileContent[3]) - 1);
    $creds['panelPseudo'] = substr($fileContent[4], 0, strlen($fileContent[4]) - 1);
    $creds['panelPassword'] = substr($fileContent[5], 0, strlen($fileContent[5]) - 1);

    return $creds;

}

function getMaxId($conn){

    $sql = "SELECT max(id) FROM `projects`";
    $result = $conn->query($sql);

    while ($maxId = $result -> fetch_assoc()) return $maxId['max(id)'];

    return 0;
}

function println($str){
    
    echo $str . "\n";
    
}

function getStateColor($state){

    switch ($state){

        case "0":
            return "success";
            break;

        case "1":
            return "secondary";
            break;

        case "2":
            return "info";
            break;

        default:
            return "danger";

    }
}

function getStateName($state){

    switch ($state){

        case "0":
            return "Actif";
            break;

        case "1":
            return "Inactif";
            break;

        case "2":
            return "Terminé";
            break;

        default:
            return "Annulé";

    }
}?>

<html lang="fr">
<head>
    <title>Panel - DevSite</title>
    <link rel="icon" href="https://www.paragoumba.fr/res/icon.gif">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="bg-theme">
    <div class="container-fluid">
        <div class="row">
<?php $creds = getCredentials();

if (isset($_POST['pseudo']) && isset($_POST['passwd']) && $_POST['pseudo'] === $creds['panelPseudo'] && $_POST['passwd'] === $creds['panelPassword']) {

    //Logged
    setcookie('pseudo', $_POST['pseudo'], time() + 3600000);
    setcookie('passwd', $_POST['passwd'], time() + 3600000);

}

if (isset($_COOKIE['pseudo']) && isset($_COOKIE['passwd']) && $_COOKIE['pseudo'] === $creds['panelPseudo'] && $_COOKIE['passwd'] === $creds['panelPassword']){

    // Create connection
    $conn = new mysqli($creds['server'], $creds['username'], $creds['password'], $creds['dbName']);

    // Check connection
    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

    mysqli_set_charset($conn, "utf8");

    if (isset($_POST['action']) && $_POST['action'] == $edit) {

        if (is_numeric($_POST['id'])) {

            //Edit
            $sql = "SELECT * FROM `projects` WHERE id=" . $_POST['id'];
            $result = $conn->query($sql);

            println("            <header class='card col-lg-12'>");
            println("                <h1>Edition d'un projet</h1>");
            println("            </header>");

            while ($row = $result->fetch_assoc()) {

                println("            <div class='card col-lg-12'>");
                println("                <div class='card-header'>");
                println("                    <h1>" . $row['title'] . "</h1>");
                println("                </div>");
                println("                <div class='card-body'>");
                println("                    <form method='post'>");
                println("                        <div class='form-row'>");
                println("                            <div class='form-group col-lg-6'>");
                println("                                <label for='title'>Titre</label>");
                println("                                <input class='form-control selectable' type='text' name='title' id='title' placeholder='Title' value='" . $row['title'] . "'>");
                println("                            </div>");
                println("                            <div class='form-group col-lg-6'>");
                println("                                <label for='creator'>Créateur</label>");
                println("                                <input class='form-control selectable' type='text' name='creator' id='creator' placeholder='Creator' value='" . $row['creator'] . "'>");
                println("                            </div>");
                println("                        </div>");
                println("                        <div class='form-group'>");
                println("                            <label for='description'>Description</label>");
                println("                            <textarea class='form-control selectable' cols='40' rows='5' name='description' id='description' placeholder='Description'>" . $row['description'] . "</textarea>");
                println("                        </div>");
                println("                        <div class='form-group'>");
                println("                            <label for='link'>Lien</label>");
                println("                            <input class='form-control selectable' type='text' name='link' id='link' placeholder='Link' value='" . $row['link'] . "'>");
                println("                        </div>");
                println("                        <div class='form-group'>");
                println("                            <label for='state'>Etat</label>");
                println("                            <input class='form-control selectable' type='number' max='3' min='0' name='state' id='state' aria-describedBy='stateInfo' value='" . $row['state'] . "'>");
                println("                            <small id='stateInfo' class='form-text selectable'>0 : Actif, 1 : Inactif, 2 : Terminé, 3 : Annulé</small>");
                println("                        </div>");
                println("                        <div class='form-group'>");
                println("                            <label for='stable'>Stabilité</label>");
                println("                            <input class='form-control selectable' type='number' max='1' min='0' name='stable' id='stable' aria-describedBy='stableInfo' value='" . $row['stable'] . "'>");
                println("                            <small id='stableInfo' class='form-text selectable'>0 : Instable, 1 : Stable</small>");
                println("                        </div>");
                println("                        <div class='form-group'>");
                println("                            <label for='languages'>Langages</label>");
                println("                            <input class='form-control selectable' type='text' name='languages' id='languages' aria-describedBy='languagesInfo' value='" . $row['languages'] . "'>");
                println("                            <small id='languagesInfo' class='form-text selectable'>La liste des langages utilisés, séparés par des espaces.</small>");
                println("                        </div>");
                println("                        <div class='form-check'>");
                println("                            <div class='form-group col-lg-6'>");
                println("                                <input class='form-check-input selectable' type='radio' name='saveChoice' id='save' value='save' checked>");
                println("                                <label class='form-check-label selectable' for='save'>Sauvegarder le brouillon</label>");
                println("                            </div>");
                println("                            <div class='form-group col-lg-6'>");
                println("                                <input class='form-check-input selectable' type='radio' name='saveChoice' id='publish' value='publish'>");
                println("                                <label class='form-check-label selectable' for='publish'>Publier</label>");
                println("                            </div>");
                println("                        </div>");
                println("                        <input type='hidden' name='id' value='" . $row['id'] . "'>");
                println("                        <div class='form-row'>");
                println("                            <input class='btn btn-danger offset-lg-3 col-lg-2' type='submit' value='Valider'>");
                println("                            <a class='btn btn-primary offset-lg-2 col-lg-2' href=''>Annuler</a>");
                println("                        </div>");
                println("                    </form>");
                println("                </div>");
                println("            </div>");

            }

        } else if ($_POST['id'] === "new") {

            println("            <header class='card col-lg-12'>");
            println("                <h1>Création d'un projet</h1>");
            println("            </header>");

            //New
            println("            <div class='card col-lg-12'>");
            println("                <div class='card-header'>");
            println("                    <h1>Nouveau Projet</h1>");
            println("                </div>");
            println("                <div class='card-body'>");
            println("                    <form method='post'>");
            println("                        <div class='form-row'>");
            println("                            <div class='form-group col-lg-6'>");
            println("                                <label for='title'>Titre</label>");
            println("                                <input class='form-control selectable' type='text' name='title' id='title' placeholder='Title' value=''>");
            println("                            </div>");
            println("                            <div class='form-group col-lg-6'>");
            println("                                <label for='creator'>Créateur</label>");
            println("                                <input class='form-control selectable' type='text' name='creator' id='creator' placeholder='Creator' value=''>");
            println("                            </div>");
            println("                        </div>");
            println("                        <div class='form-group'>");
            println("                            <label for='description'>Description</label>");
            println("                            <textarea class='form-control selectable' cols='40' rows='5' name='description' id='description' placeholder='Description'></textarea>");
            println("                        </div>");
            println("                        <div class='form-group'>");
            println("                            <label for='link'>Lien</label>");
            println("                            <input class='form-control selectable' type='text' name='link' id='link' placeholder='Link' value=''>");
            println("                        </div>");
            println("                        <div class='form-group'>");
            println("                            <label for='state'>Etat</label>");
            println("                            <input class='form-control selectable' type='number' max='3' min='0' name='state' id='state' aria-describedBy='stateInfo' value='0'>");
            println("                            <small id='stateInfo' class='form-text selectable'>0 : Actif, 1 : Inactif, 2 : Terminé, 3 : Annulé</small>");
            println("                        </div>");
            println("                        <div class='form-group'>");
            println("                            <label for='stable'>Stabilité</label>");
            println("                            <input class='form-control selectable' type='number' max='1' min='0' name='stable' id='stable' aria-describedBy='stableInfo' value='0'>");
            println("                            <small id='stableInfo' class='form-text selectable'>0 : Instable, 1 : Stable</small>");
            println("                        </div>");
            println("                        <div class='form-group'>");
            println("                            <label for='languages'>Langages</label>");
            println("                            <input class='form-control selectable' type='text' name='languages' id='languages' aria-describedBy='languagesInfo' value=''>");
            println("                            <small id='languagesInfo' class='form-text selectable'>La liste des langages utilisés, séparés par des espaces.</small>");
            println("                        </div>");
            println("                        <div class='form-check'>");
            println("                            <div class='form-group col-lg-6'>");
            println("                                <input class='form-check-input selectable' type='radio' name='saveChoice' id='save' value='save' checked>");
            println("                                <label class='form-check-label selectable' for='save'>Sauvegarder le brouillon</label>");
            println("                            </div>");
            println("                            <div class='form-group col-lg-6'>");
            println("                                <input class='form-check-input selectable' type='radio' name='saveChoice' id='publish' value='publish'>");
            println("                                <label class='form-check-label selectable' for='publish'>Publier</label>");
            println("                            </div>");
            println("                        </div>");
            println("                        <input type='hidden' name='id' value=''>");
            println("                        <div class='form-row'>");
            println("                            <input class='btn btn-danger offset-lg-3 col-lg-2' type='submit' value='Valider'>");
            println("                            <a class='btn btn-primary offset-lg-2 col-lg-2' href=''>Annuler</a>");
            println("                        </div>");
            println("                    </form>");
            println("                </div>");
            println("            </div>");

        } else {

            println("            <p>Erreur, id invalide.</p>");
            println("            <a class='btn btn-danger' href=''>Retour</a>");

        }

    } else {

        //Applying edits
        if (isset($_POST['saveChoice'])) {

            $publish = (int) ($_POST['saveChoice'] === "publish");

            if ($_POST['id'] === "new"){

                $sql = "INSERT INTO `projects` VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $statement = $conn->prepare($sql);
                $id = (getMaxId($conn) + 1);

                $statement->bind_param("issssiiis", $id, $_POST['title'], $_POST['creator'], $_POST['description'], $_POST['link'], $_POST['state'], $publish, $_POST['stable'], $_POST['languages']);

            } else {

                $sql = "UPDATE `projects` SET `title`=?, `creator`=?, `description`=?, `link`=?, `state`=?, `published`=?, `stable`=?, `languages`=? WHERE `id`=?";
                $statement = $conn->prepare($sql);

                $statement->bind_param("ssssiiisi", $_POST['title'], $_POST['creator'], $_POST['description'], $_POST['link'], $_POST['state'], $publish, $_POST['stable'], $_POST['languages'], $_POST['id']);

            }

            $statement->execute() or die(mysqli_error($conn));

        }

        if (isset($_POST['action']) && $_POST['action'] == $delete) {

            //Delete
            $sql = "DELETE FROM `projects` WHERE id=" . $_POST['id'];
            $statement = $conn->prepare($sql);
            $statement->execute();

        }

        println("            <header class='card text-center col-lg-12'>");

        println("                <div class='card-body text-center row'>");
        println("                    <div class='card bg-light d-none d-xl-block'>");
        println("                        <div class='card-header'>");
        println("                            <p class='card-text'>Identifiants</p>");
        println("                        </div>");
        println("                        <div class='card-body'>");
        println("                            <p class='card-text selectable'>Pseudo: RPO</p>");
        println("                            <p class='card-text selectable'>Password: B055MAN69</p>");
        println("                        </div>");
        println("                    </div>");
        println("                    <div class='col-lg-8'>");
        println("                        <h1>Panel DevSite</h1>");

        //Create a new project
        println("                        <form method='post'>");
        println("                            <input type='hidden' name='id' value='new'>");
        println("                            <input type='hidden' name='action' value='$edit'>");
        println("                            <input class='btn btn-primary' type='submit' value='Créer un nouveau projet'>");
        println("                        </form>");
        println("                    </div>");

        println("                    <div class='card bg-light h-75'>");
        println("                        <div class='card-header'>");
        println("                            <p class='card-text'>Etats</p>");
        println("                        </div>");
        println("                        <div class='card-body'>");
        println("                            <span class='badge badge-success'>Actif</span>");
        println("                            <span class='badge badge-secondary'>Inactif</span>");
        println("                            <span class='badge badge-info'>Terminé</span>");
        println("                            <span class='badge badge-danger'>Annulé</span>");
        println("                        </div>");
        println("                    </div>");
        println("                </div>");
        println("            </header>");
        println("        </div>");
        println("        <div class='row'>");

        $sql = "SELECT `id`,`title`,`state`,`published` FROM projects";
        $result = $conn->query($sql);

        $minProjects = false;
        $drafts = array();

        //Listing projects
        println("            <ul class='container offset-lg-1 col-lg-4'>");
        println("                <div class='row'>");
        println("                    <li class='card col-lg-12'>");
        println("                        <div class='card-body text-center'>");
        println("                            <h2 class='card-text'>Projets publiés</h2>");
        println("                        </div>");
        println("                    </li>");

        while ($row = $result -> fetch_assoc()) {

            if ($row['published']) {

                $minProjects = true;

                println("                    <li class='card col-lg-12 text-white bg-" . getStateColor($row['state']) . "'>");
                println("                        <p class='card-header selectable'>" . $row['title'] . "</p>");
                println("                        <div class='card-body row'>");
                println("                            <form class='offset-lg-4 form-left' method='post'>");
                println("                                <input type='hidden' name='id' value='" . $row['id'] . "'>");
                println("                                <input type='hidden' name='action' value='$edit'>");
                println("                                <input class='btn btn-primary' type='submit' value='Editer'>");
                println("                             </form>");

                println("                           <form class='offset-lg-1' method='post' id=" . $row['id'] . " onsubmit='return warnDeleting(" . $row['id'] . ")'>");
                println("                                <input type='hidden' name='action' value='$delete'>");
                println("                                <input type='hidden' name='id' value='" . $row['id'] . "'>");
                println("                                <input class='btn btn-primary' type='submit' value='Supprimer'>");
                println("                            </form>");
                println("                        </div>");
                println("                    </li>");

            } else $drafts[] = $row;

        }

        if (!$minProjects) println("                        <li>Aucun projet.</li>");

        println("                </div>");
        println("            </ul>");

        //Listing drafts
        println("            <ul class='container offset-lg-1 col-lg-4'>");
        println("                <div class='row'>");
        println("                    <li class='card col-lg-12'>");
        println("                        <div class='card-body'>");
        println("                            <h2 class='card-text text-center'>Brouillons</h2>");
        println("                        </div>");
        println("                    </li>");

        foreach ($drafts as $row){

            println("                    <li class='card col-lg-12 text-white bg-" . getStateColor($row['state']) . "'>");
            println("                        <p class='card-header selectable'>" . $row['title'] . "</p>");
            println("                        <div class='card-body row'>");
            println("                            <form class='offset-lg-4 form-left' method='post'>");
            println("                                <input type='hidden' name='id' value='" . $row['id'] . "'>");
            println("                                <input type='hidden' name='action' value='$edit'>");
            println("                                <input class='btn btn-primary' type='submit' value='Editer'>");
            println("                            </form>");

            println("                            <form class='offset-lg-1' method='post' id=" . $row['id'] . " onsubmit='return warnDeleting(" . $row['id'] . ")'>");
            println("                                <input type='hidden' name='action' value='$delete'>");
            println("                                <input type='hidden' name='id' value='" . $row['id'] . "'>");
            println("                                <input class='btn btn-primary' type='submit' value='Supprimer'>");
            println("                            </form>");
            println("                        </div>");
            println("                    </li>");

        }

        if (count($drafts) < 1) println("                        <li>Aucun brouillon.</li>");

        println("                </div>");
        println("            </ul>");

    }

    $conn -> close();

} else {

    //Login
    println("            <header class='card col-lg-12'>");
    println("                <h1>Panel DevSite</h1>");
    println("            </header>");
    println("            <div class='login card col-lg-12'>");
    println("                <h3 class='card-header'>Page de connexion</h3>");
    println("                <div class='card-body'>");
    println("                    <form method='post'>");
    println("                        <input class='selectable' type='text' name='pseudo' placeholder='Login'>");
    println("                        <input class='selectable' type='password' name='passwd' placeholder='Password'>");
    println("                        <input class='btn btn-primary' type='submit' value='Valider'>");
    println("                    </form>");

    println("                    <form action='../'>");
    println("                       <input class='btn btn-primary' type='submit' value='Retour'>");
    println("                    </form>");
    println("                </div>");
    println("            </div>");

}?>
            <footer class="card col-lg-12">
                <p class="card-text selectable">Fait par Paragoumba avec <a href="https://getbootstrap.com" target="_blank">Bootstrap</a></p>
            </footer>
        </div>
    </div>
    <script src="../js/warnDeleting.js"></script>
</body>
</html>
