// JavaScript Document

'use strict';

/* App Module */

var loadIndicator=$('#loadIndicator').get();


var studentCloudApp = angular.module('StudentCloudApp', [
  'ngRoute',
  'studentCloudControllers',
    'ngResource',
    'angular-loading-bar'
]);

studentCloudApp.config(['$routeProvider','$httpProvider',
  function($routeProvider,$httpProvider) {
    $routeProvider.
      when('/cloud', {
        templateUrl: 'view/file-list.html'
      }).
      when('/phones/:phoneId', {
        templateUrl: 'partials/phone-detail.html',
        controller: 'PhoneDetailCtrl'
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
      otherwise({
            templateUrl:'view/error-404.html'
      });












      $httpProvider.interceptors.push(function($q) {

          return {
              'request': function(config) {
                  $(loadIndicator).show();
                  console.log('req');
                  return config || $q.when(config);
              },

              'response': function(response) {
                  $(loadIndicator).hide();
                  console.log('respon');
                  return response || $q.when(response);
              }
          };
      });





  }]);




