// declare a new module called "myApp", and make it require the `ng-admin` module as a dependency
var myApp = angular.module("myApp", ["ng-admin"]);

// declare a function to run when the module bootstraps (during the "config" phase)
var apiFlavor = require("./api_flavor");
myApp.config(["RestangularProvider",apiFlavor.requestInterceptor]);
myApp.config(["RestangularProvider",apiFlavor.responseInterceptor]);

// configure directive
myApp.directive("toggleApiUser", require("./api_users/toggleApiUser"));
myApp.directive("notifySubscriber", require("./events/notifySubscriber"));
myApp.directive("dashboardSummary", require("./dashboard/dashboardSummary"));

// add notify subscriber state page
myApp.config(["$stateProvider", require("./events/notifySubscriberState")]);

// custom translation reference
myApp.config(["$translateProvider", function($translateProvider){
    $translateProvider.translations("en",{
        SEND_NOTIFICATION: "Send Notification",
    });
}]);

// custom controllers
myApp.controller("loginuser", ["$scope", "$window", function($scope, $window) { // used in header.html
    $scope.apiKey =  $window.localStorage.getItem("kw_api_key_store");
}]);

var headerTemplate = "<div class=\"navbar-header\">"+
    "<button type=\"button\" class=\"navbar-toggle\" ng-click=\"isCollapsed = !isCollapsed\">"+
        "<span class=\"icon-bar\"></span>"+
        "<span class=\"icon-bar\"></span>"+
        "<span class=\"icon-bar\"></span>"+
    "</button>"+
    "<a class=\"navbar-brand\" href=\"#\" ng-click=\"appController.displayHome()\">KW API</a>"+
"</div>"+
"<ul class=\"nav navbar-top-links navbar-right hidden-xs\">"+
    "<li uib-dropdown>"+
        "<a uib-dropdown-toggle href=\"#\" aria-expanded=\"true\" ng-controller=\"loginuser\">"+
            "<i class=\"fa fa-user fa-lg\"></i>&nbsp;{{ apiKey }}&nbsp;<i class=\"fa fa-caret-down\"></i>"+
        "</a>"+
        "<ul class=\"dropdown-menu dropdown-user\" role=\"menu\">"+
            "<li><a href=\"#\" onclick=\"logout()\"><i class=\"fa fa-sign-out fa-fw\"></i> Logout</a></li>"+
        "</ul>"+
    "</li></ul>";

myApp.config(["NgAdminConfigurationProvider", function (nga) {
    // create an admin application
    var admin = nga.application("KW API")
        .baseApiUrl("/v1/");

    // add entities
    admin.addEntity(nga.entity("api_users"));
    admin.addEntity(nga.entity("events"));
    admin.addEntity(nga.entity("subscribers"));

    // configure entities
    require("./api_users/config")(nga, admin);
    require("./events/config")(nga, admin);
    require("./subscribers/config")(nga, admin);

    // dashboard custom
    admin.dashboard(require("./dashboard/config")(nga, admin));

    // header custom
    admin.header(headerTemplate);

    /////////////////
    // menu custom //
    /////////////////
    admin.menu(nga.menu()
        .addChild(nga.menu(admin.getEntity("api_users"))
            .icon("<span class=\"glyphicon glyphicon-user\"></span>"))
        .addChild(nga.menu(admin.getEntity("events"))
            .icon("<span class=\"glyphicon glyphicon-bullhorn\"></span>"))
        .addChild(nga.menu(admin.getEntity("subscribers"))
            .icon("<span class=\"glyphicon glyphicon-envelope\"></span>"))
        .addChild(nga.menu()
            .title("Statistics")
            .icon("<span class=\"glyphicon glyphicon-stats\"></span>")
            .addChild(nga.menu()
                .title("Queue Stats")
                .icon("<span class=\"glyphicon glyphicon-stats\"></span>")
                .link("/stats/queues")
                .active(function(path){
                    return path.indexOf("/stats/queues") === 0;
                })
            )
            .addChild(nga.menu()
                .title("Event Stats")
                .icon("<span class=\"glyphicon glyphicon-stats\"></span>")
                .link("/stats/events")
                .active(function(path){
                    return path.indexOf("/stats/events") === 0;
                })
            )
            .addChild(nga.menu()
                .title("API Stats")
                .icon("<span class=\"glyphicon glyphicon-stats\"></span>")
                .link("/stats/api")
                .active(function(path){
                    return path.indexOf("/stats/api") === 0;
                })
            )
        )
    );

    // attach the admin application to the DOM and execute it
    nga.configure(admin);
}]);