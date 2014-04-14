var contactApp = angular.module('travelplannerApp', ['ngAnimate', 'ui.bootstrap', 'google-maps']);

contactApp.controller('travelplannerController', function($scope, $timeout, $http) {

    $scope.errors = new Array();
    $scope.locations = null;
    $scope.route = null;
    
    $scope.init = function() {
        $scope.getLocations();
    }
    
    $scope.calculateRoute = function (from_location, to_location) {
        var url = Routing.generate('dt_api_route', {'startLocation' : from_location.id, 'endLocation' : to_location.id});
        $http({method: 'GET', url: url}).success(function(data, status, headers, config) {
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
        
        $scope.calculateRoute(from, to);
    });
    
    $scope.getLocations = function() {
        var url = Routing.generate('dt_api_locations', {});
        $http({method: 'GET', url: url}).success(function(data, status, headers, config) {
            $scope.locations = data;
            $scope.$broadcast('locationsChange',$scope.locations);
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

        $.each(data, function(key ,val ){ 
            var Marker = {
                "latitude" : val.latitude,
                "longitude" : val.longitude,
                "showWindow" : true,
                "title" : val.name
            }
            $scope.markers.models.push(Marker);
            $scope.polyline.path = [];
            $scope.polyline.path.push({ latitude : Marker.latitude, longitude : Marker.longitude});
        });
        
        console.debug($scope.markers.models);
        console.debug('refresh');
        $scope.map.refresh = true;
        $scope.map.refresh = false;
    });
    
    $scope.markers = {
        models : new Array()
    }
    
    $scope.polyline = {
        visible: true,
        editable: true,
        draggable: true,
        geodesic : true,
        stroke : {
            weight: 3,
            color: '#6060FB'
        },
        path : [
            { latitude: 32.00, longitude: -89.00 },
            { latitude: 39.00, longitude: -95.00 },
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

contactApp.controller('filterController', function($scope, $timeout, $http) {

    $scope.filters = [];
    
    $scope.from_location = undefined;
    $scope.to_location = undefined;
    
    $scope.locations = new Array();
    $scope.pois = ['Eiffeltoren', 'Madame Tusseaud', 'Berlijnse muur', 'Notre dame', 'Vrijheidsbeeld'];
    
    $scope.$on('locationsChange', function(event, data){    
        if($scope.$parent.locations instanceof Array){
            $.each($scope.$parent.locations, function(key, val){
                $scope.locations[val.id] = val.city;
            });
        }
    });
    
    $scope.calculateRoute = function () {
        $scope.$emit('calculateRoute',{'from' : $scope.from_location, 'to' : $scope.to_location});
    }
    
    $scope.init = function () {
    }
    
    
    
});