function notifySubscriber($location) {
    "use strict";

    return {
        restrict: "E",
        scope: {
            event: "&",
            size: "@",
        },
        link (scope, element, attrs) {
            scope.notify = function() {
                $location.path("events/" + scope.event().values.id + "/notify");
            };
        },
        template: `<a class="btn btn-default" ng-class="size ? \'btn-\' + size : \'\'" ng-click="notify()">
            <span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span>&nbsp;Notify</a>`
    };
}

notifySubscriber.$inject = ["$location"];

exports["default"] = notifySubscriber;
module.exports = exports["default"];