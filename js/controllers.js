// JavaScript Document
'use strict';

/* Controllers */

var studentCloudControllers = angular.module('studentCloudControllers', []);

studentCloudControllers.controller('FileListCtrl', ['$rootScope','$scope', 'DriveFiles','apiService','fileUpload',
  function($rootScope,$scope, DriveFiles,apiService,fileUpload) {

    $scope.loadFileList=function() {


      DriveFiles.get({dir_id: -1}, function (response) {

        $scope.fileList = apiService.getDataResponse(response);

        jQuery('html,body').animate({

          scrollTop: 0
        }, 1000);



      });
    };

    $scope.$watch(function($rootScope) {


      return $rootScope.uploadedFileCount;
    },function(newValue) {

      console.log('upload count:'+newValue);
      $scope.loadFileList();
    });



    $scope.getFriendlySize=function(bytes) {
      if(bytes == 0) return '0 Byte';
      var k = 1000;
      var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
      var i = Math.floor(Math.log(bytes) / Math.log(k));
      return (bytes / Math.pow(k, i)).toPrecision(3) + ' ' + sizes[i];
    }
    $scope.getDocType=function(file_ext) {
      var ft;
      switch (file_ext) {

        default:
          ft='file';
          break;
        case 'jpg':
        case 'gif':
        case 'png':
        case 'bmp':
              ft='image';
        break;

        case 'ppt':
              ft='powerpoint';
        break;
        case 'doc':
        case 'docx':
        case 'txt':
              ft='doc';
          break;

        case 'xls':
              ft='excel';
          break;









      }

      return ft;
    };



    $scope.uploadFile = function(){
      var file = $scope.myFile;
      console.log('file is ' + JSON.stringify(file));

      fileUpload.uploadFileToUrl(file);

    };


    $scope.getDirectoryPath=function(fileEntry) {

        return "api/v1/s/"+fileEntry.user_id+"/"+fileEntry.file_id+"."+fileEntry.file_ext;


    };

  }]);

studentCloudControllers.controller('LoginCtrl', ['$scope', '$rootScope','AuthorizeToken','SessionService',
  function($scope, $rootScope,AuthorizeToken,SessionService) {


    SessionService.clearUserAuthenticated();


    $scope.authenticate=function(user) {


        AuthorizeToken.authenticate(user);



    };


  }]);
studentCloudControllers.controller('RegisterCtrl', ['$scope', '$rootScope','UserAccount','apiService',
  function($scope, $rootScope,UserAccount, apiService) {

    $scope.registerUserAccount=function(user) {


      UserAccount.createAccount(user);



    };



  }]);

studentCloudControllers.controller('ProfileCtrl', ['$scope', '$rootScope','UserAccount','apiService','AuthorizeToken','SessionService',
  function($scope, $rootScope,UserAccount,apiService,AuthorizeToken,SessionService) {




      UserAccount.get({user_id: -1}, function (response) {

        $scope.uad = apiService.getDataResponse(response)[0];





      });


    $scope.logout=function() {

      AuthorizeToken.unauthenticate();

    };


  }]);
