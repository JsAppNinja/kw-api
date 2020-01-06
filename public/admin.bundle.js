(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
"use strict";

// declare a new module called "myApp", and make it require the `ng-admin` module as a dependency
var myApp = angular.module("myApp", ["ng-admin"]);

// declare a function to run when the module bootstraps (during the "config" phase)
var apiFlavor = require("./api_flavor");
myApp.config(["RestangularProvider", apiFlavor.requestInterceptor]);
myApp.config(["RestangularProvider", apiFlavor.responseInterceptor]);

// configure directive
myApp.directive("toggleApiUser", require("./api_users/toggleApiUser"));
myApp.directive("notifySubscriber", require("./events/notifySubscriber"));
myApp.directive("dashboardSummary", require("./dashboard/dashboardSummary"));

// add notify subscriber state page
myApp.config(["$stateProvider", require("./events/notifySubscriberState")]);

// custom translation reference
myApp.config(["$translateProvider", function ($translateProvider) {
    $translateProvider.translations("en", {
        SEND_NOTIFICATION: "Send Notification"
    });
}]);

// custom controllers
myApp.controller("loginuser", ["$scope", "$window", function ($scope, $window) {
    // used in header.html
    $scope.apiKey = $window.localStorage.getItem("kw_api_key_store");
}]);

var headerTemplate = "<div class=\"navbar-header\">" + "<button type=\"button\" class=\"navbar-toggle\" ng-click=\"isCollapsed = !isCollapsed\">" + "<span class=\"icon-bar\"></span>" + "<span class=\"icon-bar\"></span>" + "<span class=\"icon-bar\"></span>" + "</button>" + "<a class=\"navbar-brand\" href=\"#\" ng-click=\"appController.displayHome()\">KW API</a>" + "</div>" + "<ul class=\"nav navbar-top-links navbar-right hidden-xs\">" + "<li uib-dropdown>" + "<a uib-dropdown-toggle href=\"#\" aria-expanded=\"true\" ng-controller=\"loginuser\">" + "<i class=\"fa fa-user fa-lg\"></i>&nbsp;{{ apiKey }}&nbsp;<i class=\"fa fa-caret-down\"></i>" + "</a>" + "<ul class=\"dropdown-menu dropdown-user\" role=\"menu\">" + "<li><a href=\"#\" onclick=\"logout()\"><i class=\"fa fa-sign-out fa-fw\"></i> Logout</a></li>" + "</ul>" + "</li></ul>";

myApp.config(["NgAdminConfigurationProvider", function (nga) {
    // create an admin application
    var admin = nga.application("KW API").baseApiUrl("/v1/");

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
    admin.menu(nga.menu().addChild(nga.menu(admin.getEntity("api_users")).icon("<span class=\"glyphicon glyphicon-user\"></span>")).addChild(nga.menu(admin.getEntity("events")).icon("<span class=\"glyphicon glyphicon-bullhorn\"></span>")).addChild(nga.menu(admin.getEntity("subscribers")).icon("<span class=\"glyphicon glyphicon-envelope\"></span>")).addChild(nga.menu().title("Statistics").icon("<span class=\"glyphicon glyphicon-stats\"></span>").addChild(nga.menu().title("Queue Stats").icon("<span class=\"glyphicon glyphicon-stats\"></span>").link("/stats/queues").active(function (path) {
        return path.indexOf("/stats/queues") === 0;
    })).addChild(nga.menu().title("Event Stats").icon("<span class=\"glyphicon glyphicon-stats\"></span>").link("/stats/events").active(function (path) {
        return path.indexOf("/stats/events") === 0;
    })).addChild(nga.menu().title("API Stats").icon("<span class=\"glyphicon glyphicon-stats\"></span>").link("/stats/api").active(function (path) {
        return path.indexOf("/stats/api") === 0;
    }))));

    // attach the admin application to the DOM and execute it
    nga.configure(admin);
}]);

},{"./api_flavor":2,"./api_users/config":3,"./api_users/toggleApiUser":4,"./dashboard/config":5,"./dashboard/dashboardSummary":6,"./events/config":7,"./events/notifySubscriber":8,"./events/notifySubscriberState":10,"./subscribers/config":11}],2:[function(require,module,exports){
"use strict";

function requestInterceptor(RestangularProvider) {
    // use the custom query parameters function to format the API request correctly
    RestangularProvider.addFullRequestInterceptor(function (element, operation, what, url, headers, params, httpConfig) {
        if (operation == "getList") {
            params.page = params._page; // change page variable
            delete params._page;
        }
        headers["apiKey"] = window.localStorage.getItem("kw_api_key_store");
        return { params: params, headers: headers };
    });
};

function responseInterceptor(RestangularProvider) {
    RestangularProvider.addResponseInterceptor(function (data, operation, what, url, response, deffered) {
        var extractedData = data;
        if (operation === "getList") {
            extractedData = data.data;
            response.totalCount = data.total; //set totalCount manually
        }
        return extractedData;
    });
};

module.exports = { requestInterceptor: requestInterceptor, responseInterceptor: responseInterceptor };

},{}],3:[function(require,module,exports){
"use strict";

module.exports = function (nga, admin) {
    var apiUsers = admin.getEntity("api_users");

    apiUsers.listView().fields([nga.field("id"), nga.field("apiKey"), nga.field("company"), nga.field("application"), nga.field("email")]).title("Api Users List").perPage(10).listActions([
    //"<toggle-api-user size=\"xs\" api-user=\"::entry\"></toggle-api-user>",
    "show", "edit"]).filters([nga.field("apiKey"), nga.field("company")]);

    apiUsers.showView().fields([nga.field("id"), nga.field("apiKey"), nga.field("company"), nga.field("application"), nga.field("email"), nga.field("isActive"), nga.field("created_at"), nga.field("updated_at"), nga.field("events", "referenced_list") // display list of related events
    .targetEntity(nga.entity("events")).targetReferenceField("apiUser").targetFields([nga.field("id").isDetailLink(true), nga.field("object"), nga.field("action"), nga.field("version"), nga.field("created_at")]).sortField("created_at").sortDir("desc").listActions(["show", "edit"]).label("Latest Events"), nga.field("", "template").label("").template("<ma-filtered-list-button entity-name=\"events\" filter=\"{ apiUser: entry.values.id }\" size=\"sm\"></ma-filtered-list-button>")]).title("Api User").actions(["<ma-filtered-list-button entity-name=\"events\" filter=\"{ apiUser: entry.values.id }\" label=\"Events\"></ma-filtered-list-button>", "<toggle-api-user api-user=\"::entry\"></toggle-api-user>", "list"]);

    apiUsers.creationView().fields([nga.field("apiKey").validation({ required: true, minlength: 3, maxlength: 255 }), nga.field("company").validation({ required: true, minlength: 3, maxlength: 255 }), nga.field("application").validation({ required: true, minlength: 3, maxlength: 255 }), nga.field("email", "email").validation({ required: true, maxlength: 255 })]).title("Api User").actions(["list"]);

    apiUsers.editionView().fields([nga.field("apiKey").validation({ required: true, minlength: 3, maxlength: 255 }), nga.field("company").validation({ required: true, minlength: 3, maxlength: 255 }), nga.field("application").validation({ required: true, minlength: 3, maxlength: 255 }), nga.field("email", "email").validation({ required: true, maxlength: 255 }), nga.field("isActive").editable(false), nga.field("events", "referenced_list") // display list of related events
    .targetEntity(nga.entity("events")).targetReferenceField("apiUser").targetFields([nga.field("id").isDetailLink(true), nga.field("object"), nga.field("action"), nga.field("version"), nga.field("created_at")]).sortField("created_at").sortDir("desc").listActions(["show", "edit"]).label("Latest Events"), nga.field("", "template").label("").template("<ma-filtered-list-button entity-name=\"events\" filter=\"{ apiUser: entry.values.id }\" size=\"sm\"></ma-filtered-list-button>")]).title("Api User").actions(["<ma-filtered-list-button entity-name=\"events\" filter=\"{ apiUser: entry.values.id }\" label=\"Events\"></ma-filtered-list-button>", "<toggle-api-user api-user=\"::entry\"></toggle-api-user>", "list"]);

    apiUsers.editionView().onSubmitError(["error", "form", "progression", "notification", function (error, form, progression, notification) {
        // stop the progress bar
        progression.done();
        // add a notification
        notification.log("error " + error.data.code + ": " + error.data.message, { addnCls: "humane-flatty-error" });
        // cancel the default action (default error messages)
        return false;
    }]);

    apiUsers.creationView().onSubmitError(apiUsers.editionView().onSubmitError());

    return apiUsers;
};

},{}],4:[function(require,module,exports){
"use strict";

function toggleApiUser(Restangular, $state, notification) {
    "use strict";

    return {
        restrict: "E",
        scope: {
            apiUser: "&",
            size: "@"
        },
        link: function link(scope, element, attrs) {
            scope.apiUser = scope.apiUser();
            scope.type = attrs.type;
            scope.toggle = function () {
                Restangular.one("api_users", scope.apiUser.values.id).get().then(function (apiUser) {
                    return apiUser.data.customGET("toggle");
                }).then(function () {
                    return $state.reload();
                }).then(function () {
                    return notification.log("Toggle Api User Done", { addnCls: "humane-flatty-success" });
                }).catch(function (e) {
                    return notification.log("A problem occurred, please try again", { addnCls: "humane-flatty-error" });
                });
            };
        },

        template: " <a ng-if=\"apiUser.values.isActive == 0\" class=\"btn btn-outline btn-success\" ng-class=\"size ? 'btn-' + size : ''\" ng-click=\"toggle()\">\n    <span class=\"glyphicon glyphicon-thumbs-up\" aria-hidden=\"true\"></span>&nbsp;Enable\n</a>\n<a ng-if=\"apiUser.values.isActive == 1\" class=\"btn btn-outline btn-danger\" ng-class=\"size ? 'btn-' + size : ''\" ng-click=\"toggle()\">\n    <span class=\"glyphicon glyphicon-thumbs-down\" aria-hidden=\"true\"></span>&nbsp;Disable\n</a>"
    };
};

toggleApiUser.$inject = ["Restangular", "$state", "notification"];

exports["default"] = toggleApiUser;
module.exports = exports["default"];

},{}],5:[function(require,module,exports){
"use strict";

module.exports = function (nga, admin) {
    return nga.dashboard().addCollection(nga.collection(admin.getEntity("api_users")).name("latest_api_users").title("Latest ApiUsers").fields([nga.field("id"), nga.field("apiKey"), nga.field("company")]).sortField("id").sortDir("desc").perPage(5)).addCollection(nga.collection(admin.getEntity("events")).name("latest_events").title("Latest Events").fields([nga.field("id"), nga.field("object"), nga.field("action"), nga.field("version")]).sortField("id").sortDir("desc").perPage(5)).addCollection(nga.collection(admin.getEntity("subscribers")).name("latest_subscribers").title("Latest Subscribers").fields([nga.field("id"), nga.field("event_id", "reference").targetEntity(admin.getEntity("events")).targetField(nga.field("id")).label("Event"), nga.field("api_user_id", "reference").targetEntity(admin.getEntity("api_users")).targetField(nga.field("company")).label("Api User Company")]).sortField("id").sortDir("desc").perPage(5)).template("\n<div class=\"row dashboard-starter\"></div>\n<dashboard-summary></dashboard-summary>\n<div class=\"row dashboard-content\">\n    <div class=\"col-lg-6\">\n        <div class=\"panel panel-green\">\n            <ma-dashboard-panel collection=\"dashboardController.collections.latest_api_users\" entries=\"dashboardController.entries.latest_api_users\" datastore=\"dashboardController.datastore\"></ma-dashboard-panel>\n        </div>\n        <div class=\"panel panel-yellow\">\n            <ma-dashboard-panel collection=\"dashboardController.collections.latest_events\" entries=\"dashboardController.entries.latest_events\" datastore=\"dashboardController.datastore\"></ma-dashboard-panel>\n        </div>\n    </div>\n    <div class=\"col-lg-6\">\n        <div class=\"panel panel-default\">\n            <ma-dashboard-panel collection=\"dashboardController.collections.latest_subscribers\" entries=\"dashboardController.entries.latest_subscribers\" datastore=\"dashboardController.datastore\"></ma-dashboard-panel>\n        </div>\n    </div>\n</div>\n");
};

},{}],6:[function(require,module,exports){
"use strict";

var hasSeenAlert = false;
var dashboardSummaryTemplate = "\n<div class=\"row\">\n    <div class=\"col-lg-12\">\n        <uib-alert type=\"info\" close=\"dismissAlert()\" ng-show=\"!hasSeenAlert\">\n            Welcome to the KW-API.\n        </uib-alert>\n    </div>\n</div>\n<div class=\"row\">\n    <div class=\"col-lg-3\">\n        <div class=\"panel panel-primary\">\n            <div class=\"panel-heading\">\n                <div class=\"row\">\n                    <div class=\"col-xs-3\">\n                        <i class=\"fa fa-tasks fa-5x\"></i>\n                    </div>\n                    <div class=\"col-xs-9 text-right\">\n                        <div class=\"huge\">{{mqStats.queue_totals.messages}}</div>\n                        <div>Queue, {{mqStats.queue_totals.messages_details.rate}}/s rate.</div>\n                    </div>\n                </div>\n            </div>\n            <a href=\"#/stats/queues\">\n                <div class=\"panel-footer\">\n                    <span class=\"pull-left\">View Details</span>\n                    <span class=\"pull-right\"><i class=\"fa fa-arrow-circle-right\"></i></span>\n                    <div class=\"clearfix\"></div>\n                </div>\n            </a>\n\n        </div>\n    </div>\n    <div class=\"col-lg-3\">\n        <div class=\"panel panel-green\">\n            <div class=\"panel-heading\">\n                <div class=\"row\">\n                    <div class=\"col-xs-3\">\n                        <i class=\"fa fa-download fa-5x\"></i>\n                    </div>\n                    <div class=\"col-xs-9 text-right\">\n                        <div class=\"huge\">{{mqStats.message_stats.deliver_get_details.rate}}/s</div>\n                        <div>Deliver rate</div>\n                    </div>\n                </div>\n            </div>\n            <a href=\"#/stats/queues\">\n                <div class=\"panel-footer\">\n                    <span class=\"pull-left\">View Details</span>\n                    <span class=\"pull-right\"><i class=\"fa fa-arrow-circle-right\"></i></span>\n                    <div class=\"clearfix\"></div>\n                </div>\n            </a>\n        </div>\n    </div>\n    <div class=\"col-lg-3\">\n        <div class=\"panel panel-yellow\">\n            <div class=\"panel-heading\">\n                <div class=\"row\">\n                    <div class=\"col-xs-3\">\n                        <i class=\"fa fa-bullhorn fa-5x\"></i>\n                    </div>\n                    <div class=\"col-xs-9 text-right\">\n                        <div class=\"huge\">{{eventStats.totalCount}}</div>\n                        <div>Number of Events</div>\n                    </div>\n                </div>\n            </div>\n            <a ui-sref=\"list({entity:'events'})\">\n                <div class=\"panel-footer\">\n                    <span class=\"pull-left\">View Details</span>\n                    <span class=\"pull-right\"><i class=\"fa fa-arrow-circle-right\"></i></span>\n                    <div class=\"clearfix\"></div>\n                </div>\n            </a>\n        </div>\n    </div>\n    <div class=\"col-lg-3\">\n        <div class=\"panel panel-red\">\n            <div class=\"panel-heading\">\n                <div class=\"row\">\n                    <div class=\"col-xs-3\">\n                        <i class=\"fa fa-bell fa-5x\"></i>\n                    </div>\n                    <div class=\"col-xs-9 text-right\">\n                        <div class=\"huge\">124</div>\n                        <div>Add Event Calls</div>\n                    </div>\n                </div>\n            </div>\n            <a href=\"\">\n                <div class=\"panel-footer\">\n                    <span class=\"pull-left\">View Details</span>\n                    <span class=\"pull-right\"><i class=\"fa fa-arrow-circle-right\"></i></span>\n                    <div class=\"clearfix\"></div>\n                </div>\n            </a>\n        </div>\n    </div>\n</div>\n";

function dashboardSummary(Restangular) {
    "use strict";

    return {
        restrict: "E",
        scope: {},
        controller: function controller($scope) {
            $scope.mqStats = {};
            $scope.eventStats = {};
            $scope.hasSeenAlert = hasSeenAlert;
            $scope.dismissAlert = function () {
                hasSeenAlert = true;
                $scope.hasSeenAlert = true;
            };
            Restangular.one("mq", "overview").get().then(function (response) {
                $scope.mqStats = response.data.plain();
            });
            Restangular.all("events").getList().then(function (response) {
                $scope.eventStats.totalCount = response.totalCount;
            });
        },

        template: dashboardSummaryTemplate
    };
};

dashboardSummary.$inject = ["Restangular"];

exports["default"] = dashboardSummary;
module.exports = exports["default"];

},{}],7:[function(require,module,exports){
"use strict";

module.exports = function (nga, admin) {
    var events = admin.getEntity("events");

    events.listView().fields([nga.field("id"), nga.field("object"), nga.field("action"), nga.field("version"), nga.field("apiUser", "reference").targetEntity(admin.getEntity("api_users")).targetField(nga.field("company")).label("Api User Company"), nga.field("created_at"), nga.field("updated_at")]).perPage(10).listActions(["show", "edit"]).filters([nga.field("object"), nga.field("action"), nga.field("version"), nga.field("apiUser")]);

    events.showView().fields([nga.field("id"), nga.field("apiUser"), nga.field("object"), nga.field("action"), nga.field("version"), nga.field("jsonSchema", "text"), nga.field("created_at"), nga.field("updated_at"), nga.field("subscribers", "referenced_list") // display list of related events
    .targetEntity(nga.entity("subscribers")).targetReferenceField("event_id").targetFields([nga.field("id").isDetailLink(true), nga.field("api_user_id"), nga.field("endPoint"), nga.field("created_at")]).sortField("created_at").sortDir("desc").listActions(["show", "edit"]).label("Latest Subscribers"), nga.field("", "template").label("").template("<ma-filtered-list-button entity-name=\"subscribers\" filter=\"{ event_id: entry.values.id }\"></ma-filtered-list-button>")]).actions(["<ma-filtered-list-button entity-name=\"subscribers\" filter=\"{ event_id: entry.values.id }\" label=\"Subscribers\"></ma-filtered-list-button>", "<notify-subscriber event=\"::entry\"></notify-subscriber>", "list"]);

    events.editionView().fields([nga.field("object").validation({ required: true, maxlength: 255 }), nga.field("action").validation({ required: true, maxlength: 255 }), nga.field("version").validation({ required: true }), nga.field("jsonSchema", "text").validation({ required: true }), nga.field("subscribers", "referenced_list") // display list of related events
    .targetEntity(nga.entity("subscribers")).targetReferenceField("event_id").targetFields([nga.field("id").isDetailLink(true), nga.field("api_user_id"), nga.field("endPoint"), nga.field("created_at")]).sortField("created_at").sortDir("desc").listActions(["show", "edit"]).label("Latest Subscribers"), nga.field("", "template").label("").template("<ma-filtered-list-button entity-name=\"subscribers\" size=\"sm\" filter=\"{ event_id: entry.values.id }\"></ma-filtered-list-button>")]).actions(["<ma-filtered-list-button entity-name=\"subscribers\" filter=\"{ event_id: entry.values.id }\" label=\"Subscribers\"></ma-filtered-list-button>", "<notify-subscriber event=\"::entry\"></notify-subscriber>", "list"]);

    events.editionView().onSubmitError(["error", "form", "progression", "notification", function (error, form, progression, notification) {
        // stop the progress bar
        progression.done();
        // add a notification
        notification.log("error " + error.data.code + ": " + error.data.message, { addnCls: "humane-flatty-error" });
        // cancel the default action (default error messages)
        return false;
    }]);

    return events;
};

},{}],8:[function(require,module,exports){
"use strict";

function notifySubscriber($location) {
    "use strict";

    return {
        restrict: "E",
        scope: {
            event: "&",
            size: "@"
        },
        link: function link(scope, element, attrs) {
            scope.notify = function () {
                $location.path("events/" + scope.event().values.id + "/notify");
            };
        },

        template: "<a class=\"btn btn-default\" ng-class=\"size ? 'btn-' + size : ''\" ng-click=\"notify()\">\n            <span class=\"glyphicon glyphicon-thumbs-up\" aria-hidden=\"true\"></span>&nbsp;Notify</a>"
    };
};

notifySubscriber.$inject = ["$location"];

exports["default"] = notifySubscriber;
module.exports = exports["default"];

},{}],9:[function(require,module,exports){
"use strict";

function notifySubscriberController($stateParams, notification, Restangular) {
    this.eventId = $stateParams.id;
    this.notification = notification;
    this.Restangular = Restangular;
};

notifySubscriberController.prototype.sendNotify = function () {
    //console.log(this.eventId,this.title,this.message,this.notification);
    var self = this;
    this.Restangular.one("events", this.eventId).customPOST({ title: self.title, message: self.message }, "notify").then(function (response) {
        return self.notification.log("Event notify to " + response.data.count + " subscriber(s)", { addnCls: "humane-flatty-success" });
    }).catch(function (e) {
        return self.notification.log("A problem occurred, please try again", { addnCls: "humane-flatty-error" });
    });
};

notifySubscriberController.$inject = ["$stateParams", "notification", "Restangular"];

exports["default"] = notifySubscriberController;
module.exports = exports["default"];

},{}],10:[function(require,module,exports){
"use strict";

var notifySubcriberController = require("./notifySubscriberController");
var notifySubcriberControllerTemplate = "<div class=\"row\"><div class=\"col-lg-12\">" + "<ma-view-actions><ma-back-button></ma-back-button></ma-view-actions>" + "<div class=\"page-header\">" + "<h1>Send Notification Event #{{ controller.eventId }}</h1>" + "<p class=\"lead\">You can send notification for every event subscribers email</p>" + "</div>" + "</div></div>" + "<div class=\"row form-field form-group\">" + "<label class=\"col-sm-2 control-label\">Title</label>" + "<div class=\"ng-admin-type-string col-sm-10 col-md-8 col-lg-7\"><input type=\"text\" size=\"10\" ng-model=\"controller.title\" class=\"form-control\" placeholder=\"subject / title\"/></div>" + "</div>" + "<div class=\"row form-field form-group\">" + "<label class=\"col-sm-2 control-label\">Message</label>" + "<div class=\"ng-admin-type-text col-sm-10 col-md-8 col-lg-7\"><textarea class=\"form-control\" ng-model=\"controller.message\" placeholder=\"message content\"></textarea></div>" + "</div>" + "<div class=\"row form-group\">" + "<div class=\"col-sm-offset-2 col-sm-10\">" + "<button type=\"button\" class=\"btn btn-primary\" ng-click=\"controller.sendNotify()\"><span class=\"glyphicon glyphicon-ok\"></span>&nbsp;<span class=\"hidden-xs ng-scope\" translate=\"SEND_NOTIFICATION\">Send Notification</span></button>" + "</div></div>";

exports["default"] = function ($stateProvider) {
    $stateProvider.state("eventNotify", {
        parent: "main",
        url: "/events/:id/notify",
        params: { id: null },
        controller: notifySubcriberController,
        controllerAs: "controller",
        template: notifySubcriberControllerTemplate
    });
};

module.exports = exports["default"];

},{"./notifySubscriberController":9}],11:[function(require,module,exports){
"use strict";

module.exports = function (nga, admin) {
    var subscribers = admin.getEntity("subscribers");

    subscribers.listView().fields([nga.field("id"), nga.field("event_id", "reference").targetEntity(admin.getEntity("events")).targetField(nga.field("id")).label("Event"), nga.field("api_user_id", "reference").targetEntity(admin.getEntity("api_users")).targetField(nga.field("company")).label("Api User Company"), nga.field("object"), nga.field("action"), nga.field("version"), nga.field("endPoint"), nga.field("created_at")]).perPage(10).listActions(["show", "edit"]).filters([nga.field("object"), nga.field("action"), nga.field("version"), nga.field("event_id"), nga.field("api_user_id")]);

    subscribers.showView().fields([nga.field("id"), nga.field("event_id"), nga.field("api_user_id"), nga.field("object"), nga.field("action"), nga.field("version"), nga.field("endPoint"), nga.field("created_at"), nga.field("updated_at")]);

    return subscribers;
};

},{}]},{},[1]);

//# sourceMappingURL=admin.bundle.js.map
