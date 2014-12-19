// JavaScript Document

'use strict';


var AUTH_SUCCESS=101;
var UNAUTH_SUCCESS=102;
var AUTH_FAILURE=8003;






var studentCloudApp = angular.module('StudentCloudApp', [
  'ngRoute',
  'studentCloudControllers',
    'ngResource',
    'angular-loading-bar',
    'angularMoment'
]).constant('angularMomentConfig', {
    preprocess: 'unix', // optional
    timezone: 'Europe/London' // optional
});


studentCloudApp.value('API_URL', 'api/v1/?url=');

studentCloudApp.config(['$routeProvider','$httpProvider',
  function($routeProvider,$httpProvider) {
    $routeProvider.
      when('/cloud', {
        templateUrl: 'view/file-list.html'
      }).
      when('/cloud/:cloudDIR', {
        templateUrl: 'partials/file-list.html'
      }).
        when('/', {
            templateUrl: 'view/home.html'
        }).
        when('/home', {

            templateUrl: 'view/home.html'
        }).
        when('/profile', {

            templateUrl: 'view/profile.html'
        }).
        when('/login', {

            templateUrl: 'view/login.html'
        }).
        when('/register', {

            templateUrl: 'view/register.html'
        }).
        when('/terms', {

            templateUrl: 'view/terms.html'
        }).
      otherwise({
            templateUrl:'view/error-404.html'
      });




      $httpProvider.interceptors.push('sessionInjector');

      $httpProvider.interceptors.push('responseHandleInjector');




      //$httpProvider.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";



  }]);

/***************************FILE UPLOAD*********************************/

studentCloudApp.directive('fileModel', ['$parse', function ($parse) {
    return {
        restrict: 'A',
        link: function(scope, element, attrs) {
            var model = $parse(attrs.fileModel);
            var modelSetter = model.assign;

            element.bind('change', function(){
                scope.$apply(function(){
                    modelSetter(scope, element[0].files[0]);
                });

                scope.uploadFile();

            });
        }
    };
}]);

studentCloudApp.service('fileUpload', ['$http','API_URL','$rootScope', function ($http,API_URL,$rootScope) {
    this.uploadFileToUrl = function(file){
        var fd = new FormData();
        fd.append('file', file);
        $http.post(API_URL+"drive/files", fd, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        })
            .success(function(){


                if (isNaN($rootScope.uploadedFileCount)) $rootScope.uploadedFileCount=0;else $rootScope.uploadedFileCount++;


            })
            .error(function(){
                toastr.error("Failed to upload file. Please try again.");
            });
    }
}]);
/***************************#FILE UPLOAD*********************************/


studentCloudApp.factory('DriveFiles',['$resource','API_URL',function($resource,API_URL) {
//	function files($dir_id=-1,$user_id=0,$from=0,$length=20,$shareStatus=ShareStatus::SHARE_PRIVATE) {

    return $resource(API_URL+'drive/files/:dir_id/:file_id/:user_id/:from/:length/:shareStatus', {}, {
    query: {method:'GET', params:null, isArray:true}

});




}]);

studentCloudApp.factory('UserAccount',['$resource','API_URL',function($resource,API_URL) {
//	function files($dir_id=-1,$user_id=0,$from=0,$length=20,$shareStatus=ShareStatus::SHARE_PRIVATE) {

    return $resource(API_URL+'user/account/:user_id', {}, {
        query: {method:'GET', params:null, isArray:true},
        createAccount: {method:'POST'}


    });




}]);

studentCloudApp.factory('AuthorizeToken',['$resource','API_URL',function($resource,API_URL) {
//	function files($dir_id=-1,$user_id=0,$from=0,$length=20,$shareStatus=ShareStatus::SHARE_PRIVATE) {

    return $resource(API_URL+'authorize/token', {}, {
        authenticate: {method:'POST'},
        unauthenticate:{method:'DELETE'}



    });




}]);



studentCloudApp.factory('sessionInjector', ['SessionService', function(SessionService) {
    var sessionInjector = {
        request: function(config) {
            console.log('config:');
            console.log(config);


            var auth_token= SessionService.getUserAuthenticated()?SessionService.getUserAuthenticated():'';



            config.headers['E-Authtoken'] = auth_token;


            return config;
        }
    };
    return sessionInjector;
}]);

studentCloudApp.factory('responseHandleInjector',['$location','$rootScope','SessionService',function($location,$rootScope,SessionService) {


    var responseHandleInjector = {
        response: function(response) {



            if (response.data.r) {

                if (response.data.r.code!="undefined") {

                   // console.log(response.data.r.message);

                    switch (response.data.r.code) {
                        default:
                            toastr.error(response.data.r.message);
                            break;
                        case AUTH_FAILURE:
                            //$rootScope.showLoginForm=true;



                            //toastr[error](response.data.r.message);


                            if ($location.path()!="/login") {
                                window.localStorage.redirectFromLoginPage=$location.path();

                                $location.path('/login').replace();



                            }



                            break;
                        case 1:
                            toastr.success(response.data.r.message);
                            break;
                        case AUTH_SUCCESS:
                            console.log('Auth success!');

                            var redirectTo=(window.localStorage.redirectFromLoginPage&&window.localStorage.redirectFromLoginPage.length>0)?window.localStorage.redirectFromLoginPage:"/home";



                            SessionService.setUserAuthenticated(response.data.r.data.auth_token);

                            console.log('XAUTH:'+SessionService.getUserAuthenticated());


                            toastr.success(response.data.r.message);



                            $location.path(redirectTo).replace();





                            break;

                        case UNAUTH_SUCCESS:

                            toastr.success(response.data.r.message);

                            $location.path('/login').replace();
                            break;
                    }




                }
            }
                return response;
        }
    };
    return responseHandleInjector;
}]);



studentCloudApp.service('apiService',function() {

    this.getMessageResponse=function(apiResponse) {
        return (apiResponse.r.message)?apiResponse.r.message:null;
    };


    this.getDataResponse=function(apiResponse) {

        var data=((apiResponse.r.data)?apiResponse.r.data:[]);


        data=Object.prototype.toString.call(data)=="[object Object]"?[data]:data;


        console.log('data response::::');

        console.log(data);
        return data;
    };


});

studentCloudApp.service('SessionService', function(){

    var userIsAuthenticated = false;

    this.setUserAuthenticated = function(value){
console.log('authorize local:'+value);
        window.localStorage.setItem('userIsAuthenticated',value);
    };

    this.getUserAuthenticated = function() {
        return window.localStorage.getItem('userIsAuthenticated');
    };

    this.clearUserAuthenticated=function() {

        window.localStorage.removeItem('userIsAuthenticated');
    };

});









