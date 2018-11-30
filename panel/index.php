<?php /* Translation */
$lang = isset($_GET['lang']) ? $_GET['lang'] === "fr" ? "fr" : "en" : "en";
$translations = array(
        "en" => array("language" => "Language", "panel" => "DevSite Panel", "new-project" => "New Project", "connection-error" => "Connection error.", "db-connection-error-try-again" => "Error in connection to database. Please contact an administrator or try again later.", "title" => "Title", "creator" => "Creator", "link" => "Link", "states" => "States", "active" => "Active", "inactive" => "Inactive", "finished" => "Finished", "canceled" => "Canceled", "stability" => "Stability", "unstable" => "Unstable", "snippet" => "Snippet", "is-snippet" => "Is this a snippet ?", "languages-desc" => "List of used languages, separated by spaces.", "description" => "Description", "stable" => "Stable", "save-draft" => "Save the draft", "publish" => "Publish", "login-page" => "Login page", "login" => "Login", "password" => "Password", "validate" => "Validate", "return" => "Return", "cancel" => "Cancel", "made-by" => "Made by ", "thanks-to" => " thanks to ", "published-projects" => "Published projects", "drafts" => "Drafts", "edit" => "Edit", "delete" => "Delete", "no-project" => "Actually, there's no project", "no-draft" => "No draft"),
        "fr" => array("language" => "Langage", "panel" => "Panel DevSite", "new-project" => "Nouveau Projet", "connection-error" => "Connexion échouée.", "db-connection-error-try-again" => "Erreur de connexion à la base de données. Veuillez contacter un administrateur ou réessayer ultérieurement.", "title" => "Titre", "creator" => "Créateur", "link" => "Lien", "states" => "États", "active" => "Actif", "inactive" => "Inactif", "finished" => "Terminé", "canceled" => "Annulé", "stability" => "Stabilité", "unstable" => "Instable", "snippet" => "Fragment", "is-snippet" => "Est-ce un fragment ?", "languages-desc" => "La liste des langages utilisés, séparés par des espaces.", "description" => "Description", "stable" => "Stable", "save-draft" => "Sauvegarder le brouillon", "publish" => "Publier", "login-page" => "Page de connexion", "login" => "Identifiant", "password" => "Mot de passe", "validate" => "Valider", "cancel" => "Annuler", "return" => "Retour", "made-by" => "Fait par ", "thanks-to" => " grâce à ", "published-projects" => "Projets publiés", "drafts" => "Brouillons", "edit" => "Éditer", "delete" => "Supprimer", "no-project" => "Actuellement, il n'y a aucun projet", "no-draft" => "Aucun brouillon")
);

/* States */
$edit = "0";
$delete = "1";

function getCredentials(){

    $fileContent = file_get_contents("../creds.para");
    $fileContent = explode("\n", $fileContent);

    $creds['server'] = substr($fileContent[0], 0, strlen($fileContent[0]));
    $creds['username'] = substr($fileContent[1], 0, strlen($fileContent[1]));
    $creds['password'] = substr($fileContent[2], 0, strlen($fileContent[2]));
    $creds['dbName'] = substr($fileContent[3], 0, strlen($fileContent[3]));
    $creds['panelPseudo'] = substr($fileContent[4], 0, strlen($fileContent[4]));
    $creds['panelPassword'] = substr($fileContent[5], 0, strlen($fileContent[5]));

    return $creds;

}

function getMaxId($conn){

    $sql = "SELECT max(id) FROM `projects`";
    $stmt = $conn -> query($sql);

    while ($maxId = $stmt -> fetch()) return $maxId['max(id)'];

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

$creds = getCredentials();

if (isset($_POST['pseudo']) && isset($_POST['passwd']) && $_POST['pseudo'] === $creds['panelPseudo'] && $_POST['passwd'] === $creds['panelPassword']){

    //Logged
    setcookie('pseudo', $_POST['pseudo'], time() + 3600);
    setcookie('passwd', md5($_POST['passwd']), time() + 3600);
    $_COOKIE['pseudo'] = $_POST['pseudo'];
    $_COOKIE['passwd'] = md5($_POST['passwd']);

}?>
<!DOCTYPE html>

<html lang="<?php echo $lang?>">
<head>
    <title>Panel - DevSite</title>
    <link rel="icon" href="https://www.paragoumba.fr/res/icon.gif">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../theme.css">
    <link rel="stylesheet" href="../css/style.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
</head>
<body class="text-center">
    <div class="container-fluid">
        <div class="row">
<?php if (isset($_COOKIE['pseudo']) && isset($_COOKIE['passwd']) && $_COOKIE['pseudo'] === $creds['panelPseudo'] && $_COOKIE['passwd'] === md5($creds['panelPassword'])){

    // Create connection
    $conn = new PDO("mysql:host=" . $creds["server"] . ";dbname=" . $creds["dbName"], $creds['username'], $creds['password']);
    $conn -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    try {

        if (isset($_POST['action']) && $_POST['action'] === $edit){

            if (is_numeric($_POST['id'])){

                //Edit
                $sql = "SELECT * FROM `projects` WHERE id=:id";
                $stmt = $conn -> prepare($sql);

                $stmt -> bindParam(":id", $_POST['id']);
                $stmt -> execute();
                $stmt -> setFetchMode(PDO::FETCH_ASSOC);

                println("            <header class='col-lg-12 mb-5 py-5'>");
                println("                <h1>" . $translations[$lang]['panel'] . "</h1>");
                println("            </header>");

                while ($row = $stmt -> fetch()){

                    println("            <div class='card bg-dark mb-4 px-0 offset-lg-1 col-lg-10'>");
                    println("                <div class='card-header'>");
                    println("                    <h1>" . $row['title'] . "</h1>");
                    println("                </div>");
                    println("                <div class='card-body text-left'>");
                    println("                    <form method='post'>");
                    println("                        <div class='form-row'>");
                    println("                            <div class='form-group col-lg-6'>");
                    println("                                <label for='title'>" . $translations[$lang]["title"] . "</label>");
                    println("                                <input class='form-control' type='text' name='title' id='title' placeholder='" . $translations[$lang]["title"] . "' value='" . $row['title'] . "'>");
                    println("                            </div>");
                    println("                            <div class='form-group col-lg-6'>");
                    println("                                <label for='creator'>" . $translations[$lang]["creator"] . "</label>");
                    println("                                <input class='form-control' type='text' name='creator' id='creator' placeholder='" . $translations[$lang]["creator"] . "' value='" . $row['creator'] . "'>");
                    println("                            </div>");
                    println("                        </div>");
                    println("                        <div class='form-group'>");
                    println("                            <label for='description'>" . $translations[$lang]["description"] . "</label>");
                    println("                            <textarea class='form-control' cols='40' rows='5' name='description' id='description' placeholder='" . $translations[$lang]['description'] . "'>" . $row['description'] . "</textarea>");
                    println("                        </div>");
                    println("                        <div class='form-group'>");
                    println("                            <label for='link'>" . $translations[$lang]["link"] . "</label>");
                    println("                            <input class='form-control' type='text' name='link' id='link' placeholder='" . $translations[$lang]["link"] . "' value='" . $row['link'] . "'>");
                    println("                        </div>");
                    println("                        <div>");
                    println("                            <p>" . $translations[$lang]["states"] . "</p>");
                    println("                            <div class='form-check'>");
                    println("                                <div class='form-group col-lg-3'>");
                    println("                                    <input class='form-check-input' type='radio' name='state' id='active' value='0'" . (($row['state'] === "0") ? " checked" : "") . ">");
                    println("                                    <label class='form-check-label' for='active'>" . $translations[$lang]["active"] . "</label>");
                    println("                                </div>");
                    println("                                <div class='form-group col-lg-3'>");
                    println("                                    <input class='form-check-input' type='radio' name='state' id='inactive' value='1'" . (($row['state'] === "1") ? " checked" : "") . ">");
                    println("                                    <label class='form-check-label' for='inactive'>" . $translations[$lang]["inactive"] . "</label>");
                    println("                                </div>");
                    println("                                <div class='form-group col-lg-3'>");
                    println("                                    <input class='form-check-input' type='radio' name='state' id='finished' value='2'" . (($row['state'] === "2") ? " checked" : "") . ">");
                    println("                                    <label class='form-check-label' for='finished'>" . $translations[$lang]["finished"] . "</label>");
                    println("                                </div>");
                    println("                                <div class='form-group col-lg-3'>");
                    println("                                    <input class='form-check-input' type='radio' name='state' id='canceled' value='3'" . (($row['state'] === "3") ? " checked" : "") . ">");
                    println("                                    <label class='form-check-label' for='canceled'>" . $translations[$lang]["canceled"] . "</label>");
                    println("                                </div>");
                    println("                            </div>");
                    println("                        </div>");
                    println("                        <div>");
                    println("                            <p class='pl-0'>" . $translations[$lang]["stability"] . "</p>");
                    println("                            <div class='form-check'>");
                    println("                                <div class='form-group col-lg-6'>");
                    println("                                    <input class='form-check-input' type='radio' name='stable' id='unstable' value='0'" . (($row['stable'] === "0") ? " checked" : "") . ">");
                    println("                                    <label class='form-check-label' for='unstable'>" . $translations[$lang]["unstable"] . "</label>");
                    println("                                </div>");
                    println("                                <div class='form-group col-lg-6'>");
                    println("                                    <input class='form-check-input' type='radio' name='stable' id='stable' value='1'" . (($row['stable'] === "1") ? " checked" : "") . ">");
                    println("                                    <label class='form-check-label' for='stable'>" . $translations[$lang]["stable"] . "</label>");
                    println("                                </div>");
                    println("                            </div>");
                    println("                        </div>");
                    println("                        <div>");
                    println("                            <p class='pl-0'>" . $translations[$lang]["snippet"] . "</p>");
                    println("                            <div class='form-check'>");
                    println("                                <div class='form-group col-lg-6'>");
                    println("                                    <input class='form-check-input' type='checkbox' name='snippet' id='notSnippet'>");
                    println("                                    <label class='form-check-label' for='notSnippet'>" . $translations[$lang]["is-snippet"] . "</label>");
                    println("                                </div>");
                    println("                            </div>");
                    println("                        </div>");
                    println("                        <div class='form-group'>");
                    println("                            <label for='languages'>" . $translations[$lang]["languages"] . "</label>");
                    println("                            <input class='form-control' type='text' name='languages' id='languages' placeholder='ex: php sql' aria-describedBy='languagesInfo' value='" . $row['languages'] . "'>");
                    println("                            <small id='languagesInfo' class='form-text'>" . $translations[$lang]["languages-desc"] . "</small>");
                    println("                        </div>");
                    println("                        <div class='form-check'>");
                    println("                            <div class='form-group col-lg-6'>");
                    println("                                <input class='form-check-input' type='radio' name='saveChoice' id='save' value='save' " . (($row['published'] === "0") ? " checked" : "") . ">");
                    println("                                <label class='form-check-label' for='save'>" . $translations[$lang]['save-draft'] . "</label>");
                    println("                            </div>");
                    println("                            <div class='form-group col-lg-6'>");
                    println("                                <input class='form-check-input' type='radio' name='saveChoice' id='publish' value='publish' " . (($row['published'] === "1") ? " checked" : "") . ">");
                    println("                                <label class='form-check-label' for='publish'>" . $translations[$lang]['publish'] . "</label>");
                    println("                            </div>");
                    println("                        </div>");
                    println("                        <input type='hidden' name='id' value='" . $row['id'] . "'>");
                    println("                        <div class='form-row'>");
                    println("                            <input class='btn btn-danger offset-lg-3 col-lg-2' type='submit' value='" . $translations[$lang]['validate'] . "'>");
                    println("                            <a class='btn btn-primary offset-lg-2 col-lg-2' href=''>" . $translations[$lang]['canceled'] . "</a>");
                    println("                        </div>");
                    println("                    </form>");
                    println("                </div>");
                    println("            </div>");

                }

                $stmt = null;

            } else if ($_POST['id'] === "new") {

                println("            <header class='col-lg-12 mb-5 py-5'>");
                println("                <h1>" . $translations[$lang]['panel'] . "</h1>");
                println("            </header>");

                //New
                println("            <div class='card bg-dark mb-4 px-0 offset-lg-1 col-lg-10'>");
                println("                <div class='card-header'>");
                println("                    <h1>" . $translations[$lang]['new-project'] . "</h1>");
                println("                </div>");
                println("                <div class='card-body text-left'>");
                println("                    <form method='post'>");
                println("                        <div class='form-row pl-0'>");
                println("                            <div class='col-lg-6 d-inline-block'>");
                println("                                <div class='form-group'>");
                println("                                    <label for='title'>" . $translations[$lang]['title'] . "</label>");
                println("                                    <input class='form-control' type='text' name='title' id='title' placeholder='" . $translations[$lang]['title'] . "'>");
                println("                                </div>");
                println("                                <div class='form-group'>");
                println("                                    <label for='creator'>" . $translations[$lang]['creator'] . "</label>");
                println("                                    <input class='form-control' type='text' name='creator' id='creator' placeholder='" . $translations[$lang]['creator'] . "'>");
                println("                                </div>");
                println("                            </div>");
                println("                            <div class='form-group col-lg-6'>");
                println("                                <label for='description'>" . $translations[$lang]['description'] . "</label>");
                println("                                <textarea class='form-control' cols='40' name='description' id='description' placeholder='" . $translations[$lang]['description'] . "'></textarea>");
                println("                            </div>");
                println("                        </div>");
                println("                        <div class='form-group'>");
                println("                            <label for='link'>" . $translations[$lang]["link"] . "</label>");
                println("                            <input class='form-control' type='text' name='link' id='link' placeholder='" . $translations[$lang]["link"] . "'>");
                println("                        </div>");
                println("                        <div>");
                println("                            <p class='align-middle d-inline-block mr-2'>" . $translations[$lang]["states"] . "</p>");
                println("                            <div class='form-check form-check-inline'>");
                println("                                <input class='form-check-input' type='radio' name='state' id='active' value='0' checked>");
                println("                                <label class='form-check-label' for='active'>" . $translations[$lang]["active"] . "</label>");
                println("                            </div>");
                println("                            <div class='form-check form-check-inline'>");
                println("                                <input class='form-check-input' type='radio' name='state' id='inactive' value='1'>");
                println("                                <label class='form-check-label' for='inactive'>" . $translations[$lang]["inactive"] . "</label>");
                println("                            </div>");
                println("                            <div class='form-check form-check-inline'>");
                println("                                <input class='form-check-input' type='radio' name='state' id='finished' value='2'>");
                println("                                <label class='form-check-label' for='finished'>" . $translations[$lang]["finished"] . "</label>");
                println("                            </div>");
                println("                            <div class='form-check form-check-inline'>");
                println("                                <input class='form-check-input' type='radio' name='state' id='canceled' value='3'>");
                println("                                <label class='form-check-label' for='canceled'>" . $translations[$lang]["canceled"] . "</label>");
                println("                            </div>");
                println("                        </div>");
                println("                        <div>");
                println("                            <p class='align-middle d-inline-block mr-2'>" . $translations[$lang]["stability"] . "</p>");
                println("                            <div class='form-check form-check-inline'>");
                println("                                <input class='form-check-input' type='radio' name='stable' id='unstable' value='0' checked>");
                println("                                <label class='form-check-label' for='unstable'>" . $translations[$lang]['unstable'] . "</label>");
                println("                            </div>");
                println("                            <div class='form-check form-check-inline'>");
                println("                                <input class='form-check-input' type='radio' name='stable' id='stable' value='1'>");
                println("                                <label class='form-check-label' for='stable'>" . $translations[$lang]['stable'] . "</label>");
                println("                            </div>");
                println("                        </div>");
                println("                        <div>");
                println("                            <p class='align-middle d-inline-block mr-2'>" . $translations[$lang]['snippet'] . "</p>");
                println("                            <div class='form-check form-check-inline'>");
                println("                                <input class='form-check-input' type='checkbox' name='snippet' id='isSnippet'>");
                println("                                <label class='form-check-label' for='isSnippet'>" . $translations[$lang]['is-snippet'] . "</label>");
                println("                            </div>");
                println("                        </div>");
                println("                        <div class='form-group'>");
                println("                            <label for='languages'>" . $translations[$lang]['language'] . "</label>");
                println("                            <input class='form-control' type='text' name='languages' id='languages' placeholder='ex: php sql' aria-describedBy='languagesInfo'>");
                println("                            <small id='languagesInfo' class='form-text'>" . $translations[$lang]['languages-desc'] . "</small>");
                println("                        </div>");
                println("                        <div class='form-check form-check-inline'>");
                println("                            <input class='form-check-input' type='radio' name='saveChoice' id='save' value='save' checked>");
                println("                            <label class='form-check-label' for='save'>" . $translations[$lang]["save-draft"] . "</label>");
                println("                        </div>");
                println("                        <div class='form-check form-check-inline'>");
                println("                            <input class='form-check-input' type='radio' name='saveChoice' id='publish' value='publish'>");
                println("                            <label class='form-check-label' for='publish'>" . $translations[$lang]["publish"] . "</label>");
                println("                        </div>");
                println("                        <input type='hidden' name='id' value='new'>");
                println("                        <div class='form-row'>");
                println("                            <input class='btn btn-danger offset-lg-3 col-lg-2' type='submit' value='" . $translations[$lang]['validate'] . "'>");
                println("                            <a class='btn btn-primary offset-lg-2 col-lg-2' href=''>" . $translations[$lang]['cancel'] . "</a>");
                println("                        </div>");
                println("                    </form>");
                println("                </div>");
                println("            </div>");

            } else {

                println("            <p>" . $translations[$lang]['error-invalid-id'] . "</p>");
                println("            <a class='btn btn-danger' href=''>" . $translations[$lang]['return'] . "</a>");

            }

        } else {

            //Applying edits
            if (isset($_POST['saveChoice'])) {

                $published = $_POST['saveChoice'] === "publish";

                if ($_POST['id'] === "new") {

                    $sql = "INSERT INTO `projects` VALUES (:id, :title, :creator, :description, :link, :state, :published, :stable, :snippet, :languages)";
                    $stmt = $conn -> prepare($sql);
                    $id = getMaxId($conn) + 1;

                    if ($_POST['snippet'] === null) $_POST['snippet'] = 0;

                    $stmt -> bindParam(":id", $id);
                    $stmt -> bindParam(":title", $_POST['title']);
                    $stmt -> bindParam(":creator", $_POST['creator']);
                    $stmt -> bindParam(":description", $_POST['description']);
                    $stmt -> bindParam(":link", $_POST['link']);
                    $stmt -> bindParam(":state", $_POST['state']);
                    $stmt -> bindParam(":published", $published);
                    $stmt -> bindParam(":stable", $_POST['stable']);
                    $stmt -> bindParam(":snippet", $_POST['snippet']);
                    $stmt -> bindParam(":languages", $_POST['languages']);
                    
                } else {

                    $sql = "UPDATE `projects` SET `title`=:title, `creator`=:creator, `description`=:description, `link`=:link, `state`=:state, `published`=:published, `stable`=:stable, `snippet`=:snippet, `languages`=:languages WHERE `id`=:id";
                    $stmt = $conn -> prepare($sql);

                    $stmt -> bindParam(":title", $_POST['title']);
                    $stmt -> bindParam(":creator", $_POST['creator']);
                    $stmt -> bindParam(":description", $_POST['description']);
                    $stmt -> bindParam(":link", $_POST['link']);
                    $stmt -> bindParam(":state", $_POST['state']);
                    $stmt -> bindParam(":published", $published);
                    $stmt -> bindParam(":stable", $_POST['stable']);
                    $stmt -> bindParam(":snippet", $_POST['snippet']);
                    $stmt -> bindParam(":languages", $_POST['languages']);
                    $stmt -> bindParam(":id", $_POST['id']);

                }

                $stmt -> execute();
                $stmt = null;

            }

            if (isset($_POST['action']) && $_POST['action'] == $delete) {

                //Delete
                $sql = "DELETE FROM `projects` WHERE id=:id";
                $stmt = $conn -> prepare($sql);

                $stmt -> bindParam(":id", $_POST['id']);
                $stmt -> execute();

                $stmt = null;

            }

            println("            <header class='col-lg-12 mb-5 text-center'>");
            println("                <div class='row p-3'>");
            println("                    <div class='card bg-dark col-lg-2 px-0'>");
            println("                        <div class='card-header'>");
            println("                            <p class='card-text'>" . $translations[$lang]['login'] . "</p>");
            println("                        </div>");
            println("                        <div class='card-body'>");
            println("                            <p class='card-text'>Login: RPO</p>");
            println("                            <p class='card-text'>Password: B055MAN69</p>");
            println("                        </div>");
            println("                    </div>");
            println("                    <div class='col-lg-8 mt-4'>");
            println("                        <h1>Panel DevSite</h1>");

            //Create a new project
            println("                        <form method='post'>");
            println("                            <input type='hidden' name='id' value='new'>");
            println("                            <input type='hidden' name='action' value='$edit'>");
            println("                            <input class='btn btn-dark' type='submit' value='" . $translations[$lang]['new-project'] . "'>");
            println("                        </form>");
            println("                    </div>");

            println("                    <div class='card bg-dark col-lg-2 px-0'>");
            println("                        <div class='card-header'>");
            println("                            <p class='card-text'>" . $translations[$lang]['states'] . "</p>");
            println("                        </div>");
            println("                        <div class='card-body'>");
            println("                            <span class='badge badge-success'>" . $translations[$lang]['active'] . "</span>");
            println("                            <span class='badge badge-secondary'>" . $translations[$lang]['inactive'] . "</span>");
            println("                            <span class='badge badge-info'>" . $translations[$lang]['finished'] . "</span>");
            println("                            <span class='badge badge-danger'>" . $translations[$lang]['canceled'] . "</span>");
            println("                        </div>");
            println("                    </div>");
            println("                </div>");
            println("            </header>");

            $sql = "SELECT `id`,`title`,`state`,`published` FROM projects";
            $stmt = $conn -> query($sql);
            $minProjects = false;
            $drafts = array();

            //Listing projects
            println("            <ul class='container'>");
            println("                <div class='row'>");
            println("                    <li class='col-lg-12'>");
            println("                        <h2>" . $translations[$lang]['published-projects'] . "</h2>");
            println("                    </li>");

            while ($row = $stmt -> fetch()){

                if ($row['published']) {

                    $minProjects = true;

                    println("                    <li class='col-lg-4 px-2'>");
                    println("                        <div class='card mt-3 px-0 text-white bg-" . getStateColor($row['state']) . "'>");
                    println("                            <p class='card-header'>" . $row['title'] . "</p>");
                    println("                            <div class='card-body btn-group'>");
                    println("                                <form class='w-33' method='post'>");
                    println("                                    <input type='hidden' name='id' value='" . $row['id'] . "'>");
                    println("                                    <input type='hidden' name='action' value='$edit'>");
                    println("                                    <input class='btn btn-dark w-100' type='submit' value='" . $translations[$lang]['edit'] . "'>");
                    println("                                </form>");
                    println("                                <form class='w-33' method='post' id='" . $row['id'] . "' onsubmit='return warnDeleting(" . $row['id'] . ")'>");
                    println("                                    <input type='hidden' name='action' value='$delete'>");
                    println("                                    <input type='hidden' name='id' value='" . $row['id'] . "'>");
                    println("                                    <input class='btn btn-dark w-100 mlc-7' type='submit' value='" . $translations[$lang]['delete'] . "'>");
                    println("                                </form>");
                    println("                            </div>");
                    println("                        </div>");
                    println("                    </li>");

                } else $drafts[] = $row;

            }

            $stmt = null;

            if (!$minProjects) println("                        <li class='card card-body mt-3 px-0 text-white bg-dark'>" . $translations[$lang]['no-project'] . ".</li>");

            println("                </div>");
            println("            </ul>");
            println("            <hr class='offset-lg-2 col-lg-8 my-5'>");

            //Listing drafts
            println("            <ul class='container mb-5'>");
            println("                <div class='row'>");
            println("                    <li class='col-lg-12'>");
            println("                        <h2>" . $translations[$lang]['drafts'] . "</h2>");
            println("                    </li>");

            foreach ($drafts as $row) {

                println("                    <li class='col-lg-4 px-2'>");
                println("                        <div class='card mt-3 px-0 text-white bg-" . getStateColor($row['state']) . "'>");
                println("                            <p class='card-header'>" . $row['title'] . "</p>");
                println("                            <div class='card-body btn-group'>");
                println("                                <form class='w-33' method='post'>");
                println("                                    <input type='hidden' name='id' value='" . $row['id'] . "'>");
                println("                                    <input type='hidden' name='action' value='$edit'>");
                println("                                    <input class='btn btn-dark w-100' type='submit' value='" . $translations[$lang]['edit'] . "'>");
                println("                                </form>");

                println("                                <form class='w-33' method='post' id=" . $row['id'] . " onsubmit='return warnDeleting(" . $row['id'] . ")'>");
                println("                                    <input type='hidden' name='action' value='$delete'>");
                println("                                    <input type='hidden' name='id' value='" . $row['id'] . "'>");
                println("                                    <input class='btn btn-dark w-100 mlc-7' type='submit' value='" . $translations[$lang]['delete'] . "'>");
                println("                                </form>");
                println("                            </div>");
                println("                        </div>");
                println("                    </li>");

            }

            if (count($drafts) < 1) println("                        <li class='card card-body offset-lg-3 col-lg-6 mt-3 text-white bg-dark'>" . $translations[$lang]['no-draft'] . ".</li>");

            println("                </div>");
            println("            </ul>");

        }

        $conn = null;

    } catch (PDOException $e){

        echo $e -> getMessage();
        println("        <header class='col-lg-12 py-5'>");
        println("            <h1>" . $translations[$lang]['panel'] . "</h1>");
        println("        </header>");
        println("        <h1 class='col-lg-12 mtc-15 mb-4'>" . $translations[$lang]["connection-error"] . "</h1>");
        println("        <h3 class='col-lg-12 mbc-15'>" . $translations[$lang]["db-connection-error-try-again"] . "</h3>");

    }

} else {

    //Login
    println("            <header class='col-lg-12 py-5 mb-5'>");
    println("                <div class='col-lg-1 dropdown d-inline-block align-text-bottom'>");
    println("                    <button class='btn bg-transparent dropdown-toggle text-white' type='button' id='dropdownMenuButton' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>" . $translations[$lang]['language'] . "</button>");
    println("                    <div class='dropdown-menu' aria-labelledby='dropdownMenuButton'>");
    println("                        <a class='dropdown-item' href='./?lang=fr'>Français</a>");
    println("                        <a class='dropdown-item' href='./'>English</a>");
    println("                    </div>");
    println("                </div>");
    println("                <h1 class='col-lg-10 d-inline-block'>" . $translations[$lang]['panel'] . "</h1>");
    println("            </header>");
    println("            <div class='offset-lg-4 col-lg-4 mt-lg-0 mt-md-5 mbc-8 mb-md-3'>");
    println("                <div class='card bg-dark p-5 mb-5'>");
    println("                    <h2 class='mb-5'>" . $translations[$lang]['login-page'] . "</h2>");
    println("                    <form method='post' class='text-left'>");
    println("                        <label for='inputLogin'>" . $translations[$lang]['login'] . "</label>");
    println("                        <input id='inputLogin' class='form-control mb-3' type='text' name='pseudo' placeholder='" . $translations[$lang]['login'] . "'>");
    println("                        <label for='inputPassword'>" . $translations[$lang]['password']. "</label>");
    println("                        <input id='inputPassword' class='form-control mb-4' type='password' name='passwd' placeholder='" . $translations[$lang]['password'] . "'>");
    println("                        <input class='btn btn-danger w-25' type='submit' value='" . $translations[$lang]['validate'] . "'>");
    println("                        <a href='../" . ($lang === 'fr' ? '?lang=fr' : '') . "' class='btn btn-danger w-25 ml-3'>" . $translations[$lang]['return'] . "</a>");
    println("                    </form>");
    println("                </div>");
    println("            </div>");

}?>
            <footer class="col-lg-12">
                <p><?php echo $translations[$lang]['made-by']?> Paragoumba <?php echo $translations[$lang]['thanks-to']?> <a href="https://getbootstrap.com" target="_blank">Bootstrap</a></p>
            </footer>
        </div>
    </div>
    <script src="../js/warnDeleting.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>
