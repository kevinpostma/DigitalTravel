var contactApp = angular.module('contactApp', ['ngAnimate', 'ui.bootstrap']);

contactApp.controller('contactController', function($scope, $timeout) {

    $scope.errors = new Array();

    $scope.addError = function(message) {
        $scope.errors.push(message);

        $timeout(function() {
            $scope.errors.pop();
        }, 5000);
    };

});

contactApp.controller('listController', function($scope, $timeout, $http) {

    $scope.maxRate = 5;
    $scope.maxPerPage = 4;
    $scope.currentPage = 1;
    $scope.totalItems = 1;
    $scope.pois = [];

    $scope.getItems = function(start, limit) {
        $scope.pois = [];
        var url = Routing.generate('dt_web_point_of_interests_pagination', {start: start, limit: limit});
        $timeout(function () {
            $http({method: 'GET', url: url}).success(function(data, status, headers, config) {
                $scope.totalItems = data.total;
                for (var i = 0; i < data.items.length; i++) {
                    var iterator = 0;
                    $timeout(function () {
                        $scope.pois.push(data.items[iterator]);
                        iterator++;
                    }, 100 * i);
                };
            })
        }, 1500);
    }
    
    $scope.$watch("currentPage", function(newValue, oldValue){
        $scope.getItems((newValue -1) * $scope.maxPerPage, $scope.maxPerPage);
    });

    $scope.init = function() {
        $scope.getItems(0, $scope.maxPerPage)
    }



});

contactApp.controller('filterController', function($scope, $timeout, $http) {

    $scope.filters = [];
    
    $scope.from_location = undefined;
    $scope.to_location = undefined;
    
    $scope.locations = ['Parijs', 'Amsterdam', 'New York', 'Berlijn', 'Miami', 'Kiev', 'Groningen', 'Rio'];
    $scope.pois = ['Eiffeltoren', 'Madame Tusseaud', 'Berlijnse muur', 'Notre dame', 'Vrijheidsbeeld'];
    
    $scope.addFilter = function() {
        $scope.filters.push({
            'type' : 'locations',
            'value' : 'Amsterdam',
            'method' : 'en',
        });
    };
    
});