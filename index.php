<!DOCTYPE html>

<html lang="fr">
<head>
    <title>Liste de tous mes projets publics - DevSite</title>
    <link rel="icon" href="http://paragoumba.fr/res/icon.gif">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-theme">
    <div class="container-fluid">
        <div class="row">
            <header class="card col-lg-12">
                <h1>Liste des Projets</h1>
            </header>
<?php function getCredentials(){

        $fileContent = file_get_contents("creds.para");
        $fileContent = explode("\n", $fileContent);

        $creds['server'] = substr($fileContent[0], 0, strlen($fileContent[0]) - 1);
        $creds['username'] = substr($fileContent[1], 0, strlen($fileContent[1]) - 1);
        $creds['password'] = substr($fileContent[2], 0, strlen($fileContent[2]) - 1);
        $creds['dbName'] = substr($fileContent[3], 0, strlen($fileContent[3]) - 1);

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

        switch ($state){

            case "0":
                return "Actif";

            case "1":
                return "Inactif";

            case "2":
                return "Terminé";

            default:
                return "Annulé";

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
    }

    $creds = getCredentials();

    // Create connection
    $conn = new mysqli($creds['server'], $creds['username'], $creds['password'], $creds['dbName']);

    // Check connection
    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

    mysqli_set_charset($conn, "utf8");

    $sql = "SELECT * FROM projects";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {

        println("            <ul>");

        while ($row = $result->fetch_assoc()){

            if ($row['published']) {

                $languages = explode(' ', $row['languages']);

                println("                <li class='card selectable'>");
                println("                    <h3 class='card-header selectable'>" . ($row['snippet'] === '1' ? '<span class="badge badge-info">Snippet</span> ' : '') . ($row['stable'] === '1' ? '<span class="badge badge-success">Stable</span> ' : '') . nl2br($row['title']) . ((strpos($row['title'], "DevSite") && $row['creator'] === "Paragoumba") ? " (ce site)" : "") . "</h3>");
                println("                    <div class='card-body'>");
                println("                        <p>Pour <u>" . nl2br($row['creator']) . "</u></p>");
                println("                        <p>" . nl2br($row['description']) . "</p>");
                println("                        <p>Lien: " . nl2br(($row['link'] !== "" ? "<a href='" . $row['link'] . "' target='_blank'>" . $row['link'] . "</a>" : "Non publié")) . "</p>");
                println("                        <p>Status: <span class='badge badge-" . getStateColor($row['state']) . "'>" . getStateName($row['state']) . "</span></p>");
                echo "                        <div>Langage" . (sizeof($languages) > 1 ? 's' : '') . ": ";

                if ($languages[0] === '') echo 'Langages non renseignés';

                else {

                    foreach ($languages as $language) {

                        echo "<span class='badge badge-" . getLanguageColor($language) . "'>$language</span> ";

                    }
                }

                println("</div>");
                println("                    </div>");
                println("                </li>");

            }
        }

        println("            </ul>");

    } else {

        println("            <p>Il n'y a aucun projet en cours pour le moment.</p>");

    }

    $conn->close(); ?>
        </div>
        <div class="row">
            <footer class="card col-lg-12">
                <p class="card-text">Fait par Paragoumba avec <a href="https://getbootstrap.com" target="_blank">Bootstrap</a></p>
            </footer>
        </div>
    </div>
</body>
</html>
