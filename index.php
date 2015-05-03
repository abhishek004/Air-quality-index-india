<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>jVectorMap demo</title>
        <link rel="stylesheet" href="css/jquery-jvectormap-2.0.2.css" type="text/css" media="screen"/>
        <link rel="stylesheet" href="dist/css/bootstrap-theme.min.css" type="text/css"/>
        <link rel="stylesheet" href="dist/css/bootstrap.min.css" type="text/css"/>
        <link rel="stylesheet" href="css/mycss.css" type="text/css"/>
        <script src="js/jquery-2.1.3.min.js"></script>
        <script src="dist/js/bootstrap.min.js"></script>
        <script src="js/jquery-jvectormap-2.0.2.min.js"></script>
        <script src="js/jquery-jvectormap-in-mill-en.js"></script>
        <style>

            body{
                 /*background: url(Bg/1.jpg) no-repeat center center fixed; 
                 -webkit-background-size: cover;
                 -moz-background-size: cover;
                 -o-background-size: cover;
                 background-size: cover;*/
                 background-color:#F1EDE3; 
                 padding-top: 50px;
            }

            /*.starter-template {
              padding: 40px 15px;
              text-align: center;
            }*/

            div{
                -moz-border-radius: 10px;
                -webkit-border-radius: 10px;
                border-radius: 10px; /* future proofing */
                -khtml-border-radius: 10px; /* for old Konqueror browsers */
            }

            .margin{
                margin-top: 40px;
                margin-bottom: 20px;
            }

           /* .container{
                position:relative;
            }*/
            #map{
                float:left;
               /* position: fixed;
                clear: left;*/
            }

            .myclass{
               /* float:right;*/
            }

            #test{
                color: black;
                top: 200px;
                left: 1000px;
                position: fixed;
                clear:left;
                
            }

            #table{
                position: relative;
                float:right;
                width:400px;
                top:200px;
            }



            #al{
                color:black;
            }

            #Heading{
                color: white;
                padding: 0px;
                margin-top: 15px;
            }

        </style>
    </head>
    <body>
        <nav class="navbar navbar-inverse navbar-fixed-top">
          <div class="container">
            <div class="navbar-header">
              <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
              <h3 id="Heading">Air Quality Index</h3>
            </div>
            <div id="navbar" class="collapse navbar-collapse">
              <ul class="nav navbar-nav navbar-right">
                <li class="active"><a href="#">Home</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#contact">Contact</a></li>
              </ul>
            </div><!--/.nav-collapse -->
          </div>
        </nav>

        <?php
            include_once('simple_html_dom.php'); 
            
            function getAQI($I_low, $I_high, $C_low, $C_high, $C){
                return (($I_high - $I_low)/($C_high - $C_low))*($C - $C_low) + $I_low;
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
                //echo "Gas name, concentration value, unit, concentration(previous 24 hours)/prescribed standard, AQI Value<br>";
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
                    if($result != 0){
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
            }

            function display_result(){
                echo "Most prominent pollutant is ==> ".$max["gas"]."<br>";
                echo "With AQI: ".$max["aqi"];
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


        <div class="container">

          <div class="starter-template">
            <div id="map" class="margin" style="width: 700px; height: 600px"></div>
            <div id="table">
                <table border='1' align "right">
                    <tr>
                        <th>Gas</th>
                        <th>AQI</th>
                    </tr>
                <?php
                    $url = "http://www.cpcb.gov.in/CAAQM/frmCurrentDataNew.aspx?StationName=Dwarka&StateId=6&CityId=85";
                    fill_table($url);
                ?>
                </table>
            </div>

            <!-- <div id = "result"><?php display_result() ?></div> -->
          </div>

        </div><!-- /.container -->

        <!-- <div class = "container">
            <div class = "row">
                <div id="map" class="margin" style="width: 700px; height: 600px"></div>
                <div id = "test" class="alert alert-info myclass fade in" role="alert"><p id="al">Abhishek</p></div>
            </div>
            
        </div> -->
        
            
        
        <script>
/*          
            $("#map").vectorMap({
                map: 'in_mill_en',
                zoomMax: 40,
                focusOn: 'IN-DL',
                onRegionClick: function(event, code){
                    console.log(code);
                    myfunc(code);
                }
            });

            var myfunc = function(code){
                m = $("#map").vectorMap('get','mapObject');
                m.setFocus({
                    x: 0.5,
                    y: 0.5,
                    region: code
                })
            };
*/
            
            var palette = ['#181F4B', '#001356'];

            $(function(){
                var generateColors = function(){
                var colors = {},
                    key;

                for (key in map.regions) {
                  colors[key] = palette[Math.floor(Math.random()*palette.length)];
                }
                return colors;
                }

                var markerIndex = 0, markersCoords = {};
                var x=1;
                var mar = {
                    'IN-DL': [
                        {latLng: [28.719804828832736,77.25068104282566], name: 'Dilshad Garden'},
                        {latLng: [28.783376811716238,77.00896146131728], name:'D.C.E'},
                        {latLng: [28.585246821204954,76.90802361409402], name:'Dwarka'}
                    ],
                    'IN-UP': [
                        {latLng: [27.161830820078162,77.83037160828435], name: 'Agra'},
                        {latLng: [27.111786805400317,79.79768552106408], name:'Lucknow'},
                        {latLng: [26.303126672921213,79.75353238460825], name:'Kanpur'},
                        {latLng: [25.371813455349116,82.89064557518128], name:'Varanasi'}
                        
                    ],
                    'IN-HR' : [
                        {latLng: [28.294864650453857,77.34307227196392], name:'Faridabad'}
                    ],

                   'IN-GJ' : [
                        {latLng: [22.51450643032689,71.76962942369802], name:'Ahmedabad'}
                    ],

                    'IN-AP' : [
                        {latLng: [17.050299940948165,78.69975191254275], name:'Hyderabad'}
                    ],


                    'IN-KA' : [
                        {latLng: [13.053009776010425,77.47333750835979], name:'Bangalore'}
                    ],

                    'IN-TN' : [
                        {latLng: [12.810340886808428,80.01802839488053], name:'Chennai'}
                    ]
                };

                var map = new jvm.Map({
                    map: 'in_mill_en',
                    container: $('#map'),
                    zoomMax : 40,
                    regionStyle: {
                        initial: {
                          "fill-opacity": 1,
                        }
                    },
                    backgroundColor: "rgba(0,0,0,0)",
                    series: {
                        regions: [{
                            attribute: 'fill'
                        }]
                    },
                    markerStyle: {
                        initial: {
                            fill: 'red'
                        }
                    },
                    onRegionClick: function(e,c){
                        //map.removeAllMarkers();
                        map.setFocus({
                            region:c,animate:true
                        });
                        setTimeout(function(){
                          x=0;  
                        },500);
                        map.addMarkers(mar[c]);
                    },

                    onViewportChange: function(e,s){
                      console.log(s);
                      if(x===0 && s<1.5){
                        console.log(x);
                        map.removeAllMarkers(); 
                        x=1;
                      }                    
                    },

                    onMarkerClick: function(e, c){
                    // here replace "#al" with your id for div!!
                    $("#al").html(c+' is clicked: display AQI message');
                }

                });

                map.series.regions[0].setValues(generateColors());

               /* map.container.click(function(e){
                    var latLng = map.pointToLatLng(e.offsetX, e.offsetY);
                    var targetCls = $(e.target).attr('class');
                    if (latLng && (!targetCls || (targetCls && $(e.target).attr('class').indexOf('jvectormap-marker') === -1))) {
                        markersCoords[markerIndex] = latLng;
                        map.addMarker(markerIndex, {latLng: [latLng.lat, latLng.lng], name: "hello"});
                        markerIndex += 1;
                        console.log(latLng);
                    }
                });*/
            });



            var facts = [
                    "An average American breathe 2 gallons of air per minute which means around 3400 gallons of air each day.",
                    "Inhaling Air pollution takes away at least 1-2 years of a typical human life.",
                    "It has effects as small as burning eyes and itchy throat to as large as breathing problems and death.",
                    "Pollutants that are released into the air, as opposed to land and water pollutants, are the most harmful.",
                    "Rising levels of air pollution in Beijing has brought a new disease &#8211; Beijing cough.",
                    "Air pollution is not a recent occurrence. In 1952, the Great smog of London killed 8000 people.",
                    "Deaths caused by air pollution cost the European Union €161 billion.",
                    "Electric vehicles produce less air pollutants. They stir up dirt but without producing gases.",
                    "Producing heavy crude oil increases chances of air pollution by 40% than producing light crude oil.",
                    "According to the Lancet journal, air pollution caused by waiting in traffic increases the chances of death caused due to heart attack.",
                    "Toxic air pollution poses a greater threat to children, due to their smaller physical size and lung capacity.",
                    "Air pollution and resulting deaths are increasing fastest in Asia.",

            ];




            
          
            var v = 0;
            /*setInterval(function(){
                v = (v+1)%facts.length;
                $("#al").html(facts[v]);
            },5000);

*/
        </script>

    </body>
</html>