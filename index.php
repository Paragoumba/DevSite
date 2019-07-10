<?php $version = "v3.2.3";
/* Translation */
$lang = isset($_GET['lang']) ? $_GET['lang'] === "fr" ? "fr" : "en" : "en";
$translations = array(
        "en" => array("public-projects-list" => "List of all of my public projects", "projects-list" => "Projects' list", "snippet" => "Snippet", "stable" => "Stable", "this-site" => "this site", "for" => "For", "not-specified" => "not specified", "no-description" => "No description", "link" => "Link", "status" => "Status", "not-published" => "Not published", "language" => "Language", "no-project" => "There's no project at the moment", "connection-failed" => "Connection failed", "made-by" => "Made by ", "thanks-to" => " thanks to ", "active" => "Active", "inactive" => "Inactive", "finished" => "Finished", "canceled" => "Canceled"),
        "fr" => array("public-projects-list" => "Liste de tous mes projets publics", "projects-list" => "Liste des projets", "snippet" => "Fragment", "stable" => "Stable", "this-site" => "ce site", "for" => "Pour", "not-specified" => "non renseigné", "no-description" => "Pas de description", "link" => "Lien", "status" => "Status", "not-published" => "Non publié", "language" => "Langage", "no-project" => "Il n'y a aucun projet en cours pour le moment", "connection-failed" => "Connexion echouée", "made-by" => "Fait par ", "thanks-to" => " grâce à ","active" => "Actif", "inactive" => "Inactif", "finished" => "Terminé", "canceled" => "Annulé")
);

function getCredentials(){

    $fileContent = file_get_contents("creds.para");
    $fileContent = explode("\n", $fileContent);

    $creds['server'] = substr($fileContent[0], 0, strlen($fileContent[0]));
    $creds['username'] = substr($fileContent[1], 0, strlen($fileContent[1]));
    $creds['password'] = substr($fileContent[2], 0, strlen($fileContent[2]));
    $creds['dbName'] = substr($fileContent[3], 0, strlen($fileContent[3]));

    return $creds;

}

function println($str){

    echo $str . "\n";

}

function getStateColor($state){

    switch ($state){

        case "0":
            return "success";

        case "1":
            return "secondary";

        case "2":
            return "info";
        default:
            return "danger";

    }
}

function getStateName($state){

    global $translations;
    global $lang;

    switch ($state){

        case "0":
            return $translations[$lang]['active'];

        case "1":
            return $translations[$lang]['inactive'];

        case "2":
            return $translations[$lang]['finished'];

        default:
            return $translations[$lang]['canceled'];

    }
}

function getLanguageColor($language){

    switch ($language){

        case 'java':
            return 'danger';

        case 'php':
            return 'dark';

        case 'javascript':
            return 'warning';

        case 'sql':
            return 'info';

        default:
            return 'light';
    }
}?>
<!DOCTYPE html>

<html lang="<?php echo $lang?>">
<head>
    <title><?php echo $translations[$lang]['public-projects-list']?> - DevSite</title>
    <link rel="icon" href="https://paragoumba.fr/res/icon.gif">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="theme.css">
    <link rel="stylesheet" href="css/style.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <header class="col-lg-12 mb-5 py-5">
                <div class="col-lg-1 dropdown d-inline-block align-text-bottom">
                    <button class="btn bg-transparent dropdown-toggle text-white" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo $translations[$lang]['language']?></button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="./?lang=fr">Français</a>
                        <a class="dropdown-item" href="./">English</a>
                    </div>
                </div>
                <h1 class="col-lg-10 d-inline-block text-center"><?php echo $translations[$lang]['projects-list']?></h1>
            </header>
<?php $creds = getCredentials();

    try {

        // Create connection
        $conn = new PDO("mysql:host=" . $creds['server'] . ";dbname=" . $creds['dbName'], $creds['username'], $creds['password']);
        $conn -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "CREATE TABLE IF NOT EXISTS Customers (cid int primary key auto_increment, name tinytext)";
        $conn -> exec($sql);

        $sql = "CREATE TABLE IF NOT EXISTS Languages (lid int primary key auto_increment, name tinytext)";
        $conn -> exec($sql);

        $sql = "CREATE TABLE IF NOT EXISTS Projects (pid int primary key auto_increment, name tinytext, cid int, description text, link tinytext, state tinyint, published boolean, stable boolean, snippet boolean, CONSTRAINT FK_Projects_Customers FOREIGN KEY (cid) REFERENCES Customers(cid))";
        $conn -> exec($sql);

        $sql = "CREATE TABLE IF NOT EXISTS PLangs (pid int, lid int, CONSTRAINT PK_PLangs PRIMARY KEY (pid, lid), CONSTRAINT FK_PLangs_Projects FOREIGN KEY (pid) REFERENCES Projects(pid), CONSTRAINT FK_PLangs_Languages FOREIGN KEY (lid) REFERENCES Languages(lid))";
        $conn -> exec($sql);

        $sql = "SELECT pid, p.name pname, c.name cname, description, link, state, stable, snippet, published FROM Projects p LEFT OUTER JOIN Customers c ON p.cid = c.cid WHERE `published`=1";
        $result = $conn -> query($sql);

        if ($result -> rowCount() > 0) {

            println("            <div class='container'>");
            println("                <div class='row'>");

            while ($row = $result -> fetch()) {

                $languages = array();
                $sql = "SELECT l.name lname FROM Projects p JOIN PLangs pl ON p.pid = pl.pid JOIN Languages l ON pl.lid = l.lid WHERE p.pid=:pid";
                $stmt = $conn -> prepare($sql);

                $stmt -> bindParam(":pid", $row['pid']);
                $stmt -> execute();

                while ($languages[] = $stmt -> fetch()['lname']);

                println("                <div class='col-lg-4'>");
                println("                    <div class='card bg-dark mb-3'>");
                println("                        <h3 class='card-header text-center'>" . ($row['snippet'] === '1' ? "<span class='badge badge-info'>" . $translations[$lang]['snippet'] . "</span> " : '') . ($row['stable'] === '1' ? "<span class='badge badge-success'>" . $translations[$lang]['stable'] . "</span> " : '') . nl2br($row['pname']) . ((strpos($row['pname'], "DevSite") && $row['cname'] === "Paragoumba") ? " (" . $translations[$lang]['this-site'] . ")" : "") . "</h3>");
                println("                        <div class='card-body'>");
                println("                            <p>" . $translations[$lang]['for'] . " <b>" . nl2br($row['cname'] !== NULL ? $row['cname'] : $translations[$lang]['not-specified']) . "</b></p>");
                println("                            <p>" . nl2br($row['description'] !== "" ? $row['description'] : $translations[$lang]['no-description'] . ".") . "</p>");
                println("                            <p>" . $translations[$lang]['link'] . ": " . nl2br(($row['link'] !== "" ? "<a href='" . $row['link'] . "' target='_blank'>" . $row['link'] . "</a>" : $translations[$lang]['not-published'])) . "</p>");
                println("                            <p>" . $translations[$lang]['status'] . ": <span class='badge badge-" . getStateColor($row['state']) . "'>" . getStateName($row['state']) . "</span></p>");
                echo "                            <div>" . $translations[$lang]['language'] . (sizeof($languages) > 1 ? 's' : '') . ": ";

                if (count($languages) === 0 || $languages[0] === NULL) $languages[0] = $translations[$lang]['not-specified'];

                foreach ($languages as $language) echo "<span class='badge badge-" . getLanguageColor($language) . "'>$language</span> ";

                println("</div>");
                println("                        </div>");
                println("                    </div>");
                println("                </div>");

            }

            println("                </div>");
            println("            </div>");

        } else {

            println("            <h3 class='card card-body text-center bg-dark offset-lg-2 col-lg-8 my-5'>" . $translations[$lang]['no-project'] . ".</h3>");

        }

    } catch (PDOException $e){

        echo "<br/>" . $translations[$lang]['connection-failed'] . ": " . $e -> getMessage();

    }

    $conn = null?>
        </div>
        <footer class="col-lg-12 text-center mt-3">
            <p><?php echo $version . " - " . $translations[$lang]['made-by']?> Paragoumba <?php echo $translations[$lang]['thanks-to']?> <a href="https://getbootstrap.com" target="_blank">Bootstrap</a></p>
        </footer>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
