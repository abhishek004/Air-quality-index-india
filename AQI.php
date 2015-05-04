<?php
    include_once('simple_html_dom.php'); 
    error_reporting(E_ERROR | E_PARSE);
    function getAQI($I_low, $I_high, $C_low, $C_high, $C){
        return round((($I_high - $I_low)/($C_high - $C_low))*($C - $C_low) + $I_low);
    }

    //echo getAQI(0,50,0,0.064,0.00804);
    //$html = file_get_html("http://cpcb.gov.in/CAAQM/frmCurrentDataNew.aspx?StationName=Talkatora&StateId=28&CityId=256");

    $aqi = array(
            "Ozone" => array(138, 178, 220, 262, 790, 1070, 1280),
            "PM 10" => array(54, 154, 254, 354, 424, 504, 604),
            "PM 2.5" => array(15.4, 40.4, 65.4, 150.4, 250.4, 350.4, 500.4),
            "Carbon Monoxide" => array(5.43, 11.6, 15.3, 19.0, 37.5, 49.8, 62.5),
            "Sulfur Dioxide" => array(95, 406, 632, 857, 1700, 2280, 2832),
            "Nitrogen Dioxide" => array(0, 0, 0, 1320, 2510, 3320, 4130),
            
            "AQI" => array(50, 100, 150, 200, 250, 300, 350)
        
        );

    function getIndex($str, $val){
        global $aqi;
        for($i =0; $i < 7; $i++){
            if($aqi[$str][$i] >= $val){
                $high = $aqi[$str][$i];
                $I_high = $aqi["AQI"][$i];
                //echo $high." ".$I_high."<br>";
                if($i != 0){
                    $low = $aqi[$str][$i-1]+1;
                    $I_low = $aqi["AQI"][$i-1]+1;
                }else{
                    //echo $low." ".$I_low."<br>";
                    $low = 0;
                    $I_low = 0;
                }
                break;
            }
        }

        return getAQI($I_low, $I_high, $low, $high, $val);
    }

    //function for removing special characters:
    function clean($string) {
        return preg_replace('/[^A-Za-z0-9\\xB5\-\/]/', '', $string); // Removes special chars.
    }
    
    $max = array(
        "gas" => "",
        "aqi" => 0
    ); //most prominent pollutant
    function fill_table($url){
        //$url = "http://www.cpcb.gov.in/CAAQM/frmCurrentDataNew.aspx?StationName=Dwarka&StateId=6&CityId=85";
        $dom = new DOMDocument();
        $dom->loadHTMLFile($url);

        $xpath = new DOMXpath($dom);

        $table = $xpath->query(".//*[@id='lblReportCurrentData']/table/tr");
        global $max;
        global $aqi;
        //echo "Gas name, concentration value, unit, concentration(previous 24 hours)/prescribed standard, AQI Value<br>";
        //echo '<table class="table table-bordered">';
        echo "<tr><th>Gas</th><th>AQI</th></tr>";
        for($index = 2; $index < $table->length; $index++){
            $gas = $xpath->query(".//*[@id='lblReportCurrentData']/table/tr[$index]/td[1]/text()");
            //echo $gas->item(0)->nodeValue." ";
            $concentration = $xpath->query(".//*[@id='lblReportCurrentData']/table/tr[$index]/td[4]/span/text()");
            //echo $concentration->item(0)->nodeValue." ";
            $unit = $xpath->query(".//*[@id='lblReportCurrentData']/table/tr[$index]/td[5]/text() ");   
            //echo clean($unit->item(0)->nodeValue)." ";
            $prescribed = $xpath->query(".//*[@id='lblReportCurrentData']/table/tr[$index]/td[6]/span/text() ");
            //echo $prescribed->item(0)->nodeValue." ";
            $result = getIndex($gas->item(0)->nodeValue, $concentration->item(0)->nodeValue);

            if(array_key_exists($gas->item(0)->nodeValue,$aqi)){
                echo "<tr>";
                echo "<td>" . $gas->item(0)->nodeValue . "</td>";
                echo "<td>" . $result . "</td>";
                echo "</tr>";
                if($result > $max["aqi"]){
                    $max["gas"] = $gas->item(0)->nodeValue;
                    $max["aqi"] = $result;
                }
            }
        }
        //echo "</table>";
    }

    function display_gas(){
        global $max;
       
        echo 'Most prominent pollutant is '.$max["gas"];
    }

    function display_AQI(){
        global $max;
        echo "AQI: ".$max["aqi"];
    }
    function display_bar(){
        global $max;
        $message="";
        if($max["aqi"] <= 50 ) {$message = "Minimal Impact";}
        else if($max["aqi"] <= 100) {$message = "Minor breathing discomfort to sensitive people";}
        else if($max["aqi"] <= 200) {$message = "Breathing discomfort to the people with lungs, asthma and heart diseases";}
        else if($max["aqi"] <= 300) {$message = "Breathing discomfort to most people on prolonged exposure";}
        else if($max["aqi"] <= 400) {$message = "Respiratory illness on prolonged exposure";}
        else if($max["aqi"] <= 500) {$message = "Affects healthy people and seriously impacts those with existing diseases";}
        echo $message;
    }

    //echo "<br>".getIndex("PM 2.5", 29.18);
    
    // foreach($html->find('#Td1 table') as $aa){
    //  foreach($aa->find('tr') as $row){
    //      foreach($row->find('td') as $col){

    //          echo $col->plaintext."******";
    //      }
    //      echo "<br>";
    //  }
    // }
?>