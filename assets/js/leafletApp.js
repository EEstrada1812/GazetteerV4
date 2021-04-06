let bounds;
let countryName;
let border;
let currentCountry;
let currentCurrency;

let currencyCode;
let currencyName;
let currencySymbol;

let capitalCityName;

let unescoLayerGroup;
let unescoIcon;
let unescoSite;
let unescoLat;
let unescoLng;
let unescoMarker;
let unescoNumber;

let cityMarkersCluster;
let wikiCluster;
let largeCityCluster;

let borderCountryCode;

let capitalLat;
let capitalLng;

//Function to add commas to numbers
function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

//map
mapboxgl.accessToken = 'pk.eyJ1IjoiZXN0cmFkYTExMDciLCJhIjoiY2p3cmkxaXE1MWs2ajRibGV4bjZna2cyZyJ9.rfXkxJ59K98sg9us_cOj3w';

// L.tileLayer('https://tile.jawg.io/jawg-streets/{z}/{x}/{y}.png?access-token=B5c7xyU8C9pYj2cSITJ1HHTfUeL6zaLCh8styLvSen0e5nBgU4p53kJ84IWOGAqZ', {})

var openStreetMap = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
	maxZoom: 19,
	attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }),
    hybrid = L.mapboxGL({
    attribution: "\u003ca href=\"https://www.maptiler.com/copyright/\" target=\"_blank\"\u003e\u0026copy; MapTiler\u003c/a\u003e \u003ca href=\"https://www.openstreetmap.org/copyright\" target=\"_blank\"\u003e\u0026copy; OpenStreetMap contributors\u003c/a\u003e",
    style: 'https://api.maptiler.com/maps/hybrid/style.json?key=IFRW9BnLg67kStosRQhA'});

var map = L.map('map', {
    maxZoom: 18,
    layers: [openStreetMap]
}).fitWorld();

var baseMaps = {
    "Satellite Map": hybrid,
    "Streets Map": openStreetMap
};

L.control.layers(baseMaps).addTo(map);

//Country Information Easy Button
L.easyButton('<i class="fas fa-info"></i>', function(){
    $.ajax({
        url: "assets/php/wikiCountryExcerpts.php",
        type: 'GET',
        dataType: "json",
        data: {
            countryName: countryName
        },
        success: function(result) {
            $("#txtWikiImg").html("&nbsp;");
            $("#txtWiki").html("&nbsp;");

            $('#txtWikiImg').html(`<img id='flag' src='${result.data.wikiCountryExcerpt.thumbnail.source}'><br>`);
            $('#txtWiki').html('<br>Wikipedia: ' + result.data.wikiCountryExcerpt.extract_html +'<br>');
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log('CountryExcerpt Data Error',textStatus, errorThrown);
        }
    });
    $('#wikiModal').modal('show');
}, 'Country Infomation').addTo(map);

//Weather Easy Button
L.easyButton('<i class="fas fa-cloud-sun"></i>', function(){
    $.ajax({
        url: "assets/php/weather.php",
        type: 'GET',
        dataType: "json",
        data: {
            capitalLat: capitalLat,
            capitalLng: capitalLng
        },
        success: function(result) {
            //console.log(result);
            $("#weatherTable tbody tr td").html("&nbsp;"); 
            let weatherIcon = result.data.weather.current.weather[0].icon;
                
            $('.txtCapitalWeatherName').html(capitalCityName);
            $('#txtCapitalWeatherCurrent').html( Math.round(result.data.weather.current.temp) +'&#8451<br>');
            $('#txtCapitalWeatherDescription').html( result.data.weather.current.weather[0].description);
            $('#txtCapitalWeatherWindspeed').html(result.data.weather.current.wind_speed + ' km/h');
            $('#txtCapitalWeatherHumidity').html( Math.round(result.data.weather.current.humidity) +'&#37');
            $('#txtCapitalWeatherLo').html( Math.round(result.data.weather.daily[0].temp.min) +'&#8451<br>');
            $('#txtCapitalWeatherHi').html( Math.round(result.data.weather.daily[0].temp.max) +'&#8451<br>');
            $('#txtCapitalTomorrowsWeatherLo').html( Math.round(result.data.weather.daily[1].temp.min) +'&#8451<br>');
            $('#txtCapitalTomorrowsWeatherHi').html( Math.round(result.data.weather.daily[1].temp.max) +'&#8451<br>');
            $('#CapitalWeatherIcon').html( `<img src="https://openweathermap.org/img/wn/${weatherIcon}@2x.png" width="24px">`);
            $('#CapitalHumidityIcon').html('<img src="assets/img/icons/humidity.svg" width="24px">');
            $('#CapitalWindIcon').html('<img src="assets/img/icons/007-windy.svg" width="24px">');
            $('.CapitalHiTempIcon').html('<img src="assets/img/icons/temperatureHi.svg" width="24px">');
            $('.CapitalLoTempIcon').html('<img src="assets/img/icons/temperatureLo.svg" width="24px">');

        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log('Weather Data Error',textStatus, errorThrown);
        }
    });
    $('#weatherModal').modal('show');
}, 'Weather').addTo(map);

//News Easy Button
L.easyButton('<i class="far fa-newspaper"></i>', function(){
    $.ajax({
        url: "assets/php/bingNews.php",
        type: 'GET',
        dataType: "json",
        data: {
            countryName: countryName
        },
        success: function(result) {
            $('.carousel-inner').empty();
            
            for (let i = 0; i < result.data.BingNews.value.length; i++) {
                if (result.data.BingNews.value[i].image) {
                    $('.carousel-inner').append(`
                        <div class="carousel-item">
                            <div class="container">
                                <div class="overlay-image" style="background-image: url(${result.data.BingNews.value[i].image.contentUrl});"></div>
                                <div class="carousel-caption text-start">
                                    <h5>${result.data.BingNews.value[i].name}</h5>
                                    <p><a class="btn btn-lg btn-primary" href="${result.data.BingNews.value[i].url}" target="_blank" role="button">Read Article</a></p>
                                </div>
                            </div>
                        </div>`);
                }   
            else {
                $('.carousel-inner').append(`<div class="carousel-item"><div class="container"><div class="overlay-image" style="background-image: url(assets/img/image-not-available.jpg});"></div><div class="carousel-caption text-start"><h5>${result.data.BingNews.value[i].name}</h5><p><a class="btn btn-lg btn-primary" href="${result.data.BingNews.value[i].url}" target="_blank" role="button">Read Article</a></p></div></div></div>`);
                }
            };

            $('.carousel-inner').find('.carousel-item:first' ).addClass( 'active' );
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log('Bing News Error',textStatus, errorThrown);
        }
    });
    $('#newsModal').modal('show');
}, 'News').addTo(map);


//Exchange Rates Easy Button
L.easyButton('<i class="fas fa-money-bill-wave"></i>', function(){
    $.ajax({
        url: "assets/php/exchangeRates.php",
        type: 'GET',
        dataType: "json",
        data: {
            currentCurrency: currentCurrency
        },
        success: function(result) {
            //console.log(result);
            $("#currencyTable currenctTBody currencyRow .currencyTD").html("&nbsp;");

            let exchangeRate = result.data.currentRate;
            $('#txtCurrencySymbol').html(currencySymbol);
            $('#txtCurrency').html(currencyName);
            $('#txtCurrencyCode').html(currencyCode);
            if (isNaN(exchangeRate)) {
                $('#txtRate').html( 'Exchange Rate Not Found');
            } else {
                $('#txtRate').html( exchangeRate.toFixed(2) + ' ' + currencyCode + ' to 1 EURO.');
            };
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log('ExchangeRate Data Error',textStatus, errorThrown);
        }
    });
    $('#currencyModal').modal('show');
}, 'Currency Information').addTo(map);

//Covid Easy Button
L.easyButton('<i class="fas fa-virus"></i>', function(){
    $.ajax({
        url: "assets/php/covid.php",
        type: 'GET',
        dataType: "json",
        data: {
            countryCodeA2: borderCountryCode
        },
        success: function(result) {
            $("#covidTable tbody tr td").html("&nbsp;");

            let covidDeaths = result.data.covidData.data.latest_data.deaths;
            let covidConfirmed = result.data.covidData.data.latest_data.confirmed;
            let covidcritical = result.data.covidData.data.latest_data.recovered;
            let covidPerMil = result.data.covidData.data.latest_data.calculated.cases_per_million_population;
            
            $('#covidModalLabel').html('Latest Covid data for: ' + countryName);
            $('#txtCovidDeaths').html(numberWithCommas(covidDeaths));
            $('#txtCovidCases').html(numberWithCommas(covidConfirmed));
            $('#txtCovidRecovered').html(numberWithCommas(covidcritical));
            $('#txtCovidPerMillion').html(numberWithCommas(covidPerMil));
            $('#txtCovidDeathRate').html( Math.round(result.data.covidData.data.latest_data.calculated.death_rate) +'&#37');
            $('#txtCovidRecoveryRate').html( Math.round(result.data.covidData.data.latest_data.calculated.recovery_rate) +'&#37');

        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log('Covid Data Error',textStatus, errorThrown);
        }
    });

    $('#covidModal').modal('show');
}, 'Covid-19 Information').addTo(map);

//UNESCO Sites Easy Button

var toggle = L.easyButton({
    states: [{
        stateName: 'add-markers',
        icon: 'fa-landmark',
        title: 'UNESCO (cultural) World Heritage Sites',
        onClick: function(control) {
        map.removeLayer(largeCityCluster);
        map.removeLayer(cityMarkersCluster);
        $.ajax({
            url: "assets/php/unesco.php",
            type: 'GET',
            dataType: "json",
            data: {
                countryFullName: countryName
            },
            success: function(result) {
            
                map.flyToBounds(bounds, {
                    padding: [0, 35], 
                    duration: 2
                });

            //console.log(result);
            unescoNumber = result.data.unescoSites.nhits;
                
            unescoLayerGroup = new L.markerClusterGroup();
            map.addLayer(unescoLayerGroup);

            if (unescoNumber < 1) {
                $('#unescoModal').modal('show');
                map.addLayer(largeCityCluster);
                map.addLayer(cityMarkersCluster);
            } else if (unescoNumber > 0) {
               for (let i = 0; i < result.data.unescoSites.records.length; i++) {
    
                unescoIcon = L.icon({
                    iconUrl: 'assets/img/icons/unesco.svg',
                    iconSize: [50, 50],
                    popupAnchor: [0,-15]
                });
    
                unescoSite = result.data.unescoSites.records[i].fields.site;
                unescoLat = result.data.unescoSites.records[i].fields.coordinates[0];
                unescoLng = result.data.unescoSites.records[i].fields.coordinates[1];
                unescoThumbnail = result.data.unescoSites.records[i].fields.image_url.filename;
                unsescoDescription = result.data.unescoSites.records[i].fields.short_description;
                unescoUrl = `https://whc.unesco.org/en/list/${result.data.unescoSites.records[i].fields.id_number}`;
                
                unescoMarker = L.marker(new L.LatLng(unescoLat, unescoLng), ({icon: unescoIcon})).bindPopup(`<div class="markerContainer"><h3>${unescoSite}</h3><img class="markerThumbnail" src='https://whc.unesco.org/uploads/sites/${unescoThumbnail}'><p class="markerTxtDescription">${unsescoDescription}</p></div><div id="city-link"><a href="${unescoUrl}" target="_blank">Learn more</a></div>`, {
                    maxWidth : 300
                }); 

                unescoLayerGroup.addLayer(unescoMarker);
            }
    
        
                
                control.state('remove-markers');
            };
                
    
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log('Unesco Data Error',textStatus, errorThrown);
            }
        });
        
        
        }
    }, {
        icon: 'fa-undo',
        stateName: 'remove-markers',
        onClick: function(control) {
        map.addLayer(largeCityCluster);
        map.addLayer(cityMarkersCluster);
        map.removeLayer(unescoLayerGroup);

        control.state('add-markers');
        },
        title: 'remove markers'
    }]
    });
    toggle.addTo(map);
        



$(document).ready(function () { 
//populate select options
    $.ajax({
        url: "assets/php/geoJson.php",
        type: 'GET',
        dataType: "json",
        success: function(result) {
            //console.log('populate options' , result);
            if (result.status.name == "ok") {
                for (var i=0; i<result.data.border.features.length; i++) {
                            $('#selCountry').append($('<option>', {
                                value: result.data.border.features[i].properties.iso_a2,
                                text: result.data.border.features[i].properties.name,
                            }));
                }
            }

            //sort options alphabetically
            $("#selCountry").html($("#selCountry option").sort(function (a, b) {
                return a.text == b.text ? 0 : a.text < b.text ? -1 : 1
                }));

            
//User's Location info
    const successCallback = (position) => {
        $.ajax({
            url: "assets/php/openCage.php",
            type: 'GET',
            dataType: 'json',
            data: {
                lat: position.coords.latitude,
                lng: position.coords.longitude,
            },

            success: function(result) {
                
                //console.log('openCage User Location',result);
                currentLat = result.data[0].geometry.lat;
                currentLng = result.data[0].geometry.lng;

                $("selectOpt select").val(result.data[0].components["ISO_3166-1_alpha-2"]);
                
                currentCountry = result.data[0].components["ISO_3166-1_alpha-2"];
                $("#selCountry").val(currentCountry).change();
            
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        }); 
        }

    const errorCallback = (error) => {
            console.error(error);
}
    navigator.geolocation.getCurrentPosition(successCallback, errorCallback);

        },    
    });
//end document. ready
});

//Main Ajax Call
$('#selCountry').on('change', function() {
    borderCountryCode = $("#selCountry").val();
    if (map.hasLayer(unescoLayerGroup)) {
                map.removeLayer(unescoLayerGroup);
                toggle.state('add-markers');
            }
    
    $.ajax({
        url: "assets/php/ajaxCalls.php",
        type: 'GET',
        dataType: 'json',
        data: {
                countryCode: borderCountryCode
            },
        beforeSend: function() {
            $('#loading').show();
        },
        success: function(result) {
            $('#loading').hide();
            //console.log(borderCountryCode, 'Results', result);
            
            //adds borders
            if (map.hasLayer(border)) {
                map.removeLayer(border);
            }
            
            border = L.geoJSON(result.data.border, {
                    color: '#ff7800',
                    weight: 2,
                    opacity: 0.65
                });

            bounds = border.getBounds();
            map.flyToBounds(bounds, {
                    padding: [0, 35], 
                    duration: 2
                });

            border.addTo(map); 
            
            if (result.status.name == "ok") {
                $("#countryInfoTable tbody tr td").html("&nbsp;");
                //set variables to reuse
                countryName = result.data.border.properties.name;
                capitalCityName = result.data.restCountries.capital;
                currentCurrency = result.data.restCountries.currencies[0].code;
                capitalLat = result.data.restCountries.latlng[0];
                capitalLng = result.data.restCountries.latlng[1];
                currencyCode = result.data.restCountries.currencies[0].code;
                currencyName = result.data.restCountries.currencies[0].name;
                currencySymbol = result.data.restCountries.currencies[0].symbol;
                
                //wiki country summary
                let popoulation =  numberWithCommas(result.data.restCountries.population);
                let area;
                if (result.data.restCountries.area === null) {
                    area = 'Area Data Unavailable'
                    $('#txtArea').html(area);
                } else {
                    area = numberWithCommas(result.data.restCountries.area);
                    $('#txtArea').html(area +'km<sup>2</sup>');
                }
                
                let callingCode = result.data.restCountries.callingCodes[0];
                let demonym =  result.data.restCountries.demonym;
                let domain = result.data.restCountries.topLevelDomain[0];
                let languages = "";
                    if (result.data.restCountries.languages.length === 1) {
                        languages = result.data.restCountries.languages[0].name;
                    } else if (result.data.restCountries.languages.length ===2 ) {
                        languages = result.data.restCountries.languages[0].name +" and " + result.data.restCountries.languages[1].name
                    } else if (result.data.restCountries.languages.length === 3) {
                        languages = result.data.restCountries.languages[0].name +" , " + result.data.restCountries.languages[1].name + " and " +  result.data.restCountries.languages[2].name
                    } else { result.data.restCountries.languages.forEach(language => {
                        languages += language.name + " ";
                            }) 
                        }

                $('#wikiModalLabel').html(result.data.restCountries.name);
                $('#txtPopulation').html(popoulation);
                $('#txtCapital').html(capitalCityName);
                $('#txtLanguages').html(languages);
                
                $('#txtIso2').html(result.data.border.properties.iso_a2);
                $('#txtIso3').html(result.data.border.properties.iso_a3)
                $('#txtCallingCode').html("+" + callingCode);
                $('#txtDemonym').html(demonym);
                $('#txtDomain').html(domain);
                
            //capital city cluster
            if (map.hasLayer(cityMarkersCluster)) {
                map.removeLayer(cityMarkersCluster);
            }

            cityMarkersCluster = new L.markerClusterGroup();
            map.addLayer(cityMarkersCluster);

            //cities markers with wiki summary
            if (map.hasLayer(largeCityCluster)) {
                map.removeLayer(largeCityCluster);
            }
            largeCityCluster = new L.layerGroup();
            map.addLayer(largeCityCluster);

            result.data.largeCities.forEach(largeCity => {
                let cityName = largeCity.fields.name;
                let cityLat =  largeCity.geometry.coordinates[1];
                let cityLng =  largeCity.geometry.coordinates[0];
                let cityInfo = null;
                let cityThumbnailImg;
                let cityUrl;
                let cityText;
                
                result.data.wikiCitiesTextData.forEach(city => {
                        for (let i = 0; i < city.geonames.length; i++) {
                            //console.log(city.geonames[i].title);
                            // if (city.geonames[i].countryCode === borderCountryCode && city.geonames[i].title.includes(cityName) ) {

                            if (city.geonames[i].title == cityName && (city.geonames[i].countryCode === borderCountryCode || city.geonames[i].summary.includes(countryName))) {    
                            //console.log('Code', city.geonames[i].countryCode, 'city', city.geonames[i].title);
                            cityInfo = city.geonames[i].summary;
                            cityThumbnailImg = city.geonames[i].thumbnailImg;
                            cityUrl = city.geonames[i].wikipediaUrl;
                            cityText = 'Read more';
                            };

                            
                        }
                        

                        if (cityInfo === null) {
                            cityInfo = " ";
                            cityThumbnailImg = " ";
                            cityUrl = " ";
                            cityText = " "
                            
                        };

                    

                    var cityIcon;

                    var cityOptions =
                                    {
                                    'maxWidth': '300',
                                    'className' : 'custom'
                                    };        
                    
                    if (cityName.trim() === capitalCityName.trim()) {
                            cityIcon = L.icon({
                            iconUrl: 'assets/img/icons/capital.svg',
                            iconSize: [40, 40],
                            popupAnchor: [0,-15],
                            className: 'cityIcon'
                            });
                    } else {
                            cityIcon = L.icon({
                            iconUrl: 'assets/img/icons/cityscape.svg',
                            iconSize: [40, 40],
                            popupAnchor: [0,-15],
                            className: 'cityIcon'
                            });
                    }

                    var largeCityMarker = L.marker(new L.LatLng(cityLat, cityLng), ({icon: cityIcon})).bindPopup(`<div class="markerContainer"><h3>${cityName}</h3><img class="markerThumbnail" src='${cityThumbnailImg}' onerror="this.style.display='none'"><p class="markerTxtDescription">${cityInfo}</p><div id="city-link"><a href="//${cityUrl}" target="_blank">${cityText}</a></div></div>`, cityOptions).on('click', function(e) {
                        map.flyTo(e.latlng, 10);
                        $.ajax({
                            url: "assets/php/cityMarkers.php",
                            type: 'GET',
                            dataType: 'json',
                            data: {
                                lat: this.getLatLng().lat,
                                lng: this.getLatLng().lng,
                                countryCodeA3: result.data.border.properties.iso_a3
                            },
                        
                            success: function(result) {
                                //console.log('cityMarkers',result);
                                //capital hospital markers
                                result.data.capCityHospitals.items.forEach(hospital => {
                                    var hospitalIcon = L.icon({
                                        iconUrl: 'assets/img/icons/hospital.png',
                                        iconSize: [50, 50],
                                        popupAnchor: [0,-15]
                                        });
                                    hospitalLabel = hospital.address.label;
                                    hospitalLat = hospital.position.lat;
                                    hospitalLng = hospital.position.lng;
                                    
                                    var alreadyExists = false;

                                    var latlng = new L.LatLng(hospitalLat, hospitalLng);

                                    cityMarkersCluster.getLayers().forEach((layer)=>{
                                        if(!alreadyExists && layer instanceof L.Marker && layer.getLatLng().equals(latlng)){
                                        alreadyExists = true;
                                        }
                                    });

                                    if(!alreadyExists){
                                        var hospitalMarker = L.marker(latlng, {
                                        icon: hospitalIcon
                                        }).bindPopup(hospitalLabel);

                                        cityMarkersCluster.addLayer(hospitalMarker);
                                    }

                                });
                                //capital airport markers
                                result.data.capCityAirports.items.forEach(airport => {
                                    var airportIcon = L.icon({
                                        iconUrl: 'assets/img/icons/airport.png',
                                        iconSize: [50, 50],
                                        popupAnchor: [0,-15]
                                        });
                                    airportName = airport.title;
                                    airportLat = airport.position.lat;
                                    airportLng = airport.position.lng;
                                    
                                    var alreadyExists = false;

                                    var latlng = new L.LatLng(airportLat, airportLng);

                                    cityMarkersCluster.getLayers().forEach((layer)=>{
                                        if(!alreadyExists && layer instanceof L.Marker && layer.getLatLng().equals(latlng)){
                                        alreadyExists = true;
                                        }
                                    });

                                    if(!alreadyExists){
                                        var airportMarker = L.marker(latlng, {
                                        icon: airportIcon
                                        }).bindPopup(airportName);

                                        cityMarkersCluster.addLayer(airportMarker);
                                    }


                                });
                                //capital parks markers
                                result.data.capCityParks.items.forEach(park => {
                                    var parkIcon = L.icon({
                                        iconUrl: 'assets/img/icons/park.png',
                                        iconSize: [50, 50],
                                        popupAnchor: [0,-15]
                                        });
                                    parkLabel = park.address.label;
                                    parkLat = park.position.lat;
                                    parkLng = park.position.lng;
                                    
                                    var alreadyExists = false;

                                    var latlng = new L.LatLng(parkLat, parkLng);
                                    
                                    cityMarkersCluster.getLayers().forEach((layer)=>{
                                        if(!alreadyExists && layer instanceof L.Marker && layer.getLatLng().equals(latlng)){
                                        alreadyExists = true;
                                        }
                                    });

                                    if(!alreadyExists){
                                        var parkMarker = L.marker(latlng, {
                                        icon: parkIcon
                                        }).bindPopup(parkLabel);

                                        cityMarkersCluster.addLayer(parkMarker);
                                    }

                                });
                                //capital Museums markers
                                result.data.capCityMuseums.items.forEach(museum => {
                                    var museumIcon = L.icon({
                                        iconUrl: 'assets/img/icons/museum.png',
                                        iconSize: [50, 50],
                                        popupAnchor: [0,-15]
                                        });
                                    museumLabel = museum.address.label;
                                    museumLat = museum.position.lat;
                                    museumLng = museum.position.lng;
                                    
                                    var alreadyExists = false;

                                    var latlng = new L.LatLng(museumLat, museumLng);
                                    
                                    cityMarkersCluster.getLayers().forEach((layer)=>{
                                        if(!alreadyExists && layer instanceof L.Marker && layer.getLatLng().equals(latlng)){
                                        alreadyExists = true;
                                        }
                                    });

                                    // if alreadyExists is true, it is a duplicate
                                    if(!alreadyExists){
                                        var museumMarker = L.marker(latlng, {
                                        icon: museumIcon
                                        }).bindPopup(museumLabel);

                                        cityMarkersCluster.addLayer(museumMarker);
                                    }

                                    
                                });
                                
                                
                                
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                console.log("cityMarkers",textStatus, errorThrown);
                            }
                        }); //end ajax call
                        $.ajax({
                            url: "assets/php/wikiMarkers.php",
                            type: 'GET',
                            dataType: 'json',
                            data: {
                                lat: this.getLatLng().lat,
                                lng: this.getLatLng().lng,
                                countryCodeA2: borderCountryCode,
                            },
                        
                            success: function(result) {
                                //console.log('wikiMarkers',result);

                                if (result.data.wikiCitiesData.hasOwnProperty("status") ) {
                                    console.log('API Server Error, Please click again:', result.data.wikiCitiesData.status.message)
                                } else {
                                    //wiki Find Nearby Places for cities
                                    wikiCluster = new L.markerClusterGroup();
                                                                    
                                    result.data.wikiCitiesData.geonames.forEach(place => {
                                        
                                        var wikiPlaceIcon = L.icon({
                                            iconUrl: 'assets/img/icons/wikipedia.png',
                                            iconSize: [50, 50], // size of the icon
                                            popupAnchor: [0,-15]
                                            });
                                        var customOptions =
                                            {
                                            'maxWidth': '300',
                                            'className' : 'custom'
                                            };
                                            
                                        wikiPlaceName = place.title;
                                        wikiPlaceLat = place.lat;
                                        wikiPlaceLng = place.lng;
                                        wikiSummary = place.summary;
                                        wikiUrl = place.wikipediaUrl;
                                        wikiThumbnail = place.thumbnailImg;
                                        
                                        var customPopup = `<div class="card" style="width: 18rem;"><div class="card-body"><h5 class="card-title">${wikiPlaceName}</h5><img class="img-thumbnail float-right" style="max-width: 100px" src="${wikiThumbnail}" onerror="this.style.display='none'"><p class="card-text" id="wiki-sum">${wikiSummary}</p><a href="//${wikiUrl}" target="_blank"class="card-link">Read more</a><a href="#" class="card-link"></a></div></div>`;
                                        
                                        var alreadyExists = false;

                                        var latlng = new L.LatLng(wikiPlaceLat, wikiPlaceLng);

                                        cityMarkersCluster.getLayers().forEach((layer)=>{
                                            if(!alreadyExists && layer instanceof L.Marker && layer.getLatLng().equals(latlng)){
                                            alreadyExists = true;
                                            }
                                        });

                                        if(!alreadyExists){
                                            var wikiPlaceMarker = L.marker(latlng, {
                                            icon: wikiPlaceIcon
                                            }).bindPopup(customPopup,customOptions);

                                            cityMarkersCluster.addLayer(wikiPlaceMarker);
                                        };

                                    });
                                };

                                
                                
                                
                                
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                console.log("wikiMarkers",textStatus, errorThrown);
                            }
                        }); //end ajax call

                     }); // end on click
    
                    largeCityCluster.addLayer(largeCityMarker);
                    
                    });

                });
                
            }
        
        },

        error: function(jqXHR, textStatus, errorThrown) {
            console.log('Main Call Error',textStatus, errorThrown);
        }

    });
        
//end on change code
});
