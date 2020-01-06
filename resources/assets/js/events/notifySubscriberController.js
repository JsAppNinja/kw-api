function notifySubscriberController($stateParams, notification, Restangular) {
    this.eventId = $stateParams.id;
    this.notification = notification;
    this.Restangular = Restangular;
}

notifySubscriberController.prototype.sendNotify = function() {
    //console.log(this.eventId,this.title,this.message,this.notification);
    var self=this;
    this.Restangular
        .one("events", this.eventId).customPOST({title: self.title, message: self.message},"notify")
        .then(function(response) {
            return self.notification.log("Event notify to "+response.data.count+" subscriber(s)", { addnCls: "humane-flatty-success" });
        }).catch(function(e){
            return self.notification.log("A problem occurred, please try again", { addnCls: "humane-flatty-error" }); 
        });
};

notifySubscriberController.$inject = ["$stateParams", "notification","Restangular"];

exports["default"] = notifySubscriberController;
module.exports = exports["default"];