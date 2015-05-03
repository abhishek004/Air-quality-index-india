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
                 background: url(Bg/1.jpg) no-repeat center center fixed; 
                 -webkit-background-size: cover;
                 -moz-background-size: cover;
                 -o-background-size: cover;
                 background-size: cover;
            }

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


            #map{
                float: left;
            }

            .myclass{
                float:right;
            }

            #test{
                color: black;
                margin-top: 300px;
            }

            #al{
                color:black;
            }

        </style>
    </head>
    <body>
        <div class = "container">
            <div class = "row">
                <div id="map" class="margin" style="width: 700px; height: 600px"></div>
                <div id = "test" class="alert alert-info myclass fade in" role="alert"><p id="al"></p></div>
            </div>

        </div>
        
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
            
            var palette = ['#0c2b0a', '#061133', '#542529', '#66345a', '#f72109'];

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



            <?php 
                $pg =  file_get_html('http://cpcb.gov.in/CAAQM/frmCurrentDataNew.aspx?StationName=D.C.E.&StateId=6&CityId=85');
                echo $pg;
            ?>
          
            var v = 0;
            /*setInterval(function(){
                v = (v+1)%facts.length;
                $("#al").html(facts[v]);
            },5000);

*/
        </script>

    </body>
</html>