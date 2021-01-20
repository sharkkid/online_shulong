// Angular application declaration 

var app = angular.module("directoryApp", ['ngRoute']);
// retrive server data

app.config(['$routeProvider',
	function($routeProvider) {
	   $routeProvider.
		  when('/', {
			templateUrl: 'templates/record.html',
			controller: 'recordCtrl'
		  }).
		  otherwise({
			redirectTo: '/',
			controller: 'recordCtrl'
		  });
}]);
// Putting record into servies to provide access to controllers
app.factory('records', function($http) {
  return{
    getRecords:function(callback){
		$http.get("http://private-1f087-directoryapi.apiary-mock.com/directoryapi/")
   		.success(callback);
  	}
  }
});


