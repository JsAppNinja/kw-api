
var notifySubcriberController = require("./notifySubscriberController");
var notifySubcriberControllerTemplate =
        "<div class=\"row\"><div class=\"col-lg-12\">" +
            "<ma-view-actions><ma-back-button></ma-back-button></ma-view-actions>" +
            "<div class=\"page-header\">" +
                "<h1>Send Notification Event #{{ controller.eventId }}</h1>" +
                "<p class=\"lead\">You can send notification for every event subscribers email</p>" +
            "</div>" +
        "</div></div>" +
        "<div class=\"row form-field form-group\">" +
            "<label class=\"col-sm-2 control-label\">Title</label>"+
            "<div class=\"ng-admin-type-string col-sm-10 col-md-8 col-lg-7\"><input type=\"text\" size=\"10\" ng-model=\"controller.title\" class=\"form-control\" placeholder=\"subject / title\"/></div>"+
        "</div>"+
        "<div class=\"row form-field form-group\">" +
            "<label class=\"col-sm-2 control-label\">Message</label>"+
            "<div class=\"ng-admin-type-text col-sm-10 col-md-8 col-lg-7\"><textarea class=\"form-control\" ng-model=\"controller.message\" placeholder=\"message content\"></textarea></div>"+
        "</div>"+
        "<div class=\"row form-group\">"+
        "<div class=\"col-sm-offset-2 col-sm-10\">"+
        "<button type=\"button\" class=\"btn btn-primary\" ng-click=\"controller.sendNotify()\"><span class=\"glyphicon glyphicon-ok\"></span>&nbsp;<span class=\"hidden-xs ng-scope\" translate=\"SEND_NOTIFICATION\">Send Notification</span></button>"+
        "</div></div>";


exports["default"] = function ($stateProvider) {
    $stateProvider.state("eventNotify", {
        parent: "main",
        url: "/events/:id/notify",
        params: {id: null},
        controller: notifySubcriberController,
        controllerAs: "controller",
        template: notifySubcriberControllerTemplate
    });
};

module.exports = exports["default"];