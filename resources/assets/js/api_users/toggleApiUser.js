function toggleApiUser(Restangular, $state, notification) {
    "use strict";

    return {
        restrict: "E",
        scope: {
            apiUser: "&",
            size: "@",
        },
        link (scope, element, attrs) {
            scope.apiUser = scope.apiUser();
            scope.type = attrs.type;
            scope.toggle = function() {
                Restangular
                    .one("api_users", scope.apiUser.values.id).get()
                    .then(function(apiUser) {
                        return apiUser.data.customGET("toggle");
                    }).then(function() {
                        return $state.reload();
                    }).then(function() {
                        return notification.log("Toggle Api User Done", { addnCls: "humane-flatty-success" });
                    }).catch(function(e){
                        return notification.log("A problem occurred, please try again", { addnCls: "humane-flatty-error" }); 
                    });
            };
        },
        template:
` <a ng-if="apiUser.values.isActive == 0" class="btn btn-outline btn-success" ng-class="size ? \'btn-\' + size : \'\'" ng-click="toggle()">
    <span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span>&nbsp;Enable
</a>
<a ng-if="apiUser.values.isActive == 1" class="btn btn-outline btn-danger" ng-class="size ? \'btn-\' + size : \'\'" ng-click="toggle()">
    <span class="glyphicon glyphicon-thumbs-down" aria-hidden="true"></span>&nbsp;Disable
</a>`
    };
}

toggleApiUser.$inject = ["Restangular", "$state", "notification"];

exports["default"] = toggleApiUser;
module.exports = exports["default"];