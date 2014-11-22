// JavaScript Document
'use strict';

/* Controllers */

var studentCloudControllers = angular.module('studentCloudControllers', []);

studentCloudControllers.controller('FileListCtrl', ['$scope', '$http',
  function($scope, $http) {

    $http.get('http://www.w3schools.com/website/Customers_JSON.php').success(function(data) {
      $scope.customers = data;


    });

    $scope.orderProp = 'age';
  }]);

studentCloudControllers.controller('PhoneDetailCtrl', ['$scope', '$routeParams',
  function($scope, $routeParams) {
    $scope.phoneId = $routeParams.phoneId;
  }]);
