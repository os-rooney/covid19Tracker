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
                    <h4><?= $formatConfirmed ?></h4>
                </div>
                <div>
                    <h5>Todesfälle</h5>
                    <h4 class="danger"><?= $formatDeaths ?></h4>
                </div>
                <div>
                    <h5>zuletzt aktualisiert</h5>
                    <h4>
                    <?php 
                        $dateFormat = $value[$days_count]['date'];  
                        list($year, $month, $day)= explode("-", $dateFormat);
                        echo $day . "." . $month . "." . $year;
                    ?></h4>
                </div>
            </div>
        </div>

    <div class="flex content">
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
                <?php foreach($data as $key => $value): ?>
                    <?php 
                        $increase = number_format(strval($value[$days_count]['confirmed'] - $value[$days_count_prev] ['confirmed']), 0, ',', '.'); 
                        ?>
                    <tr>
                        <th><?= $key ?></th>
                        <td><span class="yellow"><?= number_format(strval($value[$days_count]['confirmed']), 0, ',', '.') ?></span></td>
                        <td><?php 
                            if($increase === 0){
                                echo '<span class="yellow">--</span>';
                            } else if ($increase > 0) {
                                echo '<span class="danger">'. $increase . ' ' .'<i class="fas fa-arrow-up"></i>' ."</span>";
                            } else {
                                echo '<span class="green">'. $increase . ' ' .'<i class="fas fa-arrow-down"></i>' ."</span>";
                            }
                        ?></td>
                        <td><span class="danger"><?= number_format(strval($value[$days_count]['deaths']), 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach;?>
            </tbody>
        </table>
    </div>
</body>
</html>