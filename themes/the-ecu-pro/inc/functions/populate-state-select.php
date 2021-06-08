<?php

if (isset($_POST["country"])) {
    // Capture selected country
    $country = $_POST["country"];

    // Define country and city array
    $stateArr = array(
        ""    => "Select an option…",
        "EC"  => "Eastern Cape",
        "FS"  => "Free State",
        "GP"  => "Gauteng",
        "KZN" => "KwaZulu-Natal",
        "LP"  => "Limpopo",
        "MP"  => "Mpumalanga",
        "NC"  => "Northern Cape",
        "NW"  => "North West",
        "WC"  => "Western Cape"
    );

    // Display city dropdown based on country name
    if ($country == 'ZA') {
        foreach ($stateArr as $key => $value) {
            echo "<option value='".$key."'>" . $value . "</option>";
        }
    }

    die();
}