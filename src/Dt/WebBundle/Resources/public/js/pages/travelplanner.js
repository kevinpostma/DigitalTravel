var contactApp = angular.module('travelplannerApp', ['ngAnimate', 'ui.bootstrap', 'google-maps']);

contactApp.controller('travelplannerController', function($scope, $timeout, $http) {

    $scope.errors = new Array();
    $scope.locations = new Array();
    $scope.pois = new Array();
    $scope.routePois = new Array();
    $scope.route = null;
    $scope.calculatingRoute = false;
    
    $scope.init = function() {
        $scope.getLocations();
        $scope.getPois();
    }
    
    $scope.calculateRoute = function (from_location, to_location, filters) {
        $scope.calculatingRoute = true;
        var url = Routing.generate('dt_api_route', {'startLocation' : from_location.id, 'endLocation' : to_location.id});
        $http({method: 'POST', url: url, data : {'filters' : filters}}).success(function(data, status, headers, config) {
            $scope.route = data;
            $scope.$broadcast('routeChange',$scope.route);
        });
    }
    
    $scope.$on('calculateRoute', function(event, data){    
        var from = null;
        var to = null;
        $.each($scope.locations,function(key, val){
            if(val.city == data.from) {
                from = $scope.locations[key];
            }
            if(val.city == data.to) {
                to = $scope.locations[key];
            }
        });
        
        $scope.calculateRoute(from, to, data.filters);
    });
    
    $scope.getLocations = function() {
        var url = Routing.generate('dt_api_locations', {});
        $http({method: 'GET', url: url}).success(function(data, status, headers, config) {        
            $scope.locations = data;
            $scope.$broadcast('locationsChange',$scope.locations);
        });
    }
    
    $scope.getPois = function() {
        var url = Routing.generate('dt_api_pois', {offset: 0, limit: 0});
        $http({method: 'GET', url: url}).success(function(data, status, headers, config) {        
            $scope.pois = data.items;
            $scope.$broadcast('poisChange',$scope.pois);
        });
    }

    $scope.addError = function(message) {
        $scope.errors.push(message);

        $timeout(function() {
            $scope.errors.pop();
        }, 5000);
    };

});

contactApp.controller('mapsController', function($scope, $timeout, $http) {

    $scope.init = function () {
        
    }
    
    $scope.$on('routeChange', function(event, data){


        $.each($scope.polyline.path, function() {
            $scope.polyline.path.pop();
        });
            
        $.each(data, function(key ,val ){ 
            var Marker = {
                "coords" : {
                    "latitude" : val.latitude,
                    "longitude" : val.longitude
                },
                "showWindow" : true,
                "title" : val.name
            };
            
            $.each($scope.markers.models, function (key, val){
                if($scope.markers.models.length > 1) {
                    $scope.markers.models.shift();
                }
            });
            
            $scope.markers.models.push(Marker);
            $scope.polyline.path.push({ latitude : Marker.coords.latitude, longitude : Marker.coords.longitude});
        });
        
        $scope.map.refresh = true;
        $scope.map.refresh = false;
        $scope.$parent.calculatingRoute = false;
    });
    
    $scope.markers = {
        models : []
    }
    
    $scope.polyline = {
        visible: true,
        editable: false,
        draggable: false,
        geodesic : true,
        stroke : {
            weight: 3,
            color: '#6060FB'
        },
        path : [
            { latitude: 32.00, longitude: -89.00 },
            { latitude: 32.00, longitude: -89.00 },
        ]
    }
    
    $scope.map = {
        center: {
            latitude: 45,
            longitude: -73
        },
        zoom: 2,
        refresh : false,
    };


});


contactApp.controller('poiController', function($scope, $timeout, $http) {
    
    $scope.getPois = function(regionid) {
        var url = Routing.generate('dt_api_pois_by_region', {region : regionid});
        $http({method: 'GET', url: url}).success(function(data, status, headers, config) {
            
            
            
            
            
            $.each(data, function(key, val ){
                
                var found = false;
                
                $.each($scope.$parent.routePois, function(key1, val1){
                    if(val.id == val1.id) {
                        found = true;
                    }
                });
                
                if(found == false) {
                    $scope.$parent.routePois.push(val);
                }
                
            });
            
        });
    }
    
    $scope.$on('routeChange', function(event, data){
        $.each(data, function(key, val){
            $scope.getPois(val.region.id);
        });
    });
    
    $scope.init = function () {
        
    }
    
    $scope.clearPois = function() {
        $.each($scope.pois, function(key, val) {
            $scope.$parent.routePois.pop();
        });
    }
});


contactApp.controller('filterController', function($scope, $timeout, $http) {

    $scope.filters = [];
    
    $scope.from_location = undefined;
    $scope.to_location = undefined;
    
    $scope.locations = new Array();
    $scope.pois = new Array();
    
    $scope.$on('locationsChange', function(event, data){    
        if($scope.$parent.locations instanceof Array){
            $.each($scope.$parent.locations, function(key, val){
                $scope.locations[val.id] = val.city;
            });
        }
    });
    
    $scope.$on('poisChange', function(event, data){
        if($scope.$parent.pois instanceof Array){
            $.each($scope.$parent.pois, function(key, val){
                $scope.pois[val.id] = val.name;
            });
        }
    });
    
    $scope.calculateRoute = function () {
        $scope.$emit('calculateRoute',{'from' : $scope.from_location, 'to' : $scope.to_location, 'filters' : $scope.filters});
    }
    
    $scope.addFilter = function() {
         $scope.filters.push({
             'type' : 'locations',
             'value' : 'Amsterdam',
             'method' : 'en',
         });
     };
    
    
    $scope.init = function () {
    }
    
    
    
});