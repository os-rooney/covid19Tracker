<?php
// import / format Data
$jsonData = file_get_contents("https://pomber.github.io/covid19/timeseries.json");
$data = json_decode($jsonData, true);

foreach($data as $key => $value){
    $days_count = count($value) - 1;
    $days_count_prev = $days_count - 1;
}

$total_confirmed = 0;
$total_deaths = 0;

foreach($data as $key => $value){
    $total_confirmed += $value[$days_count]['confirmed'];
    $total_deaths += $value[$days_count]['deaths'];
}

$formatConfirmed = number_format(strval($total_confirmed), 0, ',', '.');
$formatDeaths = number_format(strval($total_deaths), 0, ',', '.');

// pagination
$perPage = 20;

// total number of data
$totalCount = count($data);

// calculate total pages
$totalPages = ceil($totalCount / $perPage);

// check and make sure that the current page is within reasonable boundaries
$currentPage = (int) ($_GET['page'] ?? 1);
if($currentPage < 1 || $currentPage > $totalPages){
    $currentPage = 1;
}

// number of data to skip
$offset = $perPage * ($currentPage - 1);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Style CSS -->
    <link rel="stylesheet" href="./style.css">
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/f7afecaf16.js" crossorigin="anonymous"></script>
    <!-- Proprietary <meta> Tags -->
    <meta property="og:title" content="Covid 19 Tracker App - Ronios">
    <meta property="og:description" content="Covid-19 track current infection numbers, search for specific information by country name">
    <meta property="og:image" content="https://covid.ronios.de/screenshot.jpeg">
    <meta property="og:url" content="https://covid.ronios.de">
    <title>Covid-19 Tracker</title>
</head>
<body>
<header>
    <h1>Covid-19 Tracker</h1>
    <h2>Bleib sicher, bleib drinnen</h2>
    <!-- <h5 class="fs-6">Ein OpenSource-Projekt zur Verfolgung aller COVID-19-Fälle auf der ganzen Welt.</h5> -->
</header>
<div>
    <div class="global-info">
        <h3>Globale Statistiken</h3>
        <div class="flex">
            <div>
                <h5>Fälle insgesamt</h5>
                <h4><?= htmlspecialchars($formatConfirmed) ?></h4>
            </div>
            <div>
                <h5>Todesfälle</h5>
                <h4 class="danger"><?= htmlspecialchars($formatDeaths)?></h4>
            </div>
            <div>
                <h5>zuletzt aktualisiert</h5>
                <h4>
                    <?php
                    $dateFormat = $value[$days_count]['date'];
                    list($year, $month, $day)= explode("-", $dateFormat);
                    echo $day . "." . htmlspecialchars($month) . "." . htmlspecialchars($year);
                    ?></h4>
            </div>
        </div>
    </div>
</div>
<div class="suche">
    <form action="" method="POST">
        <label for="land">Suche: </label>
        <input type="text" id="land" name="land" placeholder="enter a country">
    </form>
</div>
<div class="content">
    <table>
        <thead>
        <tr>
            <th scope="col">Ort</th>
            <th scope="col">Fälle insgesamt</th>
            <th scope="col">mehr Fälle als am Vortag</th>
            <th scope="col">Todesfälle</th>
        </tr>
        </thead>
        <tbody>
        <?php
        // if a country is entered then look for results otherwise show all countries
        if(isset($_POST['land'])){
            // remove unwanted chars and keep letters
            $remove = array('*',' ', '+', '!',  ',', '#', '@','1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '´', '.', '<', '>', ')', '((', '{', '}', '=', '/', '\\',';', ':','-','_','$','%', '&', '"');
            $land = trim(strtolower(htmlspecialchars($_POST['land'])));
            $land = str_replace($remove, "", $land);
            foreach($data as $key => $value){
                $increase = number_format(strval($value[$days_count]['confirmed'] - $value[$days_count_prev] ['confirmed']), 0, ',', '.');
                $country = trim(strtolower(htmlspecialchars($key)));
                if($country == $land){
                    if($increase == 0){
                        $result = '<span class="yellow">--</span>';
                    } else if ($increase > 0) {
                        $result = '<span class="danger">'. $increase . ' ' .'<i class="fas fa-arrow-up"></i>' ."</span>";
                    } else {
                        $result = '<span class="green">'. $increase . ' ' .'<i class="fas fa-arrow-down"></i>' ."</span>";
                    }
                    echo '
                                <tr>
                                    <th>'. htmlspecialchars($key) .'</th>
                                    <td><span class="yellow">'. number_format(strval($value[$days_count]['confirmed']), 0, ',', '.') .'</span></td>
                                    <td> '. $result .'</td>
                                    <td><span class="danger">'. number_format(strval($value[$days_count]['deaths']), 0, ',', '.') .'</span></td>
                                </tr>
                            ';
                } 
            }
        } else {
            foreach(array_slice($data, $offset, $perPage) as $key => $value){
                $increase = number_format(strval($value[$days_count]['confirmed'] - $value[$days_count_prev] ['confirmed']), 0, ',', '.');
                if($increase == 0){
                    $result =  '<span class="yellow">--</span>';
                } else if ($increase > 0) {
                    $result =  '<span class="danger">'. $increase . ' ' .'<i class="fas fa-arrow-up"></i>' ."</span>";
                } else {
                    $result =  '<span class="green">'. $increase . ' ' .'<i class="fas fa-arrow-down"></i>' ."</span>";
                }
                echo '
                        <tr>
                        <th>'. $key .'</th>
                        <td><span class="yellow">'. number_format(strval($value[$days_count]['confirmed']), 0, ',', '.') .'</span></td>
                        <td>'. $result .'</td>
                        <td><span class="danger">'. number_format(strval($value[$days_count]['deaths']), 0, ',', '.') .'</td>
                        </tr>
                        ';
            }
        }?>
        </tbody>
    </table>
</div>
<h3 class="paginationText"><?php echo "Seite {$currentPage} von {$totalPages}"; ?></h3>
<div class="paginationLinks">
    <?php
    if($currentPage > 1){
        echo "<a href='index.php?page=". ($currentPage - 1) ."'>&larr; Zurück</a>";
    }?>

    <?php
    $win = 1; // window size
    for($i = 1; $i <= $totalPages; $i++){
        if($i > $win && $i < $totalPages - $win && abs($i - $currentPage) > $win){
            // jump to the next iteration without executing any code below
            echo '. ';
            continue;
        }
        if($currentPage == $i){
            echo "<strong class='green'>{$i}</strong> ";
        } else {
            echo "<a class='pageLink' href='index.php?page=". $i ."'>{$i}</a> ";
        }
    }
    ?>

    <?php
    if($currentPage < $totalPages){
        echo "<a href='index.php?page=". ($currentPage + 1) ."'>Weiter &rarr;</a>";
    }?>

</div>
</body>
</html>
