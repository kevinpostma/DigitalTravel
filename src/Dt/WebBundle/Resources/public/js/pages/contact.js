var contactApp = angular.module('contactApp', ['ngAnimate']);

contactApp.controller('contactController', function($scope) {
    
    $scope.errors = new Array();
    
    $scope.addError = function(message) {
        $scope.errors.push(message);
        
        $timeout(function () {
            $scope.errors.pop();
        }, 500);
    };
    
});

contactApp.controller('formController', function($scope){
    
    $scope.form = {
        firstname : '',
        lastname : '',
        email : '',
        text : ''
    };
    
    $scope.submitting = false;
    
    $scope.submit = function() {
        $scope.submitting = true;
        
        console.debug($scope.form.text);
        if($scope.form.text.length < 10) {
            $scope.$parent.addError('Het bericht dat je hebt ingetypt is te kort, Dit moet minimaal 10 tekens zijn!');
        }
        
        if($scope.$parent.errors.length == 0) {
            $timeout(function () {
                $scope.submitting = false;
            }, 500);
        }
        return false;
    }
});

contactApp.controller('faqController', function($scope, $timeout) {

    $scope.faqs = [];
    
    $scope.init = function() {
        for (var i = 0; i < 7; i++) {
            $timeout(function () {
                $scope.faqs.push(
                    {
                        title : "Hoe doe ik dit?",
                        content : "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas non nibh vel purus commodo tincidunt. Ut ante quam, varius ullamcorper leo ac, venenatis pulvinar lectus. Donec ornare lacus et vestibulum auctor. Nunc pellentesque nibh sit amet dui auctor lacinia. Fusce luctus euismod augue, nec tempus velit scelerisque nec. Sed semper tortor quis enim imperdiet fermentum. Cras consequat congue massa, ut imperdiet mi fringilla et.",
                    }        
                );
            }, 100 * i);
        };		
    };
});